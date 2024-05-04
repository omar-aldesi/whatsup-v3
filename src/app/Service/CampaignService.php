<?php

namespace App\Service;

use App\Models\Campaign;
use App\Models\CampaignContact;
use App\Models\CampaignSchedule;
use App\Models\EmailGroup;
use App\Models\Group;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Gateway;
use App\Models\GeneralSetting;

class CampaignService
{
    public function __construct(
        protected EmailService $emailService,
        protected SmsService $smsService,
        protected WhatsAppService $whatsAppService
    ){}


    public function getChannelFromRoute(): string
    {
        if (request()->routeIs('admin.campaign.sms') || request()->routeIs('user.campaign.sms')) {
            return Campaign::SMS;
        }

        if (request()->routeIs('admin.campaign.email') || request()->routeIs('user.campaign.email')) {
            return Campaign::EMAIL;
        }
        return Campaign::WHATSAPP;
    }

    public function generateTitle(string $channel): string
    {
        $channelTranslations = [
            Campaign::SMS      => __('SMS Campaign'),
            Campaign::EMAIL    => __('Email Campaign'),
            Campaign::WHATSAPP => __('WhatsApp Campaign'),
        ];

        return $channelTranslations[$channel] ?? '';
    }

    public function getGroupsForChannel(string $channel)
    {
        if (request()->routeIs('admin.campaign.create') || request()->routeIs('admin.campaign.edit')) {

            return ($channel !== Campaign::EMAIL) ? Group::whereNull('user_id')->get() : EmailGroup::whereNull('user_id')->get();
        }
        elseif(request()->routeIs('user.campaign.create') || request()->routeIs('user.campaign.edit')) {

            return ($channel !== Campaign::EMAIL) ? Group::where('user_id',auth()->user()->id)->get() : EmailGroup::where('user_id',auth()->user()->id)->get();
        }
        
    }

    public function getTemplatesForChannel(string $channel)
    {
        if (request()->routeIs('admin.campaign.create') || request()->routeIs('admin.campaign.edit')) {

            return ($channel !== Campaign::EMAIL) ? Template::whereNull('user_id')->get() : [];
        }
        elseif(request()->routeIs('user.campaign.create') || request()->routeIs('user.campaign.edit')) {

            return ($channel !== Campaign::EMAIL) ? Template::where('user_id',auth()->user()->id)->get() : [];
        }
    }

    public function save(Request $request, $method = null): Campaign
    {
        $general = GeneralSetting::first();
        if($request->has('id')) {
            try{
                $current_campaign = Campaign::find($request->id);
                if($request->input('remove_media') == true) {
                    $postData = $this->whatsAppService->findAndUploadFile($request);
                } else {
                    $postData = $current_campaign->post_data ? $current_campaign->post_data : $this->whatsAppService->findAndUploadFile($request);
                }
                
            } catch( \Exception $e) {
                $notify[] = ['error', translate("Sorry Something went wrong")];
                return back()->withNotify($notify);
            }
        } else {
            $postData = $this->whatsAppService->findAndUploadFile($request);
        }
        
        $emailMethod = $request->input('gateway_id') ? Gateway::whereNotNull('mail_gateways')->where('id',$request->gateway_id)->first() : Gateway::whereNotNull('mail_gateways')->where('is_default', 1)->first();
        $smsMethod   = $request->input('gateway_id') ? Gateway::whereNotNull('sms_gateways')->where('id',$request->gateway_id)->first() : Gateway::whereNotNull('sms_gateways')->where('is_default', 1)->first();
        $campaignData = [
            'name'           => $request->input('name'),
            'channel'        => $request->input('channel'),
            'body'           => $request->input('message'),
            'subject'        => $request->input('subject'),
            'sms_type'       => $request->input('smsType'),
            'from_name'      => $request->input('from_name'),
            'post_data'      => count($postData) > 0 ? $postData : null,
            'reply_to_email' => $request->input('reply_to_email'),
            'status'         => 'Active',
            'schedule_time'  => $request->input('schedule_date'),
        ];
       
       
        
       
        if (request()->routeIs('user.campaign.*')) {
           
            $campaignData['user_id'] = auth()->user()->id;
            
            if($campaignData['channel'] == Campaign::EMAIL) {
            
                $campaignData['sender_id'] = $method->id;
            }
            if($campaignData['channel'] == Campaign::SMS) {

                if(auth()->user()) {
                    if(auth()->user()->sms_gateway == 1){
                        $campaignData['sender_id'] = $request->input('gateway_id') ? $request->input('gateway_id') : (int)$method->id;
                    }else{
                        $campaignData['sender_id'] = null;
                    }
                }
                else {
                    if($general->sms_gateway == 1) {
                        $campaignData['sender_id'] = $request->input('gateway_id') ? $request->input('gateway_id') : (int)$method->id;
                    }else{
                        $campaignData['sender_id'] = null;
                    }
                }
                
            }
        }
        else {
            if($campaignData['channel'] == Campaign::EMAIL) {
            
                $campaignData['sender_id'] = $emailMethod->id;
            }
            if($campaignData['channel'] == Campaign::SMS) {
    
                $campaignData['sender_id'] = null;
                if($general->sms_gateway == 1){
                    $campaignData['sender_id'] = (int)$smsMethod->id;
                }
            }
        }
        
        return Campaign::updateOrCreate(['id' => $request->input('id')], $campaignData);
    }


    public function saveSchedule(Request $request, int $campaignId): void
    {
        CampaignSchedule::create([
            'campaign_id'   => $campaignId,
            'repeat_number' => $request->input('repeat_number'),
            'repeat_format' => $request->input('repeat_format'),
        ]);
    }

    public function saveContacts(array $attachableData, Campaign $campaign): void
    {
        $contactNewArray = array_unique($attachableData['contacts']);
        $groupName       = $attachableData['contact_with_name'] ?? [];
        $data            = collect($contactNewArray)->map(function ($value) use ($campaign, $groupName) {
            $content     = $campaign->body;
            $replacement = $groupName[$value] ?? $value;
            $content     = str_replace('{{name}}', $replacement, $content);
            return [
                'campaign_id' => $campaign->id,
                'contact'     => $value,
                'message'     => $content,
            ];
        })->toArray();

        CampaignContact::insert($data);
    }

    public function processContacts(Request $request): array
    {
        $groupName = []; $contacts = [];
        
        if($request->input('channel') == Campaign::EMAIL){

            if($request->has('group_id')){
                $request->merge([
                    'email_group_id' => $request->input('group_id')
                ]);
            }
            if(request()->routeIs('user.campaign.store') || request()->routeIs('user.campaign.update') ) {
                $this->emailService->processEmail($request,$contacts, auth()->user()->id);
                $this->emailService->processEmailGroup($request, $contacts, $groupName, auth()->user()->id);
                $this->emailService->processEmailFile($request, $contacts, $groupName, auth()->user()->id);
            }
            elseif(request()->routeIs('admin.campaign.store') || request()->routeIs('admin.campaign.update')) {

                $this->emailService->processEmail($request,$contacts);
                $this->emailService->processEmailGroup($request, $contacts, $groupName, null);
                $this->emailService->processEmailFile($request, $contacts, $groupName);
            }
        }
        
        if(in_array($request->input('channel'), [Campaign::SMS, Campaign::WHATSAPP])) {

            if(request()->routeIs('user.campaign.store') || request()->routeIs('user.campaign.update') ) {

                $this->smsService->processNumber($request, $contacts, auth()->user()->id);
                $this->smsService->processGroupId($request, $contacts, $groupName, auth()->user()->id);
                $this->smsService->processFile($request, $contacts, $groupName, auth()->user()->id);
            }
            elseif(request()->routeIs('admin.campaign.store') || request()->routeIs('admin.campaign.update')) {

                $this->smsService->processNumber($request, $contacts);
                $this->smsService->processGroupId($request, $contacts, $groupName);
                $this->smsService->processFile($request, $contacts, $groupName);
            }
        }
        $contacts = $this->flattenAndUnique($contacts);
        
        return ([
            "contacts"          => $contacts,
            "contact_with_name" => $groupName,
        ]);
    }

    public function flattenAndUnique(array $allEmail): array
    {
        
        $newArray = [];
        foreach ($allEmail as $childArray) {
            foreach ($childArray as $value) {
                $newArray[] = $value;
            }
        }
        return array_unique($newArray);
    }

}

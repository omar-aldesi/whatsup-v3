<?php

namespace App\Service;

use App\Http\Requests\StoreSMSRequest;
use App\Http\Utility\SendSMS;
use App\Models\CampaignContact;
use App\Models\EmailLog;
use Illuminate\Http\Request;
use App\Jobs\ProcessSms;
use App\Models\Contact;
use App\Models\GeneralSetting;
use App\Models\SmsGateway;
use App\Models\SMSlog;
use App\Models\CreditLog;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Gateway;
use Illuminate\Support\Arr;
use PHPUnit\Exception;

class SmsService
{

    public CustomerService $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    /**
     * @param SMSlog $smsLog
     * @return void
     */ 
    public function sendSmsByOwnGateway($smsLog, $diffInSeconds = null): void
    {
        if(is_null($smsLog->android_gateway_sim_id) && !is_null($smsLog->api_gateway_id)){
            $smsGateway = Gateway::where('id', $smsLog->api_gateway_id)->first();
            if($smsGateway) {
                $creds = $smsGateway->sms_gateways;
                ProcessSms::dispatch($smsLog, (array)$creds, $smsGateway)->delay(now()->addSeconds($diffInSeconds));
            }
        }
	}


    public function searchSmsLog($search, $searchDate): \Illuminate\Database\Eloquent\Builder
    {
        $smsLogs = SMSlog::query();
        if (!empty($search)) {
            $smsLogs->whereHas('user',function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('to', 'like', "%$search%");
            });
        }

        if (!empty(request()->input('status'))) {
            $status = match (request()->input('status')){
                'pending' => [1],
                'schedule' => [2],
                'fail' => [3],
                'delivered' => [4],
                'processing' => [5],
                default => [1,2,3,4,5],
            };
            $smsLogs->whereIn('status',$status);
        }


        if (!empty($searchDate)) {

            $dateRange = explode('-', $searchDate);
            $firstDate = Carbon::createFromFormat('m/d/Y', trim($dateRange[0]))->startOfDay();
            $lastDate  = isset($dateRange[1]) ? Carbon::createFromFormat('m/d/Y', trim($dateRange[1]))->endOfDay() : null;
            if ($firstDate) {
                $smsLogs->whereDate('created_at', '>=', $firstDate);
            }
            if ($lastDate) {
                $smsLogs->whereDate('created_at', '<=', $lastDate);
            }
        }

        return $smsLogs;
    }



	public function smsLogStatusUpdate(int $status, array $smsLogIds, GeneralSetting $general): void
	{
        $general = GeneralSetting::first();
       
		foreach(array_reverse($smsLogIds) as $smsLogId){
			$smslog = SMSlog::find($smsLogId);

			if(!$smslog){
				continue;
			}
           
            $wordLength = $smslog->sms_type == "plain" ? $general->sms_word_text_count : $general->sms_word_unicode_count;
            $user = User::find($smslog->user_id);

            if($status == SMSlog::PENDING && $user){
                
                $messages = str_split($smslog->message,$wordLength);
                $totalCredit = count($messages);
                
                if($user->credit >= $totalCredit){
                    $smslog->status = $status;
                    if($smslog->api_gateway_id) {
                        ProcessSms::dispatch($smslog, (array)$smslog->smsGateway()->first()->sms_gateways, $smslog->smsGateway()->first());
                    }
                    $this->customerService->deductCreditAndLogTransaction($user, $totalCredit, 1);
                }
            }else{
                $smslog->status = $status;
                if($smslog->api_gateway_id) {
                    ProcessSms::dispatch($smslog, (array)$smslog->smsGateway()->first()->sms_gateways, $smslog->smsGateway()->first());
                }
            }

			$smslog->update();
           
		}
	}


    /**
     * @param Request $request
     * @param array $allContactNumber
     * @return void
     */
    public function processNumber(Request $request, array &$allContactNumber): void
    {
        if($request->has('number')){
            $contactNumber = preg_replace('/[ ,]+/', ',', trim($request->input('number')));
            $allContactNumber[]  = explode(",",$contactNumber);
        }
    }


    /**
     * @param Request $request
     * @param array $allContactNumber
     * @param array $numberGroupName
     * @param null $userId
     * @return void
     */
    public function processGroupId(Request $request, array &$allContactNumber, array &$numberGroupName, $userId = null): void
    {
        
        if ($request->has('group_id')) {
       
            $contact = Contact::query();
            $contact->whereIn('group_id', $request->input('group_id'));
      
            if (!is_null($userId)) {
                $contact->where('user_id', $userId);
            } else {
                $contact->whereNull('user_id');
            }

            $allContactNumber[] = $contact->pluck('contact_no')->toArray();
            
            $numberGroupName = $contact->pluck('name', 'contact_no')->toArray();
    
        }
    }


    /**
     * @param Request $request
     * @param array $allContactNumber
     * @param array $numberGroupName
     * @return void
     */
    public function processFile(Request $request, array &$allContactNumber, array &$numberGroupName): void
    {
        if($request->has('file')){
            $service = new FileProcessService();
            $extension = strtolower($request->file('file')->getClientOriginalExtension());

            if($extension == "csv"){
                $response =  $service->processCsv($request->file('file'));
                $allContactNumber[] = array_keys($response);
                $numberGroupName = $numberGroupName + $response;
            }

            if($extension == "xlsx"){
                $response =  $service->processExel($request->file('file'));
                $allContactNumber[] = array_keys($response);
                $numberGroupName = $numberGroupName + $response;
            }
            
        }
    }


    /**
     * @param $allContactNumber
     * @return array
     */
    public function flattenAndUnique($allContactNumber): array {

        $contactNewArray = [];
        foreach ($allContactNumber as $childArray) {
            foreach ($childArray as $value) {
                $contactNewArray[] = $value;
            }
        }
        $filtered = Arr::where($contactNewArray, function (string|int $value, int $key) {
            return $value !== "" && filter_var($value, FILTER_SANITIZE_NUMBER_INT);
        });

        return array_unique($filtered);
    }


    /**
     * @param string $value
     * @param array $numberGroupName
     * @param string $message
     * @return string
     */
    public function getFinalContent(string $value, array $numberGroupName, string $message): string
    {

        $finalContent = str_replace('{{name}}',$value, offensiveMsgBlock($message));
      
        if(array_key_exists($value,$numberGroupName)){
            $finalContent = str_replace('{{name}}', $numberGroupName ? $numberGroupName[$value]:$value, offensiveMsgBlock($message));
        }

        return $finalContent;
    }


    /**
     * @param string $contact
     * @param StoreSMSRequest $request
     * @param array $numberGroupName
     * @param int|null $apiGatewayId
     * @param int|null $userId
     * @return array
     */
    public function prepParams(string $contact, StoreSMSRequest $request, array $numberGroupName, ?int $apiGatewayId, ?int $userId = null): array
    {   
        $generalSetting = GeneralSetting::first();
        $contact        = filterContactNumber($contact);
        $value          = array_key_exists($contact, $numberGroupName) ? $numberGroupName[$contact] : $contact;
        $setTimeInDelay = $request->input('schedule') == 2 ? $request->input('schedule_date') : Carbon::now();
        $wordLength = $request->input('smsType') == "plain" ? $generalSetting->sms_word_text_count : $generalSetting->sms_word_unicode_count;
        $finalContent = $this->getFinalContent($value, $numberGroupName, $request->input('message'));
        
        return  [
            'to' => $contact,
            'word_length' => $wordLength,
            'user_id' => $userId,
            'sms_type' => $request->input('sms_type') == "plain" ? 1 : 2,
            'initiated_time' => $setTimeInDelay,
            'message' => $finalContent,
            'status' => $request->input('schedule') == 2 ? 2 : SMSlog::PENDING,
            'schedule_status' => $request->input('schedule'),
            'api_gateway_id' => $apiGatewayId,
        ];
    }

    /**
     * @param array $params
     * @return SMSlog
     */
    public function saveSMSLog(array $params): SMSlog
    {
        return SMSlog::create([
            'to' => $params['to'],
            'word_length' => $params['word_length'],
            'user_id' => $params['user_id'],
            'sms_type' => $params['sms_type'],
            'initiated_time' => $params['initiated_time'],
            'message' => $params['message'],
            'status' => $params['status'],
            'schedule_status' => $params['schedule_status'],
            'api_gateway_id' => $params['api_gateway_id'],
        ]);
    }


    /**
     * @param array $contactNewArray
     * @param GeneralSetting $general
     * @param gateway $smsGateway
     * @param StoreSMSRequest $request
     * @param array $numberGroupName
     * @return void
     */
    public function sendSMS(array $contactNewArray, GeneralSetting $general, $smsGateway, StoreSMSRequest $request, array $numberGroupName): void
    {

        $apiGatewayId = null;
        if($general->sms_gateway == 1){
            $apiGatewayId = (int) $smsGateway->id;
        }

        foreach ($contactNewArray as $value) {
            $log = $this->saveSMSLog($this->prepParams((string)$value, $request, $numberGroupName, $apiGatewayId));
            if($log->status == 1){
                $this->sendSmsByOwnGateway($log);
            }
        }
    }


    /**
     * @param SMSlog $log
     * @param $status
     * @param $errorMessage
     * @return void
     */
    public static function updateSMSLogAndCredit(SMSlog $log, $status, $errorMessage = null): void
    {
        $log->status = $status == 'Success' ? SMSlog::SUCCESS :  SMSlog::FAILED;
        $log->response_gateway = !is_null($errorMessage) ? $errorMessage : null;
        $log->save();


        $user = User::find($log->user_id);

        if($user && $status == 'Fail'){
            $messages = str_split($log->message,$log->word_length);
            $totalcredit = count($messages);
            CustomerService::addedCreditLog($user, $totalcredit, $log->to);
        }



        if($log->contact_id){

            CampaignContact::where('id',$log->contact_id)->update([
                "status" => $status,
            ]);
        }
    }

}

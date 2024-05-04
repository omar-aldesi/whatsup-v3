<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSMSRequest;
use App\Service\SmsService;
use Exception;
use Illuminate\Http\Request;
use App\Models\SMSlog;
use App\Models\User;
use App\Models\CreditLog;
use App\Models\Group;
use App\Models\GeneralSetting;
use App\Models\SmsGateway;
use App\Models\Template;
use Carbon\Carbon;
use App\Models\Gateway;


class SmsController extends Controller
{
    public SmsService $smsService ;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function index()
    {
    	$title = "All SMS History";
    	$smslogs = SMSlog::orderBy('id', 'DESC')->with('user', 'androidGateway', 'smsGateway')->paginate(paginateNumber());
    	return view('admin.sms.index', compact('title', 'smslogs'));
    }


    public function search(Request $request)
    {
        $search = $request->input('search');
        $searchDate = $request->input('date');
        $status = $request->input('status');

        if (empty($search) && empty($searchDate) && empty($status)) {
            $notify[] = ['error', 'Search data field empty'];
            return back()->withNotify($notify);
        }

        $smslogs = $this->smsService->searchSmsLog($search, $searchDate);
        $smslogs = $smslogs->paginate(paginateNumber());

        $title = 'Email History Search - ' . $search . ' '.$searchDate.' '.$status;
        return view('admin.sms.index', compact('title', 'smslogs', 'search', 'searchDate', 'status'));
    }

    public function smsStatusUpdate(Request $request)
    {
        $request->validate([
            'id'     => 'nullable|exists:s_m_slogs,id',
            'status' => 'required|in:1,3,4',
        ]);
        $general = GeneralSetting::first();
       

        if($request->input('smslogid') !== null){
            
            $smsLogIds = array_filter(explode(",",$request->input('smslogid')));
            if(!empty($smsLogIds)){
            
                $this->smsService->smsLogStatusUpdate((int) $request->status, (array) $smsLogIds, $general);
            }
        }

        if($request->has('id')){
           
            $this->smsService->smsLogStatusUpdate((int) $request->status, (array) $request->input('id'), $general);
        }

        $notify[] = ['success', 'SMS status has been updated'];
        return back()->withNotify($notify);
    }

    public function create()
    {
        $title = "Compose SMS";
        $general = GeneralSetting::first();
        $templates = Template::whereNull('user_id')->get();
        $groups = Group::whereNull('user_id')->get();
        $credentials = SmsGateway::orderBy('id','asc')->get();
        return view('admin.sms.create', compact('title', 'general', 'groups', 'templates', 'credentials'));
    }


    /**
     * @param StoreSMSRequest $request
     * @return mixed
     */
    public function store(StoreSMSRequest $request):mixed
    {
       
        $general = GeneralSetting::first();
        if($general->sms_gateway == 2) {
            $smsGateway = null;
        } else {
            $defaultGateway = Gateway::whereNotNull('sms_gateways')->whereNull('user_id')->where('is_default', 1)->first();
        if($request->input('gateway_id')) {
            $smsGateway = Gateway::whereNotNull('sms_gateways')->whereNull('user_id')->where('id',$request->gateway_id)->firstOrFail();
        }
            else{
                if($defaultGateway) {
                    $smsGateway = $defaultGateway;
                }
                else {
                    $notify[] = ['error', 'No Available Default SMS Gateway'];
                    return back()->withNotify($notify);
                }
            }
            if(!$smsGateway){
                $notify[] = ['error', 'Invalid Sms Gateway'];
                return back()->withNotify($notify);
            }
        }
        
        if(!$request->input('number') && !$request->has('group_id') && !$request->has('file')){
            $notify[] = ['error', 'Invalid number collect format'];
            return back()->withNotify($notify);
        }

        

        if($request->has('file')) {
            $extension = strtolower($request->file('file')->getClientOriginalExtension());
            if (!in_array($extension, ['csv', 'xlsx'])) {
                $notify[] = ['error', 'Invalid file extension'];
                return back()->withNotify($notify);
            }
        }

        $numberGroupName = []; $allContactNumber = [];
     
        $this->smsService->processNumber($request, $allContactNumber);
        $this->smsService->processGroupId($request, $allContactNumber, $numberGroupName);
        $this->smsService->processFile($request, $allContactNumber, $numberGroupName);
        
        $contactNewArray = $this->smsService->flattenAndUnique($allContactNumber);
          
        $this->smsService->sendSMS($contactNewArray, $general, $smsGateway, $request, $numberGroupName);
        
    
        session()->forget(['old_sms_message', 'number']);

        $notify[] = ['success', 'New SMS request sent, please see in the SMS history for final status'];
        return back()->withNotify($notify);
    }

    public function delete(Request $request)
    {
        $this->validate($request, [
            'id' => 'required'
        ]);
        try {
            $smsLog = SMSlog::findOrFail($request->id);
            $general = GeneralSetting::first();

            $wordLenght = $smsLog->sms_type == 1 ? $general->sms_word_text_count : $general->sms_word_unicode_count;

            if ($smsLog->status==1) {

                $user = User::find($smsLog->user_id);
                if($user){

                    $messages = str_split($smsLog->message,$wordLenght);
                    $totalcredit = count($messages);
                    $user->credit += $totalcredit;
                    $user->save();
                    $creditInfo = new CreditLog();
                    $creditInfo->user_id = $smsLog->user_id;
                    $creditInfo->credit_type = "+";
                    $creditInfo->credit = $totalcredit;
                    $creditInfo->trx_number = trxNumber();
                    $creditInfo->post_credit =  $user->credit;
                    $creditInfo->details = $totalcredit." Credit Return ".$smsLog->to." is Falied";
                    $creditInfo->save();
                }
            }
            $smsLog->delete();
            $notify[] = ['success', "Successfully SMS log deleted"];
        } catch (Exception $e) {
            $notify[] = ['error', "Error occur in SMS delete time. Error is ".$e->getMessage()];
        }
        return back()->withNotify($notify);
    }
}

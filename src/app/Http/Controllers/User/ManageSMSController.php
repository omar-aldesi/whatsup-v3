<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSMSRequest;
use App\Service\CustomerService;
use App\Service\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use App\Models\Contact;
use App\Models\SMSlog;
use App\Models\GeneralSetting;
use App\Models\CreditLog;
use App\Models\SmsGateway;
use Carbon\Carbon;
use Shuchkin\SimpleXLSX;
use App\Jobs\ProcessSms;
use App\Models\Subscription;
use App\Service\FileProcessService;
use Illuminate\Support\Facades\Route;
use App\Models\Gateway;
use App\Models\PricingPlan;
use Closure;

class ManageSMSController extends Controller
{

    public SmsService $smsService;
    public CustomerService $customerService;
    
    public function __construct(SmsService $smsService, CustomerService $customerService)
    {
        $this->smsService = $smsService;
        $this->customerService = $customerService;
        $this->middleware(function (Request $request, Closure $next) {
            
            if(Auth::user()->credit == 0 && in_array(str_replace('user.sms.', '', Route::currentRouteName()),  ['send', 'store'])) {
               
                $notify[] = ['error', 'You no longer have sufficient SMS credits. Please purchase a new subscription plan.'];
                return redirect()->route('user.dashboard')->withNotify($notify);
            }
            return $next($request);
        });
    }

    public function create()
    {
    	$user = Auth::user();
        $title = "Compose SMS";
        $groups = $user->group()->get();
        $templates = $user->template()->get();
        $credentials = SmsGateway::orderBy('id','asc')->get();
        $allowed_access = (object)planAccess($user);

       
        if($allowed_access->type == PricingPlan::USER && ($user->gateway->isNotEmpty() && $user->gateway()->sms()->active()->exists()) || auth()->user()->sms_gateway == 2) {
    
            return view('user.sms.create', compact('title','groups', 'templates', 'credentials', 'user', 'allowed_access'));
        }
        elseif($allowed_access->type == PricingPlan::ADMIN) {
            return view('user.sms.create', compact('title','groups', 'templates', 'credentials', 'user', 'allowed_access'));
        }
        else{
            $notify[] = ['error', 'Can Not Compose SMS. No Active Gateway Found'];
            return back()->withNotify($notify);
        }
        
       
    }

    public function index()
    {
    	$title = "SMS History";
        $user = Auth::user();
        $smslogs = SMSlog::where('user_id', $user->id)->orderBy('id', 'DESC')->with('smsGateway', 'androidGateway')->paginate(paginateNumber());
    	return view('user.sms.index', compact('title', 'smslogs'));
    }


    public function store(StoreSMSRequest $request)
    {
        
        $user = Auth::user();
        $subscription = Subscription::where('user_id',$user->id)->where('status','1')->count();
        $general = GeneralSetting::first();
        $wordLength = $request->input('sms_type') == "plain" ? $general->sms_word_text_count : $general->sms_word_unicode_count;

        if($subscription == 0){
            $notify[] = ['error', 'Your Subscription Is Expired! Buy A New Plan'];
            return back()->withNotify($notify);
        }
        if(auth()->user()->sms_gateway == 2) {
            $smsGateway = null;
        }
        else{
            $defaultGateway = Gateway::whereNotNull('sms_gateways')->where("user_id", $user->id)->where('is_default', 1)->first();
        
            if($request->input('gateway_type')) {
    
                $smsGateway = Gateway::where('id', $request->input('gateway_id'))->firstOrFail();
            }
            else{
                if($defaultGateway) {
                    $smsGateway = $defaultGateway;
                }
                else {
                    $notify[] = ['error', 'You Do Not Have Any Default Gateway.'];
                    return back()->withNotify($notify);
                }
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
        

        $numberGroupName  = []; $allContactNumber  = [];

        $this->smsService->processNumber($request, $allContactNumber, $user->id);
        $this->smsService->processGroupId($request, $allContactNumber, $numberGroupName, $user->id);
        $this->smsService->processFile($request, $allContactNumber, $numberGroupName, $user->id);

        $contactNewArray = $this->smsService->flattenAndUnique($allContactNumber);
        $totalMessage = count(str_split($request->input('message'),$wordLength));
        $totalNumber = count($contactNewArray);
        $totalCredit = $totalNumber * $totalMessage;
  
        if($totalCredit > $user->credit){
            $notify[] = ['error', 'You do not have a sufficient credit for send message'];
            return back()->withNotify($notify);
        }

        $this->customerService->deductCreditAndLogTransaction($user, (int)$totalCredit, (int) $totalNumber);
        foreach ($contactNewArray as $value) {

            if(!filter_var($value, FILTER_SANITIZE_NUMBER_INT)){
                continue;
            }
            
            if(auth()->user()->sms_gateway == 1){
                $apiGatewayId = $request->input('gateway_id') ? $request->input('gateway_id') : (int)$smsGateway->id;
            }else{
                $apiGatewayId = null;
            }
            
            $params = $this->smsService->prepParams((string)$value, $request, $numberGroupName, $apiGatewayId, (int)$user->id);
            $log = $this->smsService->saveSMSLog($params);
            if($log->status == 1) {
                $this->smsService->sendSmsByOwnGateway($log);
            }
        }

        session()->forget('user_sms_message');

        $notify[] = ['success', 'New SMS request sent, please see in the SMS history for final status'];
        return redirect()->route('user.sms.index')->withNotify($notify);
    }


    public function search(Request $request)
    {
        $user = \auth()->user();
        $search = $request->input('search');
        $searchDate = $request->input('date');
        $status = $request->input('status');

        if (empty($search) && empty($searchDate) && empty($status)) {
            $notify[] = ['error', 'Search data field empty'];
            return back()->withNotify($notify);
        }

        $smslogs = $this->smsService->searchSmsLog($search, $searchDate);
        $smslogs = $smslogs->where('user_id', $user->id)
            ->orderBy('id','desc')
            ->with('user', 'androidGateway', 'smsGateway')
            ->paginate(paginateNumber());

        $title = 'Email History Search - ' . $search . ' '.$searchDate.' '.$status;
        return view('user.sms.index', compact('title', 'smslogs', 'search', 'searchDate', 'status'));
    }

    public function smsStatusUpdate(Request $request)
    {
       
        $request->validate([
            'id' => 'nullable|exists:s_m_slogs,id',
            'status' => 'required|in:1,4',
        ]);
        $general = GeneralSetting::first();
        $smsGateway = SmsGateway::where('id', $general->sms_gateway_id)->first();

        if(!$smsGateway){
            $notify[] = ['error', 'Invalid Sms Gateway'];
            return back()->withNotify($notify);
        }

        if($request->input('smslogid') !== null){
            $smsLogIds = array_filter(explode(",",$request->input('smslogid')));
            if(!empty($smsLogIds)){
                $this->smsService->smsLogStatusUpdate((int) $request->status, (array) $smsLogIds, $general, $smsGateway);
            }
        }

        if($request->has('id')){
            $this->smsService->smsLogStatusUpdate((int) $request->status, (array) $request->input('id'), $general, $smsGateway);
        }

        $notify[] = ['success', 'SMS status has been updated'];
        return back()->withNotify($notify);
    }


    //Select Gateway
    public function selectGateway(Request $request) {
        
        $user = Auth::user();
        $allowed_access = (object)planAccess($user);
        $rows = $allowed_access->type == PricingPlan::USER ? 
                Gateway::whereNotNull("sms_gateways")->where('status', 1)->where("user_id", $user->id)->where('type', $request->type)->latest()->get()
                : Gateway::whereNotNull("sms_gateways")->where('status', 1)->whereNull("user_id")->where('type', $request->type)->latest()->get();
        return response()->json($rows);
    }
}

<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWhatsAppRequest;
use App\Service\CustomerService;
use App\Service\SmsService;
use App\Service\WhatsAppService;
use Illuminate\Http\Request;
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
use App\Models\WhatsappCreditLog;
use App\Models\WhatsappDevice;
use App\Models\WhatsappLog;
use App\Rules\MessageFileValidationRule;
use App\Service\FileProcessService;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Closure;
use App\Models\User;

class ManageWhatsappController extends Controller
{

    public WhatsAppService $whatsAppService;
    public SmsService $smsService;
    public CustomerService $customerService;

    public function __construct(WhatsAppService $whatsAppService, SmsService $smsService, CustomerService $customerService)
    {
        $this->whatsAppService = $whatsAppService;
        $this->smsService = $smsService;
        $this->customerService = $customerService;
        $this->middleware(function (Request $request, Closure $next) {
            
            if(Auth::user()->whatsapp_credit == 0 && in_array(str_replace('user.whatsapp.', '', Route::currentRouteName()),  ['send', 'store'])) {
               
                $notify[] = ['error', 'You no longer have sufficient Whatsapp credits. Please purchase a new subscription plan.'];
                return redirect()->route('user.dashboard')->withNotify($notify);
            }
            return $next($request);
        });
    }
    public function create()
    {
    	$title = "Compose WhatsApp Massage";
    	$user = Auth::user();
    	$groups = $user->group()->get();
    	$templates = $user->template()->get();
    	return view('user.whatsapp.create', compact('title', 'groups', 'templates'));
    }

    public function index()
    {
    	$title = "WhatsApp History";
        $user = Auth::user();
        $whatsAppLogs = WhatsappLog::where('user_id', $user->id)->orderBy('id', 'DESC')->with('whatsappGateway')->paginate(paginateNumber());
    	return view('user.whatsapp.index', compact('title', 'whatsAppLogs'));
    }

    public function store(StoreWhatsAppRequest $request)
    {
        session()->put('old_wa_message',$request->input('message') ? $request->input('message') : "");
        $user = Auth::user();

        $general = GeneralSetting::first();
        $subscription = Subscription::where('user_id',$user->id)->where('status','1')->get();

        if(count($subscription) == 0){
            $notify[] = ['error', 'Your Subscription Is Expired! Buy A New Plan'];
            return back()->withNotify($notify);
        }

        $this->whatsAppService->fileValidationRule(request());

        if(!$request->input('number') && !$request->input('group_id') && !$request->has('file')){
            $notify[] = ['error', 'Invalid number collect format'];
            return back()->withNotify($notify);
        }

        
        $whatsappGateway = WhatsappDevice::where('user_id', auth()->user()->id)->where('status', 'connected')->select('max_delay','min_delay','id')->get();
        if(count($whatsappGateway) < 1){
            $notify[] = ['error', 'Not available WhatsApp Gateway'];
            return back()->withNotify($notify);
        }

        if($request->has('file')) {
            $extension = strtolower($request->file('file')->getClientOriginalExtension());
            if (!in_array($extension, ['csv', 'xlsx'])) {
                $notify[] = ['error', 'Invalid file extension'];
                return back()->withNotify($notify);
            }
        }

        $allContactNumber = []; $numberGroupName  = [];

        $this->smsService->processNumber(request(), $allContactNumber);
        $this->smsService->processGroupId(request(), $allContactNumber, $numberGroupName, $user->id);
        $this->smsService->processFile(request(), $allContactNumber, $numberGroupName);

        $contactNewArray = $this->smsService->flattenAndUnique($allContactNumber);

        $wordLength = $general->whatsapp_word_count;
        $messages = str_split($request->input('message'),$wordLength);
        $totalCredit = count($contactNewArray) * count($messages);

        if($totalCredit > $user->whatsapp_credit){
            $notify[] = ['error', 'You do not have a sufficient credit for send message'];
            return back()->withNotify($notify);
        }

        $this->customerService->deductWhatsAppCredit($user, (int)$totalCredit, count($contactNewArray));
        $this->whatsAppService->save(\request(), $contactNewArray, (int)$wordLength, $numberGroupName, $whatsappGateway, $user->id);

        session()->forget('old_wa_message');

        $notify[] = ['success', 'New WhatsApp Message request sent, please see in the WhatsApp Log history for final status'];
        return redirect()->route('user.whatsapp.index')->withNotify($notify);
    }


    public function search(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');
        $searchDate = $request->input('date');
        $status = $request->input('status');

        if (empty($search) && empty($searchDate) && empty($status)) {
            $notify[] = ['error', 'Search data field empty'];
            return back()->withNotify($notify);
        }

        $whatsAppLogs = $this->whatsAppService->searchWhatsappLog($search, $searchDate);
        $whatsAppLogs = $whatsAppLogs->where('user_id', $user->id)->paginate(paginateNumber());

        $title = 'Email History Search - ' . $search . ' '.$searchDate.' '.$status;
        return view('user.whatsapp.index', compact('title', 'whatsAppLogs', 'search', 'searchDate', 'status'));
    }

    public function statusUpdate(Request $request)
    {
        $request->validate([
            'id' => 'nullable|exists:whatsapp_logs,id',
            'status' => 'required|in:1,4',
        ]);

        if($request->input('smslogid') !== null){
            $smsLogIds = array_filter(explode(",",$request->input('smslogid')));
            if(!empty($smsLogIds)){
                $this->whatsappLogStatusUpdate((int) $request->input('status'), (array) $smsLogIds);
            }
        }

        if($request->has('id')){
            $this->whatsappLogStatusUpdate((int) $request->input('status'), (array) $request->input('id'));
        }

        $notify[] = ['success', 'WhatsApp status has been updated'];
        return back()->withNotify($notify);
    }
    private function whatsappLogStatusUpdate(int $status, array $smsLogIds): void
    {
        $general = GeneralSetting::first();

        foreach($smsLogIds as $smsLogId){
            $smslog = WhatsappLog::find($smsLogId);

            if(!$smslog){
                continue;
            }

            $wordLength = $general->whatsapp_word_count;
            $user = User::find($smslog->user_id);

            if($status == WhatsappLog::PENDING && $user){
                $messages = str_split($smslog->message,$wordLength);
                $totalCredit = count($messages);

                if($user->credit > $totalCredit){
                    $smslog->status = $status;
                    $this->customerService->deductWhatsAppCredit($user, $totalCredit, 1);
                }
            }else{
                $smslog->status = $status;
            }
            $smslog->save();
        }
    }

}


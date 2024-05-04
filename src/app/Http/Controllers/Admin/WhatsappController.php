<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWhatsAppRequest;
use App\Service\CustomerService;
use App\Service\SmsService;
use App\Service\WhatsAppService;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Group;
use App\Models\GeneralSetting;
use App\Models\Template;
use App\Models\Contact;
use App\Models\WhatsappCreditLog;
use App\Models\WhatsappDevice;
use App\Models\WhatsappLog;
use App\Rules\MessageFileValidationRule;
use App\Service\FileProcessService;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use App\Jobs\ProcessWhatsapp;

class WhatsappController extends Controller
{

    public WhatsAppService $whatsAppService;
    public CustomerService $customerService;
    public SmsService $smsService;

    public function __construct(WhatsAppService $whatsAppService, SmsService $smsService, CustomerService $customerService)
    {
        $this->whatsAppService = $whatsAppService;
        $this->customerService = $customerService;
        $this->smsService = $smsService;
    }
    public function index()
    {
        $title = "All Whatsapp Message History";
        $smslogs = WhatsappLog::orderBy('id', 'DESC')->with('user', 'whatsappGateway')->paginate(paginateNumber());
        return view('admin.whatsapp_messaging.index', compact('title', 'smslogs'));
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

        $smslogs = $this->whatsAppService->searchWhatsappLog($search, $searchDate);
        $smslogs = $smslogs->paginate(paginateNumber());

        $title = 'Email History Search - ' . $search . ' '.$searchDate.' '.$status;
        return view('admin.whatsapp_messaging.index', compact('title', 'smslogs', 'search', 'searchDate', 'status'));
    }

    public function statusUpdate(Request $request)
    {
        $request->validate([
            'id' => 'nullable|exists:whatsapp_logs,id',
            'status' => 'required|in:1,3,4',
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
        $i = 1; $addSecond = 50;

        foreach(array_reverse($smsLogIds) as $smsLogId) {
            
            $smslog = WhatsappLog::find($smsLogId);
            if(!$smslog) {

                continue;
            }
            if($smslog->status != WhatsappLog::PENDING) {
                $wordLength = $general->whatsapp_word_count;
                $user = User::find($smslog->user_id);

                if($status == WhatsappLog::PENDING && $user) {
                    $whatsappGateway = WhatsappDevice::where('user_id', $smslog->user_id)->where('status', 'connected')->firstorFail();
                    $messages = str_split($smslog->message,$wordLength);
                    $totalCredit = count($messages);
                
                    if($user->whatsapp_credit >= $totalCredit){
                        $smslog->status = $status;
                        $this->customerService->deductWhatsAppCredit($user, $totalCredit, 1);
                    }
                }else{
                    $whatsappGateway = WhatsappDevice::where('admin_id', auth()->guard('admin')->user()->id)->where('status', 'connected')->firstorFail();
                    $smslog->whatsapp_id = $whatsappGateway->id;
                    $smslog->status = $status;
                }
                $rand = rand($whatsappGateway->min_delay ,$whatsappGateway->max_delay);
                $addSecond = $i * $rand;
                $smslog->save();
                if($smslog->status == WhatsappLog::PENDING) { 
                   
                    ProcessWhatsapp::dispatch($smslog)->delay(Carbon::now()->addSeconds($addSecond));
                    $i++;
                }
            }
        }
    }

    public function create()
    {
        $title = "Compose WhatsApp Message";
        $templates = Template::whereNull('user_id')->get();
        $groups = Group::whereNull('user_id')->get();
        return view('admin.whatsapp_messaging.create', compact('title', 'groups', 'templates'));
    }

    public function store(StoreWhatsAppRequest $request) {
       
        session()->put('old_message',$request->input('message') ?  $request->input('message') : "");
        $this->whatsAppService->fileValidationRule(request());
        $general = GeneralSetting::first();

        if(!$request->input('number') && !$request->input('group_id') && !$request->has('file')){
            $notify[] = ['error', 'Invalid number collect format'];
            return back()->withNotify($notify);
        }

        
        $whatsappGateway = WhatsappDevice::where('admin_id', auth()->guard('admin')->user()->id)->where('status', 'connected')->select('max_delay','min_delay','id')->get();
        if(count($whatsappGateway) < 1){
            $notify[] = ['error', 'Not available WhatsApp Gateway'];
            return back()->withNotify($notify);
        }

        $numberGroupName = []; $allContactNumber = [];

        $this->smsService->processNumber(request(), $allContactNumber);
        $this->smsService->processGroupId(request(), $allContactNumber, $numberGroupName);
        $this->smsService->processFile(request(), $allContactNumber, $numberGroupName);

        $contactNewArray = $this->smsService->flattenAndUnique($allContactNumber);
        $wordLength = $general->whatsapp_word_count;
      
        $this->whatsAppService->save(\request(), $contactNewArray, (int)$wordLength, $numberGroupName, $whatsappGateway);

        $notify[] = ['success', 'New WhatsApp Message request sent, please see in the WhatsApp Log history for final status'];
        session()->forget('old_message');
        return back()->withNotify($notify);
    }

    public function delete(Request $request)
    {
        $this->validate($request, [
            'id' => 'required'
        ]);

        $smsLog = WhatsappLog::findOrFail($request->id);
        $user = User::find($smsLog->user_id);

        if($user && $smsLog->status == 1){
            $messages = str_split($smsLog->message,160);
            $totalcredit = count($messages);

            $user->credit += $totalcredit;
            $user->save();

            $creditInfo = new WhatsappCreditLog();
            $creditInfo->user_id = $smsLog->user_id;
            $creditInfo->type = "+";
            $creditInfo->credit = $totalcredit;
            $creditInfo->trx_number = trxNumber();
            $creditInfo->post_credit =  $user->whatsapp_credit;
            $creditInfo->details = $totalcredit." Credit Return ".$smsLog->to." is Falied";
            $creditInfo->save();
        }

        $smsLog->delete();
        $notify[] = ['success', "Successfully SMS log deleted"];
        return back()->withNotify($notify);

    }
}

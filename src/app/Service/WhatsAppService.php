<?php

namespace App\Service;


use App\Models\SMSlog;
use App\Models\User;
use App\Models\WhatsappCreditLog;
use App\Models\WhatsappLog;
use App\Rules\MessageFileValidationRule;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Jobs\ProcessWhatsapp;

class WhatsAppService
{

    public SmsService $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function fileValidationRule(Request $request): void
    {
        $files = ['document', 'audio', 'image', 'video'];
        $message = 'message';
        $rules = 'required';

        foreach ($files as $file) {
            if ($request->hasFile($file)) {
                $message = $file;
                $rules = ['required', new MessageFileValidationRule($file)];
                break;
            }
        }

        $request->validate([
            $message => $rules,
        ]);
    }

    /**
     * @param $request
     * @return array|null
     */
    public function findAndUploadFile($request): ?array
    {
        $fileTypes = ['document', 'audio', 'image', 'video'];
        foreach ($fileTypes as $fileType) {
            if($request->hasFile($fileType)){
                $file = $request->file($fileType);
                $fileName = uniqid().time().'.'.$file->getClientOriginalExtension();
                $path = filePath()['whatsapp']['path_'.$fileType];

                if(!file_exists($path)){
                    mkdir($path, 0777, true);
                }
                try {
                    move_uploaded_file($file->getRealPath(), $path.'/'.$fileName);
                    return [
                        'type' => $fileType,
                        'url_file' => $path . '/' . $fileName,
                        'name' => $fileName
                    ];
                } catch (\Exception $e) {
                    return [];
                }
            }
        }

        return [];
    }


    /**
     * @param Request $request
     * @param array $contactNewArray
     * @param int $wordLength
     * @param array $numberGroupName
     * @param array $whatsappGateway
     * @param int|null $userId
     * @return void
     */
    public function save(Request $request, array $contactNewArray, int $wordLength, array $numberGroupName, mixed $whatsappGateway, ?int $userId = null): void
    {
        
        $postData = $this->findAndUploadFile(request());
        
        $setTimeInDelay = Carbon::now();

        if($request->input('schedule') == 2){
            
            $setTimeInDelay = $request->input('schedule_date');
        }
       
        $setWhatsAppGateway =  $whatsappGateway->pluck('id')->toArray();

       	$i = 1; $addSecond = 50;
        foreach ($contactNewArray as $index_key => $number) {

            $contact = filterContactNumber($number);
            $value   = array_key_exists($contact, $numberGroupName) ? $numberGroupName[$contact] : $contact;
            
            $content = $this->smsService->getFinalContent($value,$numberGroupName,$request->input('message'));
           
            foreach ($setWhatsAppGateway as $key => $appGateway){
              
              	

                $gateway = $whatsappGateway->where('id',$appGateway)->first();
                $rand = rand($gateway->min_delay ,$gateway->max_delay);
                $addSecond = $i * $rand;
                unset($setWhatsAppGateway[$key]);
                
                if(empty($setWhatsAppGateway)){
                    
                    $setWhatsAppGateway = $whatsappGateway->pluck('id')->toArray();
                  	$i++;
                }
                break;
                
            }
            $log = new WhatsappLog();
            $log->user_id = $userId;
            if(count($whatsappGateway) > 0){
                $log->whatsapp_id = $gateway->id;
            }
            $log->to = $contact;
            $log->initiated_time = $setTimeInDelay;
            $log->message = $content;
            $log->word_length = $wordLength;
            $log->status = $request->input('schedule');
            $log->file_info = count($postData) > 0 ? $postData : null;
            $log->schedule_status = $request->input('schedule');
            $log->save();
             
            if($log->status == WhatsappLog::PENDING){ 
                ProcessWhatsapp::dispatch($log)->delay(Carbon::parse($setTimeInDelay)->addSeconds($addSecond));
                 
            }
        }


    }


    /**
     * @param $search
     * @param $searchDate
     * @return Builder
     */
    public function searchWhatsappLog($search, $searchDate): Builder
    {
        $smsLogs = WhatsappLog::query();
        if (!empty($search)) {
            $smsLogs->whereHas('user',function ($q) use ($search) {
                $q->where('to', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
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
            $firstDate = Carbon::createFromFormat('m/d/Y', trim($dateRange[0]))->startOfDay() ?? null;
            $lastDate = isset($dateRange[1]) ? Carbon::createFromFormat('m/d/Y', trim($dateRange[1]))->endOfDay() : null;

            if ($firstDate) {
                $smsLogs->whereDate('created_at', '>=', $firstDate);
            }

            if ($lastDate) {
                $smsLogs->whereDate('created_at', '<=', $lastDate);
            }

        }

        return $smsLogs;
    }


    /**
     * @param WhatsappLog $whatsappLog
     * @param $gwException
     * @return void
     */
    public function addedCredit(WhatsappLog $whatsappLog ,$gwException): void
    {
        $whatsappLog->status = WhatsappLog::FAILED;
        $whatsappLog->response_gateway = $gwException;
        $whatsappLog->save();
        $user = User::find($whatsappLog->user_id);

        if($whatsappLog->contact_id){
            $status = "Fail";
        }

        if($user){
            $messages = str_split($whatsappLog->message,$whatsappLog->word_length);
            $totalcredit = count($messages);
            $user->whatsapp_credit += $totalcredit;
            $user->save();
            $creditInfo = new WhatsappCreditLog();
            $creditInfo->user_id = $whatsappLog->user_id;
            $creditInfo->type = "+";
            $creditInfo->credit = $totalcredit;
            $creditInfo->trx_number = trxNumber();
            $creditInfo->post_credit =  $user->whatsapp_credit;
            $creditInfo->details = $totalcredit." Credit Return ".$whatsappLog->to." is Falied";
            $creditInfo->save();
        }
    }
}

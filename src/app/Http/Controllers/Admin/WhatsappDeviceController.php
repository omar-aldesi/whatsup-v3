<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsappDevice;
use App\Rules\WhatsappDeviceRule;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\View\View;
use App\Rules\DifferenceRule;
use Illuminate\Support\Str;
use Dotenv\Dotenv;

class WhatsappDeviceController extends Controller
{
    /**
     * create form show
     */
    public function create(): View
    {
        $view = "admin.whatsapp.create"; 
        $message = null;
        $title = "WhatsApp Device";
        $whatsAppDevice = null;

        try {

            $wpUrl = env('WP_SERVER_URL');
            Http::get($wpUrl);
            $devices = WhatsappDevice::where('admin_id', auth()->guard('admin')->user()->id)
            ->orderBy('id', 'desc')
            ->get();

            foreach ($devices as $value) {
                try {
                    $sessions = Http::get($wpUrl . '/sessions/status/' . $value->name);
                    $whatsAppDevice = WhatsappDevice::where('id', $value->id)->first();
                    $whatsAppDevice->status = 'disconnected';
                    if ($sessions->status() == 200) {
                        $whatsAppDevice->status = 'connected';
                    }
                    $whatsAppDevice->save();

                } catch (\Exception $e) {
                
                }
            }

            $whatsAppDevice = WhatsappDevice::where('admin_id', auth()->guard('admin')->user()->id)
            ->orderBy('id', 'desc')
            ->paginate(paginateNumber());

        } catch (\Exception $e) {

            $message = strip_tags($e->getMessage());
            $view ="admin.whatsapp.error";
        }

        return view($view, [
            
            'title' => $title,
            'whatsapps' => $whatsAppDevice,
            'message'  => $message,
        ]);
    }

    /**
     * whatsapp store method
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:wa_device,name',
            'min_delay' => 'required|gte:10',
            'max_delay' => ['required', 'gt:min_delay', new DifferenceRule($request->min_delay)],
        ]);

        $currentUrl = $request->root();
        $parsedUrl = parse_url($currentUrl);
        $domain = $parsedUrl['host'];

        $response = Http::post(env('WP_SERVER_URL').'/sessions/init', [ 
            'domain' => $domain
        ]);

        $responseBody = json_decode($response->body());
 
        if ($response->status() === 200 && $responseBody->success) {
            $whatsapp = new WhatsappDevice();
            $whatsapp->admin_id = auth()->guard('admin')->user()->id;
            $whatsapp->name = randomNumber()."_".$request->input('name');
            $whatsapp->min_delay = $request->input('min_delay');
            $whatsapp->max_delay = $request->input('max_delay');
            $whatsapp->status = 'initiate';
            $whatsapp->save();
            $notify[] = ['success', 'Whatsapp Device successfully added'];
        }else{
            $notify[] = ['error', $responseBody->message];
        } 
        return back()->withNotify($notify);
    }

    /**
     * whatsapp edit form
     * @param $id
     * @return Application|Factory|\Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $title = "WhatsApp Device Edit";
        $whatsDevices = WhatsappDevice::where('admin_id',auth()->guard('admin')->user()->id)->orderBy('id','desc');

        foreach ($whatsDevices as $value) {
            try {
                $session = json_decode(Http::withoutVerifying()->get(config('requirements.core.wa_key_get').'/sessions/find/'.$value->name));
                $whatsAppDevice = WhatsappDevice::where('id', $value->id)->first();
                $whatsAppDevice->status = 'disconnected';
                if ($session->message == "Session found.") {
                    $whatsAppDevice->status = 'connected';
                }
                $whatsAppDevice->save();

            } catch (\Exception $e) {
                return view('admin.whatsapp.error', [
                    'title' => $title,
                    'message' => "Unable to connect to WhatsApp node server. Please configure the server settings and try again.",
                ]);
            }
        }

        $whatsAppDevices = WhatsappDevice::where('admin_id',auth()->guard('admin')->user()->id)
            ->orderBy('id', 'desc')
            ->paginate(paginateNumber());

        $whatsApp = WhatsappDevice::where('admin_id',auth()->guard('admin')->user()->id)
            ->where('id', $id)
            ->first();

        return view('admin.whatsapp.edit', [
            'title' => $title,
            'whatsapp' => $whatsApp,
            'whatsapps' => $whatsAppDevices,
        ]);
    }

    /**
     * whatsapp update method
     * @param Request $request
     * @return mixed
     */
    public function update(Request $request)
    {
        $request->validate([
            'min_delay' => 'required|gte:10',
            'max_delay' => ['required', 'gt:min_delay', new DifferenceRule($request->min_delay)],
        ]);

        $adminId = auth()->guard('admin')->user()->id;

        $whatsapp = WhatsappDevice::where('admin_id',$adminId)
            ->where('id', $request->input('id'))
            ->first();

        $whatsapp->admin_id = $adminId;
        $whatsapp->min_delay = $request->input('min_delay');
        $whatsapp->max_delay = $request->input('max_delay');
        $whatsapp->update();

        $notify[] = ['success', 'WhatsApp Device successfully Updated'];
        return back()->withNotify($notify);
    }

    /**
     * whatsapp delete method
     * @param Request $request
     * @return mixed
     */
    public function delete(Request $request): mixed
    {
        $adminId = auth()->guard('admin')->user()->id;
        $whatsApp = WhatsappDevice::where('admin_id',$adminId)->where('id', $request->input('id'))->first();

        try {
            $response = Http::delete(env('WP_SERVER_URL').'/sessions/delete/'.$whatsApp->name);
            $notify[] = ['success', 'WhatsApp device has been successfully deleted.'];

            if ($response->status() == 200) {
                $notify[] = ['success', 'WhatsApp device has been successfully deleted, and the associated sessions have been cleared from the node.'];
            }

            $whatsApp->delete();
            return back()->withNotify($notify);

        } catch (\Exception $e) {
            $notify[] = ['error', 'Oops! Something went wrong. The Node server is not running.'];
            return back()->withNotify($notify);
        }
    }

    /**
     * whatsapp device status update method
     * @param Request $request
     * @return false|string
     */
    public function statusUpdate(Request $request): bool|string
    {
        $adminId = auth()->guard('admin')->user()->id;
        $wpUrl = env('WP_SERVER_URL');

        $whatsApp = WhatsappDevice::where('admin_id',$adminId)
            ->where('id', $request->input('id'))
            ->first();

        try {
            if ($request->input('status') == 'connected') {
                $session = Http::get($wpUrl.'/sessions/status/'.$whatsApp->name);
                if ($session->status() == 200) {
                    $whatsApp->status = 'connected';
                    $message = "Successfully whatsapp sessions reconnect";
                }else{
                    Http::delete($wpUrl.'/sessions/delete/'.$whatsApp->name);
                    $whatsApp->status = 'disconnected';
                    $message = "Successfully whatsapp sessions disconnected";
                }

            }elseif ($request->input('status') == 'disconnected') {
                $response = Http::delete($wpUrl.'/sessions/delete/'.$whatsApp->name);
                if ($response->status() == 200) {
                    $whatsApp->status = 'disconnected';
                    $message = 'Whatsapp Device successfully Deleted';
                }else{
                    $message = 'Opps! Something went wrong, try again';
                }
            }else{
                $response = Http::delete($wpUrl.'/sessions/delete/'.$whatsApp->name);
                if ($response->status() == 200) {
                    $whatsApp->status = 'disconnected';
                    $message = 'Whatsapp Device successfully Deleted';
                }else{
                    $message ='Opps! Something went wrong, try again';
                }
                $whatsApp->status = $request->input('status');
            }
        }catch (\Exception $exception){
            $message = 'Opps! Something went wrong, try again';
        }

        $whatsApp->update();

        return json_encode([
            'success' => $message
        ]);
    }


    /**
     * whatsapp qr quote scan method
     * @param Request $request
     * @return false|string
     */
    public function whatsappQRGenerate(Request $request): bool|string
    {

 
        $whatsapp = WhatsappDevice::where('admin_id',auth()->guard('admin')->user()->id)
            ->where('id', $request->input('id'))
            ->first();

        $data = [];
        $currentUrl = $request->root();
        $parsedUrl = parse_url($currentUrl);
        $domain = $parsedUrl['host'];

        $response = Http::post(env('WP_SERVER_URL').'/sessions/create', [
            'id' => $whatsapp->name,
            'isLegacy' => !($whatsapp->multidevice === "YES"),
            'domain' => $domain
        ]);

        $responseBody = json_decode($response->body());
 
        if ($response->status() === 200) {
            $data['status'] = $response->status();
            $data['qr'] = $responseBody->data->qr;
            $data['message'] = $responseBody->message;
        } else {
            $msg = $response->status() === 500 ? "Invalid Software License" : $responseBody->message;
            $data['status'] = $response->status();
            $data['qr'] = '';
            $data['message'] = $msg;
        }

        $response = [
            'response' => $whatsapp,
            'data' => $data
        ];

        return json_encode($response);
    }

    /**
     * whatsapp wp device status method
     * @param Request $request
     * @return false|string
     */
    public function getDeviceStatus(Request $request): bool|string
    {
        $whatsapp = WhatsappDevice::where('admin_id',auth()->guard('admin')->user()->id)->where('id', $request->input('id'))->first();
        $data = [];
        $wpUrl = env('WP_SERVER_URL');
        try {
            $checkConnection = Http::get($wpUrl.'/sessions/status/'.$whatsapp->name);
            if ($whatsapp->status == "connected" || $checkConnection->status() === 200) {
                $whatsapp->status = 'connected';
                $response = json_decode($checkConnection->body());
                if (isset($response->data->wpInfo)) {
                    $wpNumber = str_replace('@s.whatsapp.net', '', $response->data->wpInfo->id);
                    $wpNumber = explode(':', $wpNumber);
                    $wpNumber = $wpNumber[0] ?? $whatsapp->number;
                    $whatsapp->number = $wpNumber;
                }
                $whatsapp->save();

                $data['status']  = 301;
                $data['qr']      = asset('assets/file/dashboard/image/done.gif');
                $data['message'] = 'Successfully connected WhatsApp device';
            }
        } catch (\Exception $e) {
            $data['status'] = $e->getCode();
            $data['qr'] = '';
            $data['message'] = $e->getMessage();
        }

        $response = [
            'response' => $whatsapp,
            'data' => $data
        ];
        return json_encode($response);
    }

    /**
     * Whatsapp Server Informations
     * Update Method
     * @param Request $request
     * 
     */ 

    public function updateServer(Request $request) {
        
        $request->validate([
           
            'server_host'        => 'required|ip',
            'server_port'        => 'required|numeric',
            'max_retries'        => 'required|numeric',
            'reconnect_interval' => 'required|numeric',
        ]);
        
        $updated_env = [
            
            'WP_SERVER_URL'      => "http://$request->server_host:$request->server_port",
            'NODE_SERVER_HOST'   => $request->server_host,
            'NODE_SERVER_PORT'   => $request->server_port,
            'MAX_RETRIES'        => $request->max_retries,
            'RECONNECT_INTERVAL' => $request->reconnect_interval,
        ];

        $path = app()->environmentFilePath();
        foreach ($updated_env as $key => $value) {
            $escaped = preg_quote('='.env($key), '/');
            
            file_put_contents($path, preg_replace(
                "/^{$key}{$escaped}/m",
                "{$key}={$value}",
                file_get_contents($path)
            ));

        }
        $notify[] = ['success', 'Whatsapp Server Settings Updated'];
        return back()->withNotify($notify);
    }

    public function whatsAppDevices()
    {
    
    	$title = "Whatsapp Device list";
        $whatsapps = WhatsappDevice::where('admin_id', auth()->guard('admin')->user()->id)
            ->orderBy('id', 'desc')
            ->paginate(paginateNumber());

        $checkWhatsAppServer = $this->checkWhatsappServerStatus();
    	return view('admin.whatsapp.device', compact('title', 'whatsapps', 'checkWhatsAppServer'));
    }

    protected function checkWhatsappServerStatus()
    {
        $checkWhatsappServer = true;
        try {
            $wpUrl = env('WP_SERVER_URL');
            Http::get($wpUrl);
        } catch (\Exception $e) {
            $checkWhatsappServer = false;
        }

        $devices = WhatsappDevice::where('admin_id', auth()->guard('admin')->user()->id)
            ->orderBy('id', 'desc')
            ->get();

        foreach ($devices as $value) {
            try {
                $sessions = Http::get($wpUrl . '/sessions/status/' . $value->name);
                $whatsAppDevice = WhatsappDevice::where('id', $value->id)->first();
                $whatsAppDevice->status = 'disconnected';
                if ($sessions->status() == 200) {
                    $whatsAppDevice->status = 'connected';
                }
                $whatsAppDevice->save();
            } catch (\Exception $e) {
                $checkWhatsappServer = false;
            }
        }

        return $checkWhatsappServer;
      
    }

}

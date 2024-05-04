<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use App\Models\PricingPlan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\GeneralSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;

class UpgradeVersionMigrateController extends Controller
{
    public function index() {
        
        $file_path = base_path('update_info.md');
        $file_contents = [];
        $markdownContent = File::get($file_path);
        $sections = explode('## ', $markdownContent);
        array_shift($sections);
        foreach ($sections as $section) {
            
            $section = trim($section);
            list($section_title, $section_content) = explode("\n", $section, 2);
            $file_contents[$section_title] = $section_content;
        }   

        $general = GeneralSetting::first();
        $current_version = $general->app_version; 
        $new_version     = config('requirements.core.appVersion'); 
        $title = "update $new_version";
        return view('update.index', compact(
            'general',
            'current_version',
            'new_version',
            'title',
            'file_contents'
        ));
    }

    public function update() {
        
        $general         = GeneralSetting::first();
        $current_version = $general->app_version; 
        $new_version     = config('requirements.core.appVersion'); 
        $file_path       = base_path('update_info.md');

        if(version_compare($new_version, $current_version, '>')) {
            
            try {
                session(["queue_restart" => true]);

                $migrationFiles = [
                    '/database/migrations/2024_01_27_173025_alter_table_pricing_plans.php',
                ];

                $dropTableOrColumn = [
                    '/database/migrations/2024_01_27_184122_drop_pricing_plan_columns.php',
                ];
                foreach($migrationFiles as $migrationFile) {

                    Artisan::call('migrate', ['--force' => true, '--path' => $migrationFile ]);
                }

                $this->updatePricingPlans();

                foreach($dropTableOrColumn as $drop) {

                    Artisan::call('migrate', ['--force' => true, '--path' => $drop ]);
                    
                }
               
                
                if (File::exists($file_path)) {
                
                    File::delete($file_path);
                }
                Artisan::call('queue:restart');
                Artisan::call('optimize:clear');
                $this->versionUpdate($general, $new_version);
                $notify[] = ['success', 'Succesfully updated database.'];
                return redirect()->route('admin.dashboard')->withNotify($notify);
                
            }catch(\Exception $e) {
               
                $notify[] = ['error', "Internal Error"];
                return back()->withNotify($notify);
            }
        }

        $notify[] = ['error', "No update needed"];
        return back()->withNotify($notify);
    }
    
    private function updatePricingPlans() {
        $pricing_plans = PricingPlan::all();

        $mapping = [

            "sms" => [
                "is_allowed" => true,
                "gateway_limit" => 10,
                "allowed_gateways" => [

                ],
                "credits" => 100,
                "android" => [
                    "is_allowed"    => true,
                    "gateway_limit" => 10
                ]
            ],
            "email" => [
                "is_allowed" => true,
                "gateway_limit" => 10,
                "allowed_gateways" => [

                ],
                "credits" => 100
            ],
            "whatsapp" => [
                "is_allowed" => true,
                "gateway_limit" => 10,
                "credits" => 100,
            ],
        ];

        foreach($pricing_plans as $pricing_plan) {
            foreach($mapping as $map_key => $map_value) {
               
                if($map_key == "sms") {
                
                    $mapping[$map_key]['allowed_gateways'] = json_decode($pricing_plan->gateway_credentials)->sms ?? null;
                    $mapping[$map_key]['credits']          = $pricing_plan->credit;
                    $pricing_plan->sms = $mapping[$map_key];

                } elseif($map_key == "email") {
                    
                    $mapping[$map_key]['allowed_gateways'] = json_decode($pricing_plan->gateway_credentials)->mail ?? null;
                    $mapping[$map_key]['credits']          = $pricing_plan->email_credit;
                    $pricing_plan->email = $mapping[$map_key];
                } elseif($map_key == "whatsapp") {
                    
                    $mapping[$map_key]['credits']          = $pricing_plan->whatsapp_credit;
                    $pricing_plan->whatsapp = $mapping[$map_key];
                }
            }
            $pricing_plan->carry_forward = PricingPlan::DISABLED;
            $pricing_plan->type = PricingPlan::USER;
        
            $pricing_plan->save();
        }
    }

    public function versionUpdate($general, $new_version) {
        $current_version      = $new_version;
        $general->app_version = $current_version;
        $general->save();
    }

    public function verify() {
        
        $general         = GeneralSetting::first();
        $current_version = $general->app_version;
        $new_version     = config('requirements.core.appVersion'); 
        $title           = "update $new_version";
        return view('update.verify', compact(
            'general',
            'current_version',
            'new_version',
            'title'
        ));

    }

    public function store(Request $request) {

        $admin_credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
            
        ]);
        $request->validate([
            'purchased_code' => ['required'],
        ]);
        
        try {
            
            if (Auth::guard('admin')->attempt($admin_credentials)) {

                $buyer_domain   = url()->current();
                $purchased_code = $request->purchased_code;
                $response = Http::withoutVerifying()->get('https://license.igensolutionsltd.com', [
                    'buyer_domain'   => $buyer_domain,
                    'purchased_code' => $purchased_code,
                ]);
               
                if($response->json()['status']) {
                    if(File::exists(base_path('update_info.md'))) {
                        Session::put('is_verified', true);
                        $notify[] = ['success', "Verification Successfull"];
                        return redirect()->route('admin.update.index')->withNotify($notify);
                    } 
                    $notify[] = ['error', "Files are not available"];
                    return back()->withNotify($notify); 
                    
                } else {
                    $notify[] = ['error', "Invalid licence key"];
                    return back()->withNotify($notify);
                }
            }
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
        } catch(\Exception $e) {
           
            $notify[] = ['info', "Please Try Again"];
            return back()->withNotify($notify);
        }
    }
}

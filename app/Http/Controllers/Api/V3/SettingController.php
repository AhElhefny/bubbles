<?php

namespace App\Http\Controllers\Api\V3;
use App\Http\Controllers\Controller;
use App\Http\Resources\V3\CityResource;
use App\Http\Resources\V3\SliderCollection;
use App\Models\Bank;
use App\Models\City;
use App\Models\NotificationSubscription;
use App\Models\PaymentMethod;
use App\Models\Slide;
use App\Models\HomePageSetting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    
    public function cities()
    {
        $cities = CityResource::collection(City::where('active', true)->get());

        return $this->sendResponse($cities, trans('messages.sent_data_success'));
    }

    public function paymentMethods(Request $request)
    {
        
        $user = auth('api')->user();
        $paymentMethods = [];
        $disabledPaymentMethods = ($user && $user->disabled_payment_methods)?json_decode($user->disabled_payment_methods):[];
        $paymentMethodsData = PaymentMethod::where('status', 1);
        if(!empty($disabledPaymentMethods)){
            $paymentMethodsData = $paymentMethodsData->whereNotIn('gateway', $disabledPaymentMethods);
        }
        foreach ($paymentMethodsData->get() as $method) {
            if($request->is_online && in_array($method->gateway, ['cash', 'bank'])){
                continue;
            }

            $paymentMethods[] = [
                
                'id' => $method->id,
                'name' => $method->name,
                'gateway' => $method->gateway,
                'is_online' => !in_array($method->gateway, ['cash', 'bank']),
                'icon' => url('/images/icons/'.$method->icon),
            ];
        }

         return $this->sendResponse($paymentMethods, trans('messages.get_data_success'));
    }

    public function banks()
    {
        
        $banks = [];

        foreach (Bank::all() as $bank){

            $banks[] = [

                'id' => $bank->id,
                'name' => $bank->name,
                'logo' => $bank->logo,
                'hint' => $bank->hint_image,
                'account_number' => $bank->account_number,
                'iban' => $bank->iban,
            ];
        }

          return $this->sendResponse($banks, __('messages.get_data_success'));
    }


    public function worktimeSettings(Request $request)
    {
       
        $settings = HomePageSetting::find(1);
        $worktime[]=[

          'start_time' => $settings->starttime,
          'end_time' => $settings->endtime,
            
        ];
       
          return $this->sendResponse($worktime , trans('messages.get_data_success'));
    }

    public function slider(Request $request)
    {

        return new SliderCollection(Slide::where('status', true)->where('position', $request->get('position', 'top'))->get());
    }

    public function notificationSettings(Request $request)
    {
       
        NotificationSubscription::where('player_id', $request->player_id)->update(['app_enabled' => (boolean) $request->enabled]);
       
        return $this->sendResponse([], trans('messages.get_data_success'));
    }

    public function socialsAccounts()
    {
        
          return $this->sendResponse([

            "socials" => [

                "twitter" => "https://twitter.com",
                "instagram" => "https://www.instagram.com",
                "facebook" => null,
                "whatsapp" => "https://wa.me/966597864215",
                "snapchat-ghost" => "https://www.snapchat.com"
            ],

           trans('messages.get_data_success')
      ]);
  }

 
}

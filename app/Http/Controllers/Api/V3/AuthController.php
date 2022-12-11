<?php

namespace App\Http\Controllers\Api\V3;
use App\Classes\Helper;
use App\Classes\Operation;
use App\Http\Controllers\Controller;
use App\Http\Resources\V3\UserResource;
use App\User;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Auth ;

class AuthController extends Controller
{
     public const adminMobile = '9664331373';
     public const adminOtp = '6450';
     
     public function otp_login(Request $request)
     {
             
           $validator = Validator::make($request->all(), [

              'mobile' => 'required',
          ]);

          if($validator->fails()) {
        
               return $this->sendError(error_processor($validator), trans('messages.validation_error'), 442);
          }
          
           if($request->mobile == self::adminMobile){
             $otpCode = self::adminOtp;
         }else{
             $digits = 4;
             $otpCode = rand(pow(10, $digits - 1), pow(10, $digits) - 1);
         }
          $user = User::where("mobile", $request->mobile)->first();
        
          if($user){

              $user->update(['mobile' => $request->mobile, "otp_code" => $otpCode]);
        
           }else{

               User::create(['mobile' => $request->mobile, 'status' => 0,'email' => hexdec(uniqid()).'_customer@bubbles.com', 'password' => Hash::make(uniqid()), "otp_code" => $otpCode]);
           }

            return $this->sendResponse(['otp_code' => $otpCode, 'show_otp' => true], trans('messages.get_data_success'));
      }

      public function vendor_login(Request $request)
      {
          
           if(Auth::attempt(['email' => request('email'), 'password' => request('password')]) && auth()->user()->seller_type == 'branch_manager'){
                
              $user = Auth::user();
              $success['token'] =  $user->createToken('MyApp')->accessToken;
              $success['id'] =  $user->id;
              $success['name'] =  $user->name;
              $success['branch'] = $user->branches[0];

                return response()->json(['data' => [$success], 'status_code'=>200 , 'message'=>'success']);
             }
             
            else{
                // $this->logout();
                 return response()->json(['status_code'=>401 , 'message'=>'Unauthorized']);
            }
      }

      public function otp_verify(Request $request)
      {
          
           $validator = Validator::make($request->all(), [
  
                'mobile' => 'required',
                'otp_code' => 'required|integer'
           ]); 

          if($validator->fails()) {

               return $this->sendError(error_processor($validator), trans('messages.some_fields_are_missing'), 442);
          }

          $user = User::where("mobile", $request->mobile)->where("otp_code", $request->otp_code)->first();

          if(!$user){

              return $this->sendError([], trans('messages.otp_code_is_not_correct'), 442);
         }

         $cart_id = Cart::where('user_id',$user->id)->first();
         $user->update(['otp_code' => null, "is_mobile_verified" => 1]);
         $accessToken = $user->createToken('Personal Access Token')->accessToken;
         $new_user =str_contains($user->email,'_customer@bubbles.com') ? true : false;

         return $this->sendResponse(["access_token" => $accessToken, 'cart_id'=>$cart_id?$cart_id->id:null ,'new_user'=>$new_user], trans('messages.get_data_success'));
    }

    public function otp_register(Request $request)
    {
          
           $validator = Validator::make($request->all(),[
     
              'name' => 'required',
              'email' => 'required|email|unique:users,email,'.auth()->id(),
          ]);

          if ($validator->fails()) {

               return $this->sendError(error_processor($validator), trans('messages.validation_error'), 442);
          }

          $user = auth()->user()->update([

             'name' => $request->name,
             'email' => $request->email,
             'city_id' => $request->city_id,
             'status' => 1,
         ]);
     
         return $this->sendResponse(new UserResource(auth()->user()), trans('messages.updated_success'));
    }

    public function logout(Request $request)
    {
         $request->user()->token()->revoke();

         return $this->sendResponse([], trans('messages.updated_success'));
    }
}

<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\Controller;
use App\Http\Resources\V3\AddressResource;
use App\Models\Contactus;
use App\Models\CustomerShippingAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactUsController extends Controller
{
    public function send(Request $request)
    { 
        
         $validator = Validator::make($request->all(), [

              'title' => 'required',
              'info' => 'required',
//             'email' => 'required',
              'username' => 'required',
              'mobile' => 'required'
         ]);

         if ($validator->fails()) {
            
              return $this->sendError(error_processor($validator), trans('messages.validation_error'), 442);
         }

         $data = $request->all();
         $data['user_id'] = auth('api')->id();
         Contactus::create($data);

         return $this->sendResponse([], trans('messages.send_data_success'));
    }

}

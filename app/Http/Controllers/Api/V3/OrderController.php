<?php

namespace App\Http\Controllers\Api\V3;

use App\Classes\Checkout;
use App\Enums\StatusEnum;
use App\Classes\Operation;
use App\Http\Controllers\Controller;
use App\Http\Resources\V3\DeliverTimeResource;
use App\Http\Resources\V3\OrderDetailsResource;
use App\Http\Resources\V3\OrderResource;
use App\Http\service\push;
use App\Models\Cart;
use App\Models\DeliveryDay;
use App\Models\Notification;
use App\Models\NotificationSubscription;
use App\Models\Order;
use App\Models\OrderShippingAddress;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Rating;
use App\Models\Branch;
use App\Models\AdminNotification;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    use push;

     public function index(Request $request)
     {

         $orders = Order::where('user_id', auth()->guard('api')->user()->id)->where('status','!=',99);

         if(isset($request->order_number) && $orderNumber = $request->order_number)
          {

              $orders=$orders->where('id',$orderNumber);
          }

         if (isset($request->status)) {

              $orders=$orders->where('status',$request->status);
          }

          $orders = OrderResource::collection($orders->orderBy('id', 'desc')->paginate(10));

          return $this->sendResponse($orders->resource, trans('messages.get_data_success'));
      }

      public function washerOrders(Request $request)
      {

            $branch = Branch::where('user_id',auth('api')->user()->id)->first();
            $orders = Order::notPending()->where('branch_id',$branch->id)->where('status','!=',99);
             if($orderNumber = $request->order_number){

                  $orders->where('id',$orderNumber);
             }
             if(isset($request->status)){

                 $orders->where('status',$request->status);
             }

            $orders = OrderResource::collection($orders->orderBy('id', 'desc')->paginate(10));

            return $this->sendResponse($orders->resource, trans('messages.get_data_success'));
      }

       public function show(Request $request, $orderId)
       {

            $order = Order::where('id',$orderId)->first();

            if(!$order){

                 return $this->sendError([], trans('messages.not_found_data'), 404);
            }

            return $this->sendResponse(new OrderDetailsResource($order), trans('messages.get_data_success'));
       }

       public function rate(Request $request, $orderId)
       {

             $orderId = substr($orderId, 2);
             $order = Order::where('id',$orderId)->where('user_id', auth()->id())->first();

             if(!$order){

                  return $this->sendError([], trans('messages.not_found_data'), 404);
             }

             foreach (['driver', 'products'] as $type){

               Rating::updateOrCreate(['type' => $type, 'user_type' => User::class, 'user_id' => auth()->id(), 'ratingable_type' => Order::class, 'ratingable_id' => $orderId],
               [

                    'type' => $type,
                    'user_id' => auth()->id(),
                    'user_type' => User::class,
                    'ratingable_type' => Order::class,
                    'ratingable_id' => $orderId,
                    'rating' => $type == 'driver'?$request->driver_rating:$request->products_rating,
                    'comment' => $request->comment

                ]);
          }
              return $this->sendResponse([], trans('messages.data_updated_success'));
       }

      public function cancel(Request $request, $orderId)
      {

            $orderId = substr($orderId, 2);

            $order = Order::where('id',$orderId)
               ->where('user_id', auth()->id())
               ->where('status', '!=', Order::STATUS_DELIVERED)->first();

              if(!$order){

                  return $this->sendError([], trans('messages.not_found_data'), 404);
              }

               $order->update(['status' => Order::STATUS_CANCELED, 'cancelled_by' => auth()->id(), 'cancelled_at' => Carbon::now()]);

               return $this->sendResponse(new OrderDetailsResource($order), trans('messages.data_updated_success'));
        }

        public function create(Request $request)
        {

             $validator = Validator::make($request->all(), [

                   'delivery_date'      => 'required|date_format:Y-m-d|after_or_equal:' . Carbon::now()->toDateString(),
                   'delivery_time' => 'required',
                   'payment_method'      => 'bail|required|in:mastercard,visa,cash,bank',
                   'branch_id' =>'required',
                   'cart_id' =>'required',
                   'car_type'=>'required',
                   'car_color'=>'required',
                   'car_model'=>'required',
                   'car_number'=>'required',

               ]);

               if($validator->fails()) {

                   return $this->sendError(error_processor($validator), trans('messages.validation_error'), 442);
               }

               $authUser = auth('api')->user();
               $cityId = $request->header('city_id');
               $cart = Cart::where('id', $request->cart_id)->where('status', 1)->first();
               $items = $cart->items()->with('service')->get();
               $servicesprices = [];

               $tax = 0;

               foreach($items as $item){

                $amount = (int)$item->amount;
                $itemService = $item->service;
                $servicePrice = $itemService?(float)$itemService->price:0;
                $servicesprices[] =  $servicePrice * $amount;

                if ($itemService->tax_type == 'percent') {

                      $tax += ($itemService->price * $itemService->tax) / 100;

                      } elseif ($itemService->tax_type == 'amount') {

                       $tax +=  $itemService->tax ;
                   }

                  }

                 $coupon_discount = 0;
                 $servicestotal= array_sum($servicesprices);
                 $total = array_sum( $servicesprices) + $tax;

                if($request->promocode){

                     $response = Checkout::checkCoupon($request->promocode, $total);

                  if($response['success']){

                        $total = $response['new_price'];
                        $coupon_discount = $response['discount_value'];
                   }
                }

                $paymentMethod = PaymentMethod::where('gateway', $request->payment_method)->where('status', 1)->first();

                if(!$paymentMethod){

                    return $this->sendError([], __('messages.payment_method_not_supported'), 442);
                }

                $branch_details= Branch::where('id',$request->branch_id)->first();

               if($branch_details->is_open == 0)
                {
                    return $this->sendError([], __('messages.orders_branch_stop'), 442);
                }



                $data = $request->all();
                $data['user_id'] = $authUser->id;
                $data['payment_status'] = false;
                $data['city_id'] = $cityId;
                $data['sub_total']=$servicestotal;
                $data['total']= $total;
                $data['tax']=$tax;
                $data['coupon_discount'] = $coupon_discount;
                $data['status'] = Order::STATUS_RESERVED;
                $data['last_payment_reference_id'] = hexdec(uniqid());
                $data['origin_payment_method'] = $request->payment_method;
                $data['branch_id'] = $request->branch_id;
                $data['seller_id'] =  $branch_details->seller_id;

            try {

                DB::beginTransaction();
                $order = Order::create($data);
                if($request->hasFile('car_image')){
                    $order->addMedia($request->file('car_image'))
                        ->withCustomProperties(['root' => 'user_prr'.uniqid()])
                        ->toMediaCollection('order_car_image');
                }
                if($request->payment_method == 'visa'){
                    $order->update(['status'=>Order::STATUS_UNPAID]);
                    $PayResponse = $this->Pay($order);
                    // dd($PayResponse);
                    if(isset($PayResponse->errors)){

                        return $this->sendError([], __('messages.something went wrong'), 442);
                    }
                    $order->update(['payment_status' => $PayResponse->status,'tap_id'=>$PayResponse->id,'status'=>Order::STATUS_RESERVED]);
                    $order->user()->update(['last_payment_reference_id' => $PayResponse->id]);
                }
                Checkout::saveServices($order,  $items);
                $addressRequest = $request->get('address',[]);
                $addressRequest['order_id'] = $order->id;
                $addressRequest['mobile'] = $request->mobile?:$authUser->mobile;
                $addressRequest['username'] = $request->username?:$authUser->name;
                OrderShippingAddress::create($addressRequest);

                Payment::create([

                    'payment_method_id' => $paymentMethod->id,
                    'status' => in_array($paymentMethod->gateway, ['cash', 'visa'])?1:0,
                    'payment_reference_id' => isset($PayResponse)?$PayResponse->id:$data['last_payment_reference_id'],
                    'referenceable_id' => $order->id,
                    'referenceable_type' => 'orders',
                ]);

               if(in_array($paymentMethod->gateway, ['cash'])){

                    Checkout::emptyAllCarts($authUser);
                }

                DB::commit();

                $notification = new AdminNotification();
                $notification->title ="New Order Received with "." ".$order->order_number;
                $notification->content ="New Order Received with "." ".$order->order_number." "."from  ".$order->user->name;
                $notification->type="order";
                $notification->save();
                $userNotification = Notification::create([
                   'title' =>$notification->title,
                   'content' => $notification->content,
                    'group' => 'private',
                    'send_at' => Carbon::now(),
                    'created_at'=>Carbon::now()
                ]);
                $userBranch=User::where('id',$branch_details->user_id)->first();
                $userNotification->users()->attach($userBranch->id);
                $userToken = NotificationSubscription::whereIn('user_id',[auth()->user()->id])->pluck('player_id')->toArray();
                $branchToken = NotificationSubscription::whereIn('user_id',[$userBranch->id])->pluck('player_id')->toArray();
                $notification->order_id = $order->id;
                $result = $this->send_notification($notification->title,$notification->content,$notification,$branchToken);
                $result1 = $this->send_notification('your order #'. $order->id.' saved successfully',"New Order Received to vendor with "." ".$order->order_number." "."from you",$notification,$userToken);
                // dd($result,$result1);

                $this->firestoreSaveOrder($order);
                $output = $request->payment_method == 'visa' ? [
                    'url' =>$PayResponse->transaction->url,
                    'firebase_id' => $order->firebase_id
                    ]: new OrderDetailsResource($order);
                return $this->sendResponse($output, trans('messages.get_data_success'));

               }catch (\Exception $e){

                 DB::rollback();

                  return $this->sendError([], $e->getMessage().'-'.$e->getFile().'- line'.$e->getLine(), 442);
              }
        }

        public function reorder(Request $request)
        {

            $validator = Validator::make($request->all(), [

                'order_id'            => 'required',
                'delivery_date'       => 'required|date_format:Y-m-d|after_or_equal:'.Carbon::now()->toDateString(),
                'delivery_time' => 'required',
            ]);

            if($validator->fails()) {

                 return $this->sendError(error_processor($validator), trans('messages.validation_error'), 442);
            }

            $order = Order::find($request->order_id);

            $paymentMethod = PaymentMethod::where('gateway', $request->payment_method)->where('status', 1)->first();

            if(!$paymentMethod){

                return $this->sendError([], __('messages.payment_method_not_supported'), 442);
            }

            $authUser = auth()->user();
            $data =$request->all();
            $data['user_id'] = $authUser->id;
            $data['payment_status'] = false;
            $data['city_id'] =$order->city_id;
            $data['sub_total']=$order->sub_total;
            $data['total']=$order->total;
            $data['tax']=$order->tax;
            $data['coupon_discount'] = $order->coupon_discount;
            $data['status'] = in_array($paymentMethod->gateway, ['cash', 'bank'])?Order::STATUS_RESERVED:Order::STATUS_PENDING;
            $data['last_payment_reference_id'] = hexdec(uniqid());
            $data['origin_payment_method'] =$order->origin_payment_method ;
            $data['branch_id'] = $order->branch_id;
            $data['car_type'] = $order->car_type;
            $data['seller_id']=  $order->seller_id;

           try {

            DB::beginTransaction();

            $newOrder = Order::create($data);
               if($request->hasFile('car_image')){
                   $newOrder->addMedia($request->file('car_image'))
                       ->withCustomProperties(['root' => 'user_prr'.uniqid()])
                       ->toMediaCollection('order_car_image');
               }
            Checkout::getServices($order, $newOrder);
            $address = OrderShippingAddress::where('order_id',$request->order_id)->first();
            $addressRequest['address'] = $address->address;
            $addressRequest['lat'] = $address->lat;
            $addressRequest['lng'] = $address->lng;
            $addressRequest['order_id'] = $newOrder->id;
            $addressRequest['mobile'] = $request->mobile?:$authUser->mobile;
            $addressRequest['username'] = $request->username?:$authUser->name;
            OrderShippingAddress::create($addressRequest);

            Payment::create([

                'payment_method_id' => $paymentMethod->id,
                'status' => in_array($paymentMethod->gateway, ['cash', 'bank'])?1:0,
                'payment_reference_id' => $data['last_payment_reference_id'],
                'referenceable_id' => $newOrder->id,
                'referenceable_type' => 'orders',

            ]);

                DB::commit();
                $notification = new AdminNotification();
                $notification->title ="New Order Received with "." ". $newOrder->order_number;
                $notification->content ="New Order Received with "." ".$newOrder->order_number." "." from ".$order->user->name;
                $notification->type="order";
                $notification->save();
                $userNotification = Notification::create([
                   'title' =>$notification->title,
                   'content' => $notification->content,
                   'group' => 'private',
                    'send_at' => Carbon::now(),
                    'created_at'=>Carbon::now()
                ]);
                $branch = Branch::find($order->branch_id);
                $userBranch = User::where('id',$branch->user_id)->first();
                $userNotification->users()->attach($userBranch->id);
                $userToken = NotificationSubscription::where('user_id',$userBranch->id)->pluck('player_id')->toArray();
                $notification->order_id = $newOrder->id;
                $result = $this->send_notification($notification->title,$notification->content,$notification,$userToken);
                return $this->sendResponse(new OrderDetailsResource($newOrder), trans('messages.get_data_success'));

               }catch (\Exception $e){

                DB::rollback();

                return $this->sendError([], $e->getMessage().'-'.$e->getFile().'- line'.$e->getLine(), 442);
            }
      }

     public function applyCoupon(Request $request)
     {

         $response = Checkout::checkCoupon($request->promo_code, $request->price);

         if(!$response['success']){

             return $this->sendError([], $response['message'], 442);
          }

           return $this->sendResponse(['new_price' => $response['new_price'], 'discount_value' => $response['discount_value']], trans('messages.coupon_applied_success'));
    }

    public function getAvailableShifts(Request $request)
    {

         $validator = Validator::make($request->all(), [

              'delivery_date' => 'required|date',
         ]);

         if($validator->fails()) {

             return $this->sendError(error_processor($validator), trans('messages.validation_error'), 442);
         }

         $deliveryDate = Carbon::parse($request->get('delivery_date'));

         $day = DeliveryDay::whereDayOfWeek($deliveryDate->dayOfWeek)->first();

         if(!$day){

             return $this->sendError(error_processor($validator), trans('messages.delivery_not_available_at_this_date'), 442);
         }

         if(!$day->status){

             return $this->sendError(error_processor($validator), trans('messages.delivery_on_x_day_not_available', ['day' => trans('admin.week_days_list.'.$day->day_of_week)]), 442);
        }

         $carbonDate = \Carbon\Carbon::createFromFormat('Y-m-d',  $request->get('delivery_date'));
         if($carbonDate->isToday()){
             $nowTime = \Carbon\Carbon::now()->format('H:i:s');
             $times = $day->times()->where('start_time','>',$nowTime);

          }else{

              $times = $day->times();
         }

         $timesList = $times->where('delivery_times.city_id', (int) $request->header('city_id'))->get();
         $timesCollection = [];
         foreach ($timesList as $delivery_time){
            $countOrders = Operation::countOrdersByDeliveryShifts($request->delivery_date, $delivery_time->start_time, $delivery_time->end_time);

            $diff = max((int) $delivery_time->max_orders_to_accept - $countOrders, 0);
            if($delivery_time->max_orders_to_accept > 0 && $diff > 0){
                   $timesCollection[] = [

                       'name' => implode(' - ', [$delivery_time->start_time, $delivery_time->end_time]),
                       'is_available' => $delivery_time->max_orders_to_accept > 0? ($diff > 0): true,
                       'id'   => $delivery_time->getRouteKey()
                   ];
              }
          }
             return $this->sendResponse($timesCollection, trans('messages.get_data_success'));
     }


    public function paymentStatus(Request $request, $status)
    {

        if($status == 'success'){

            return $this->sendResponse([], trans('messages.get_data_success'));
        }

         return $this->sendError([], trans('messages.something_went_wrong'), 442);
    }

    public function changeOrderStatus(Request $request)
    {
        $state =app()->getLocale()=='ar'? StatusEnum::STATUS_AR:StatusEnum::STATUS_EN;
        $order = Order::where('id', $request->order_id)->first();

        if(!$order)
         {
              return response()->json([

                  'status' => 422,
                  'success' => false,
                  'message' =>   trans('messages.order_notfound')
              ]);
          }
        $oldStat =$order->status;
        $order->update(['status'=>$request->status]);
        $userNotification = Notification::create([
            'title' =>'order status updated to '.$state[$request->status],
            'content' => 'Your order status changed from '. $state[$oldStat] . 'to be '. $state[$request->status],
            'group' => 'private',
            'send_at' => Carbon::now(),
            'created_at'=>Carbon::now()
        ]);
        $userNotification->users()->attach($order->user_id);
        $userToken = NotificationSubscription::where('user_id',$order->user_id)->pluck('player_id')->toArray();
        $userNotification->order_id = $order->id;
        $result = $this->send_notification($userNotification->title,$userNotification->content,$userNotification,$userToken);
        $orderRef = app('firebase.firestore')
            ->database()
            ->collection('orders')
            ->Document('branch'.$order->branch_id)
            ->collection('orders')
            ->Document($order->firebase_id);
            $orderRef->update([
                ['path' => 'status', 'value' => $request->status]
            ]);
        return $this->sendResponse([], trans('messages.get_data_success'));
    }

    public function orderActivate(Request $request)
    {
         $branch = Branch::where('user_id',auth()->user()->id)->first();
         $branch->update(['orders_status' => $request->status ]);

         return $this->sendResponse([], trans('messages.get_data_success'));
    }

    public function firestoreSaveOrder($order)
    {
        $data=[
            'id'=>$order->id,
            'order_number'=>$order->order_number,
            'car_type'=>$order->car_type,
            'car_color'=>$order->car_color,
            'car_number'=>$order->car_number,
            'car_model'=>$order->car_model,
            'total'=>$order->total,
            'delivery_date'=>$order->delivery_date,
            'delivery_time'=>$order->delivery_time,
            'status'=>$order->status,
            'payment_status' => $order->payment_status,
            'branch_id' => $order->branch_id,
            'accepted' => null
        ];

        $bookingFire = app('firebase.firestore')
            ->database()
            ->collection('orders')
            ->Document('branch'.$order->branch_id)
            ->collection('orders')
            ->newDocument();
            $bookingFire->set($data);
            $b_id =app('firebase.firestore')
                ->database()
                ->collection('orders')
                ->Document('branch'.$order->branch_id)
                ->collection('branch status')
                ->Document('status');

            $branch=Branch::find($order->branch_id);
            $b_id->set(['status'=>$branch->is_open]);
            $order->firebase_id=$bookingFire->id();
            $order->save();
    }

    public function Pay($order){
        $data =[
            'amount' => $order->total,
            'currency' => 'SAR',
            'threeDSecure' => 'true',
            'statement_descriptor' => 'sample',
            'customer' => [
                'first_name' => $order->user->name,
                'email' => $order->user->email,
                'phone' => [
                    'country_code' => '+20',
                    'number' => '1064322137'
                ],
            ],
            'source' => [
                'id' => 'src_card'
            ],
            'redirect' => [
                'url' => 'http://bubbles.badee.com.sa/api/v3/payments/callback'
            ]
        ];
        $headers = [
            "Content-Type:application/json",
            "Authorization:Bearer sk_test_DrGReBpqzfb79CAHIF3YlTdy"//.env('SECRET_API_KEY'),
        ];
        $ch =curl_init();
        $url = 'https://api.tap.company/v2/charges';
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_POST,true);
        curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode($data));
        curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        $res =curl_exec($ch);
        curl_close($ch);
        $response = json_decode($res);
        return $response;
    }

    public function callback(Request $request){
        $data = $request->all();
        $headers = [
            "Content-Type:application/json",
            "Authorization:Bearer sk_test_DrGReBpqzfb79CAHIF3YlTdy"//.env('SECRET_API_KEY'),
        ];
        $url ='https://api.tap.company/v2/charges/'.$data['tap_id'];
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
        $res = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($res);
        $order = Order::where('tap_id',$response->id)->first();
        // dd($order);
        $order->update(['payment_status' => $response->status]);
        $payment = Payment::where('payment_reference_id',$response->id)->first();
        $bookStore = app('firebase.firestore')
            ->database()
            ->collection('orders')
            ->Document('branch'.$order->branch_id)
            ->collection('orders')
            ->Document($order->firebase_id);

        if($response->status == 'CAPTURED'){
            $payment->update(['status'=>1]);
            $bookStore->update([
                ['path' => 'status', 'value' => 1]
            ]);
            $bookStore->update([
                ['path' => 'payment_status', 'value' => 'CAPTURED']
            ]);
            return '';
            // response()->json([
            //     'success' => true,
            //     'message' => 'operation success',
            // ],402);
        }
        else{
            $payment->update(['status'=>0]);
            $bookStore->update([
                ['path' => 'payment_status', 'value' => $response->status]
            ]);
            return '';
            // response()->json([
            //   'success' => false,
            //   'message' => 'operation failed',
            // ],402);
        }
    }

    public function accepted(Request $request){
         $order = Order::find($request->order_id);
         $order->update(['accepted'=>$request->accepted]);
        $orderFir = app('firebase.firestore')
            ->database()
            ->collection('orders')
            ->Document('branch'.$order->branch_id)
            ->collection('orders')
            ->Document($order->firebase_id);
        $orderFir->update([['path'=>'accepted','value'=>$request->accepted]]);
        return $this->sendResponse([], trans('messages.get_data_success'));
    }

}

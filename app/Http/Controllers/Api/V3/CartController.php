<?php

namespace App\Http\Controllers\Api\V3;
use App\Http\Controllers\Controller;
use App\Models\Service;
use App\User;
use App\Models\BranchesTax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ServiceWorktime;
use App\Models\WorkTime;
use App\Models\Branch;


class CartController extends Controller
{

     public function addToCart(Request $request)
    {

        $service = Service::find($request->service_id);

        if(!$service){
            return response()->json([
                'status' => 422,
                'success' => false,
                'message' => trans('messages.service_not_available'),
            ]);
        }

        $authUser = auth()->guard('api')->user();


        if (!$authUser){
                $userCart = $request->cart_id ? Cart::find($request->cart_id) : null;
                $userCart = $userCart ?: Cart::create([ 'status' => 1,'branch_id' => $request->branch_id]);
        }else
            $userCart = $authUser->cart;

        if (!$userCart){
            $userCart= Cart::create([
               'user_id' => $authUser->id,
               'device_id' => uniqid().'_'.rand(100, 100000),
                'status' => 1,
                'branch_id' => $request->branch_id
            ]);
        }

        if(isset($request->branch_id) && $userCart->branch_id != $request->branch_id && isset($userCart->items)){
            $userCart->items()->delete();
            $userCart->update(['branch_id' => $request->branch_id]);
        }

        $userCartItems = CartItem::where('cart_id','=',$userCart->id)->get();


        if(isset($userCartItems) && in_array($request->service_id,$userCartItems->pluck('service_id')->toArray()) ==1){
            return response()->json([
                'status' => 422,
                'success' => false,
                'message' => trans('messages.serviceExist'),
            ]);
        }



        //        add item to cart
        $userCart->items()->create(['service_id' => $request->service_id , 'amount' => 1]);



        // what else after add item to cart
        $userCartItems = $userCart->items();

        if($userCartItems->count() > 0){
            $userServicesIds =$userCartItems->pluck('service_id')->toArray();
            $userServices = Service::find($userServicesIds);
            $totalPrice = $userServices->sum('price');
        }

         return response()->json([

            'status' => 200,
            'success' => true,
            'message' => trans('messages.service_added_successfully'),
            'data' => [
                'cart_id' => (int) $userCart->id,
                'items_count' => (int) $userCartItems->count(),
                'items_sum_prices' => (float) $totalPrice,
                'services'=> $userServices,
                'branch_id'=>(int)$userCart->branch_id
            ]
       ]);

    }


    /*
        public function addserviceToCart(Request $request)
        {
        if(!Product::find($request->service_id)){

            return response()->json([

                  'status' => 422,
                  'success' => false,
                  'message' => trans('هذه الخدمة غير موجودة'),
               ]);
            }

            $hasUserId = User::find($request->user_id)?true:false;
            $cartData = [];
            if($hasUserId){

              $cart = Cart::updateOrCreate(['user_id' => $request->user_id, 'status' => 1],['user_id' => $request->user_id]);

            }else{

            $device_id = $request->device_id;

            if(!$device_id){

                 $device_id = uniqid().'_'.rand(100, 100000);
            }

            if($request->cart_id){

                 $cart = Cart::where('id',$request->cart_id)->where('status', 1)->first();
                 $device_id = $cart->device_id;

             }else{

                 $cart = Cart::where('status', 1)->where('device_id', $device_id)->orderBy('created_at', 'desc')->first();
             }

             if(!$cart){

                   $cart = Cart::create(['device_id' => $device_id, 'status' => 1]);

                }
             }

             $cartItem = CartItem::where('cart_id', $cart->id)->where('product_id', $request->service_id)->orderBy('created_at', 'desc')->first();

             if($cartItem){

                }

              else{

               $cartItem = CartItem::create([

                        'cart_id' => $cart->id,
                        'product_id' => $request->service_id,
                        'size_id'=> $request->size_id ,
                        'car_type'=> $request->car_type,
                        'mattress_type_id'=> $request->mattress_id,
                        'city_id' => $request->city_id,
                        'amount' => 1

                 ]);
              }

              $additions = Additions::whereIn('id',$request->get('additions_ids',[]))->get();

             foreach($additions as $addition)
             {

                  $getaddition = CartAddition::where(['addition_id'=> $addition->id , 'service_id'=>$request->service_id])->get();

                 if(count($getaddition) < 1)
                  {

                        $insert = new CartAddition;
                        $insert->cartitem_id = $cartItem->id;
                        $insert->addition_id = $addition->id;
                        $insert->addition = $addition->addition;
                        $insert->price  = $addition->addition_price;
                        $insert->service_id = $addition->service_id ;
                        $insert->save();
                   }
                }

                $items = $cart->items()->with('serviceProduct')->get();
                $itemsCount = $items->sum('amount');

                $sumPricesArray = [];
                $productPrices = [];
                $servicePrices = [] ;

              if($itemsCount){
                   foreach ($items as $item){
                    $amount = (int)$item->amount;
                    $itemAll = $item->serviceProduct;
                    $itemService = $item->service;
                    $itemProduct = $item->product;

                    $itemSize= $item->size;
                    $itemMatterss= $item->Mattresstype;
                    $total_additions = $item->additions->sum('price');
                    $productPrice = $itemProduct?(float)$itemProduct->price:0;
                    $servicePrice = $itemService?(float)$itemService->price:0;
                    $allPrice = $itemAll?(float)$itemAll->price:0;

                    $productsizePrice = $itemSize?(float)$itemSize->price:0;
                    $productMatterssPrice = $itemMatterss?(float)$itemMatterss->price:0;
                    $sumPricesArray[] = $allPrice * $amount + $productsizePrice + $productMatterssPrice + $total_additions;
                    $productPrices[] = $productPrice * $amount;
                    $servicePrices[] = $servicePrice + $productsizePrice + $productMatterssPrice + $total_additions;

                 }
             }

            $sumPrices = array_sum($sumPricesArray);
            $sumproducts = array_sum($productPrices);
            $sumservices = array_sum($servicePrices);

            $productItemArray= [];
            $serviceItemArray= [];

            if($items->count()){
                 foreach($items as $item){
                     $product = $item->product;
                      if($product){
                           $productItemArray[] = [

                                'in_cart_quantity' => (int) $item->amount,
                                'cart_item_id' => (int) $item->id,
                                'id' => (int) $product->id,
                                'title' => (string) $product->title,
                                'quantity' => (float) $product->quantity,
                                'price' => (double) $product->price,
                                'img' => $product->img?(string) url($product->img):null,
                                'available'=>(int) $product->available,

                           ];
                        }
                  }
              }

             if($items->count()){

               foreach($items as $item){

                    $service = $item->service;
                    $size = $item->size;
                    $Matterss = $item->Mattresstype;
                    $Additions = $item->additions;
                    $carType =$item->car_type;

                    if($service){

                       $serviceItemArray[] = [

                            'cart_item_id' => (int) $item->id,
                            'id' => (int)  $service->id,
                            'title' => (string)   $service->title,
                            'price' => (double)   $service->price,
                            'img' =>   $service->img?(string) url($service->img):null,
                            'available'=>(int) $service->available,
                            'size_title' => (string)  isset($size)? $size->size: null,
                            'size_price' => (double) isset($size) ?$size->price: null,
                            'mattress_type_title' => (string) isset($Matterss) ? $Matterss->name: null,
                            'mattress_type_price' => (double) isset($Matterss) ? $Matterss->price:null,
                            'car_type'=>$item->car_type,
                            'additions' => $Additions

                      ];
                   }
                }
             }

             return response()->json([

                'status' => 200,
                'success' => true,
                'message' => 'تم جلب الداتا بنجاح',
                'data' => ['cart_id' => (int) $cart->id, 'items_count' => (int) $itemsCount, 'items_sum_prices' => (float) $sumPrices ,'products_prices'=>$sumproducts ,'services_prices'=>$sumservices ,'products' => $productItemArray , 'services'=>$serviceItemArray]

            ]);
      }
    */

     public function removeFromCart(Request $request, $cart_id, $service_id)
     {

          $cart = Cart::where('id',$cart_id)->where('status', 1)->first();

          if(!$cart){

             return response()->json([

                    'status' => 422,
                    'success' => false,
                    'message' => trans('messages.cart_empty'),

                ]);
          }

         $device_id = $cart->device_id;
         $item = CartItem::where('cart_id', $cart_id)->where('service_id', $service_id)->orderBy('created_at', 'desc')->first();

           if(!$item){

               return response()->json([

                   'status' => 422,
                   'success' => false,
                   'message' => trans('messages.service_not_exist'),
              ]);
         }

        $item->delete();
        $hasUserId = User::find($request->user_id)?true:false;
        $items = $cart->items()->with('service')->get();
        $itemsCount = $items->sum('amount');
        $sumFinalPricesArray = [];
        $sumPricesArray = [];
        $tax = 0;

        if($itemsCount){
            foreach ($items as $item){

                $amount = (int)$item->amount;
                $itemAll = $item->service;
                $allPrice = $itemAll?(float)$itemAll->price:0;
                // $sellertax = BranchesTax::where('seller_id',$itemService->seller_id)->first();

                // if($sellertax){

                //     if ($sellertax->tax_type == 'percent') {

                //         $tax += ($itemService->price * $sellertax->tax) / 100;

                //     } elseif ($sellertax->tax_type == 'amount') {

                //          $tax +=  $sellertax->tax;
                //     }
                // }

                    $sumPricesArray[] = $allPrice * $amount;
               }
           }

          $sumPrices = array_sum($sumPricesArray);

          $final_price =  $sumPrices ;// + $tax ;

           return response()->json([

              'status' => 200,
              'success' => true,
              'message' => trans('messages.get_data_success'),
              'data' => ['cart_id' => (int) $cart->id, 'items_count' => (int) $itemsCount, 'items_sum_prices' => (float) $final_price ,'isRegistered' => $hasUserId, 'message' => trans('products::alert.remove_from_cart_successfully')]
        ]);
    }

    public function emptyCart(Request $request, $cart_id)
    {

           $cart = Cart::where('id',$cart_id)->where('status', 1)->first();

           if(!$cart){

              return response()->json([

                    'status' => 422,
                    'success' => false,
                    'message' => trans('messages.cart_empty'),

                ]);
             }

             $cart->items()->delete();

             return response()->json([

                  'status' => 200,
                  'success' => true,
                  'message' => trans('messages.empty_your_cart'),
             ]);
    }

    public function userCart(Request $request)
    {

        $cart = Cart::where('user_id', auth('api')->id())->where('status', 1)->orderBy('created_at', 'desc')->first();

        return response()->json([

            'status' => 200,
            'success' => true,
            'message' => trans('messages.empty_your_cart'),
            'data' => ['cart_id' => $cart?$cart->id:null,'has_cart' => $cart?true:false]

        ]);
    }

    public function cartItems(Request $request, $cart_id)
    {

        $cart = Cart::where('id', $cart_id)->where('status', 1)->first();

        if(!$cart){

            return response()->json([

                'status' => 422,
                'success' => false,
                'message' => trans('messages.cart_empty'),

            ]);
        }

        $items = $cart->items()->with('service')->get();
        $itemsCount = $items->sum('amount');
        $sumPricesArray = [];
        $tax =0 ;

        if($itemsCount){

            foreach ($items as $item){

                $amount = (int)$item->amount;
                $itemAll = $item->service;
                $allPrice = $itemAll?(float)$itemAll->price:0;
                // $sellertax = BranchesTax::where('seller_id',$itemService->seller_id)->first();

                //    if($sellertax){

                //       if ($sellertax->tax_type == 'percent') {

                //           $tax += ($itemService->price * $sellertax->tax) / 100;

                //          } elseif ($sellertax->tax_type == 'amount') {

                //             $tax +=  $sellertax->tax;
                //         }
                //      }

                     $sumPricesArray[] = $allPrice * $amount;
                }
           }

          $sumPrices = array_sum($sumPricesArray);
          $final_price =  $sumPrices ;// + $tax;
          $serviceItemArray= [];

           if($items->count()){
               foreach($items as $item){
                  $service = $item->service;
                    if($service){
                        $tax +=$service->tax_type == 'percent' ? ($service->price * $service->tax)/100:$service->tax;
                        $serviceItemArray[]= [

                            'cart_item_id' => (int) $item->id,
                            'id' => (int)  $service->id,
                            'title' => (string)   $service->title,
                            'price' => (double)  $service->price,
                            'tax' =>$service->tax_type == 'percent' ? ($service->price * $service->tax)/100:$service->tax,
                            'final_price' => (double)  home_price($service),
                            'img' =>  $service->img?(string) url( $service->img):null,
                            'available'=>(int)  $service->available,
                       ];
                   }
                }
             }

              return response()->json([

                  'status' => 200,
                  'success' => true,
                  'message' => trans('messages.get_data_success'),
                  'data' => ['cart_id' => (int) $cart->id, 'branch_id'=>(int)$cart->branch_id ,'items_count' => (int) $itemsCount , 'items_sum_prices' => (float) $sumPrices , 'tax'=>$tax , 'final_prices' => (float) $final_price +$tax, 'services'=>$serviceItemArray]
            ]);
      }

      public function updateCartUser(Request $request, $cart_id)
      {

            $cart = Cart::where('id', $cart_id)->where('status', 1)->first();

            if(!$cart){

                return response()->json([

                   'status' => 422,
                   'success' => false,
                   'message' => trans('messages.cart_empty')
              ]);
         }

          $user = auth('api')->user();
          $userCart = Cart::where('status', 1)->where('user_id', $user->id)->orderBy('created_at', 'desc')->first();
          if($userCart){
              Cart::where('status', 1)->where('user_id', $user->id)->whereNotIn('id', [$userCart->id])->delete();
               $newCartItems = CartItem::where('cart_id', $cart->id)->update(['cart_id' => $userCart->id]);
          }
          else{

              $cart->update(['user_id' => $user->id]);
              $userCart = $cart;
          }

           return response()->json([

               'status' => 200,
               'success' => true,
               'data' => ['cart_id' => $userCart->id,'message' => __('products::alert.added_user_to_cart_successfully')],

           ]);
        }

    public function serviceWorkTimes($cart_id)
    {

        $items=CartItem::where('cart_id',$cart_id)->select('service_id');

        if ($items->count() > 0) {

            $services_ids=$items->pluck('service_id')->toArray();
            $work_time_ids=ServiceWorktime::whereIn('service_id',$services_ids)->select('work_time_id')->pluck('work_time_id')->toArray();
            $work_times=WorkTime::whereIn('id',$work_time_ids)->get();

            return response()->json([

               'status' => 200,
               'success' => true,
               'data' => ['work_times' => $work_times,'message' => 'list of work times'],

           ]);
        }
    }

    public function add_cart_to_user($cart_id){
         $oldCart = Cart::where(['user_id' => auth('api')->user()->id, 'status' => 1])->first();
         $newCart = Cart::where(['id' => $cart_id,'status' => 1])->first();

        //new and old not exist
        if(!$newCart && !$oldCart){
            return response()->json([
                'success' => true,
                // 'message' => trans('messages.cart_empty')
            ],200);
        }

        // new exist and old not exist
         if(!isset($oldCart) && $newCart){
             $newCart->update(['user_id'=>auth('api')->user()->id]);
             return response()->json([
                 'status' => 200,
                 'success' => true,
                 'data' => $newCart,
                 'message' => trans('messages.get_data_success')
             ]);
         }

        // new not exist and old exist
        if(!isset($newCart) && $oldCart){
            return response()->json([
                'status' => 200,
                'success' => true,
                'data' => $oldCart,
                'message' => trans('messages.get_data_success')
            ]);
        }

        // new and old exist
        if($newCart && $oldCart){
            if($newCart->branch_id != $oldCart->branch_id){
                $oldCart->items()->delete();
                $oldCart->delete();
                $newCart->update(['user_id'=>auth('api')->user()->id]);
                return response()->json([
                    'status' => 200,
                    'success' => true,
                    'data' => $newCart,
                    'message' => trans('messages.get_data_success')
                ]);
            }
            else{
                $cartItems = CartItem::whereIn('cart_id',[$newCart->id,$oldCart->id])->get();
                foreach ($cartItems as $item){
                    if(!in_array($item->service_id,$oldCart->items->pluck('service_id')->toArray()))
                        $item->update(['cart_id'=>$oldCart->id]);
                }
                $newCart->items()->delete();
                $newCart->delete();
            }
        }
        return response()->json([
            'status' => 200,
            'success' => true,
            'data' => ['cart_id' => $oldCart->id,'message' => __('products::alert.added_user_to_cart_successfully')],
        ]);
    }
 }

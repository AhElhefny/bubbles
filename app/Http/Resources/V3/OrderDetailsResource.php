<?php

namespace App\Http\Resources\V3;

use App\Classes\Operation;
use App\Models\Order;
use App\Models\OrderReceipt;
use App\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailsResource extends JsonResource
{
    public function toArray($request)
    {

        $order = $this;
        $statusText = trans('admin.orders_status_options.'.$order->status);
        $driverData = (object) [];

        if($driver = $order->driver){

            $driverData = [

                'id' => $driver->id,
                'name' => $driver->name,
                'mobile' => $driver->mobile
            ];
        }

        $payment = $order->payment;

        return [

            'id' => (int) $order->id ,
            'formated_number' => (int) $order->order_number,
            'payment_status' => (boolean) $order->payment_status,
            'is_rated' => (boolean) $order->is_rated,
            'statusLabel' => $statusText,
            'status' => $order->status,
            'customer_name' => $order->user->name,
            'customer_phone' => $order->user->mobile,
            'delivery_date' => $order->delivery_start_time,
            'payment_method' => $payment?$payment->paymentMethod->name:'cash',
            'address' => $order->address->address,
            'city_id' => $order->city_id ,
            'services' => $this->loadServices($order->services),
            'pricing' => $this->loadPriceses($order),
            'can_cancel' =>  Operation::canCancelOrder($order),
           // 'driver' => $driverData,
            'car_type' => $order->car_type,
            'car_model' => $order->car_model,
            'car_color' => $order->car_color,
            'car_number' => $order->car_number,
            'firebase_id' => $order->firebase_id,
            'created_at' => (string) $order->created_at->format('d/m/Y h:i A'),
            'branch_id' => $order->branch_id
        ];
    }


    public function loadServices($services)
    {

        return collect($services)->map(function ($service) {

            $tax=0;

            if($service->tax_type=='amount'){

                $tax=$service->tax;
            }

            if($service->tax_type=='percent'){

                $tax=$service->price * $service->tax / 100;
            }

            return [

                'title' => (string) $service->title,
                'price' => (double) $service->price,
                'total' => (double) $service->pivot->price+$tax,
                'thumbnail' => isset($service->img)?url($service->img):'',
                'tax'=> $tax
            ];
        });
    }

    public function loadPriceses($order)
    {

           return collect([

               'sub_total' => round((double) $order->sub_total,2),
               'total' => round((double) $order->total,2),
               'tax' => round((double) $order->tax,2),
               'copoun_amount' => (double) $order->coupon_discount,
         ]);
    }
}

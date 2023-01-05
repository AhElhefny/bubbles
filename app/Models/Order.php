<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    protected $table = 'orders';
    protected $fillable = [
        'firebase_id',
        'order_number',
        'user_id',
        'delivery_date',
        'status',
        'total',
        'delivered_at',
        'payment_status',
        'cancelled_by',
        'cancelled_at',
        'cancelled_reason',
        'finished_from',
        'origin_payment_method',
        'delete_reason',
        'deleted_at',
        'coupon_discount',
        'discount',
        'city_id',
        'car_type',
        'car_model',
        'car_number',
        'car_color',
        'branch_id',
        'sub_total',
        'tax',
        'tap_id'
    ];

    const STATUS_RESERVED = 0;
    const STATUS_PROCESSING = 1;
    const STATUS_SHIPPING = 3;
    const STATUS_CANCELED = 5;
    const STATUS_DELIVERED = 8;
    const STATUS_UNPAID = 99;
    const STATUS_PENDING = 10;


    const ACTIVE_ORDER_STATUS_LIST = [
        self::STATUS_RESERVED,
        self::STATUS_PROCESSING,
//        self::STATUS_SCHEDULED,
        self::STATUS_SHIPPING,
//        self::STATUS_SHIPPED,
        self::STATUS_CANCELED,
//        self::STATUS_FAIL_PAYMENT,
//      self::STATUS_RESCHEDULED,
        self::STATUS_DELIVERED,
       // self::STATUS_DELETE,
//      self::STATUS_PENDING,

    ];

    const ON_PROCESS_ORDER_STATUS_LIST = [
        self::STATUS_RESERVED,
        self::STATUS_SHIPPING,
        //self::STATUS_RESCHEDULED,

    ];

    protected $dates = ['created_at', 'updated_at', 'cancelled_at','delivery_start_time'];

   public function getOrderNumberAttribute()
   {
       return (int) '99'.$this->id;

   }

    public function getProductsRatingAttribute()
    {

        $ratingData = $this->ratings->where('type', 'products')->first();

        return $ratingData?(float) $ratingData->rating:0;
    }

    public function scopeNotPending($query)
    {
        return $query->where('status','!=', self::STATUS_PENDING);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'referenceable_id');
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'order_services', 'order_id', 'service_id')

            ->withPivot(['price', 'city_id']);
    }

    public function getPaymentMethodAttribute()
    {
        $payment = $this->payment;
        $paymentMethod = null;
        if($payment){
            $paymentMethod = $payment->paymentMethod;
        }
        return $paymentMethod?$paymentMethod->gateway:null;
    }

    public function getIsRatedAttribute()
    {
        return $this->ratings()->count() > 0;
    }

    public function address()
    {
        return $this->hasOne(OrderShippingAddress::class)->withDefault(function ($address, $parent) {

            $address->username = $parent->user?$parent->user->name:null;
            $address->mobile = $parent->user?$parent->user->mobile:null;
            $address->address = $parent->user?$parent->user->address:null;

            return $address;
        });
    }

    public function ratings()
    {
        return $this->morphMany(Rating::class, 'ratingable');
    }

    public function promotions()
    {
        return $this->belongsToMany(PromoCode::class, 'promocode_user', 'promocode_id', 'referenceable_id')->where('promocode_user.referenceable_type', 'orders');
    }

    public function receipt()
    {
        return $this->hasOne(OrderReceipt::class, 'order_id');
    }

}

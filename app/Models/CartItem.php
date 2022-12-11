<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    
    protected $guarded = ['id'];
    protected $table = "cart_items";

    public function  service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function cart()
    {
        return $this->belongsTo(Cart::class, 'cart_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }


}

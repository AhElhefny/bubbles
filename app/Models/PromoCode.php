<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{

    protected $table = 'promocodes';

    public $timestamps= false;

    public function users(){

        return $this->hasMany('App\Models\PromoCodeuser','promocode_id');
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'promo_code_services', 'promocode_id', 'service_id');
    }

}



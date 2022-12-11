<?php

namespace App\Models;
use App\User;
use Illuminate\Database\Eloquent\Model;


class Seller extends Model
{
    
    protected $guarded = ['id'];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');  
    }

    public function city()
    {
        return $this->belongsTo(City::Class, 'city_id');
    }

}

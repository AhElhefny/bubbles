<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceWorktime extends Model
{
    public $table='service_work_time';
    protected $fillable=['work_time_id','service_id'];
}

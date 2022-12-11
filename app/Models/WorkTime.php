<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkTime extends Model
{
    
    
    protected $guarded = ['id'];
    public function rules()
    {
        
        return [

            'start_time' => 'required',
            'end_time' => 'required',
        ];
    }
}

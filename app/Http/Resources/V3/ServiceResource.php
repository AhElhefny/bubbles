<?php

namespace App\Http\Resources\V3;

use App\Models\Product;
use App\Models\Size;
use App\Models\MattressType;
use App\Models\Additions;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class ServiceResource extends JsonResource
{
    public function toArray($request)
    {
        $service = $this;
        $additions =  Additions::where('service_id' , $service->id)->get();
        $tax = $service->tax_type =='percent'?'%':'';
        return [

            'id' => (int) $service->id,
            'type' => $service->type,
            'title' => (string)  $service->title,
            'description' => (string) $service->description,
            'price' => (float) $service->price ,
            'tax' => $service->tax.$tax,
            'available'=>(int) $service->available,
            'additions'=>$additions,
           'branch_id' => $service->b_id,
           'distance' => $service->distance,
           'branch_name' => $service->name,
        //   'img' => url($service->img)
        ];
    }

}

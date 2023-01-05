<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Illuminate\Validation\Rule;

class Service extends Model implements HasMedia
{

    use HasMediaTrait;
    protected $table = 'services';
    protected $guarded = ['id'];
    public $mediaImageCollectionName = "services-image";

    public function rules()
    {

        return [

            'title' => ['required','string',Rule::unique('services','title')->ignore($this->id)],
            'price' => 'required',
            'worktimes' => ['required',Rule::exists('work_times','id')]
        ];
    }

    public function getImgAttribute()
    {
        $media = $this->getFirstMedia($this->mediaImageCollectionName);

        if ($media) {

            return $media->getFullUrl();
        }
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'product_id');
    }

    public function orders()
    {
        return $this->belongsToMany(Order::Class, 'order_products');
    }

    public function work_times()
    {
        return $this->belongsToMany(WorkTime::Class, 'service_work_time');
    }



}

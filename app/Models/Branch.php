<?php

namespace App\Models;
use App\User;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model implements HasMedia
{
    
    
    use HasMediaTrait;
    protected $guarded = ['id'];
    public $mediaImageCollectionName ="branches-images";

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function getImgAttribute()
    {
    
        $media = $this->getFirstMedia($this->mediaImageCollectionName);
        
        if ($media) {
            
             return $media->getFullUrl();
        }
    }

}

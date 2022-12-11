<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Category extends Model implements HasMedia
{
    
    use HasMediaTrait;

    protected $guarded = ['id'];
    public $mediaIconCollectionName ="category-photoes";
    
    public function getIconAttribute()
    {
        $media = $this->getFirstMedia($this->mediaIconCollectionName);
        
        if ($media) {

            return $media->getUrl();
        }
    }

    public function rules()
    {
        return [
            
            'name' => 'required|string',
        ];
    }
}

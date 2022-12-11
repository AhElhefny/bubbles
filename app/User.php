<?php

namespace App;
use App\Models\City;
use App\Models\CustomerShippingAddress;
use App\Models\Order;
use App\Models\Seller;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use DB;
use App\Models\Module;
use App\Models\Branch;
use App\Models\Cart;

class User extends Authenticatable implements HasMedia
{
    
    use HasApiTokens, Notifiable, HasMediaTrait,HasRoles;

    const CUSTOMER_STATUSES_LIST = [

        'active' => 'active',
        'inactive' => 'inactive',
        'pending' => 'pending',
        'before_pending' => 'before_pending',
        'special_active' => 'special_active',
        'special_pending' => 'special_pending'
    ];
  
    protected $guarded = ['id'];
    public $mediaCollectionName = 'user-avatar';

   
    protected $hidden = [
        
        'password', 'remember_token',
    ];

    protected $casts = [

        'email_verified_at' => 'datetime',
    ];

    public function getAvatarAttribute()
    {
        $media = $this->getFirstMedia($this->mediaCollectionName);

        if ($media) {
            return $media->getUrl();
        } else {
            return null;
        }
    }

    public function getOrdersCountAttribute()
    {

       return $this->orders()->count();
    }

    public function getDefaultAddressAttribute()
    {
        $defaultAddress = $this->addresses()->orderBy('customer_shipping_addresses.id', 'desc')->first();
        if($defaultAddress){
            return $defaultAddress->address;
        }
        return null;
    }


    public function getCityNameAttribute()
    {
        $city = $this->city;
        return $city ? $city->name:null;
    }

    public function addresses()
    {
        return $this->hasMany(CustomerShippingAddress::Class, 'user_id');
    }

    public function seller()
    {

       return $this->hasOne(Seller::class);

    }

    public function orders()
    {
        return $this->hasMany(Order::Class, 'user_id');
    }


    public function city()
    {
        return $this->belongsTo(City::Class, 'city_id');
    }

    public function rules($update = false)
    {
        
        $rules = [
           
           'name' => 'required|string',
           'email' => 'unique:users',
//          'password' => 'required|min:6|confirmed',
       ];

       if($update){
           $rules['email'] = "unique:users,email,$this->id,id";
           $rules['password'] = 'nullable';
       }

       return $rules;

    }

    public function hasPermissionName($permission_name)
    {
        $permision =Permission::where('name',$permission_name)->first();
        
        if(!$permision)
        {
            return false;
        }

        $user_roles=DB::table('model_has_roles')->where('model_id',$this->id)->pluck('model_has_roles.role_id')->toArray();
        $role_has_permissions=DB::table('role_has_permissions')->where('permission_id',$permision->id)->whereIn('role_id',$user_roles)->first();
        if ($role_has_permissions) {
            return true;
        }
        return false;
    }
    
    public function branches(){
        return $this->hasMany(Branch::class);
    }
    
    public function cart(){
        return $this->hasOne(Cart::class);
    }


}

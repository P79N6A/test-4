<?php
namespace App\Models;
class BusinessModel extends Model{
    protected $table = 'bus_users';
    protected $fillable = ['name','description','mobile','email','password','salt','pid','city_id','disabled','avatar'];

    public function city(){
        return $this->hasOne('App\Models\CityModel','id','city_id');
    }

    public function stores(){
        return $this->hasManyThrough('App\Models\StoreModel','App\Models\BrandModel','bus_user_id','brand_id');
    }
}

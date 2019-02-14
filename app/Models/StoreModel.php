<?php
namespace App\Models;
class StoreModel extends Model{
    protected $table = 'stores';
    protected $fillable = ['name','tel','brand_id','address','imgs','teachers','longitude','latitude'];

    public function brand(){
        return $this->hasOne('App\Models\BrandModel','id','brand_id');
    }

    public function pic(){
        return $this->hasOne('App\Models\AttachmentModel','id','img');
    }

    public function city(){
        return $this->hasOne('App\Models\CityModel','id','city_id');
    }
}

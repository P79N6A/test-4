<?php
namespace App\Models;
class EquipmentModel extends Model{
    protected $table = 'iot_equipments';
    protected $fillable = ['city_id', 'store_id','model','code','name', 'disabled'];

	protected $primaryKey = 'id';

	protected $dateFormat = 'U';

    public function city(){
        return $this->hasOne('App\Models\CityModel','id','city_id');
    }

    public function store(){
        return $this->hasOne('App\Models\StoreModel','id','store_id');
    }
}
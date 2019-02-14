<?php
namespace App\Models;
class EquipmentGamesModel extends Model{
    protected $table = 'iot_equipments_games';
    // protected $fillable = ['city_id', 'store_id','model','code','name', 'disabled'];

    // protected $primaryKey = 'id';

    // protected $dateFormat = 'U';
    public function store(){
        return $this->hasOne('App\Models\StoreModel','id','store_id');
    }

    public function equipment(){
        return $this->hasOne('App\Models\EquipmentModel','id','equipment_id');
    }
}
<?php
namespace App\Models;
class EquipmentRevenueModel extends Model{
    protected $table = 'iot_equipments_revenue';
    protected $fillable = ['code', 'type','coin'];

	protected $primaryKey = 'id';

	protected $dateFormat = 'U';
}
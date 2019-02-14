<?php
namespace App\Models;
class EquipmentSeqModel extends Model{
    protected $table = 'iot_equipments_seq';
    protected $fillable = ['code', 'seq','orderid'];

	protected $primaryKey = 'id';
}
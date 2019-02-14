<?php
namespace App\Models;
class StoreStaffRecordModel extends Model{
    protected $table = 'store_staff_record';
    protected $fillable = ['store_id','user_id','equipment','pos'];

	protected $primaryKey = 'id';

	protected $dateFormat = 'U';
}
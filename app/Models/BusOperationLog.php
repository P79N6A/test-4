<?php
namespace App\Models;

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BusOperationLog extends Model{

	public $guarded = [];
    public $timestamps = false;
	protected $table = 'bus_operate_log';
	protected $primaryKey = 'log_id';

	public function user(){
		return $this->hasOne('App\Models\User','id','userid');
	}

	
}
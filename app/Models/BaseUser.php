<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseUser extends Model{

	public $guarded = [];
    public $timestamps = false;
	protected $table = 'base.users';
	protected $primaryKey = 'id';
}
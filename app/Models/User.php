<?php
/**
 * Created by PhpStorm.
 * User: AIMPER
 * Date: 2016/9/18
 * Time: 17:22
 */

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Model{
    use EntrustUserTrait; // add this trait to your user model

    protected $table = 'bus_users';

}
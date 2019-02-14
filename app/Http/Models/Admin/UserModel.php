<?php
/**
 * Created by PhpStorm.
 * User: AIMPER
 * Date: 2016/9/25
 * Time: 22:36
 */

namespace App\Http\Models\Admin;
use Illuminate\Database\Eloquent\Model;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class UserModel extends Model
{
    use EntrustUserTrait;

    protected $table = 'admin_users';
    protected $fillable = ['name','email','password'];

}
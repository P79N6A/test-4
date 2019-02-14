<?php
/**
 * Created by PhpStorm.
 * User: AIMPER
 * Date: 2016/9/29
 * Time: 9:48
 */

namespace App\Http\Models;
use Illuminate\Database\Eloquent\Model;


class UserModel extends Model
{
    protected $table = 'users';
    protected $timestamp = false;
}
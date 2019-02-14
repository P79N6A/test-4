<?php
/**
 * Created by PhpStorm.
 * User: AIMPER
 * Date: 2016/9/25
 * Time: 22:36
 */

namespace App\Http\Models\Merchant;
use Illuminate\Database\Eloquent\Model;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class MemberModel extends Model
{
    use EntrustUserTrait;

    protected $table = 'users';
    protected $fillable = [];

}
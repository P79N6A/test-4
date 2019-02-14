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

class DoctorMoneyRecordModel extends Model
{
    use EntrustUserTrait;

    protected $table = 'doc_money_record';
    protected $fillable = [];

}
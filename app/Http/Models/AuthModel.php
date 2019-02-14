<?php

namespace App\Http\Models;
use Illuminate\Database\Eloquent\Model;

class AuthModel extends Model {

    // 设置表名
    protected $table = 'tokens';
    // 取消 create_at 和 update_at 字段操作
    public $timestamps = false;
}

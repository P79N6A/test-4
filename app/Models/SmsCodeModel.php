<?php
namespace App\Models;
class SmsCodeModel extends Model{
    protected $table = 'sms_code';
    protected $fillable = ['mobile','code'];
}

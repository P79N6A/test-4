<?php
namespace App\Models;
class VipModel extends Model{
    protected $table = 'vip_info';
    protected $fillable = ['price','content'];
}

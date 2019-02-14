<?php
/**
 * 用户模型
 */
namespace App\Models;
class UsersModel extends Model{
    protected $table = 'users';
    protected $fillable = ['mobile','nickname','img','openid','password','salt','is_vip','last_city','vip_expire','role','invite_parent_id'];

    public function city(){
        return $this->hasOne('App\Models\CityModel','id','last_city');
    }
}

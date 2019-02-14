<?php
namespace App\Models;
class BrandModel extends Model{
    protected $table = 'brands';
    protected $fillable = ['name','description','mobile','email','password','salt','pid','city_id','disabled','avatar'];

    /**
     * 所属商户
     */
    public function belongs(){
        return $this->hasOne('App\Models\BusinessModel','id','bus_user_id');
    }
}

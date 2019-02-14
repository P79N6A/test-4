<?php

namespace App\Models;
class UserTokenModel extends Model{
    protected $table = 'user_token';
    protected $primaryKey = 'user_id';
    protected $fillable = ['user_id','token','expire'];

    public function user(){
        return $this->hasOne('App\Models\UsersModel','id','user_id');
    }

    public function collection(){
        return $this->hasManyThrough('App\Models\CourseModel','App\Models\CollectionModel','user_id','id');
    }
}

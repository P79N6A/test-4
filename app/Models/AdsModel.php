<?php
namespace App\Models;
class AdsModel extends Model{
    protected $table = 'ads';
    protected $fillable = ['title','img','url','type','pos_id','sort'];

    public function pos(){
        return $this->hasOne('App\Models\AdsPosModel','id','pos_id');
    }

    public function pic(){
        return $this->hasOne('App\Models\AttachmentModel','id','img');
    }
}

<?php
namespace App\Models;
class TeacherModel extends Model{
    public $timestamps = false;
    protected $table = 'teachers';
    protected $fillable = ['name','job','img'];

    public function pic(){
        return $this->hasOne('App\Models\AttachmentModel','id','img');
    }
}

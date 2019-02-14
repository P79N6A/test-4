<?php
namespace App\Models;
class CourseTypeModel extends Model{
    public $timestamps = false;
    protected $table = 'course_type';
    protected $fillable = ['name','icon','pid','sort','disabled'];

    public function img(){
        return $this->hasOne('App\Models\AttachmentModel','id','icon');
    }
}

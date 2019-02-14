<?php
namespace App\Models;
class CourseClassModel extends Model{
    protected $table = 'course_class';
    protected $fillable = ['name','course_id','type','sort','times'];

    public function course(){
        return $this->hasOne('App\Models\CourseModel','id','course_id');
    }

}

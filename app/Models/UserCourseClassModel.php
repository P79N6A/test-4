<?php
namespace App\Models;
class UserCourseClassModel extends Model{
    protected $table = 'user_course_class';
    protected $fillable = ['class_id','user_course_id','user_id','start_at','finish_at','times','total_times'];

    public function class(){
        return $this->hasOne('App\Models\CourseClassModel','id','class_id');
    }

    public function store(){
        return $this->hasOne('App\Models\StoreModel','id','store_id');
    }
}

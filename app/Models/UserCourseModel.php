<?php
namespace App\Models;
class UserCourseModel extends Model{
    protected $table = 'user_course';
    protected $fillable = ['name','type_id','class_num','finish_num','price','suitable_age','class_id','content','img','course_id','is_finish','started_at'];

    public function pic(){
        return $this->hasOne('App\Models\AttachmentModel','id','img');
    }

    public function suitable(){
        return $this->hasOne('App\Models\SuitableAgeModel','id','suitable_age');
    }

    public function type(){
        return $this->hasOne('App\Models\CourseTypeModel','id','type_id');
    }

    public function course(){
        return $this->hasOne('App\Models\CourseModel','id','course_id');
    }

    public function user_class(){
        return $this->hasMany('App\Models\UserCourseClassModel','user_course_id','id');
    }
}

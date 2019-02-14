<?php
namespace App\Models;
class UserCourseRecordModel extends Model{
    protected $table = 'user_course_record';
    protected $fillable = ['user_course_class_id','user_id','start_at','finish_at'];
}
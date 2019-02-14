<?php
namespace App\Models;
class CourseStoreModel extends Model{
    protected $table = 'course_store';
    protected $fillable = ['course_id','store_id'];
}

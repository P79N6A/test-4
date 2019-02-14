<?php
namespace App\Models;
class CourseModel extends Model{
    protected $table = 'course';
    protected $fillable = ['name','type_id','class_num','price','suitable_age','class_id','content','store_ids','img','is_hot','is_recommend','sort', 'buy_limit'];

    public function pic(){
        return $this->hasOne('App\Models\AttachmentModel','id','img');
    }

    public function suitable(){
        return $this->hasOne('App\Models\SuitableAgeModel','id','suitable_age');
    }

    public function type(){
        return $this->hasOne('App\Models\CourseTypeModel','id','type_id');
    }

    public function city(){
        return $this->hasOne('App\Models\CityModel','id','city_id');
    }

    public function class(){
        return $this->hasMany('App\Models\CourseClassModel','course_id','id');
    }
}

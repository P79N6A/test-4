<?php
namespace App\Models;
class CollectionModel extends Model{
    protected $table = 'collections';
    protected $primaryKey = 'course_id';
    protected $fillable = ['course_id','user_id'];
}

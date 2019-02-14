<?php
namespace App\Models;
class NoticeModel extends Model{
    protected $table = 'notice';
    protected $fillable = ['title','content','disabled'];
}

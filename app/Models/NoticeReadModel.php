<?php
namespace App\Models;
class NoticeReadModel extends Model{
    protected $table = 'notice_read';
    protected $fillable = ['user_id','notice_id'];
}

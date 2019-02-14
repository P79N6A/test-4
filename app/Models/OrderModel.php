<?php
namespace App\Models;
use Illuminate\Support\Facades\DB;
class OrderModel extends Model{
    protected $table = 'order';
    protected $fillable = ['order_num','type','total','course_id','user_id','status','wechat_num'];

    public function course(){
        return $this->hasOne('App\Models\CourseModel','id','course_id');
    }

    public function user(){
        return $this->hasOne('App\Models\UsersModel','id','user_id');
    }

    public function pic(){
        return $this->hasOne('App\Models\AttachmentModel','id','img');
    }

    public function orderTimeGetNum($data){

        $data = DB::table('order')->whereIn(  DB::raw('DATE_FORMAT(created_at,"%Y-%m-%d")'),$data)
        ->where('status', 1)        
        ->select( 
            DB::raw( 'ifNull(count(id),0) as num'),
            DB::raw( 'ifNull(sum(total)/100,0) as total'),
            DB::raw( 'DATE_FORMAT(created_at,"%Y-%m-%d") as day')
        )
        ->groupBy('day')
        ->get();
        return $data;
    }
}

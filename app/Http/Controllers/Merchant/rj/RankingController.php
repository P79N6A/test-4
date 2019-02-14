<?php

namespace App\Http\Controllers\Merchant\rj;
use App\Http\Controllers\Controller;
use EasyWeChat\Core\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\json_decode;
use GuzzleHttp\json_encode;

class RankingController extends Controller
{

   //怪兽猎人
   public function MonsterHunter(Request $request){
       $model = 'C-499';
       $season = DB::table(config('tables.base').'.rj_game_season')->where('model',$model)->first();
       $season = json_decode(json_encode($season),true);

       $GL = DB::table(config('tables.base').'.rj_game_log AS gl')
           ->leftJoin(config('tables.base').'.rj_iot_machine AS m', 'm.id',' =','gl.m_id')
           ->leftJoin(config('tables.base').'.rj_iot_product AS p', 'p.id',' =','m.product_id')
           ->leftJoin(config('tables.base').'.rj_machine_model AS mm', 'mm.model_code',' =','p.model')
           ->leftJoin(config('tables.base').'.users AS u', 'u.id',' =','gl.u_id');

       //排行榜
       $GL->where('gl.u_id','>','0')
           ->where('mm.model_code',$model)
           ->groupBy('gl.u_id')
           ->orderBy('ticket_num','desc')
           ->select(
               'gl.u_id as u_id',
               'u.nickname as nickname',
               'u.mobile as mobile',
               'u.avatar as avatar',
               DB::raw('SUM(gl.num) as ticket_num')
           );

       $data = $GL->paginate(20);

      $return = json_decode(json_encode($data),true);
//    echo '<pre>';
//    print_r();

       //今天
       $res = $this->data($return['data'],['model'=>$model,'dateType'=>1,'field'=>'day']);

       //月
       $res = $this->data($res,['model'=>$model,'dateType'=>3,'field'=>'month']);

       //赛季
       $res = $this->data($res,['model'=>$model,'dateType'=>4,'field'=>'saiji']);

       //年
       $res = $this->data($res,['model'=>$model,'dateType'=>5,'field'=>'yean']);




       $res = json_decode(json_encode($res),true);

//    echo '<pre>';
//    print_r($res);exit;


       return view(env('Merchant_view').'.ranking.MonsterHunter',[
            'model'=>$model,
            'season'=>$season,
            'ads'=>$data,
            'res'=>$res
       ]);
   }

   public function data($return,$where){
       $data = $this->sql($where);

       if(empty($data)){
           foreach($return as $key=>$val){
               $return[$key][$where['field']] = 0;
           }
       }else{
           $this->CreataTable();
           $this->InsertTable($data);

//           echo '<pre>';
//           print_r($return);exit;

           foreach($return as $key=>$val){
               $ranking = $this->Ranking($val['u_id']);
               $return[$key][$where['field']] = $ranking;
           }

           $this->DropTable($data);
       }

       return $return;
   }

   public function sql($where){
       $GL = DB::table(config('tables.base').'.rj_game_log AS gl')
           ->leftJoin(config('tables.base').'.rj_iot_machine AS m', 'm.id',' =','gl.m_id')
           ->leftJoin(config('tables.base').'.rj_iot_product AS p', 'p.id',' =','m.product_id')
           ->leftJoin(config('tables.base').'.rj_machine_model AS mm', 'mm.model_code',' =','p.model')
           ->leftJoin(config('tables.base').'.users AS u', 'u.id',' =','gl.u_id');

       switch ($where['dateType']){
           case '1':   //按日
               $today = strtotime(date("Y-m-d"),time());
               $end = $today+60*60*24;
               $startdate = date("Y-m-d H:i:s",$today);
               $enddate = date("Y-m-d H:i:s", $end);

               $GL->whereBetween('gl.create_time',[$startdate,$enddate]);
               break;
           case '2':    //按周
               $today = strtotime(date("Y-m-d"),time());
               $end = $today+60*60*24;
               $startdate = date("Y-m-d H:i:s",$today-(7*24*60*60));
               $enddate = date("Y-m-d H:i:s", $end);

               $GL->whereBetween('gl.create_time',[$startdate,$enddate]);
               break;
           case '3':    //按月
               $lastMonth = date("Y-m-d ",strtotime("-1 month"));
               $startdate = date("Y-m-d H:i:s",strtotime($lastMonth));

               $today = strtotime(date("Y-m-d"),time());
               $end = $today+60*60*24;
               $enddate = date("Y-m-d H:i:s", $end);

               $GL->whereBetween('gl.create_time',[$startdate,$enddate]);
               break;
           case '4':   //按赛季
               $first = DB::table(config('tables.base').'.rj_game_season')->where('model',$where['model'])->orderBy('id','desc')->first();
//$this->_sql();
               //  print_r($first);exit;
               if(!$first){
                   $this->error = '没有赛季信息';
                   return false;
               }

               $season = $first->code;

               $startdate = date("Y-m-d H:i:s",$first->start_time);

               $enddate = date("Y-m-d H:i:s", $first->end_time);

               $GL->whereBetween('gl.create_time',[$startdate,$enddate]);
               break;
           case '5':
               $lastMonth = date("Y-m-d",strtotime("-1year"));   //按年
               $startdate = date("Y-m-d H:i:s",strtotime($lastMonth));

               $today = strtotime(date("Y-m-d"),time());
               $end = $today+60*60*24;
               $enddate = date("Y-m-d H:i:s", $end);

               $GL->whereBetween('gl.create_time',[$startdate,$enddate]);
               break;
           default:
               $this->error = '查询方式错误';
               return false;
               break;
       }

       //排行榜
       $GL->where('gl.u_id','>','0')
           ->where('mm.model_code',$where['model'])
           ->groupBy('gl.u_id')
           ->orderBy('ticket_num','desc')
           ->select(
               'gl.u_id as u_id',
               DB::raw('SUM(gl.num) as ticket_num')
           );

       $return = $GL->get();
       $return = json_decode(json_encode($return),true);
       return $return;
   }

    //获取排名
    public function Ranking($uid){
        $tableName = "##rj_user_ticket_num";
        $select = "SELECT (SELECT COUNT(*) FROM `$tableName` WHERE a.ticket_num<=ticket_num) AS ranking 
                    FROM `$tableName` AS a where a.u_id = ".$uid;

        //获取排名
        $ranking =  DB::select($select);

        if(empty($ranking)){
            return 0;
        }else{
            $ranking = json_decode(json_encode($ranking),true);
            return $ranking[0]['ranking'];
        }
    }

    //创建表
    public function CreataTable(){
        $tableName = "##rj_user_ticket_num";
        $table = "   CREATE TABLE `$tableName` (
                      `ticket_num` int(32) NOT NULL COMMENT '订单号',
                      `u_id` int(11) NOT NULL COMMENT '用户ID'
                    ) ENGINE=InnoDB AUTO_INCREMENT=144981 DEFAULT CHARSET=utf8 COMMENT='临时表(奖票数量)';
                ";
        //创建表
        DB::select($table);
    }

    //插入表
    public function InsertTable($data){
        $tableName = "##rj_user_ticket_num";
        //插入表
        DB::table($tableName)->insert($data);
    }

    //删除表
    public function DropTable(){
        $tableName = "##rj_user_ticket_num";
        $drop = "DROP TABLE `$tableName`";
        //删除表
        DB::select($drop);
    }





    /**
     * @name 赛季设置
     * @param model  {str}   游戏类型
     * @param name   {str}   机台名称
     * @param start2 {str}   开始时间
     * @param end2   {str}   结束时间
     */
   public function seasonSize(Request $request){
       $param = $_POST;
       $find = DB::table(config('tables.base').'.rj_game_season')->where('model',$param['model'])->first();
      if(empty($find)){
           $data = array(
               'model'=>$param['model'],
               'code'=>$param['code'],
               'start_time'=>strtotime($param['start_time']),
               'end_time'=>strtotime($param['end_time']),
           );
          $id = DB::table(config('tables.base').'.rj_game_season')->insert($data);
      }else{
          $data = array(
              'code'=>$param['code'],
              'start_time'=>strtotime($param['start_time']),
              'end_time'=>strtotime($param['end_time']),
          );
          $id = DB::table(config('tables.base').'.rj_game_season')->where('model',$param['model'])->update($data);
      }
      if(!$id){
          return ['code'=>'500','msg'=>'设置失败'];
      }
      return ['code'=>'200','msg'=>'设置成功'];
   }

    
   
  
}

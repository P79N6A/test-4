<?php

namespace App\Http\Controllers\Merchant;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;


/**
 * @author arcy.tan <arcy@zs-shiyu.com>
 * 积分/彩票 转换记录
 */
class BusExchangeLogController extends Controller
{
    /**
     * 门店积分转换为平台积分的记录数据
     * @param  Illuminate\Http\Request $request 
     * @return view
     */
    public function busScoresExchange(Request $request){
        $data = $request->only(['id','start_date', 'end_date', 'code','mobile']);
        $store_id = (int)$data['id'];
        if(empty($store_id)){
            return $tihs->response(500,'内部错误！');
        }

        if(!in_array($store_id, $this->storeIds)){
            return $this->response(500,'无门店管理权限！');
        }

        $builder = DB::table('member_score_output_log as ms')->where('ms.store_id',$store_id)
            ->leftJoin(config('tables.base') . '.users as u' ,'ms.userid' ,'=' ,'u.id');

        empty($data['start_date']) || $builder->where('ms.create_date','>=',$data['start_date']);
        empty($data['end_date']) || $builder->where('ms.create_date','<=',$data['end_date']);
        empty($data['code']) || $builder->where('ms.member_card_no',$data['code']);
        empty($data['mobile']) || $builder->where('u.mobile',$data['mobile']);

        $fields = [
            // DB::raw('FROM_UNIXTIME(ms.create_date, "YYYY-MM-DD") AS create_time'), //转换时间
            'ms.create_date', //转换时间
            'u.mobile',//会员手机号
            'ms.member_card_no',//会员编码
            'ms.score',//会员卡转出积分
            'ms.rate',//转换比例
        ];

        $lists = $builder->select($fields)->orderBy('ms.create_date','DESC')->paginate(20);

        return view('business.bus-scores-exchage-log',[
            'list'=>$lists,
            'params' => $data
        ]);
    }


    /**
     * 门店积分转换为平台积分的记录数据
     * @param  Illuminate\Http\Request $request 
     * @return view
     */
    public function busTicketsExchange(Request $request){
        $data = $request->only(['id','start_date', 'end_date', 'code','mobile']);
        $store_id = (int)$data['id'];
        if(empty($store_id)){
            return $tihs->response(500,'内部错误！');
        }
        if(!in_array($store_id, $this->storeIds)){
            return $this->response(500,'无门店管理权限！');
        }

        $builder = DB::table('ticket_out_log as tol')->where('tol.store_id',$store_id)
            ->leftJoin(config('tables.base') . '.users as u' ,'tol.user_id' ,'=' ,'u.id');

        empty($data['start_date']) || $builder->where('tol.create_date','>=',$data['start_date']);
        empty($data['end_date']) || $builder->where('tol.create_date','<=',$data['end_date']);
        empty($data['code']) || $builder->where('tol.leag_no',$data['code']);
        empty($data['mobile']) || $builder->where('u.mobile',$data['mobile']);

        $fields = [
            'tol.create_date', //转换时间
            'u.mobile',//会员手机号
            'tol.real_ticket', //正式彩票
            'tol.leag_no AS member_card_no',//会员编码
            // DB::raw('ROUND(SUM(tol.rate * tol.real_ticket),2) AS ticket_transferred_scores'),  // 彩票转出积分
            DB::raw('ROUND(tol.real_ticket/tol.rate,0) AS ticket_transferred_scores'),  // 彩票转出积分
            'tol.rate',//转换比例
        ];

        $lists = $builder->select($fields)->orderBy('tol.create_date','DESC')->paginate(20);

        return view('business.bus-ticket-exchage-log',[
            'list'=>$lists,
            'params' => $data
        ]);
    }

    /**
     * 会员积分记录
     */
    public function memberScoreLog(Request $request){
        $data = $request->only(['id','start_date','end_date']);
        $user_id = (int)$data['id'];
        if(empty($user_id)){
            return $this->response(500,'内部错误！');
        }

        $user_info = DB::table(config('tables.base') . '.users')->where('id',$user_id)->first();

        if(!$user_info){
            return $this->response(503,'会员不存在错误！');
        }

        $builder = DB::table(config('tables.base') . '.score_log as sl')->where('userid',$user_id);

        $start_date = strtotime($data['start_date']);
        $end_date = strtotime($data['end_date']);
        empty($start_date) || $builder->where('sl.add_time','>=',$start_date);
        empty($end_date) || $builder->where('sl.add_time','<=',$end_date);

        $lists = $builder->orderBy('sl.add_time','DESC')->paginate(20);


        return view('business.bus-member-score-log',[
            'list'=>$lists,
            'user_info'=>$user_info,
            'params' => $data
        ]);
    }

    

}

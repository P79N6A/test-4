<?php
/**
 * 用户模型
 */
namespace App\Models;
use Illuminate\Support\Facades\DB;

class DoctorMoneyRecordModel extends Model{
	protected $table = 'doc_money_record';
	protected $primaryKey = 'id';

	protected $dateFormat = 'U';

	/*
    *return $total['order_amount'](用户消费总金额)   $total['record_amount'](奖金总金额)   $total['money'](可提现金额)
    */
    public static function totalDoctor($id){
        $total = DB::table('doc_money_record')->where('user_id',$id)
                ->where('status', 1)
                ->select(
                     DB::raw( 'IFNULL(sum(if(type=1,convert(order_amount/100,decimal(10,2)),0)),0) as order_amount'),/*用户消费总金额*/
                     DB::raw( 'IFNULL(sum(if(type=1,convert(money/100,decimal(10,2)),0)),0) as record_amount'),/*奖金总金额*/
                     DB::raw('IFNULL(sum(if(type=1,if(status=1,convert(money/100,decimal(10,2)),0),0))+sum(if(type=3,-abs(convert(money/100,decimal(10,2))),0)),0) as money') /*可提现金额*/
                )
                ->first();
        return $total;
    }
}
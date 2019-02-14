<?php
/**
 * 商户门店财务管理
 * @author Arcy <  arcy@zs-shiyu.com  > 
 * @date 2018-01-02
 */

namespace App\Http\Controllers\Merchant;
use DB;
use Cache;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Merchant\UserModel;

class FinanceController extends Controller
{

    /**
     * 可提现门店列表
     **/
    public function stores(){

        $stores = DB::table('bus_stores as bs')
            ->whereIn('bs.id',$this->storeIds)
            ->select(['name','id'])
            ->paginate(20);

        return view('business.store-balance-list',['stores'=>$stores]);
    }

    /**
     * 门店余额
     */
    public function storeBalance(Request $request){
        $store_id = $request->get('id');

        if(intval($store_id) <= 0){
            return $this->response(403, '非法操作！');
        }

        //获取门店未结算、已核销的订单
        $result = [
            'available' => 0, //可提余额
            'balance' => 0, //账户余额
            'user_pay' => 0, //用户实付
            'platform_pay' => 0, //平台补贴
            'service_fee' => 0, //平台服务费
            'score_fee' => 0, //积分转入费用
            'ticket_fee' => 0, //彩票转入费用
            'order_ids' => [], //可提现的订单ID
            'score_ids' => [], //转入平台积分的ID
            'ticket_ids' => [], //转入平台彩票的ID
        ];
        $expire = strtotime(date('Y-m-d'));

        /** 提取数据库未结算订单 **/
        DB::table('order')
            ->where([
                ['store_id','=',$store_id],
                ['status','=',2],
                ['is_withdraw','=',0]
            ])
            ->orderBy('id','ASC')
            ->chunk(100,function($orders) use (&$result,$expire){
                foreach ($orders as $order) {
                    /**记录订单ID**/
                    $result['order_ids'][] = $order->id;
                    /**计算用户实付,用户实付=用户单笔实付+单笔使用积分**/
                    $result['user_pay'] += $order->pay_price;
                    $result['user_pay'] += round(($order->score_uses / 100) , 2);
                    // $result['user_pay'] -= $order->service_cost;

                    /**计算账户余额和可提余额**/
                    $result['balance'] += $order->pay_price;
                    $result['balance'] += round(($order->score_uses / 100) , 2);

                    /* 余额和可提余额减去服务 */
                    $result['balance'] -= $order->service_cost;

                    if($order->addtime < $expire){ //可提余额
                        $result['available'] += $order->pay_price;
                        $result['available'] += round(($order->score_uses / 100) , 2);
                        $result['available'] -= $order->service_cost;
                    }

                    if($order->cash_ticket_platform == 1){ //现金券为平台现金券，则需要把平台补贴部分加进商户余额
                        $result['balance'] += $order->ticket_denomination;
                        $result['platform_pay'] += $order->ticket_denomination; 

                        ($order->addtime < $expire) && $result['available'] += $order->ticket_denomination;
                    }

                    if($order->discount_ticket_platform == 1){ //折扣券为平台折扣券，则需要把平台补贴部分加进商户余额
                        $result['balance'] += $order->ticket_discount;
                        $result['platform_pay'] += $order->ticket_discount;

                        ($order->addtime < $expire) && $result['available'] += $order->ticket_denomination;
                    }


                    /**计算服务费**/
                    $result['service_fee'] += $order->service_cost;
                }
            });

        /** 处理积分转入收费 */
        DB::table('member_score_output_log')
            ->where([
                ['store_id','=',$store_id],
                ['is_withdraw','=',0],
                ['create_date','<',date('Y-m-d H:i:s',$expire)]
            ])
            ->orderBy('id','ASC')
            ->chunk(100,function($scores) use (&$result){
                $sum=0;
                foreach ($scores as $score) {
                    /**计算转换的平台积分**/
                    if($score->score < 0){
                        $sum -= round( (abs($score->score)/$score->rate), 2);
                    }else{
                        $sum += round( ($score->score/$score->rate), 2);
                    }

                    $sum = round(($sum/100),2); //每100积分=1元
                    
                    /**记录积分处理ID**/
                    $result['score_ids'] = $score->id;
                }
                $result['score_fee'] += $sum;
            });

        /**处理彩票转入收费**/
        DB::table('ticket_out_log')
            ->where([
                ['store_id','=',$store_id],
                ['is_withdraw','=',0],
                ['create_date','<',date('Y-m-d H:i:s',$expire)]
            ])
            ->orderBy('id','ASC')
            ->chunk(100,function($tickets) use (&$result){
                foreach ($tickets as $ticket) {
                    /* 记录处理的彩票ID */
                    $result['ticket_ids'] = $ticket->id;

                    /* 计算彩票转入费用 */
                    $result['ticket_fee'] += round(($ticket->rate * $ticket->real_ticket) , 2);
                }
                
            });

        /* 用户实付，可提余额，账户余额需要减去积分转入费用和彩票转入费用 */
        $result['user_pay'] -= $result['score_fee'];
        $result['user_pay'] -= $result['ticket_fee'];
        $result['balance'] -= $result['score_fee'];
        $result['balance'] -= $result['ticket_fee'];
        $result['available'] -= $result['score_fee'];
        $result['available'] -= $result['ticket_fee'];

        $result = array_map(function($v){
            if(is_numeric($v)){
                return round($v,2);
            }else{
                return $v;
            }
        }, $result);

        /* 获取当前门店名称 */
        $store_name = DB::table('bus_stores')->where('id',$store_id)->value('name');

        return view('business.store-balance',[
            'store_name' => $store_name,
            'value' => $result
        ]);
    }

}
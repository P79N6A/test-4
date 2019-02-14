<?php
namespace App\Http\Controllers\Api;

use App\Models\DoctorMoneyRecordModel;
use App\Models\OrderModel;
use App\Models\UsersModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DoctorController extends Controller
{

/*
 *生成订单时  加入一条流水记录  记录医生的佣金
 *当type=1(为增加佣金)   type=2(退款)   type=3(提现)
 *当type=1 status=1(奖金审核通过  用户消费后已上课  无法退款  可提现)
 *当type=2 status=1   (退款审核通过  扣减奖金流水)  奖金流水的status=-1
 *当type=3 status=1(提现审核通过  ...)
 *退款步骤：  用户点击退款后   后台生成流水 status=0 不做扣减   退款审核通过后 退款流水：status=1    对应的奖金流水：status=-1  做扣减
 */
    /*
     *医生首页
     */
    public function index(Request $request)
    {
        $id = $this->auth->user_id;
        $result = UsersModel::leftjoin('doc_money_record as dmr', 'users.id', '=', 'dmr.user_id')
            ->where('dmr.status', '>', -1)
            ->where('users.id', '=', $id)
            ->select(
                DB::raw('IFNULL(sum(if(dmr.type=1,dmr.order_amount,0)),0) as order_amount'), /*用户消费总金额*/
                DB::raw('IFNULL(sum(if(dmr.type=1,if(dmr.status=1,dmr.money,0),0))+sum(if(dmr.type=3,-abs(dmr.money),0)),0) as money'), /*可提现金额*/
                DB::raw('IFNULL(users.realname,"") as realname'),
                DB::raw('IFNULL(users.invite_code,"") as invite_code')
            )
            ->first()->toArray();

        $result['realname'] = mb_substr($result['realname'], 0, 1, 'utf-8') . '医生';
        $list = DoctorMoneyRecordModel::where('user_id', $id)
            ->where('status', '>', -1)
            ->whereIn('type', [1, 3])
            ->orderby('id', 'desc')
            ->select(
                DB::raw('from_unixtime(created_at,"%Y-%m-%d %H:%i") as date'),
                DB::raw('if(type=1,money,-abs(money)) as amount'),
                'id',
                'order_num',
                DB::raw('if(type=1,"得到奖金","提现奖金") as description')
            )
            ->limit(5)->get()->toArray();
        $result['list'] = $list;
        return $this->success($result);
    }

    /*
     *消费列表
     */
    public function record_list()
    {
        $id = $this->auth->user_id;
        $result = UsersModel::leftjoin('users as u', 'u.invite_parent_id', '=', 'users.id')
            ->where('users.id', $id)
            ->select(
                //  DB::raw('IFNULL(u.nickname,"") as nickname'),
                //  DB::raw('IFNULL(u.img,"") as img'),
                // DB::raw('IFNULL(u.id,"") as id')
                'u.id',
                'u.nickname',
                'u.img'

            )
            ->get()->toArray();
        //子用户消费总额
        if ($result[0]['id'] == null) {
            $result = [];
        } else {
            foreach ($result as $keys => $val) {
                $list = OrderModel::where('user_id', $result[$keys]['id'])->whereIn('status', [1, 5])
                    ->select(
                        'type',
                        DB::raw('IFNULL(total,0) as order_amount'),
                        'status'

                    )->get()->toArray();
                $result[$keys]['order_amount'] = 0;
                foreach ($list as $key => $val) {
                    $result[$keys]['order_amount'] += $list[$key]['order_amount'];
                    $result[$keys]['count_num'] = $key + 1;
                }
            }
            //排序  从大到小
            $length = count($result);
            for ($n = 0; $n < $length - 1; $n++) {
                for ($i = 0; $i < $length - $n - 1; $i++) {
                    if ($result[$i]['order_amount'] < $result[$i + 1]['order_amount']) {
                        $temp = $result[$i + 1]['order_amount'];
                        $result[$i + 1]['order_amount'] = $result[$i]['order_amount'];
                        $result[$i]['order_amount'] = $temp;
                    }
                }
            }
        }

        //最近消费列表
        $list = UsersModel::leftjoin('users as u', 'u.invite_parent_id', '=', 'users.id')
            ->leftjoin('order as o', 'o.user_id', '=', 'u.id')
            ->where('users.id', $id)
        //->where('dmr.type',1)
            ->whereIn('o.status', [1, 5])
            ->orderby('o.created_at', 'desc')
            ->select(
                DB::raw('IFNULL(u.nickname,"") as nickname'),
                'u.id',
                DB::raw('IFNULL(u.img,"") as img'),
                DB::raw('IFNULL(o.total,"") as order_amount'),
                DB::raw('IFNULL(o.order_num,"") as order_num'),
                DB::raw('date_format(o.created_at,"%Y/%m/%d") as date')
            )
            ->get()->toArray();
        $list = array_values($list);
        $data['hight'] = $result;
        $data['near'] = $list;
        return $this->success($data);
    }

    /*
     *奖金记录
     */
    public function bonus_record(Request $request)
    {
        $id = $this->auth->user_id;
        $list = UsersModel::leftjoin('doc_money_record as dmr', 'dmr.user_id', '=', 'users.id')
            ->where('users.id', $id)
            ->where('dmr.status', '>', -1)
            ->whereIn('type', [1, 3])
            ->orderby('dmr.id', 'desc')
            ->select(
                DB::raw('from_unixtime(dmr.created_at,"%Y-%m-%d %H:%i") as date'),
                DB::raw('if(dmr.type=1,dmr.money,-abs(dmr.money)) as amount'),
                'dmr.id',
                'order_num',
                DB::raw('if(dmr.type=1,"得到奖金","提现奖金") as description')
            )
            ->paginate(13);
        //$list = array_values($list);
        return $this->success($list);
    }

    /*
     *记录详细
     *Request id(流水ID)
     */
    public function record_detail(Request $request)
    {
        $record_id = $request->input('id');
        if (empty($record_id)) {
            return $this->error('缺少参数');
        }

        $id = $this->auth->user_id;

        $record = DoctorMoneyRecordModel::where('id', $record_id)->select('type', 'user_id')->first();
        if (empty($record)) {
            return $this->error('没有相关记录');
        } else {
            $record = $record->toArray();
        }

        if ($id !== $record['user_id']) {
            return $this->error('无访问权限');
        }

        if ($record['type'] == 1) { //奖金

            $result = DoctorMoneyRecordModel::where('id', $record_id)
                ->select(
                    //DB::raw( 'count(id) as count'),
                    DB::raw('IFNULL(record_id,"") as record_id'),
                    DB::raw('if(status=1,"成功","待消费") as status'),
                    DB::raw('IFNULL(source,"") as source'),
                    DB::raw('IFNULL(`desc`,"") as description'),
                    DB::raw('IFNULL(money,0) as money'),
                    DB::raw('IFNULL(order_amount,0) as order_amount'),
                    DB::raw('from_unixtime(created_at,"%Y-%m-%d %H:%i") as date')
                )
                ->first()->toArray();

            $result['title'] = "奖金金额";
            $content[0]['title'] = "单号";
            $content[0]['value'] = $result['record_id'];
            $content[1]['title'] = "状态";
            $content[1]['value'] = $result['status'];
            $content[2]['title'] = "来源";
            $content[2]['value'] = $result['source'];
            $content[3]['title'] = "备注";
            $content[3]['value'] = $result['description'];
            $content[4]['title'] = "用户消费金额";
            $content[4]['value'] = $result['order_amount'];
            $content[5]['title'] = "转入时间";
            $content[5]['value'] = $result['date'];

        // $result['content'] = [{ "title": "单号", "value": $result['record_id'] }, { "title": "状态", "value": $result['status'] }, { "title": "来源", "value": $result['source'] }];
        } elseif ($record['type'] == 3) { //提现

            $result = DoctorMoneyRecordModel::leftjoin('users as u', 'u.id', '=', 'doc_money_record.user_id')
                ->leftjoin('doc_bank_card as dbc', 'dbc.card_num', '=', 'doc_money_record.card_num')
                ->where('doc_money_record.id', $record_id)
                ->select(
                    DB::raw('IFNULL(doc_money_record.order_amount,0) as order_amount'),
                    DB::raw('IFNULL(doc_money_record.money,0) as money'),
                    DB::raw('IFNULL(doc_money_record.record_id,0) as record_id'),

                    DB::raw('IFNULL(if(doc_money_record.status=1,"提现成功","待审核"),0) as status'),
                    DB::raw('IFNULL(doc_money_record.card_num,"") as card_num'),
                    DB::raw('IFNULL(dbc.card_type,"") as card_type'),
                    DB::raw('IFNULL(dbc.bank_name,"") as bank_name'),
                    DB::raw('IFNULL(from_unixtime(doc_money_record.created_at,"%Y-%m-%d %H:%i"),"") as date')
                )
                ->first()->toArray();
            $result['title'] = "提现金额";
            $content[0]['title'] = "单号";
            $content[0]['value'] = $result['record_id'];
            $content[1]['title'] = "状态";
            $content[1]['value'] = $result['status'];
            $content[2]['title'] = "卡号";
            $content[2]['value'] = $result['card_num'];
            $content[3]['title'] = "卡类型";
            $content[3]['value'] = $result['bank_name'];
            $content[4]['title'] = "申请时间";
            $content[4]['value'] = $result['date'];
        } else {
            return $this->error('状态非法！');
        }
        $balance = UsersModel::leftjoin('doc_money_record as dmr', 'dmr.user_id', '=', 'users.id')
            ->where('dmr.status', '>', -1)
            ->where('users.id', $id)
            ->select(
                DB::raw('sum(if(dmr.type=1,if(dmr.status=1,dmr.money,0),0))+sum(if(dmr.type=3,-abs(dmr.money),0)) as money') /*可提现金额*/
            )
            ->first()->toArray();
        $result['balance'] = $balance['money'];
        $result['type'] = $record['type'];
        $result['content'] = $content;

        return $this->success($result);
    }
}

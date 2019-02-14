<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\PHPTree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{

    /**
     * 后台首页
     */
    public function index()
    {
        $menus = $this->getMenus($this->roleIds);
        if ($menus) {
            $menus = PHPTree::makeTree($menus);
        } else {
            $menus = [];
        }

        return view('business.index', [
            'username' => session('username'),
            'id' => session('id'),
            'leftMenus' => $menus,
        ]);
    }

    /**
     * 后台首页概览
     */
    public function overview()
    {
        // 获取系统消息
        $messages = $this->getMessages();   // 未读消息
        // 获取昨天今天访客
        $visitors = $this->getRecentVisitors();
        // 获取昨日今日消费人数
        $consumers = $this->getRecentConsumers();
        // 全部套餐（包括非会员）今日、昨日收益
        $packageIncome = $this->getPackageIncome();       // 今日非会员套餐收益金额
        $memberIncome = $this->getMemberIncome();         // 今日会员套餐收益金额
        $income = [
            'package' => $packageIncome,
            'member' => $memberIncome
        ];
        // 优惠券领取张数
        $ticketCount = $this->ticketGetCount();
        // 访客总数
        $allVisitors = DB::table('store_visit_log')->whereIn('store_id', $this->storeIds)->distinct()->count('userid');
        // 本月（自然月）订单数 和 销售额
        $orderSum = DB::table('order')->where('from', 1)->whereIn('type', [2, 3])->where('status', 2)
            ->whereIn('store_id', $this->storeIds)
            ->whereBetween('convert_time', [strtotime(date('Y-m-01')), strtotime(date('Y-m-01 23:59:59') . ' + 1 month - 1 day')])
            ->select([
                DB::raw('ROUND(SUM(pay_price),2) AS amount'),
                DB::raw('COUNT(id) AS quantity'),
            ])->first();

        $sum = [
            'allVisitorCount' => $allVisitors,
            'amount' => $orderSum->amount,
            'quantity' => $orderSum->quantity,
        ];

        return view('business.overview', [
            'messageCount' => count($messages),
            'visitors' => $visitors,
            'consumers' => $consumers,
            'sum' => $sum,
            'income' => $income,
            'ticketCount' => $ticketCount,
            'messages' => $messages
        ]);
    }

    /**
     * 获取首页图标数据
     * @return \Illuminate\Http\JsonResponse
     */
    public function getChartData()
    {
        $list = DB::table('order')
            ->whereBetween('convert_time', [strtotime(date('Y-m-01')), strtotime(date('Y-m-01 23:59:59') . ' + 1 month -1 day')])
            ->whereIn('store_id', $this->storeIds)
            ->whereIn('type', [2, 3])
            ->where('from', 1)
            ->where('status', 2)
            ->select([
                DB::raw("DATE_FORMAT(FROM_UNIXTIME(convert_time), '%Y-%m-%d') AS `date`"),
                DB::raw('ROUND(SUM(pay_price),2) AS amount'),
                DB::raw('COUNT(id) AS quantity'),
            ])
            ->groupBy(DB::raw("DATE_FORMAT(FROM_UNIXTIME(convert_time), '%Y-%m-%d')"))
            ->get();

        return $this->response(200, '获取成功', '', $list);

    }

    /**
     * 获取系统消息
     * @return mixed
     */
    private function getMessages()
    {
        $msgs = DB::table('bus_user_message as bum')->where('bum.bus_user_id', session('id'))
            ->where('bum.read_status', 0)
            ->leftJoin('bus_message as bm', 'bm.id', '=', 'bum.message_id')
            ->select('bum.id', 'bm.title', 'bum.read_status', 'bum.addtime as receive_time')
            ->get();

        return $msgs;
    }

    /**
     * 获取今日非会员套餐收益金额
     * @return array
     */
    private function getPackageIncome()
    {
        if (!$this->storeIds) {
            $income = [
                'yesterday' => 0,
                'today' => 0
            ];
        } else {
            $today = DB::table('order as o')
                ->whereIn('o.store_id', $this->storeIds)
                ->where('o.type', 2)
                ->where('o.status', 2)
                ->where('o.from', 1)
                ->whereBetween('o.convert_time', [strtotime(date('Y-m-d')), strtotime(date('Y-m-d 23:59:59'))])
                ->sum('o.pay_price');
            $yesterday = DB::table('order as o')
                ->whereIn('o.store_id', $this->storeIds)
                ->where('o.type', 2)
                ->where('o.status', 2)
                ->where('o.from', 1)
                ->whereBetween('o.convert_time', [strtotime(date('Y-m-d') . ' -1 day'), strtotime(date('Y-m-d 23:59:59') . ' -1 day')])
                ->sum('o.pay_price');
            $income = [
                'today' => round($today, 2),
                'yesterday' => round($yesterday, 2)
            ];
        }
        return $income;
    }

    /**
     * 获取今日会员卡套餐收益金额
     * @return array
     */
    private function getMemberIncome()
    {
        if (!$this->storeIds) {
            $income = [
                'today' => 0,
                'yesterday' => 0
            ];
        } else {
            $today = DB::table('order as o')
                ->whereIn('o.store_id', $this->storeIds)
                ->where('o.type', 3)
                ->where('o.status', 2)
                ->where('o.from', 1)
                ->whereBetween('o.convert_time', [strtotime(date('Y-m-d')), strtotime(date('Y-m-d 23:59:59'))])
                ->sum('o.pay_price');
            $yesterday = DB::table('order as o')
                ->whereIn('o.store_id', $this->storeIds)
                ->where('o.type', 3)
                ->where('o.status', 2)
                ->where('o.from', 1)
                ->whereBetween('o.convert_time', [strtotime(date('Y-m-d') . ' -1 day'), strtotime(date('Y-m-d 23:59:59') . ' -1 day')])
                ->sum('o.pay_price');
            $income = [
                'today' => round($today, 2),
                'yesterday' => round($yesterday, 2)
            ];
        }
        return $income;
    }

    /**
     * 获取最近访客
     */
    private function getRecentVisitors()
    {
        // 今天新旧访客数
        if (!$this->storeIds) {
            $today = [
                'new' => 0,
                'old' => 0,
                'total' => 0,
            ];
        } else {
            $todayNew = '
            SELECT
            COUNT(DISTINCT userid) AS new_count
            FROM store_visit_log
            WHERE userid IN(
                SELECT userid
                FROM(
                    SELECT userid,COUNT(id) AS visit_count 
                    FROM store_visit_log 
                    WHERE userid > 0 AND store_id IN (' . implode(',', $this->storeIds) . ')
                    GROUP BY userid
                    ORDER BY visit_count 
                ) AS tmp
                WHERE tmp.visit_count = 1
            ) 
            AND store_id IN (' . implode(',', $this->storeIds) . ')
            AND last_visit_time BETWEEN ' . strtotime(date('Y-m-d')) . ' AND ' . strtotime(date('Y-m-d 23:59:59'));

            $todayOld = '
            SELECT
            COUNT(DISTINCT userid) AS old_count
            FROM store_visit_log
            WHERE userid IN(
                SELECT userid
                FROM(
                    SELECT userid,COUNT(id) AS visit_count 
                    FROM store_visit_log 
                    WHERE userid > 0 AND store_id IN (' . implode(',', $this->storeIds) . ')
                    GROUP BY userid
                    ORDER BY visit_count 
                ) AS tmp
                WHERE tmp.visit_count > 1
             )
            AND store_id IN (' . implode(',', $this->storeIds) . ')
            AND last_visit_time BETWEEN ' . strtotime(date('Y-m-d')) . ' AND ' . strtotime(date('Y-m-d 23:59:59'));

            $todayNewVisitors = DB::select($todayNew);
            $todayOldVisitors = DB::select($todayOld);
            $today = [
                'new' => $todayNewVisitors[0]->new_count,
                'old' => $todayOldVisitors[0]->old_count,
                'total' => $todayNewVisitors[0]->new_count + $todayOldVisitors[0]->old_count
            ];
        }

        // 昨天新旧访客
        if (!$this->storeIds) {
            $yesterday = [
                'new' => 0,
                'old' => 0,
                'total' => 0,
            ];
        } else {
            $yesterdayNew = '
            SELECT
            COUNT(DISTINCT userid) AS new_count
            FROM store_visit_log
            WHERE userid IN(
                SELECT userid
                FROM(
                    SELECT userid,COUNT(id) AS visit_count 
                    FROM store_visit_log 
                    WHERE userid > 0 AND store_id IN (' . implode(',', $this->storeIds) . ')
                    GROUP BY userid
                    ORDER BY visit_count 
                ) AS tmp
                WHERE tmp.visit_count = 1
            )
            AND store_id IN (' . implode(',', $this->storeIds) . ')
            AND last_visit_time BETWEEN ' . strtotime(date('Y-m-d') . ' -1 day') . ' AND ' . strtotime(date('Y-m-d 23:59:59') . ' -1 day');

            $yesterdayOld = '
            SELECT
            COUNT(DISTINCT userid) AS old_count
            FROM store_visit_log
            WHERE userid IN(
                SELECT userid
                FROM(
                    SELECT userid,COUNT(id) AS visit_count 
                    FROM store_visit_log 
                    WHERE userid > 0 AND store_id IN (' . implode(',', $this->storeIds) . ')
                    GROUP BY userid
                    ORDER BY visit_count 
                ) AS tmp
                WHERE tmp.visit_count > 1
            ) 
            AND store_id IN (' . implode(',', $this->storeIds) . ')
            AND last_visit_time BETWEEN ' . strtotime(date('Y-m-d') . ' -1 day') . ' AND ' . strtotime(date('Y-m-d 23:59:59') . ' -1 day');

            $yesterdayNewVisitors = DB::select($yesterdayNew);
            $yesterdayOldVisitors = DB::select($yesterdayOld);
            $yesterday = [
                'new' => $yesterdayNewVisitors[0]->new_count,
                'old' => $yesterdayOldVisitors[0]->old_count,
                'total' => $yesterdayNewVisitors[0]->new_count + $yesterdayOldVisitors[0]->old_count
            ];
        }

        $visitors = [
            'today' => $today,
            'yesterday' => $yesterday
        ];

        return $visitors;
    }

    /**
     * 获取昨日今日消费人数
     * @return object
     */
    private function getRecentConsumers()
    {
        if (!$this->storeIds) {
            $obj = new \stdClass();
            $obj->yesterday = 0;
            $obj->today = 0;
            return $obj;
        }
        $res = DB::table('order')
            ->whereIn('store_id', $this->storeIds)
            ->where('from', 1)
            ->whereIn('type', [2, 3])
            ->where('pay_date', '>', 0)
            ->select([
                DB::raw('COUNT(DISTINCT IF(pay_date >= ' . strtotime(date('Y-m-d')) . ' AND pay_date <= ' . strtotime(date('Y-m-d 23:59:59')) . ', userid, null)) AS today'),
                DB::raw('COUNT(DISTINCT IF(pay_date >= ' . strtotime(date('Y-m-d') . ' -1 day') . ' AND pay_date <= ' . strtotime(date('Y-m-d 23:59:59') . ' -1 day') . ', userid, null)) AS yesterday'),
            ])
            ->first();
        return $res;
    }

    /**
     * 获取优惠券券领取数
     * @return array
     */
    private function ticketGetCount()
    {
        if (!$this->storeIds) {
            $count = [
                'today' => 0,
                'yesterday' => 0
            ];
        } else {
            $today = DB::table('ticket_get_record as tgr')
                ->join('ticket as t', function ($join) {
                    $join->on('t.id', '=', 'tgr.ticket_id')->where('t.type', '=', 2);
                })
                ->join('ticket_extend as te', function ($join) {
                    $join->on('te.ticket_id', '=', 't.id')->whereIn('te.store_id', $this->storeIds);
                })
                ->whereBetween('tgr.addtime', [strtotime(date('Y-m-d')), strtotime(date('Y-m-d 23:59:59'))])
                ->count('tgr.id');

            $yesterday = DB::table('ticket_get_record as tgr')
                ->join('ticket as t', function ($join) {
                    $join->on('t.id', '=', 'tgr.ticket_id')->where('t.type', '=', 2);
                })
                ->join('ticket_extend as te', function ($join) {
                    $join->on('te.ticket_id', '=', 't.id')->whereIn('te.store_id', $this->storeIds);
                })
                ->whereBetween('tgr.addtime', [strtotime(date('Y-m-d') . ' -1 day'), strtotime(date('Y-m-d 23:59:59') . ' -1 day')])
                ->count('tgr.id');
            $count = [
                'today' => $today,
                'yesterday' => $yesterday
            ];
        }
        return $count;
    }

    /**
     * 下载商户端APP
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function downloadBusApp()
    {
        return view('business.download-bus-app');
    }

}

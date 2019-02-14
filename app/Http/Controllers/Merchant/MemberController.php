<?php

namespace App\Http\Controllers\Merchant;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class MemberController extends Controller
{

    /**
     * 会员管理 - old
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index_old(Request $request)
    {
        $sids = implode(',', $this->storeIds);
        $fields = [
            'u.id', 'u.mobile', 'r.full_name as region', 'sr.score', 'c.coin',
            DB::raw('(SELECT convert_time FROM `order` WHERE userid = u.id AND store_id IN(' . $sids . ') ORDER BY convert_time DESC LIMIT 1) AS last_consume_time'),
            'ut.update_date as last_login_time',
            't.total_consume_amount',
            't.total_consume_count',
            'u.from',
            DB::raw('IF(mc.userid IS NOT NULL,1,0) as is_bind_membership_card'),
            'u.status',
        ];

        $data = $request->only(['price_start', 'price_end', 'from', 'count_start', 'count_end',
            'card_bind', 'account', 'convert_start', 'convert_end'
        ]);

        if (!empty($this->storeIds)) {
            // 分步走：1、先筛选出有在所属门店消费的用户ID ; 2、再统计该些用户的相关数据
            $uids = DB::table('order as o')->whereIn('store_id', $this->storeIds)
                ->whereIn('o.type', [2, 3])->leftJoin(config('tables.base') . '.users as u', 'u.id', '=', 'o.userid')
                ->distinct()->lists('userid');

            if (!$data['convert_start'] && !$data['convert_end']) {
                $joinSql = "
                (SELECT
                    userid,
                    SUM(pay_price) AS total_consume_amount,
                    COUNT(userid) AS total_consume_count
                FROM
                    `order`
                WHERE TYPE IN (2, 3)
                    AND `from` = 1
                    AND `status` = 2
                    AND store_id IN (" . $sids . ")
                GROUP BY userid) AS t";
            } elseif ($data['convert_start'] && !$data['convert_end']) {
                $joinSql = "
                (SELECT
                    userid,
                    SUM(pay_price) AS total_consume_amount,
                    COUNT(userid) AS total_consume_count
                FROM
                    `order`
                WHERE TYPE IN (2, 3)
                    AND `from` = 1
                    AND `status` = 2
                    AND store_id IN (" . $sids . ")
                    AND convert_time >= " . strtotime($data['convert_start']) . "
                GROUP BY userid) AS t";
            } elseif (!$data['convert_start'] && $data['convert_end']) {
                $joinSql = "
                (SELECT
                    userid,
                    SUM(pay_price) AS total_consume_amount,
                    COUNT(userid) AS total_consume_count
                FROM
                    `order`
                WHERE TYPE IN (2, 3)
                    AND `from` = 1
                    AND `status` = 2
                    AND store_id IN (" . $sids . ")
                    AND convert_time <= " . strtotime($data['convert_end']) . "
                GROUP BY userid) AS t";
            } elseif ($data['convert_start'] && $data['convert_end']) {
                $joinSql = "
                (SELECT
                    userid,
                    SUM(pay_price) AS total_consume_amount,
                    COUNT(userid) AS total_consume_count
                FROM
                    `order`
                WHERE TYPE IN (2, 3)
                    AND `from` = 1
                    AND `status` = 2
                    AND store_id IN (" . $sids . ")
                    AND convert_time BETWEEN " . strtotime($data['convert_start']) . " AND " . strtotime($data['convert_end']) . "
                GROUP BY userid) AS t";
            }

            $builder = DB::table(config('tables.base') . '.users as u')->whereIn('u.id', $uids)
                ->leftJoin(config('tables.base') . '.region as r', 'r.id', '=', 'u.area_id')
                ->leftJoin('base.score_record as sr', 'sr.userid', '=', 'u.id')
                ->leftJoin('coins as c', 'c.userid', '=', 'u.id')
                ->leftJoin('user_token as ut', 'ut.userid', '=', 'u.id')
                ->leftJoin('membership_card as mc', 'mc.userid', '=', 'u.id')
                ->join(DB::raw($joinSql), 't.userid', '=', 'u.id');

            /* 过滤条件 */

            // 累计消费金额
            if ($data['price_start'] && !$data['price_end']) {
                $builder->where('total_consume_amount', '>=', intval($data['price_start']));
            } elseif (!$data['price_start'] && $data['price_end']) {
                $builder->where('total_consume_amount', '<=', intval($data['price_end']));
            } elseif ($data['price_start'] && $data['price_end']) {
                $builder->whereBetween('total_consume_amount', [intval($data['price_start']), intval($data['price_end'])]);
            }

            // 累计消费次数
            if ($data['count_start'] && !$data['count_end']) {
                $builder->where('total_consume_count', '>=', intval($data['count_start']));
            } elseif (!$data['count_start'] && $data['count_end']) {
                $builder->where('total_consume_count', '<=', intval($data['count_end']));
            } elseif ($data['count_start'] && $data['count_end']) {
                $builder->whereBetween('total_consume_count', [intval($data['count_start']), intval($data['count_end'])]);
            }

            // 会员来源
            if ($data['from']) {
                $builder->where('u.from', $data['from']);
            }
            // 账号筛选
            if ($data['account']) {
                $builder->where('u.mobile', 'like', '%' . $data['account'] . '%');
            }
            // 是否绑定会员卡
            if ($data['card_bind'] == 1) {
                $builder->where('mc.userid', '>', 0);
            } elseif ($data['card_bind'] == 2) {
                $builder->whereNull('mc.userid');
            }

            // 时间段筛选
            /*
            if (!$data['convert_start'] && !$data['convert_end']) {
                $builder->leftJoin('order as o', function ($join) {
                    $join->on('o.userid', '=', 'u.id')
                        ->whereIn('o.store_id', $this->storeIds)
                        ->whereIn('o.type', [2, 3])
                        ->where('o.from', '=', 1);
                });
            } elseif ($data['convert_start'] && !$data['convert_end']) {
                $builder->leftJoin('order as o', function ($join) use ($data) {
                    $join->on('o.userid', '=', 'u.id')
                        ->whereIn('o.store_id', $this->storeIds)
                        ->whereIn('o.type', [2, 3])
                        ->where('o.convert_time', '>=', strtotime($data['convert_start']))
                        ->where('o.from', '=', 1);
                });
            } elseif (!$data['convert_start'] && $data['convert_end']) {
                $builder->leftJoin('order as o', function ($join) use ($data) {
                    $join->on('o.userid', '=', 'u.id')
                        ->whereIn('o.store_id', $this->storeIds)
                        ->whereIn('o.type', [2, 3])
                        ->where('o.convert_time', '<=', strtotime($data['convert_end']))
                        ->where('o.from', '=', 1);
                });
            } elseif ($data['convert_start'] && $data['convert_end']) {
                $builder->leftJoin('order as o', function ($join) use ($data) {
                    $join->on('o.userid', '=', 'u.id')
                        ->whereIn('o.store_id', $this->storeIds)
                        ->whereIn('o.type', [2, 3])
                        ->where('o.convert_time', '>=', strtotime($data['convert_start']))
                        ->where('o.convert_time', '<=', strtotime($data['convert_end']))
                        ->where('o.from', '=', 1);
                });
            }
            */

            $count = $builder->distinct()->count('u.id');
            $members = $builder->select($fields)->groupBy('u.id')->orderBy('u.id', 'desc')->paginate(20);

        } else {
            $count = 0;
            $members = null;
        }

        return view('business.member-management', [
            'params' => $data,
            'count' => $count,
            'members' => $members,
        ]);
    }

    /**
     * 会员管理
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        // 查询参数列表
        $data = $request->only([
            'price_start', 'price_end', 'from', 'count_start', 'count_end',
            'card_bind', 'account', 'convert_start', 'convert_end'
        ]);

        if ($this->storeIds) {
            // 所有门店ID列表
            $sids = implode(',', $this->storeIds);
            $limit = 20;

            if ($data['convert_start'] && !$data['convert_end']) {
                $joinSql = '
            (SELECT
                userid,
                SUM(pay_price) AS total_consume_amount,
                COUNT(userid) AS total_consume_count,
                ROUND(SUM(pay_price)/COUNT(userid),2) as average_consume_price
            FROM
                `order`
            WHERE TYPE IN (2, 3)
                AND `from` = 1
                AND `status` = 2
                AND store_id IN (' . $sids . ')
                AND convert_time >= ' . strtotime($data['convert_start']) . '
            GROUP BY userid) AS tmp
            ';
            } elseif (!$data['convert_start'] && $data['convert_end']) {
                $joinSql = '
            (SELECT
                userid,
                SUM(pay_price) AS total_consume_amount,
                COUNT(userid) AS total_consume_count,
                ROUND(SUM(pay_price)/COUNT(userid),2) as average_consume_price
            FROM
                `order`
            WHERE TYPE IN (2, 3)
                AND `from` = 1
                AND `status` = 2
                AND store_id IN (' . $sids . ')
                AND convert_time <= ' . strtotime($data['convert_end']) . '
            GROUP BY userid) AS tmp
            ';
            } elseif ($data['convert_start'] && $data['convert_end']) {
                $joinSql = '
            (SELECT
                userid,
                SUM(pay_price) AS total_consume_amount,
                COUNT(userid) AS total_consume_count,
                ROUND(SUM(pay_price)/COUNT(userid),2) as average_consume_price
            FROM
                `order`
            WHERE TYPE IN (2, 3)
                AND `from` = 1
                AND `status` = 2
                AND store_id IN (' . $sids . ')
                AND convert_time BETWEEN ' . strtotime($data['convert_start']) . ' AND ' . strtotime($data['convert_end']) . '
            GROUP BY userid) AS tmp
            ';
            } else {
                $joinSql = '
            (SELECT
                userid,
                SUM(pay_price) AS total_consume_amount,
                COUNT(userid) AS total_consume_count,
                ROUND(SUM(pay_price)/COUNT(userid),2) as average_consume_price
            FROM
                `order`
            WHERE TYPE IN (2, 3)
                AND `from` = 1
                AND `status` = 2
                AND store_id IN (' . $sids . ')
            GROUP BY userid) AS tmp
            ';
            }

            $fields = [
                'u.id', 'u.mobile', 'r.full_name AS region_name', 'sr.score', 'c.coin',
                DB::raw('(SELECT FROM_UNIXTIME(convert_time) FROM `order` WHERE userid = u.id AND store_id IN(' . $sids . ') ORDER BY convert_time DESC LIMIT 1) AS last_consume_time'),
                DB::raw('FROM_UNIXTIME(ut.update_date) AS last_login_time'),
                'u.from',
                'tmp.total_consume_amount',
                'tmp.total_consume_count',
                'tmp.average_consume_price',
                DB::raw('IF(mc.userid IS NOT NULL,1,0) as card_bind_status'),
                DB::raw('u.`status`'),
            ];

            $userJoin = "
                (
                SELECT DISTINCT userid 
                FROM `order` AS o 
                WHERE o.store_id IN(".implode(',',$this->storeIds).") AND o.type IN(2,3)
                ) AS fu
            ";

            $utJoin = "(SELECT * FROM (SELECT userid, update_date FROM user_token AS ut ORDER BY update_date DESC ) AS sorted GROUP BY sorted.userid) AS ut";

            $builder = DB::table(config('tables.base') . '.users as u')
                ->join(DB::raw($userJoin),'fu.userid','=','u.id')
                ->leftJoin(config('tables.base') . '.region as r', 'r.id', '=', 'u.area_id')
                ->leftJoin(config('tables.base') . '.score_record as sr', 'sr.userid', '=', 'u.id')
                ->leftJoin('coins as c', 'c.userid', '=', 'u.id')
                ->leftJoin(DB::raw($utJoin), 'ut.userid', '=', 'u.id')
                ->leftJoin(DB::raw($joinSql), 'tmp.userid', '=', 'u.id')
                ->leftJoin('membership_card as mc', 'mc.userid', '=', 'u.id');

            // 累计消费金额
            if ($data['price_start'] && !$data['price_end']) {
                $builder->where('total_consume_amount', '>=', floatval($data['price_start']));
            } elseif (!$data['price_start'] && $data['price_end']) {
                $builder->where('total_consume_amount', '<=', floatval($data['price_end']));
            } elseif ($data['price_start'] && $data['price_end']) {
                $builder->whereBetween('total_consume_amount', [floatval($data['price_start']), floatval($data['price_end'])]);
            }

            // 累计消费次数
            if ($data['count_start'] && !$data['count_end']) {
                $builder->where('total_consume_count', '>=', intval($data['count_start']));
            } elseif (!$data['count_start'] && $data['count_end']) {
                $builder->where('total_consume_count', '<=', intval($data['count_end']));
            } elseif ($data['count_start'] && $data['count_end']) {
                $builder->whereBetween('total_consume_count', [intval($data['count_start']), intval($data['count_end'])]);
            }

            // 会员来源
            if ($data['from']) {
                $builder->where('u.from', $data['from']);
            }
            // 账号筛选
            if ($data['account']) {
                $builder->where('u.mobile', 'like', '%' . $data['account'] . '%');
            }
            // 是否绑定会员卡
            if ($data['card_bind'] == 1) {
                $builder->whereNotNull('mc.userid');
            } elseif ($data['card_bind'] == 2) {
                $builder->whereNull('mc.userid');
            }

            $count = $builder->distinct()->count('u.id');
            $res = $builder->select($fields)->groupBy('u.id')
                ->orderBy('u.id', 'desc')
                ->paginate($limit);
        } else {
            $count = 0;
            $res = null;
        }

        return view('business.member-management', [
            'params' => $data,
            'count' => $count,
            'members' => $res,
        ]);

    }

    /**
     * 会员分析
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function analysis(Request $request)
    {
        if ($request->ajax()) {
            if ($this->storeIds) {
                $date1 = $request->has('date1') ? strtotime($request->get('date1')) : strtotime(date('Y-m-01 00:00:00'));
                $date2 = $request->has('date2') ? strtotime($request->get('date2')) : strtotime(date('Y-m-d H:i:s'));
                $sids = implode(',', $this->storeIds);

                // 每日访客统计
                $users = DB::table('store_visit_log')
                    ->where('userid', '>', 0)
                    ->whereIn('store_id', $this->storeIds)
                    ->whereBetween('last_visit_time', [$date1, $date2])
                    ->select([
                        DB::raw("FROM_UNIXTIME(last_visit_time,'%Y-%m-%d') as date"),
                        DB::raw('COUNT(DISTINCT userid) as amount')
                    ])
                    ->groupBy(DB::raw("FROM_UNIXTIME(last_visit_time,'%Y-%m-%d')"))->get();

                // 消费次数统计
                $countSql = "
                    SELECT t.cnt AS consume_count,COUNT(userid) AS people_count
                    FROM (
                        SELECT userid,
                              COUNT(1) cnt
                        FROM `order`
                        WHERE TYPE IN (2, 3) AND ADDTIME BETWEEN " . $date1 . " AND " . $date2 . " AND store_id IN (" . $sids . ")
                        GROUP BY userid
                     ) t
                    GROUP BY t.cnt
                    ORDER BY cnt
                ";
                $counts = DB::select($countSql);
                $counter = ['1次' => 0, '2至5次' => 0, '6至10次' => 0, '10次以上' => 0];
                foreach ($counts as $count) {
                    if ($count->consume_count == 1) {
                        $counter['1次'] += $count->people_count;
                    } elseif ($count->consume_count > 1 && $count->consume_count <= 5) {
                        $counter['2至5次'] += $count->people_count;
                    } elseif ($count->consume_count > 5 && $count->consume_count <= 10) {
                        $counter['6至10次'] += $count->people_count;
                    } elseif ($count->consume_count > 10) {
                        $counter['10次以上'] += $count->people_count;
                    }
                }

                // 消费金额统计
                $sumSql = "SELECT
                                o.consume_sum,
                                COUNT(o.userid) AS people_count
                            FROM
                                (
                                    SELECT
                                        userid,
                                        ROUND(SUM(pay_price),2) AS consume_sum
                                    FROM
                                        `order`
                                    WHERE
                                        type IN (2, 3) AND addtime BETWEEN " . $date1 . " AND " . $date2 . "
                                        AND `status` = 2
                                        AND store_id IN(" . $sids . ")
                                    GROUP BY
                                        userid
                                ) AS o
                            GROUP BY o.consume_sum";
                $sums = DB::select($sumSql);
                $money = ['0' => 0, '0-50' => 0, '50-100' => 0, '100 以上' => 0];
                foreach ($sums as $sum) {
                    if ($sum->consume_sum == 0) {
                        $money['0'] += $sum->people_count;
                    } elseif ($sum->consume_sum > 0 && $sum->consume_sum <= 50) {
                        $money['0-50'] += $sum->people_count;
                    } elseif ($sum->consume_sum > 50 && $sum->consume_sum <= 100) {
                        $money['50-100'] += $sum->people_count;
                    } elseif ($sum->consume_sum > 100) {
                        $money['100 以上'] += $sum->people_count;
                    }
                }

                $data = [
                    'users' => $users,
                    'counts' => $counter,
                    'sums' => $money
                ];
                return response()->json($data);

            } else {
                $data = [
                    'users' => [],
                    'counts' => [],
                    'sums' => []
                ];
                return response()->json($data);
            }
        } else {
            return view('business.member-analysis');
        }
    }

}

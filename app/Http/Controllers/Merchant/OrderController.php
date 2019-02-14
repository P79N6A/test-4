<?php

namespace App\Http\Controllers\Merchant;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    /**
     * 订单管理
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $data = $request->only([
            'addtime_start', 'addtime_end', 'convert_start', 'convert_end',
            'price_start', 'price_end', 'account', 'store', 'package', 'order_no',
            'status', 'pay_type'
        ]);

        if ($this->storeIds) {
            $limit = 20;

            $fields = [
                'o.id', 'o.order_id', 'u.mobile', 's.name as store_name', 'o.price', 'o.pay_price',
                'p.name as package_name', 'o.status', 'o.payment_type',
                'o.pay_no', 'o.addtime', 'o.pay_date', 'o.convert_time'
            ];

            $buider = DB::table('order as o')->whereIn('o.store_id', $this->storeIds)
                ->where('o.type', 2)->orderBy('o.addtime', 'desc')->orderBy('o.id', 'desc')
                ->leftJoin(config('tables.base') . '.users as u', 'u.id', '=', 'o.userid')
                ->leftJoin('bus_stores as s', 's.id', '=', 'o.store_id')
                ->leftJoin('packages as p', 'p.id', '=', 'o.good_id');

            /**
             * 条件筛选
             */
            // 下单时间
            if ($data['addtime_start'] && !$data['addtime_end']) {
                $buider->where('o.addtime', '>=', strtotime($data['addtime_start']));
            } elseif (!$data['addtime_start'] && $data['addtime_end']) {
                $buider->where('o.addtime', '<=', strtotime($data['addtime_end']));
            } elseif ($data['addtime_start'] && $data['addtime_end']) {
                $buider->whereBetween('o.addtime', [strtotime($data['addtime_start']), strtotime($data['addtime_end'])]);
            }
            // 兑换时间
            if ($data['convert_start'] && !$data['convert_end']) {
                $buider->where('o.convert_time', '>=', strtotime($data['convert_start']));
            } elseif (!$data['convert_start'] && $data['convert_end']) {
                $buider->where('o.convert_time', '<=', strtotime($data['convert_end']));
            } elseif ($data['convert_start'] && $data['convert_end']) {
                $buider->whereBetween('o.convert_time', [strtotime($data['convert_start']), strtotime($data['convert_end'])]);
            }
            // 价格范围
            if ($data['price_start'] && !$data['price_end']) {
                $buider->where('o.price', '>=', floatval($data['price_start']));
            } elseif (!$data['price_start'] && $data['price_end']) {
                $buider->where('o.price', '<=', floatval($data['price_end']));
            } elseif ($data['price_start'] && $data['price_end']) {
                $buider->whereBetween('o.price', [floatval($data['price_start']), floatval($data['price_end'])]);
            }
            // 账号搜索
            if (!empty($data['account'])) {
                $buider->where('u.mobile', 'like', '%' . $data['account'] . '%');
            }
            // 门店名称搜索
            if (!empty($data['store'])) {
                $sids = DB::table('bus_stores')
                    ->where('name', 'like', '%' . $data['store'] . '%')
                    ->whereIn('id', $this->storeIds)->lists('id');
                $buider->whereIn('o.store_id', $sids);
            }
            // 套餐名称
            if (!empty($data['package'])) {
                $buider->where('p.name', 'like', '%' . $data['package'] . '%');
            }
            // 订单号
            if (!empty($data['order_no'])) {
                $buider->where('o.order_id', $data['order_no']);
            }
            // 交易状态
            if (intval($data['status'])) {
                $buider->where('o.status', $data['status'] - 1);
            }
            // 支付平台
            if (intval($data['pay_type'])) {
                $buider->where('o.payment_type', $data['pay_type']);
            }

            $buider1 = clone $buider;
            $buider2 = clone $buider;
            $orders = $buider1->select($fields)->paginate($limit);
            $sum = $buider2->sum('o.price');

            return view('business.order-management', [
                'orders' => $orders,
                'sum' => $sum,
                'addtime_start' => $data['addtime_start'] ? $data['addtime_start'] : '',
                'addtime_end' => $data['addtime_end'] ? $data['addtime_end'] : '',
                'convert_start' => $data['convert_start'] ? $data['convert_start'] : '',
                'convert_end' => $data['convert_end'] ? $data['convert_end'] : '',
                'price_start' => $data['price_start'] ? $data['price_start'] : '',
                'price_end' => $data['price_end'] ? $data['price_end'] : '',
                'account' => $data['account'] ? $data['account'] : '',
                'store' => $data['store'] ? $data['store'] : '',
                'package' => $data['package'] ? $data['package'] : '',
                'order_no' => $data['order_no'] ? $data['order_no'] : '',
                'status' => $data['status'] ? $data['status'] : 0,
                'pay_type' => $data['pay_type'] ? $data['pay_type'] : 0,
            ]);
        } else {
            return view('business.order-management', [
                'sum' => !empty($sum) ? $sum : 0,
                'addtime_start' => $data['addtime_start'] ? $data['addtime_start'] : '',
                'addtime_end' => $data['addtime_end'] ? $data['addtime_end'] : '',
                'convert_start' => $data['convert_start'] ? $data['convert_start'] : '',
                'convert_end' => $data['convert_end'] ? $data['convert_end'] : '',
                'price_start' => $data['price_start'] ? $data['price_start'] : '',
                'price_end' => $data['price_end'] ? $data['price_end'] : '',
                'account' => $data['account'] ? $data['account'] : '',
                'store' => $data['store'] ? $data['store'] : '',
                'package' => $data['package'] ? $data['package'] : '',
                'order_no' => $data['order_no'] ? $data['order_no'] : '',
                'status' => $data['status'] ? $data['status'] : 0,
                'pay_type' => $data['pay_type'] ? $data['pay_type'] : 0,
            ]);
        }


    }

    /**
     * 订单分析
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function analysis(Request $request)
    {
        $data = $request->only('start_date', 'end_date');
        if ($request->ajax()) {
            if ($this->storeIds) {
                if (!$data['start_date'] || !$data['end_date']) {
                    $sd = strtotime(date('Y-m-01'));
                    $ed = time();
                } elseif ($data['start_date'] || !$data['end_date']) {
                    $sd = strtotime($data['start_date']);
                    $ed = time();
                } elseif (!$data['start_date'] || $data['end_date']) {
                    $sd = strtotime(date('Y-m-01'));
                    $ed = strtotime($data['Y-m-d H:i:s']);
                } else {
                    $sd = strtotime($data['start_date']);
                    $ed = strtotime(date('Y-m-d 23:59:59', strtotime($data['end_date'])));
                }
                // 统计订单
                $list = DB::table('order')
                    ->whereIn('type', [2, 3])
                    ->where('status', 2)
                    ->where('from', 1)
                    ->whereBetween('addtime', [$sd, $ed])
                    ->whereIn('store_id', $this->storeIds)
                    ->select([
                        DB::raw("FROM_UNIXTIME(ADDTIME, '%Y-%m-%d') AS date"),
                        DB::raw('ROUND(SUM(pay_price),2) AS price_sum'),
                        DB::raw('COUNT(id) AS order_count'),
                        DB::raw('ROUND(SUM(pay_price) / COUNT(id), 2) AS price')
                    ])
                    ->groupBy(DB::raw("FROM_UNIXTIME(ADDTIME, '%Y-%m-%d')"))
                    ->get();

                $sids = implode(',', $this->storeIds);

                // 统计新老用户比例

                /*
                $percentSql = "
                    SELECT
                        statistics1.new_user,
                        statistics2.old_user
                    FROM
                        (
                        SELECT
                            COUNT(DISTINCT o2.userid) AS new_user
                        FROM
                            (SELECT userid, COUNT(id) AS consume_count FROM `order` WHERE store_id IN(" . $sids . ") AND addtime BETWEEN {$sd} AND {$ed} GROUP BY userid) AS o1
                        LEFT JOIN
                            (SELECT userid, COUNT(id) AS consume_count FROM `order` WHERE store_id IN(" . $sids . ") AND addtime BETWEEN {$sd} AND {$ed} GROUP BY userid) AS o2 ON o2.consume_count = o1.consume_count
                        WHERE o1.consume_count = 1
                        GROUP BY
                            o1.consume_count
                        ) AS statistics1,
                        
                        (
                        SELECT
                            COUNT(DISTINCT o2.userid) AS old_user
                        FROM
                            (SELECT userid, COUNT(id) AS consume_count FROM `order` WHERE store_id IN(" . $sids . ") AND  addtime BETWEEN {$sd} AND {$ed} GROUP BY userid) AS o1
                        LEFT JOIN
                            (SELECT userid, COUNT(id) AS consume_count FROM `order` WHERE store_id IN(" . $sids . ") AND  addtime BETWEEN {$sd} AND {$ed} GROUP BY userid) AS o2 ON o2.consume_count = o1.consume_count
                        WHERE o1.consume_count > 1
                        ) AS statistics2
                ";
                //$percent = DB::select($percentSql);
                */

                $newSql = "
                    SELECT
                        new_user
                    FROM
                        (
                        SELECT
                            COUNT(DISTINCT o2.userid) AS new_user
                        FROM
                            (SELECT userid, COUNT(id) AS consume_count FROM `order` WHERE store_id IN(" . $sids . ") AND addtime BETWEEN {$sd} AND {$ed} GROUP BY userid) AS o1
                        LEFT JOIN
                            (SELECT userid, COUNT(id) AS consume_count FROM `order` WHERE store_id IN(" . $sids . ") AND addtime BETWEEN {$sd} AND {$ed} GROUP BY userid) AS o2 ON o2.consume_count = o1.consume_count
                        WHERE o1.consume_count = 1
                        ) AS new
                ";

                $oldSql = "
                    SELECT
                        old_user
                    FROM
                        (
                        SELECT
                            COUNT(DISTINCT o2.userid) AS old_user
                        FROM
                            (SELECT userid, COUNT(id) AS consume_count FROM `order` WHERE store_id IN(" . $sids . ") AND addtime BETWEEN {$sd} AND {$ed} GROUP BY userid) AS o1
                        LEFT JOIN
                            (SELECT userid, COUNT(id) AS consume_count FROM `order` WHERE store_id IN(" . $sids . ") AND addtime BETWEEN {$sd} AND {$ed} GROUP BY userid) AS o2 ON o2.consume_count = o1.consume_count
                        WHERE o1.consume_count > 1
                        ) AS old
                ";

                $new = DB::select($newSql);
                $old = DB::select($oldSql);

                $percent = new \stdClass();
                $percent->new_user = $new[0]->new_user;
                $percent->old_user = $old[0]->old_user;

                // 支付方式构成
                $paymentTypes = DB::table('order')
                    ->whereIn('type', [2, 3])
                    ->whereBetween('addtime', [$sd, $ed])->whereIn('store_id', $this->storeIds)
                    ->select([
                        DB::raw('SUM(IF(payment_type = 1,1,0)) AS alipay'),
                        DB::raw('SUM(IF(payment_type = 2,1,0)) AS wechat'),
                        DB::raw('SUM(IF(payment_type = 3,1,0)) AS wechat_public_account'),
                    ])->first();

                $res['trend'] = $list;
                $res['percent'] = $percent;
                $res['paymentStructure'] = !empty($paymentTypes) ? $paymentTypes : ['alipay' => 0, 'wechat' => 0, 'wechat_public_account' => 0];

                return response()->json($res);
            } else {
                return response()->json([]);
            }

        } else {
            return view('business.order-analysis');
        }

    }

    /**
     * 订单退款
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function deposit(Request $request)
    {
        if ($request->isMethod('get')) {

            $data = $request->only('id');

            if (!$data['id']) {
                return view('business.error', ['code' => 500, 'msg' => '内部错误']);
            }

            $order = DB::table('order as o')->where('o.id', $data['id'])
                ->leftJoin('packages as p', function ($join) {
                    $join->on('p.id', '=', 'o.good_id')->where('p.userid', '=', $this->parentUserId);
                })
                ->select(['o.*', 'p.name', 'p.price'])
                ->first();

            if ($order->status == 0) {
                return view('business.error', ['code' => 403, 'msg' => '该订单处在待付款状态，不能退款']);
            }

            if ($order->status == 2) {
                return view('business.error', ['code' => 403, 'msg' => '已兑换的订单不能退款']);
            }

            if ($order->status >= 5) {
                return view('business.error', ['code' => 403, 'msg' => '该订单已退款，不能重复操作']);
            }

            return view('business.order-deposit', ['order' => $order]);

        } elseif ($request->isMethod('post')) {
            $data = $request->only('id');

            if (!intval($data['id'])) {
                return $this->response(500, '内部错误');
            }

            $order = DB::table('order as o')->where('o.id', $data['id'])
                ->leftJoin('packages as p', function ($join) {
                    $join->on('p.id', '=', 'o.good_id')->where('p.userid', '=', $this->parentUserId);
                })
                ->select('o.*', 'p.name', 'p.price')->first();

            if ($order->status == 0) {
                return $this->response(403, '该订单处在待付款状态，不能退款');
            }

            if ($order->status == 2) {
                return $this->response(403, '已兑换的订单不能退款');
            }

            if ($order->status >= 5) {
                return $this->response(403, '该订单已退款，不能重复操作');
            }

            $client = new Client();
            $url = config('misc.order_deposit_url') . '?orderNo=' . $order->order_id;
            $res = $client->post($url);

            if ($res->getStatusCode() != 200) {
                return $this->response(500, '内部错误，退款失败');

            } else {
                $content = $res->getBody()->getContents();
                $arr = json_decode($content);

                if ($arr->retCode != 0) {
                    return $this->response(500, '内部错误，退款失败');
                }

                return $this->response(200, '退款成功', route('business.order-list'));
            }

        }
    }

    /**
     * 订单核销
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function verify(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('business.order-verify');

        } elseif ($request->isMethod('post')) {
            $convertCode = $request->get('convert_code');
            $orderNo = $request->get('order_no');

            if (empty($convertCode) && empty($orderNo)) {
                return $this->response(403, '请提供兑换码或者订单号');
            }

            // ajax 请求订单信息
            if ($request->has('type') && $request->get('type') == 'getInfo') {
                if (!$this->storeIds) {
                    return $this->response(403, '您未被授权任何门店，不能核销该订单');
                }

                if ($request->has('convert_code')) {
                    if (preg_replace('/\s/', '', $convertCode)) {

                        // 判断二维码链接
                        $pattern = '/^' . str_replace('/', '\/', config('misc.qrcode_http_url')) . '([\w\d]+)/';
                        if (preg_match($pattern, $convertCode, $matches)) {
                            $qrcode = $matches[1];
                            $convertCode = DB::table('qrcode')->where('code', $qrcode)->first();
                            if ($convertCode) {
                                $order = DB::table('order as o')
                                    ->where('o.order_id', $convertCode->data)
                                    ->whereIn('o.store_id', $this->storeIds)
                                    ->leftJoin('packages as p', 'p.id', '=', 'o.good_id')
                                    ->select('o.*', 'p.expire_date')
                                    ->first();
                                $codeObj = DB::table('convert_code')
                                    ->where('data', $convertCode->data)
                                    ->select('code')->first();
                                $code = $codeObj->code;
                            }
                        } else {
                            $order = DB::table('convert_code as cc')
                                ->where('cc.code', $convertCode)
                                ->join('order as o', function ($join) {
                                    $join->on('o.order_id', '=', 'cc.data')
                                        ->whereIn('o.store_id', $this->storeIds);
                                })
                                ->leftJoin('packages as p', function ($join) {
                                    $join->on('p.id', '=', 'o.good_id')
                                        ->where('o.type', '=', 2);
                                })
                                ->select(['o.*', 'p.expire_date'])->first();
                        }

                    }

                    if (empty($order)) {
                        return $this->response(404, '该订单不存在');
                    }

                    if (!$this->checkStatus($order->status)) {
                        return $this->response(403, '该状态的订单不可核销');
                    }

                    if ($order->convert_time > 0) {
                        return $this->response(403, '该订单已核销');
                    }

                    $data = json_decode($order->snapshot);
                    $data->expire_date = $order->expire_date;
                    if (isset($code)) {
                        $data->convert_code = $code;
                    }

                    return $this->response(200, '操作成功', '', $data);
                } elseif ($request->has('order_no')) {
                    if (preg_replace('/\s/', '', $orderNo)) {
                        $order = DB::table('order as o')
                            ->where('o.order_id', $orderNo)
                            ->whereIn('o.store_id', $this->storeIds)
                            ->leftJoin('packages as p', function ($join) {
                                $join->on('p.id', '=', 'o.good_id')
                                    ->where('o.type', '=', 2);
                            })
                            ->select(['o.*', 'p.expire_date'])->first();
                    }

                    if (empty($order)) {
                        return $this->response(404, '该订单不存在');
                    }

                    if (!$this->checkStatus($order->status)) {
                        return $this->response(403, '该状态的订单不可核销');
                    }

                    if ($order->convert_time > 0) {
                        return $this->response(403, '该订单已核销');
                    }

                    $data = json_decode($order->snapshot);
                    $data->expire_date = $order->expire_date;

                    return $this->response(200, '操作成功', '', $data);
                }


            } else {
                if (!$this->storeIds) {
                    return $this->response(403, '您未被授权任何门店，不能核销该订单');
                }

                if ($request->has('convert_code')) {
                    if (preg_replace('/\s/', '', $convertCode)) {

                        // 判断二维码链接
                        $pattern = '/^' . str_replace('/', '\/', config('misc.qrcode_http_url')) . '([\w\d]+)/';
                        if (preg_match($pattern, $convertCode, $matches)) {
                            $code = $matches[1];
                            $convertCode = DB::table('qrcode')->where('code', $code)->first();
                            if ($convertCode) {
                                $order = DB::table('order as o')
                                    ->where('o.order_id', $convertCode->data)
                                    ->whereIn('o.store_id', $this->storeIds)
                                    ->leftJoin('packages as p', 'p.id', '=', 'o.good_id')
                                    ->select('o.*', 'p.expire_date')
                                    ->first();
                            }
                        } else {
                            $order = DB::table('convert_code as cc')
                                ->where('cc.code', $convertCode)
                                ->join('order as o', function ($join) {
                                    $join->on('o.order_id', '=', 'cc.data')
                                        ->whereIn('o.store_id', $this->storeIds);
                                })
                                ->leftJoin('packages as p', function ($join) {
                                    $join->on('p.id', '=', 'o.good_id')
                                        ->where('o.type', '=', 2);
                                })
                                ->select(['o.*', 'p.expire_date'])->first();
                        }
                    }

                    if (empty($order)) {
                        return $this->response(404, '该订单不存在');
                    }

                    if (!$this->checkStatus($order->status)) {
                        return $this->response(403, '该状态的订单不可核销');
                    }

                    if ($order->convert_time > 0) {
                        return $this->response(403, '该订单已核销');
                    }

                    $log = [
                        'bus_userid' => session()->get('id'),
                        'target_type' => 1,
                        'target_id' => $order->id,
                        'description' => 'ID为 ' . session()->get('id') . ' 的商户在商户后台进行了订单核销操作',
                        'addtime' => time()
                    ];

                    $balance = [
                        'bus_userid' => $this->parentUserId,
                        'balance' => $order->pay_price * 100,
                        'order_id' => $order->order_id,
                        'from_type' => 1,
                        'operator' => session()->get('id'),
                        'addtime' => time()
                    ];

                    // 该用户是否存在积分记录
                    $scoreExist = DB::table(config('tables.base') . '.score_record')
                        ->where('userid', $order->userid)->first();
                    // 下单门店的会员卡积分转出比率
                    $memScoreOutRate = DB::table('bus_stores')
                        ->where('id', $order->store_id)
                        ->select('member_score_out_rate')
                        ->first();

                    DB::beginTransaction();
                    try {
                        // 更新订单表状态
                        DB::table('order')->where('order_id', $order->order_id)
                            ->update(['status' => 2, 'convert_time' => time()]);

                        // 写入商户操作日志
                        DB::table('bus_user_operation_log')->insert($log);

                        // 写商户余额表
                        DB::table('bus_balance')->insert($balance);

                        // 消费的积分
                        $scorePercent = DB::table('params')->where('name', 'consume_score')->first();
                        $score = floor($order->pay_price * ($scorePercent->value));
                        if ($scoreExist) {
                            DB::table(config('tables.base') . '.score_record')
                                ->where('userid', $scoreExist->userid)
                                ->increment('score', $score);
                        } else {
                            $scoreRecord = [
                                'userid' => $order->userid,
                                'score' => $score,
                                'add_time' => time()
                            ];
                            DB::table(config('tables.base') . '.score_record')
                                ->insert($scoreRecord);
                        }
                        // 插入积分变化流水
                        $scoreLog = [
                            'appid' => 1,
                            'userid' => $order->userid,
                            'score' => $score,
                            'member_score_out_rate' => $memScoreOutRate->member_score_out_rate,
                            'msg' => '消费得积分',
                            'add_time' => time()
                        ];
                        DB::table(config('tables.base') . '.score_log')->insert($scoreLog);

                        DB::commit();
                        return $this->response(200, '订单核销成功', route('business.order-verify'));

                    } catch (Exception $e) {
                        DB::rollBack();
                        return $this->response(500, '内部错误，核销失败');
                    }

                } elseif ($request->has('order_no')) {
                    if (preg_replace('/\s/', '', $orderNo)) {
                        $order = DB::table('order as o')
                            ->where('o.order_id', $orderNo)
                            ->whereIn('o.store_id', $this->storeIds)
                            ->leftJoin('packages as p', function ($join) {
                                $join->on('p.id', '=', 'o.good_id')
                                    ->where('o.type', '=', 2);
                            })
                            ->select(['o.*', 'p.expire_date'])->first();
                    }

                    if (empty($order)) {
                        return $this->response(404, '该订单不存在');
                    }

                    if (!$this->checkStatus($order->status)) {
                        return $this->response(403, '该状态的订单不可核销');
                    }

                    if ($order->convert_time > 0) {
                        return $this->response(403, '该订单已核销');
                    }

                    $log = [
                        'bus_userid' => session()->get('id'),
                        'target_type' => 1,
                        'target_id' => $order->id,
                        'description' => 'ID为 ' . session()->get('id') . ' 的商户在商户后台进行了订单核销操作',
                        'addtime' => time()
                    ];

                    $balance = [
                        'bus_userid' => $this->parentUserId,
                        'balance' => $order->pay_price * 100,
                        'order_id' => $order->order_id,
                        'from_type' => 1,
                        'operator' => session()->get('id'),
                        'addtime' => time()
                    ];

                    // 该用户是否存在积分记录
                    $scoreExist = DB::table(config('tables.base') . '.score_record')
                        ->where('userid', $order->userid)->first();
                    // 下单门店的会员卡积分转出比率
                    $memScoreOutRate = DB::table('bus_stores')
                        ->where('id', $order->store_id)
                        ->select('member_score_out_rate')
                        ->first();

                    DB::beginTransaction();
                    try {
                        // 更新订单表状态
                        DB::table('order')->where('order_id', $order->order_id)
                            ->update(['status' => 2, 'convert_time' => time()]);

                        // 写入商户操作日志
                        DB::table('bus_user_operation_log')->insert($log);

                        // 写商户余额表
                        DB::table('bus_balance')->insert($balance);

                        // 消费的积分
                        $scorePercent = DB::table('params')->where('name', 'consume_score')->first();
                        $score = floor($order->pay_price * ($scorePercent->value));
                        if ($scoreExist) {
                            DB::table(config('tables.base') . '.score_record')
                                ->where('userid', $scoreExist->userid)
                                ->increment('score', $score);
                        } else {
                            $scoreRecord = [
                                'userid' => $order->userid,
                                'score' => $score,
                                'add_time' => time()
                            ];
                            DB::table(config('tables.base') . '.score_record')
                                ->insert($scoreRecord);
                        }
                        // 插入积分变化流水
                        $scoreLog = [
                            'appid' => 1,
                            'userid' => $order->userid,
                            'score' => $score,
                            'member_score_out_rate' => $memScoreOutRate->member_score_out_rate,
                            'msg' => '消费得积分',
                            'add_time' => time()
                        ];
                        DB::table(config('tables.base') . '.score_log')->insert($scoreLog);

                        DB::commit();
                        return $this->response(200, '订单核销成功', route('business.order-verify'));

                    } catch (Exception $e) {
                        DB::rollBack();
                        return $this->response(500, '内部错误，核销失败');
                    }
                }

            }
        }
    }

    /**
     * 检测订单状态
     * @param $status
     * @return bool
     */
    private function checkStatus($status)
    {
        if ($status != 1 && $status != 3) {
            return false;
        }
        return true;
    }
}

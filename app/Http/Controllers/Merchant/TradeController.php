<?php
/**
 * Created by PhpStorm.
 * User: AIMPER
 * Date: 2016/10/28
 * Time: 11:29
 */

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TradeController extends Controller
{

    /**
     * 商品订单列表
     */
    public function orders(Request $request)
    {
        $data = $request->only([
            'start_date', 'end_date', 'range', 'store', 'mobile', 'order_no', 'pay_no',
            'package_name', 'payment_type', 'price_start', 'price_end', 'status', 'convert_type'
        ]);

        // 字段集
        $fields = [
            'o.*', 'p.name as good_name', 'u.username', 'u.mobile', 'bs.name as store_name',
            'buol.addtime as convert_time', 'bu.name as convert_user', 'cm.name as convert_machine',
            DB::raw('IF(o.discount_ticket_platform = 1, o.ticket_discount, 0) AS admin_discount'),
            DB::raw('IF(o.discount_ticket_platform = 0, o.ticket_discount, 0) AS bus_discount'),
            DB::raw('IF(o.cash_ticket_platform = 1, o.ticket_denomination, 0) AS admin_cash'),
            DB::raw('IF(o.cash_ticket_platform = 0, o.ticket_denomination, 0) AS bus_cash'),
        ];

        // 每页记录数
        $limit = 20;

        // 查询构造器
        $builder = DB::table('order as o')->where('o.type', 2)
            ->leftJoin('packages as p', function ($join) {
                $join->on('p.id', '=', 'o.good_id')->where('p.userid', '=', $this->parentUserId);
            })
            ->leftJoin('base.users as u', 'u.id', '=', 'o.userid')
            ->leftJoin('bus_stores as bs', 'bs.id', '=', 'o.store_id');

        // 限定兑换条件和不限定兑换条件相比，数据少了，原因是从 2.0 迁移过来的订单在 2.5 的兑换记录表是没有记录的
        if ($data['convert_type']) {
            $builder->join('bus_user_operation_log as buol', function ($join) use ($data) {
                $join->on('buol.target_id', '=', 'o.id')->where('buol.target_type', '=', 1)
                    ->where('buol.converter_type', '=', intval($data['convert_type']));
            });
        } else {
            $builder->leftJoin('bus_user_operation_log as buol', function ($join) use ($data) {
                $join->on('buol.target_id', '=', 'o.id')->where('buol.target_type', '=', 1);
            });
        }

        $builder->leftJoin('bus_users as bu', function ($join) {
            $join->on('bu.id', '=', 'buol.bus_userid')->where('buol.converter_type', '=', 1);
        })->leftJoin('coin_machine as cm', function ($join) {
            $join->on('cm.id', '=', 'buol.bus_userid')->where('buol.converter_type', '=', 2);
        });

        // 订单状态
        if (isset($data['status']) && is_numeric($data['status']) && $data['status'] > 0) {
            $status = $data['status'];
            switch ($status) {
                case 1:
                    $builder->where('o.status', 0);
                    break;
                case 2:
                    $builder->where('o.status', 1);
                    break;
                case 3:
                    // 这里包含已付款但过期或者退款了了的，所以必须加上支付时间限制来确保取出来的数据是确实是已支付然后过期或者退款的
                    // 而不是仅仅下了单未付款就过期了的
                    $builder->where('o.pay_date', '>', 0)->whereIn('o.status', [1, 2, 3, 5]);
                    break;
                case 4:
                    $builder->where('o.status', 2);
                    break;
                case 5:
                    $builder->where('o.status', 3);
                    break;
                case 6:
                    $builder->where('o.status', 5);
                    break;
                case 7:
                    $builder->where('o.status', 6);
                    break;
            }
        }

        // 门店名搜索
        if ($request->has('store')) {
            $storeIds = DB::table('bus_stores')
                ->where('name', 'like', '%' . $request->get('store') . '%')
                ->whereIn('id', $this->storeIds)
                ->lists('id');
            $store_name = $request->get('store');
            $builder->whereIn('o.store_id', $storeIds);
        } else {
            $builder->whereIn('o.store_id', $this->storeIds);
        }

        // 订单号搜索
        if (!empty($data['order_no'])) {
            $order_no = $data['order_no'];
            $builder->where('o.order_id', $data['order_no']);
        }

        //套餐名称筛选
        if (!empty($data['package_name'])) {
            $package_name = $data['package_name'];
            $builder->where('p.name', 'like', '%' . $data['package_name'] . '%');
        }

        // 用户名筛选
        if (!empty($data['mobile'])) {
            $mobile = $data['mobile'];
            $builder->where('u.mobile', $data['mobile']);
        }

        // 交易号查询
        if (!empty($data['pay_no'])) {
            $pay_no = $data['pay_no'];
            $builder->where('o.pay_no', $data['pay_no']);
        }

        // 支付平台筛选
        if (!empty($data['payment_type'])) {
            $payment_type = $data['payment_type'];
            $builder->where('o.payment_type', $payment_type);
        }

        // 订单价格筛选
        if (!empty($data['price_start']) && empty($data['price_end'])) {
            $builder->where('o.price', '>=', $data['price_start']);
        } elseif (empty($data['price_start']) && !empty($data['price_end'])) {
            $builder->where('o.price', '<=', round(floatval($data['price_end']), 2));
        } elseif (!empty($data['price_start']) && !empty($data['price_end'])) {
            $builder->where('o.price', '>=', $data['price_start'])->where('o.price', '<=', round(floatval($data['price_end']), 2));
        }

        $status = $data['status'];

        // 时间段筛选
        if ($request->has('start_date') && !$request->has('end_date')) {      // 只有开始时间
            $start_date = strtotime($request->get('start_date'));
            switch ($status) {
                case 0:
                case 1:
                    $builder->where('o.addtime', '>=', $start_date);
                    break;
                case 2:
                case 3:
                    $builder->where('o.pay_date', '>=', $start_date);
                    break;
                case 4:
                    $builder->where('o.convert_time', '>=', $start_date);
                    break;
                case 5:
                    $builder->where('p.expire_date', '>=', $start_date);
                    break;
                case 6:
                    $builder->where('o.refundtime', '>=', $start_date);
                    break;
                case 7:
                    $builder->where('o.abolishtime', '>=', $start_date);
                    break;
            }

        } elseif (!$request->has('start_date') && $request->has('end_date')) { // 只有结束时间
            $end_date = strtotime($request->get('end_date'));
            switch ($status) {
                case 0:
                case 1:
                    $builder->where('o.addtime', '<=', $end_date);
                    break;
                case 2:
                case 3:
                    $builder->where('o.pay_date', '<=', $end_date);
                    break;
                case 4:
                    $builder->where('o.convert_time', '<=', $end_date);
                    break;
                case 5:
                    $builder->where('p.expire_date', '<=', $end_date);
                    break;
                case 6:
                    $builder->where('o.refundtime', '<=', $end_date);
                    break;
                case 7:
                    $builder->where('o.abolishtime', '<=', $end_date);
                    break;
            }

        } elseif ($request->has('start_date') && $request->has('end_date')) {  // 有开始时间和结束时间
            $start_date = strtotime($request->get('start_date'));
            $end_date = strtotime($request->get('end_date'));
            switch ($status) {
                case 0:
                case 1:
                    $builder->whereBetween('o.addtime', [$start_date, $end_date]);
                    break;
                case 2:
                case 3:
                    $builder->whereBetween('o.pay_date', [$start_date, $end_date]);
                    break;
                case 4:
                    $builder->whereBetween('o.convert_time', [$start_date, $end_date]);
                    break;
                case 5:
                    $builder->whereBetween('p.expire_date', [$start_date, $end_date]);
                    break;
                case 6:
                    $builder->whereBetween('o.refundtime', [$start_date, $end_date]);
                    break;
                case 7:
                    $builder->whereBetween('o.abolishtime', [$start_date, $end_date]);
                    break;
            }
        }

        if (!empty($start_date) && !empty($end_date)) {
            if ($start_date == strtotime(date('Y-m-d')) && $end_date == (strtotime(date('Y-m-d')) + 86399)) {
                $range = 1;
            } elseif ($start_date == strtotime(date('Y-m-d', strtotime('-1 day'))) && $end_date == (strtotime(date('Y-m-d', strtotime('-1 day'))) + 86399)) {
                $range = 2;
            } elseif ($start_date == strtotime(date('Y-m-d', strtotime('-7 day'))) && $end_date == strtotime(date('Y-m-d 23:59:59'))) {
                $range = 3;
            } elseif ($start_date == strtotime(date('Y-m-d', strtotime('-30 day'))) && $end_date == strtotime(date('Y-m-d 23:59:59'))) {
                $range = 4;
            }
        }

        // 交易汇总
        $sum = $builder
            ->select([
                DB::raw('ROUND(SUM(o.price),2) AS total'),
                DB::raw('ROUND(SUM(IF(o.status = 2,pay_price,0)),2) AS converted'),
                DB::raw('ROUND(SUM(IF(o.status = 1,pay_price,0)),2) AS unconverted'),
                DB::raw('COUNT(order_id) AS count'),
            ])
            ->first();

        // 订单数据列表
        $orders = $builder->select($fields)->orderBy('o.addtime', 'desc')->paginate($limit);

        return view('business.order-list', [
            'orders' => $orders,
            'sum' => $sum,
            'start_date' => !empty($start_date) ? $request->get('start_date') : '',
            'end_date' => !empty($end_date) ? $request->get('end_date') : '',
            'store_name' => !empty($store_name) ? $store_name : '',
            'status' => isset($status) ? $status : 0,
            'range' => isset($range) ? $range : 0,
            'order_no' => isset($order_no) ? $order_no : '',
            'package_name' => isset($package_name) ? $package_name : '',
            'mobile' => isset($mobile) ? $mobile : '',
            'pay_no' => isset($pay_no) ? $pay_no : '',
            'payment_type' => isset($payment_type) ? $payment_type : 0,
            'price_start' => isset($data['price_start']) ? $data['price_start'] : '',
            'price_end' => isset($data['price_end']) ? $data['price_end'] : '',
            'convert_type' => isset($data['convert_type']) ? $data['convert_type'] : 0
        ]);
    }

    /**
     * 订单详情
     */
    public function orderDetail(Request $request)
    {
        if (!$id = $request->get('id')) {
            return view('merchant.error', ['code' => 500, 'msg' => '内部错误']);
        }

        $order = DB::table('order as o')->where('o.order_id', $id)
            ->leftJoin('package_store_relation as psr', function ($join) {
                $join->on('psr.store_id', '=', 'o.store_id')->where('psr.package_id', '=', 'o.good_id');
            })
            ->leftJoin('bus_stores as bs', 'bs.id', '=', 'o.store_id')
            ->leftJoin('packages as p', 'p.id', '=', 'o.good_id')
            ->leftJoin('base.users as u', 'u.id', '=', 'o.userid')
            ->select(['o.*', 'u.username', 'bs.name as store_name', 'p.name as good_name',])->first();

        if (!$order) {
            return view('merchant.error', ['code' => 500, 'msg' => '内部错误']);
        }
        return view('merchant.orderdetail', ['order' => $order]);

    }

    /**
     * 会员卡套餐交易明细
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function memberCardOrders(Request $request)
    {
        $data = $request->only([
            'start_date', 'end_date', 'store', 'mobile', 'order_no', 'pay_no',
            'package_name', 'payment_type', 'price_start', 'price_end', 'status', 'exception_status'
        ]);
        // 字段
        $fields = [
            'o.id', 'o.addtime', 'bs.name as store_name', 'o.order_id', 'o.pay_no',
            'o.payment_type', 'u.username', 'u.mobile', 'o.price', 'o.pay_price', 'o.status',
            'buol.addtime as convert_time', 'bu.name as convert_user', 'vip_package_name as good_name',
            'o.member_number as card_number', 'o.member_name', 'o.sync_flag',
            DB::raw('IF(o.cash_ticket_platform = 1, ticket_denomination, 0) AS admin_cash'),
            DB::raw('IF(o.cash_ticket_platform = 0, ticket_denomination, 0) AS business_cash'),
            DB::raw('IF(o.discount_ticket_platform = 0, ticket_discount, 0) AS business_discount'),
            DB::raw('ROUND(o.score_uses / 100) AS score_discount'),
        ];
        // 每页记录数
        $limit = 20;

        // 查询构造器
        $builder = DB::table('order as o')->where('o.type', 3)
            ->join('base.users as u', 'u.id', '=', 'o.userid')
            ->leftJoin('bus_stores as bs', 'bs.id', '=', 'o.store_id')
            ->leftJoin('bus_user_operation_log as buol', function ($join) {
                $join->on('buol.target_id', '=', 'o.id')->where('buol.target_type', '=', 1);
            })
            ->leftJoin('bus_users as bu', 'bu.id', '=', 'buol.bus_userid');

        // 时间筛选
        $status = $data['status'];
        if ($data['start_date'] && !$data['end_date']) {      // 只有开始时间
            $startDate = $data['start_date'];
            switch ($status) {
                case 0:
                case 1:
                    $builder->where('o.addtime', '>=', strtotime($startDate));
                    break;
                case 2:
                    $builder->where('o.pay_date', '>=', strtotime($startDate));
                    break;
                case 3:
                    $builder->where('o.convert_time', '>=', strtotime($startDate));
                    break;
                case 4:
                    $builder->where('o.refundtime', '>=', strtotime($startDate));
                    break;
            }
        } elseif (!$data['start_date'] && $data['end_date']) { // 只有结束时间
            $endDate = $data['end_date'];
            switch ($status) {
                case 0:
                case 1:
                    $builder->where('o.addtime', '<=', strtotime($endDate));
                    break;
                case 2:
                    $builder->where('o.pay_date', '<=', strtotime($endDate));
                    break;
                case 3:
                    $builder->where('o.convert_time', '<=', strtotime($endDate));
                    break;
                case 4:
                    $builder->where('o.refundtime', '<=', strtotime($endDate));
                    break;
            }
        } elseif ($request->has('start_date') && $request->has('end_date')) {  // 有开始时间和结束时间
            $startDate = $data['start_date'];
            $endDate = $data['end_date'];
            switch ($status) {
                case 0:
                case 1:
                    $builder->whereBetween('o.addtime', [strtotime($startDate), strtotime($endDate)]);
                    break;
                case 2:
                    $builder->whereBetween('o.pay_date', [strtotime($startDate), strtotime($endDate)]);
                    break;
                case 3:
                    $builder->whereBetween('o.convert_time', [strtotime($startDate), strtotime($endDate)]);
                    break;
                case 4:
                    $builder->whereBetween('o.refundtime', [strtotime($startDate), strtotime($endDate)]);
                    break;
            }
        }

        if (!empty($startDate) && !empty($endDate)) {
            $sd = strtotime($startDate);
            $ed = strtotime($endDate);
            if ($sd == strtotime(date('Y-m-d')) && $ed == strtotime(date('Y-m-d 23:59:59'))) {
                $range = 1;
            } elseif ($sd == strtotime(date('Y-m-d', strtotime('-1 day'))) && $ed == (strtotime(date('Y-m-d 23:59:59', strtotime('-1 day'))))) {
                $range = 2;
            } elseif ($sd == strtotime(date('Y-m-d', strtotime('-7 day'))) && $ed == strtotime(date('Y-m-d 23:59:59'))) {
                $range = 3;
            } elseif ($sd == strtotime(date('Y-m-d', strtotime('-30 day'))) && $ed == strtotime(date('Y-m-d 23:59:59'))) {
                $range = 4;
            }
        }

        // 门店名搜索
        if ($request->has('store')) {
            $storeName = $request->get('store');
            $storeIds = DB::table('bus_stores')
                ->where('name', 'like', '%' . $storeName . '%')
                ->whereIn('id', $this->storeIds)
                ->lists('id');
            $builder->whereIn('o.store_id', $storeIds);
        } else {
            $builder->whereIn('o.store_id', $this->storeIds);
        }

        // 用户名筛选
        if (!empty($data['mobile'])) {
            $mobile = $data['mobile'];
            $builder->where('u.mobile', $mobile);
        }

        // 订单号搜索
        if (!empty($data['order_no'])) {
            $orderNo = $data['order_no'];
            $builder->where('o.order_id', $orderNo);
        }
        // 交易号查询
        if (!empty($data['pay_no'])) {
            $payNo = $data['pay_no'];
            $builder->where('o.pay_no', $payNo);
        }

        //套餐名称筛选
        if (!empty($data['package_name'])) {
            $packageName = $data['package_name'];
            $builder->where('vip_package_name', 'like', '%' . $packageName . '%');
        }

        // 支付平台筛选
        if (!empty($data['payment_type'])) {
            $paymentType = $data['payment_type'];
            $builder->where('o.payment_type', $paymentType);
        }

        // 订单价格筛选
        if (!empty($price_start) && empty($data['price_end'])) {
            $builder->where('o.price', '>=', $data['price_start']);
        } elseif (empty($data['price_start']) && !empty($data['price_end'])) {
            $builder->where('o.price', '<=', round(floatval($data['price_end']), 2));
        } elseif (!empty($data['price_start']) && !empty($data['price_end'])) {
            $builder->where('o.price', '>=', $data['price_start'])->where('o.price', '<=', round(floatval($data['price_end']), 2));
        }

        // 订单价格筛选
        if (!empty($data['price_start']) && empty($data['price_end'])) {
            $priceStart = $data['price_start'];
            $builder->where('o.price', '>=', $priceStart);
        } elseif (empty($data['price_start']) && !empty($data['price_end'])) {
            $priceEnd = $data['price_end'];
            $builder->where('o.price', '<=', round(floatval($priceEnd), 2));
        } elseif (!empty($data['price_start']) && !empty($data['price_end'])) {
            $priceStart = $data['price_start'];
            $priceEnd = $data['price_end'];
            $builder->where('o.price', '>=', $priceStart)->where('o.price', '<=', round(floatval($priceEnd), 2));
        }

        // 订单状态筛选
        switch ($status) {
            case 1:
                $builder->where('o.status', 0);
                break;
            case 2:
                $builder->where('o.pay_date', '>', 0)->whereIn('o.status', [1, 2, 5]);
                break;
            case 3:
                $builder->where('o.status', 2);
                break;
            case 4:
                $builder->where('o.status', 5);
                break;
        }

        // 异常状态筛选
        if (intval($data['exception_status'])) {
            $exceptionStatus = $data['exception_status'];
            $builder->where('o.sync_flag', $exceptionStatus - 1);
        }

        // 交易总汇
        $sum = $builder
            ->select([
                DB::raw('ROUND(SUM(o.price),2) AS total'),
                DB::raw('ROUND(COUNT(o.id),2) AS count'),
            ])
            ->first();

        // 订单数据
        $orders = $builder->select($fields)->orderBy('o.addtime', 'desc')->paginate($limit);

        return view('business.member-order-list', [
            'sum' => $sum,
            'orders' => $orders,
            'start_date' => isset($startDate) ? $startDate : '',
            'end_date' => isset($endDate) ? $endDate : '',
            'range' => isset($range) ? $range : 0,
            'store' => isset($storeName) ? $storeName : '',
            'mobile' => isset($mobile) ? $mobile : '',
            'order_no' => isset($orderNo) ? $orderNo : '',
            'pay_no' => isset($payNo) ? $payNo : '',
            'package_name' => isset($packageName) ? $packageName : '',
            'payment_type' => isset($paymentType) ? $paymentType : 0,
            'price_start' => isset($priceStart) ? $priceStart : '',
            'price_end' => isset($priceEnd) ? $priceEnd : '',
            'status' => isset($status) ? $status : 0,
            'exception_status' => isset($exceptionStatus) ? $exceptionStatus : 0,
        ]);

    }

    /**
     * 交易汇总
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function summary(Request $request)
    {
        $data = $request->only(['start_date', 'end_date', 'store_name', 'payment_type', 'status']);

        $builder = DB::table('order as o')
            ->whereIn('o.store_id', $this->storeIds)
            ->where('o.store_id', '>', 0)
            ->whereIn('o.status', [1, 2])
            ->where('o.from', 1);

        // 时间段筛选
        if (!empty($data['start_date']) && empty($data['end_date'])) {
            $start_date = strtotime($data['start_date']);
            $builder->where('o.addtime', '>=', $start_date);
        } elseif (empty($data['start_date']) && !empty($data['end_date'])) {
            $end_date = strtotime($data['end_date']);
            $builder->where('o.addtime', '<=', $end_date);
        } elseif (!empty($data['start_date']) && !empty($data['end_date'])) {
            $start_date = strtotime($data['start_date']);
            $end_date = strtotime($data['end_date']);
            $builder->whereBetween('o.addtime', [$start_date, $end_date]);
        }
        if (!empty($start_date) && !empty($end_date)) {
            if ($start_date == strtotime(date('Y-m-d')) && $end_date == (strtotime(date('Y-m-d')) + 86399)) {
                $range = 1;
            } elseif ($start_date == strtotime(date('Y-m-d', strtotime('-1 day'))) && $end_date == (strtotime(date('Y-m-d', strtotime('-1 day'))) + 86399)) {
                $range = 2;
            } elseif ($start_date == strtotime(date('Y-m-d', strtotime('-7 day'))) && $end_date == strtotime(date('Y-m-d 23:59:59'))) {
                $range = 3;
            } elseif ($start_date == strtotime(date('Y-m-d', strtotime('-30 day'))) && $end_date == strtotime(date('Y-m-d 23:59:59'))) {
                $range = 4;
            }
        }

        // 门店名搜索
        if (!empty($data['store_name'])) {
            $storeIds = DB::table('bus_stores')->whereIn('id', $this->storeIds)
                ->where('name', 'like', '%' . $data['store_name'] . '%')->lists('id');
            $builder->whereIn('o.store_id', $storeIds);
        }

        // 支付平台搜索
        if (!empty($data['payment_type']) && intval($data['payment_type'])) {
            $paymentType = $data['payment_type'];
            $builder->where('o.payment_type', $paymentType);
        }

        if (!$this->storeIds) {
            return view('business.trade-summary', [
                'totalPrice' => !empty($totalPrice) ? $totalPrice : 0,
                'packagePrice' => !empty($packagePrice) ? $packagePrice : 0,
                'memberPackagePrice' => !empty($memberPackagePrice) ? $memberPackagePrice : 0,
                'startDate' => !empty($data['start_date']) ? $data['start_date'] : '',
                'endDate' => !empty($data['end_date']) ? $data['end_date'] : '',
                'range' => !empty($range) ? $range : 0,
                'storeName' => !empty($data['store_name']) ? $data['store_name'] : '',
                'paymentType' => !empty($paymentType) ? $paymentType : 0,
            ]);
        }

        // 总金额，已兑换、未兑换金额
        $sum = $builder
            ->select([
                DB::raw('ROUND(SUM(o.pay_price),2) AS total'),   // 总金额
                DB::raw('ROUND(SUM(IF(o.status = 2,o.pay_price,0)),2) AS converted'),    // 已兑换
                DB::raw('ROUND(SUM(IF(o.status =1,o.pay_price,0)),2) AS unconverted'),   // 未兑换
                DB::raw('ROUND(SUM(IF(o.type = 2,pay_price,0)),2) AS package_total'),  // 套餐总金额
                DB::raw('ROUND(SUM(IF(o.type = 3,pay_price,0)),2) AS member_total'),  // 会员卡总金额
                DB::raw('ROUND(SUM(IF(o.type = 2 AND o.status = 2,pay_price,0)),2) AS package_converted'),  // 套餐已兑换
                DB::raw('ROUND(SUM(IF(o.type = 3 AND o.status = 2,pay_price,0)),2) AS member_converted'),  // 会员卡已兑换
                DB::raw('ROUND(SUM(IF(o.type = 2 AND o.status = 1,pay_price,0)),2) AS package_unconverted'),  // 套餐未兑换
                DB::raw('ROUND(SUM(IF(o.type = 3 AND o.status = 1,pay_price,0)),2) AS member_unconverted'),  // 会员卡未兑换
            ])->first();

        $summary = $builder
            ->select([
                'o.store_id',
                'br.name as brand_name',
                'bs.name as store_name',
                DB::raw('ROUND(SUM(IF(o.type = 2,o.pay_price,0)),2) AS package_total'),   // 套餐总营收
                DB::raw('ROUND(SUM(IF(o.type = 3,o.pay_price,0)),2) AS member_total'),    // 会员卡总营收
                DB::raw('ROUND(SUM(IF(o.type = 2 AND o.status = 1,o.pay_price,0)),2) AS package_unconverted'), // 套餐未兑换
                DB::raw('ROUND(SUM(IF(o.type = 2 AND o.status = 2,o.pay_price,0)),2) AS package_converted'),   // 套餐已兑换
                DB::raw('ROUND(SUM(IF(o.type = 3 AND o.status = 1,o.pay_price,0)),2) AS member_unconverted'),// 会员卡未兑换
                DB::raw('ROUND(SUM(IF(o.type = 3 AND o.status = 2,o.pay_price,0)),2) AS member_converted'),  // 会员卡已兑换

                DB::raw('SUM(IF(o.type = 2,1,0)) AS package_total_count'),   // 套餐总订单笔数
                DB::raw('SUM(IF(o.type = 3,1,0)) AS member_total_count'),    // 会员卡总订单笔数
                DB::raw('SUM(IF(o.type = 2 AND o.status = 2,1,0)) AS package_converted_count'),   // 套餐已兑换订单笔数
                DB::raw('SUM(IF(o.type = 3 AND o.status = 2,1,0)) AS member_converted_count'),  // 会员卡已兑换订单笔数
                DB::raw('SUM(IF(o.type = 2 AND o.status = 1,1,0)) AS package_unconverted_count'), // 套餐未兑换订单笔数
                DB::raw('SUM(IF(o.type = 3 AND o.status = 1,1,0)) AS member_unconverted_count'),// 会员卡未兑换订单笔数
            ])
            ->join('bus_stores as bs', 'bs.id', '=', 'o.store_id')
            ->leftJoin(config('tables.base') . '.brand as br', 'br.id', '=', 'bs.brand_id')
            ->groupBy('o.store_id')->paginate(20);

        return view('business.trade-summary', [
            'summary' => !empty($summary) ? $summary : null,
            'sum' => !empty($sum) ? $sum : null,
            'startDate' => !empty($data['start_date']) ? $data['start_date'] : '',
            'endDate' => !empty($data['end_date']) ? $data['end_date'] : '',
            'range' => !empty($range) ? $range : 0,
            'storeName' => !empty($data['store_name']) ? $data['store_name'] : '',
            'paymentType' => !empty($paymentType) ? $paymentType : 0,
        ]);
    }

    /**
     * 机台营收概况
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function machineSalesOverview(Request $request)
    {
        if ($request->ajax()) {
            $sids = implode(',', $this->storeIds);
            if ($sids) {
                // 机台在线/离线比例及数据
                $sql = "SELECT t.total_qty,
        t.online_qty,
        (t.total_qty - t.online_qty) offline_qty,
        IFNULL(ROUND(t.online_qty / t.total_qty * 100),0) online_percent
        FROM (SELECT COUNT(DISTINCT m1.id) total_qty,
        SUM(IF(os.status = 'online', 1, 0)) online_qty
        FROM bus_stores s1
        INNER JOIN iot_product p1 ON p1.store_id = s1.id AND p1.del_flag = 0
        INNER JOIN iot_machine m1 ON m1.product_id = p1.id AND m1.usable = 1 AND m1.del_flag = 0
        LEFT JOIN iot_online_status os ON os.id = m1.id
        WHERE s1.status = 1
        AND s1.is_delete = 0
        AND s1.id IN(" . $sids . ")) t";
                $percent = DB::select($sql);

                // 昨天线上线下营收
                $sql1 = "SELECT 
       SUM(IF(cr.mode = 0, ROUND(cr.qty*cr.price/100, 2), 0)) online_amount,
       SUM(IF(cr.mode = 0, 0, cr.qty)) offline_qty
  FROM bus_stores s1
 INNER JOIN iot_product p1 ON p1.store_id = s1.id AND p1.del_flag = 0
 INNER JOIN iot_machine m1 ON m1.product_id = p1.id AND m1.usable = 1 AND m1.del_flag = 0
 INNER JOIN iot_coin_record cr ON cr.machine_id = m1.id AND cr.del_flag = 0
 WHERE s1.status = 1
   AND s1.is_delete = 0
   AND s1.id IN(" . $sids . ")
   AND TO_DAYS(NOW()) - TO_DAYS(cr.create_date) = 1";
                $yesterday = DB::select($sql1);

                // 今天线上线下营收
                $sql2 = "SELECT 
       SUM(IF(cr.mode = 0, ROUND(cr.qty*cr.price/100, 2), 0)) online_amount,
       SUM(IF(cr.mode = 0, 0, cr.qty)) offline_qty
  FROM bus_stores s1
 INNER JOIN iot_product p1 ON p1.store_id = s1.id AND p1.del_flag = 0
 INNER JOIN iot_machine m1 ON m1.product_id = p1.id AND m1.usable = 1 AND m1.del_flag = 0
 INNER JOIN iot_coin_record cr ON cr.machine_id = m1.id AND cr.del_flag = 0
 WHERE s1.status = 1
   AND s1.is_delete = 0
   AND s1.id IN(" . $sids . ")
   AND TO_DAYS(NOW()) - TO_DAYS(cr.create_date) = 0";
                $today = DB::select($sql2);

                // 最近30天线上线下营收
                $sql3 = "SELECT 
       SUM(IF(cr.mode = 0, ROUND(cr.qty*cr.price/100, 2), 0)) online_amount,
       SUM(IF(cr.mode = 0, 0, cr.qty)) offline_qty
  FROM bus_stores s1
 INNER JOIN iot_product p1 ON p1.store_id = s1.id AND p1.del_flag = 0
 INNER JOIN iot_machine m1 ON m1.product_id = p1.id AND m1.usable = 1 AND m1.del_flag = 0
 INNER JOIN iot_coin_record cr ON cr.machine_id = m1.id AND cr.del_flag = 0
 WHERE s1.status = 1
   AND s1.is_delete = 0
   AND s1.id IN(" . $sids . ")
   AND TO_DAYS(NOW()) - TO_DAYS(cr.create_date) <= 30";
                $thirtyDays = DB::select($sql3);
            } else {
                $percent[] = [
                    'total_qty' => 0,
                    'online_qty' => 0,
                    'offline_qty' => 0
                ];
                $yesterday[] = [
                    'online_amount' => 0,
                    'offline_qty' => 0,
                ];
                $today[] = [
                    'online_amount' => 0,
                    'offline_qty' => 0,
                ];
                $thirtyDays[] = [
                    'online_amount' => 0,
                    'offline_qty' => 0,
                ];
            }

            $res = [
                'percent' => $percent,
                'yesterday' => $yesterday,
                'today' => $today,
                'thirtyDays' => $thirtyDays,
            ];
            return response()->json($res);

        } else {
            if (empty($this->storeIds)) {
                $sd = $request->get('sd');
                $ed = $request->get('ed');
                $mode = $request->get('mode');
                if (strtotime($sd) && strtotime($ed)) {
                    $s = $sd;
                    $e = $ed;
                }
                return view('business.machine-sales-overview-new', [
                    'mode' => $mode ? $mode : 1,
                    's' => !empty($s) ? $s : '',
                    'e' => !empty($e) ? $e : '',
                ]);
            } else {
                $mode = !empty($request->get('mode')) ? $request->get('mode') : 1;

                $builder = DB::table('iot_order as io')
                    ->whereIn('io.status', [2, 3])
                    ->where('io.del_flag', 0);
                $limit = 20;

                $sd = $request->get('sd');
                $ed = $request->get('ed');
                if (strtotime($sd) && strtotime($ed)) {
                    $s = $sd;
                    $e = $ed;
                    $builder->whereBetween('io.create_date', [$sd, $ed]);
                }

                switch ($mode) {
                    case 1:
                        $res = $builder->join('iot_machine as im', 'im.id', '=', 'io.machine_id')
                            ->join('iot_product as ip', 'ip.id', '=', 'im.product_id')
                            ->join('bus_stores as bs', function ($join) {
                                $join->on('bs.id', '=', 'ip.store_id')->whereIn('bs.id', $this->storeIds);
                            })
                            ->select([
                                DB::raw('DATE_FORMAT(io.create_date, "%Y-%m-%d") AS create_date'),
                                DB::raw('count(io.id) AS order_count'),
                                DB::raw('round(sum(io.coin_price * io.coin_qty * io.num /100),2) AS price_sum')
                            ])->groupBy(DB::raw('DATE_FORMAT(io.create_date, "%Y-%m-%d")'))
                            ->orderBy(DB::raw('DATE_FORMAT(io.create_date, "%Y-%m-%d")'), 'desc')->paginate($limit);
                        break;
                    case 2:
                        $res = $builder->join('iot_machine as im', 'im.id', '=', 'io.machine_id')
                            ->join('iot_product as ip', 'ip.id', '=', 'im.product_id')
                            ->join('bus_stores as bs', function ($join) {
                                $join->on('bs.id', '=', 'ip.store_id')->whereIn('bs.id', $this->storeIds);
                            })
                            ->select([
                                'ip.name as product_name',
                                DB::raw('count(io.id) as order_count'),
                                DB::raw('round(sum(io.coin_price * io.coin_qty * io.num /100),2) AS price_sum')
                            ])->groupBy('ip.name')->paginate($limit);
                        break;
                    case 3:
                        $res = $builder->join('iot_machine as im', 'im.id', '=', 'io.machine_id')
                            ->join('iot_product as ip', 'ip.id', '=', 'im.product_id')
                            ->join('bus_stores as bs', function ($join) {
                                $join->on('bs.id', '=', 'ip.store_id')->whereIn('bs.id', $this->storeIds);
                            })
                            ->join('iot_dev as id', 'id.id', '=', 'im.dev_id')
                            ->select([
                                'im.name as machine_name',
                                'id.serial_no',
                                DB::raw('count(io.id) AS order_count'),
                                DB::raw('round(sum(io.coin_price * io.coin_qty * io.num /100),2) AS price_sum')
                            ])->groupBy('im.name')->paginate($limit);
                        break;
                    case 4:
                        $res = $builder->join('iot_machine as im', 'im.id', '=', 'io.machine_id')
                            ->join('iot_product as ip', 'ip.id', '=', 'im.product_id')
                            ->join('bus_stores as bs', function ($join) {
                                $join->on('bs.id', '=', 'ip.store_id')->whereIn('bs.id', $this->storeIds);
                            })
                            ->select([
                                'bs.name as store_name',
                                DB::raw('count(io.id) AS order_count'),
                                DB::raw('round(sum(io.coin_price * io.coin_qty * io.num /100),2) AS price_sum')
                            ])->groupBy('bs.name')->paginate($limit);
                        break;
                }

                return view('business.machine-sales-overview-new', [
                    'mode' => $mode,
                    's' => !empty($s) ? $s : '',
                    'e' => !empty($e) ? $e : '',
                    'res' => $res
                ]);
            }
        }
    }

    /**
     * 过滤机台营收数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function machineSalesFilter(Request $request)
    {
        $data = $request->only('start_date', 'end_date');

        $builder = DB::table('iot_coin_record as ic')
            ->join('iot_machine as im', function ($join) {
                $join->on('im.id', '=', 'ic.machine_id')->where('im.del_flag', '=', 0)->where('im.usable', '=', 1);
            })
            ->join('iot_product as ip', function ($join) {
                $join->on('ip.id', '=', 'im.product_id')->where('ip.del_flag', '=', 0);
            })
            ->join('bus_stores as bs', function ($join) {
                $join->on('bs.id', '=', 'ip.store_id')->whereIn('bs.id', $this->storeIds);
            });

        $fields = [
            DB::raw("DATE_FORMAT(ic.create_date, '%Y-%m-%d') AS day"),
            DB::raw("ROUND(SUM(IF(ic.mode = 0, ic.qty * ic.price / 100, 0)), 2) AS total_amount"),
            DB::raw("SUM(IF(ic.mode = 0, 0, ic.qty)) AS total_qty"),
        ];

        if (strtotime($data['start_date']) && !strtotime($data['end_date'])) {
            $builder->where('ic.create_date', '>=', $data['start_date']);
        } elseif (!strtotime($data['start_date']) && strtotime($data['end_date'])) {
            $builder->where('ic.create_date', '<=', $data['end_date']);
        } elseif (strtotime($data['start_date']) && strtotime($data['end_date'])) {
            $builder->whereBetween('ic.create_date', [$data['start_date'], $data['end_date']]);
        } else {
            $builder->whereBetween('ic.create_date', [date('Y-m-01'), date('Y-m-d 23:59:59')]);
        }

        $res = $builder
            ->select($fields)
            ->groupBy(DB::raw("DATE_FORMAT(ic.create_date, '%Y-%m-%d')"))
            ->get();

        return response()->json($res);
    }

    /**
     * 过滤机台营收数据 - 已废弃
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function machineSalesFilterOld(Request $request)
    {
        if (empty($this->storeIds)) {
            return response()->json([]);
        }

        $data = $request->only('ons_date', 'one_date', 'offs_date', 'offe_date');
        $sids = implode(',', $this->storeIds);

        if (!empty($data['ons_date']) && !empty($data['one_date']) && empty($data['offs_date']) && empty($data['offe_date'])) {
            // 线上营收过滤
            $limit = (strtotime($data['one_date']) - strtotime($data['ons_date'])) / 86400 + 1;
            $endDate = strtotime($data['one_date']) ? date('Y-m-d', strtotime($data['one_date'] . '+1 day')) : date('Y-m-d', strtotime('+1 day'));

            $sql = "SELECT DATE_FORMAT(t.count_date, '%Y-%m-%d') day,
                    IFNULL(tab.online_amount, 0) total_amount,
                    IFNULL(tab.offline_qty, 0) total_qty
                FROM (SELECT @cdate := DATE_ADD(@cdate, INTERVAL - 1 DAY) count_date
          FROM (SELECT @cdate := '" . $endDate . "' FROM iot_coin_record LIMIT " . $limit . ") t1) t
          LEFT JOIN (SELECT DATE_FORMAT(cr.create_date, '%Y-%m-%d') count_date,
                   SUM(IF(cr.mode = 0, ROUND(cr.qty*cr.price/100, 2), 0)) online_amount,
                   SUM(IF(cr.mode = 0, 0, cr.qty)) offline_qty
              FROM bus_stores s1
             INNER JOIN iot_product p1 ON p1.store_id = s1.id AND p1.del_flag = 0
             INNER JOIN iot_machine m1 ON m1.product_id = p1.id AND m1.usable = 1 AND m1.del_flag = 0
             INNER JOIN iot_coin_record cr ON cr.machine_id = m1.id AND cr.del_flag = 0
             WHERE s1.status = 1
               AND s1.is_delete = 0
               AND s1.id IN(" . $sids . ")
             GROUP BY DATE_FORMAT(cr.create_date, '%Y-%m-%d')) tab ON tab.count_date = t.count_date
             ORDER BY day";
            $online = DB::select($sql);

            $res = ['online' => $online];
            return response()->json($res);

        } elseif (empty($data['ons_date']) && empty($data['one_date']) && !empty($data['offs_date']) && !empty($data['offe_date'])) {
            // 线下营收过滤
            $limit = floor((strtotime($data['offe_date']) - strtotime($data['offs_date'])) / 86400) + 1;
            $endDate = strtotime($data['offe_date']) ? date('Y-m-d', strtotime($data['offe_date'] . '+1 day')) : date('Y-m-d', strtotime('+1 day'));

            $sql = "SELECT DATE_FORMAT(t.count_date, '%Y-%m-%d') day,
                    IFNULL(tab.online_amount, 0) total_amount,
                    IFNULL(tab.offline_qty, 0) total_qty
                FROM (SELECT @cdate := DATE_ADD(@cdate, INTERVAL - 1 DAY) count_date
          FROM (SELECT @cdate := '" . $endDate . "' FROM iot_coin_record LIMIT " . $limit . ") t1) t
          LEFT JOIN (SELECT DATE_FORMAT(cr.create_date, '%Y-%m-%d') count_date,
                   SUM(IF(cr.mode != 0, ROUND(cr.qty*cr.price/100, 2), 0)) online_amount,
                   SUM(IF(cr.mode != 0, 0, cr.qty)) offline_qty
              FROM bus_stores s1
             INNER JOIN iot_product p1 ON p1.store_id = s1.id AND p1.del_flag = 0
             INNER JOIN iot_machine m1 ON m1.product_id = p1.id AND m1.usable = 1 AND m1.del_flag = 0
             INNER JOIN iot_coin_record cr ON cr.machine_id = m1.id AND cr.del_flag = 0
             WHERE s1.status = 1
               AND s1.is_delete = 0
               AND s1.id IN(" . $sids . ")
             GROUP BY DATE_FORMAT(cr.create_date, '%Y-%m-%d')) tab ON tab.count_date = t.count_date
             ORDER BY day";
            $offline = DB::select($sql);

            $res = ['offline' => $offline];
            return response()->json($res);

        } elseif (!empty($data['ons_date']) && !empty($data['one_date']) && !empty($data['offs_date']) && !empty($data['offe_date'])) {
            // 线上营收过滤
            $oendDate = strtotime($data['one_date']) ? date('Y-m-d', strtotime($data['one_date'] . '+1 day')) : date('Y-m-d', strtotime('+1 day'));
            $olimit = (strtotime($data['one_date']) - strtotime($data['ons_date'])) / 86400 + 1;

            $sql1 = "SELECT DATE_FORMAT(t.count_date, '%Y-%m-%d') day,
                    IFNULL(tab.online_amount, 0) total_amount,
                    IFNULL(tab.offline_qty, 0) total_qty
                FROM (SELECT @cdate := DATE_ADD(@cdate, INTERVAL - 1 DAY) count_date
          FROM (SELECT @cdate := '" . $oendDate . "' FROM iot_coin_record LIMIT " . $olimit . ") t1) t
          LEFT JOIN (SELECT DATE_FORMAT(cr.create_date, '%Y-%m-%d') count_date,
                   SUM(IF(cr.mode = 0, ROUND(cr.qty*cr.price/100, 2), 0)) online_amount,
                   SUM(IF(cr.mode = 0, 0, cr.qty)) offline_qty
              FROM bus_stores s1
             INNER JOIN iot_product p1 ON p1.store_id = s1.id AND p1.del_flag = 0
             INNER JOIN iot_machine m1 ON m1.product_id = p1.id AND m1.usable = 1 AND m1.del_flag = 0
             INNER JOIN iot_coin_record cr ON cr.machine_id = m1.id AND cr.del_flag = 0
             WHERE s1.status = 1
               AND s1.is_delete = 0
               AND s1.id IN(" . $sids . ")
             GROUP BY DATE_FORMAT(cr.create_date, '%Y-%m-%d')) tab ON tab.count_date = t.count_date
             ORDER BY day";
            $online = DB::select($sql1);

            // 线下营收过滤
            $fendDate = strtotime($data['offe_date']) ? date('Y-m-d', strtotime($data['offe_date'] . '+1 day')) : date('Y-m-d', strtotime('+1 day'));
            $flimit = (strtotime($data['offe_date']) - strtotime($data['offs_date'])) / 86400 + 1;

            $sql = "SELECT DATE_FORMAT(t.count_date, '%Y-%m-%d') day,
                    IFNULL(tab.online_amount, 0) total_amount,
                    IFNULL(tab.offline_qty, 0) total_qty
                FROM (SELECT @cdate := DATE_ADD(@cdate, INTERVAL - 1 DAY) count_date
          FROM (SELECT @cdate := '" . $fendDate . "' FROM iot_coin_record LIMIT " . $flimit . ") t1) t
          LEFT JOIN (SELECT DATE_FORMAT(cr.create_date, '%Y-%m-%d') count_date,
                   SUM(IF(cr.mode = 0, ROUND(cr.qty*cr.price/100, 2), 0)) online_amount,
                   SUM(IF(cr.mode = 0, 0, cr.qty)) offline_qty
              FROM bus_stores s1
             INNER JOIN iot_product p1 ON p1.store_id = s1.id AND p1.del_flag = 0
             INNER JOIN iot_machine m1 ON m1.product_id = p1.id AND m1.usable = 1 AND m1.del_flag = 0
             INNER JOIN iot_coin_record cr ON cr.machine_id = m1.id AND cr.del_flag = 0
             WHERE s1.status = 1
               AND s1.is_delete = 0
               AND s1.id IN(" . $sids . ")
             GROUP BY DATE_FORMAT(cr.create_date, '%Y-%m-%d')) tab ON tab.count_date = t.count_date
             ORDER BY day";
            $offline = DB::select($sql);

            $res = ['online' => $online, 'offline' => $offline];
            return response()->json($res);
        } else {
            // 线上线下都只取本月数据
            $limit = (strtotime(date('Y-m-d')) - strtotime(date('Y-m-01'))) / 86400 + 1;

            // 线上营收
            $sql1 = "SELECT DATE_FORMAT(t.count_date, '%m-%d') day,
                    IFNULL(tab.online_amount, 0) total_amount,
                    IFNULL(tab.offline_qty, 0) total_qty
                FROM (SELECT @cdate := DATE_ADD(@cdate, INTERVAL - 1 DAY) count_date
          FROM (SELECT @cdate := DATE_ADD(CURDATE(),INTERVAL 1 DAY) FROM iot_coin_record LIMIT " . $limit . ") t1) t
          LEFT JOIN (SELECT DATE_FORMAT(cr.create_date, '%Y-%m-%d') count_date,
                   SUM(IF(cr.mode = 0, ROUND(cr.qty*cr.price/100, 2), 0)) online_amount,
                   SUM(IF(cr.mode = 0, 0, cr.qty)) offline_qty
              FROM bus_stores s1
             INNER JOIN iot_product p1 ON p1.store_id = s1.id AND p1.del_flag = 0
             INNER JOIN iot_machine m1 ON m1.product_id = p1.id AND m1.usable = 1 AND m1.del_flag = 0
             INNER JOIN iot_coin_record cr ON cr.machine_id = m1.id AND cr.del_flag = 0
             WHERE s1.status = 1
               AND s1.is_delete = 0
               AND s1.id IN(" . $sids . ")
             GROUP BY DATE_FORMAT(cr.create_date, '%Y-%m-%d')) tab ON tab.count_date = t.count_date
             ORDER BY day ASC";

            $online = DB::select($sql1);


            // 线下营收
            $sql2 = "SELECT DATE_FORMAT(t.count_date, '%m-%d') day,
                    IFNULL(tab.online_amount, 0) total_amount,
                    IFNULL(tab.offline_qty, 0) total_qty
                FROM (SELECT @cdate := DATE_ADD(@cdate, INTERVAL - 1 DAY) count_date
          FROM (SELECT @cdate := DATE_ADD(CURDATE(),INTERVAL 1 DAY) FROM iot_coin_record LIMIT " . $limit . ") t1) t
          LEFT JOIN (SELECT DATE_FORMAT(cr.create_date, '%Y-%m-%d') count_date,
                   SUM(IF(cr.mode = 0, ROUND(cr.qty*cr.price/100, 2), 0)) online_amount,
                   SUM(IF(cr.mode = 0, 0, cr.qty)) offline_qty
              FROM bus_stores s1
             INNER JOIN iot_product p1 ON p1.store_id = s1.id AND p1.del_flag = 0
             INNER JOIN iot_machine m1 ON m1.product_id = p1.id AND m1.usable = 1 AND m1.del_flag = 0
             INNER JOIN iot_coin_record cr ON cr.machine_id = m1.id AND cr.del_flag = 0
             WHERE s1.status = 1
               AND s1.is_delete = 0
               AND s1.id IN(" . $sids . ")
             GROUP BY DATE_FORMAT(cr.create_date, '%Y-%m-%d')) tab ON tab.count_date = t.count_date
            ORDER BY day ASC";
            $offline = DB::select($sql2);

            $res = [
                'online' => $online,
                'offline' => $offline
            ];
            return response()->json($res);

        }
    }

    /**
     * VR机台营收统计
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function vrOrders(Request $request)
    {
        $data = $request->only('start_date', 'end_date', 'store_id');

        $fields = [
            'vo.no', 'vo.machine_name', 'vo.game_name',
            DB::raw('ROUND(vo.amount/100,2) AS amount'),
            'vo.is_pay', 'vo.pay_no', 'vo.pay_date', 'vo.status',
            DB::raw('ROUND(vo.consume_amount/100,2) AS consume_amount'),
            'vo.use_date', 'vo.create_date', 'vo.vr_charge', 'u.mobile', 'bs.name as store_name',
            'vo.charge_type', 'vo.game_charge'
        ];

        $builder = DB::table('vr_order as vo')->where('vo.del_flag', 0)->where('vo.pay_date', '>', 0);

        // 时间段筛选
        if ($data['start_date'] && !$data['end_date']) {
            $builder->where('vo.use_date', '>=', $data['start_date']);
        } elseif (!$data['start_date'] && $data['end_date']) {
            $builder->where('vo.use_date', '<=', $data['end_date']);
        } elseif ($data['start_date'] && $data['end_date']) {
            $builder->whereBetween('vo.use_date', [$data['start_date'], $data['end_date']]);
        }

        // 门店筛选
        if ($data['store_id'] && in_array($data['store_id'], $this->storeIds)) {
            $builder->where('vo.store_id', $data['store_id']);
        }

        $sum = $builder->select(DB::raw('ROUND(SUM(consume_amount/100),2) AS `sum`'))->first();

        $list = $builder
            ->leftJoin(config('tables.base') . '.users as u', 'u.id', '=', 'vo.userid')
            ->leftJoin('bus_stores as bs', 'bs.id', '=', 'vo.store_id')
            ->select($fields)
            ->orderBy('vo.create_date', 'desc')->paginate(20);

        return view('business.vr-orders', [
            'orders' => $list,
            'stores' => $this->stores,
            'sum' => $sum->sum ? $sum->sum : 0,
            'params' => $data
        ]);
    }

    /**
     * VR机台营收概况
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function vrSaleOverview(Request $request)
    {
        $data = $request->only('store_id', 'machine_name', 'start_date', 'end_date');
        $fields = [
            'bs.name as store_name',
            'vo.machine_name',
            'vo.machine_id',
            DB::raw('ROUND(SUM(consume_amount / 100),2) AS sales'),
        ];

        $builder = DB::table('vr_order as vo')
            ->join('bus_stores as bs', 'bs.id', '=', 'vo.store_id')
            ->where('vo.is_pay', 1)
            ->where('vo.status', 3)
            ->whereIn('vo.store_id', $this->storeIds);

        // 门店筛选
        if (intval($data['store_id'])) {
            $builder->where('vo.store_id', intval($data['store_id']));
        }
        // 机台名称筛选
        if ($data['machine_name']) {
            $builder->where('vo.machine_name', 'like', '%' . $data['machine_name'] . '%');
        }
        // 时间筛选
        if (strtotime(strtotime($data['start_date']) && !strtotime($data['end_date']))) {
            $builder->where('vo.create_date', '>=', $data['start_date']);
        } elseif (!strtotime($data['start_date']) && strtotime($data['end_date'])) {
            $builder->where('vo.create_date', '<=', $data['end_date']);
        } elseif (strtotime($data['start_date']) && strtotime($data['end_date'])) {
            $builder->whereBetween('vo.create_date', [$data['start_date'], $data['end_date']]);
        }

        $sum = $builder->select(DB::raw('ROUND(SUM(consume_amount / 100),2) AS `sum`'))->first();

        $list = $builder
            ->select($fields)
            ->orderBy('vo.create_date', 'desc')
            ->groupBy('vo.machine_id')
            ->paginate(20);

        return view('business.vr-sales-overview', [
            'list' => $list,
            'sum' => $sum->sum,
            'params' => $data,
            'stores' => $this->stores,
        ]);

    }

    /**
     * 机台营收流水
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function vrMachineSalesFlow(Request $request)
    {
        $data = $request->only('id', 'start_date', 'end_date', 'game_name');

        if (!intval($data['id'])) {
            return view('business.error', ['code' => 500, 'msg' => '内部错误']);
        }

        $builder = DB::table('vr_order as vo')
            ->whereIn('vo.store_id', $this->storeIds)
            ->where('vo.machine_id', intval($data['id']));

        // 时间筛选
        if (strtotime($data['start_date']) && !strtotime($data['end_date'])) {
            $builder->where('vo.end_date', '>=', $data['start_date']);
        } elseif (!strtotime($data['start_date']) && strtotime($data['end_date'])) {
            $builder->where('vo.end_date', '<=', $data['end_date']);
        } elseif (strtotime($data['start_date']) && strtotime($data['end_date'])) {
            $builder->whereBetween('vo.end_date', [$data['start_date'], $data['end_date']]);
        }
        // 游戏名称筛选
        if ($data['game_name']) {
            $builder->where('game_name', 'like', '%' . $data['game_name'] . '%');
        }

        $list = $builder->select([
            'vo.game_name',
            'vo.game_id',
            DB::raw('ROUND(SUM(vo.consume_amount / 100),2) AS income'),
            'vo.end_date',
            'vo.use_date',
        ])->orderBy('create_date', 'desc')->paginate(20);

        $sum = $builder->select(DB::raw('ROUND(SUM(consume_amount / 100),2) AS `sum`'))->first();

        return view('business.vr-machine-sales-flow', ['list' => $list, 'sum' => $sum->sum ? $sum->sum : 0, 'params' => $data]);
    }

    /**
     * 会员卡套餐退款
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function refundMemberOrder(Request $request)
    {
        if (!intval($request->get('id'))) {
            return $this->response(500, '内部错误');
        }

        if (!$this->storeIds) {
            return $this->response(403, '您未被授权任何门店，无法进行该订单的退款操作');
        }

        $order = DB::table('order')
            ->where('type', 3)
            ->whereIn('store_id', $this->storeIds)
            ->where('id', $request->get('id'))
            ->first();

        if (!$order) {
            return $this->response(404, '该订单不存在');
        }

        if ($order->status != 1) {
            return $this->response(403, '该状态下的订单不能退款');
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

            return $this->response(200, '退款成功', route('business.member-order-list'));
        }


    }

    /**
     * VR点数报表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function vrMemberScoreLog(Request $request)
    {

        $data = $request->only('store', 'start_date', 'end_date');

        if (!$this->storeIds) {
            return view('business.vr-member-score-log', ['params' => $data]);
        }

        $builder = DB::table('vr_member_score as vms')
            ->join('bus_stores as bs', function ($join) use ($data) {
                $join->on('bs.id', '=', 'vms.store_id')->whereIn('bs.id', $this->storeIds);

                if ($data['store']) {
                    $join->where('bs.name', 'like', '%' . $data['store'] . '%');
                }
            })
            ->leftJoin(config('tables.base') . '.brand as b', 'b.id', '=', 'bs.brand_id');

        $fields = [
            'bs.id',
            DB::raw('CONCAT(b.name, "（", bs.name, "）") AS store_name'),
            'vms.score', 'vms.reason', 'vms.create_date'
        ];

        if (strtotime($data['start_date']) && !strtotime($data['end_date'])) {
            $builder->where('vms.create_date', '>=', $data['start_date']);
        } elseif (!strtotime($data['start_date']) && strtotime($data['end_date'])) {
            $builder->where('vms.create_date', '<=', $data['end_date']);
        } elseif (strtotime($data['start_date']) && strtotime($data['end_date'])) {
            $builder->whereBetween('vms.create_date', [$data['start_date'], $data['end_date']]);
        }

        $list = $builder->select($fields)->orderBy('vms.create_date', 'desc')->paginate(20);

        return view('business.vr-member-score-log', ['list' => $list, 'params' => $data]);

    }

}
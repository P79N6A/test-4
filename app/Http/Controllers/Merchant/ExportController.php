<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Libraries\ExcelProcessor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExportController extends Controller
{
    /**
     * 非会员套餐订单导出
     * @param Request $request
     */
    public function packageOrders(Request $request)
    {
        $data = $request->only([
            'status', 'start_date', 'end_date', 'store', 'mobile', 'order_no', 'pay_no',
            'package_name', 'payment_type', 'price_start', 'price_end', 'convert_type'
        ]);

        $builder = DB::table('order as o')->where('o.type', 2)
            ->leftJoin('bus_stores as bs', 'bs.id', '=', 'o.store_id')
            ->leftJoin('packages as p', 'p.id', '=', 'o.good_id')
            ->leftJoin(config('tables.base') . '.users as u', 'u.id', '=', 'o.userid');

        if ($data['convert_type']) {
            $builder->join('bus_user_operation_log as buol', function ($join) use ($data) {
                $join->on('buol.target_id', '=', 'o.id')->where('buol.target_type', '=', 1)
                    ->where('buol.converter_type', '=', intval($data['convert_type']));
            });
        } else {
            $builder->leftJoin('bus_user_operation_log as buol', function ($join) {
                $join->on('buol.target_id', '=', 'o.id')->where('buol.target_type', '=', 1);
            });
        }

        $builder->leftJoin('bus_users as bu', function ($join) {
            $join->on('bu.id', '=', 'buol.bus_userid')->where('buol.converter_type', '=', 1);
        })->leftJoin('coin_machine as cm', function ($join) {
            $join->on('cm.id', '=', 'buol.bus_userid')->where('buol.converter_type', '=', 2);
        });

        $fields = [
            DB::raw('FROM_UNIXTIME(o.addtime) AS addtime'),
            DB::raw('CASE WHEN o.payment_type = 1 THEN "支付宝" WHEN o.payment_type = 2 THEN "微信APP支付" WHEN o.payment_type = 3 THEN "微信公众号" END AS payment_type'),
            'o.order_id', 'o.pay_no', 'bs.name as store_name', 'p.name as package_name',
            'o.price', 'o.pay_price',
            DB::raw('IF(o.cash_ticket_platform = 1,ticket_denomination,0) AS admin_cash'),
            DB::raw('ROUND(o.score_uses / 100) AS score_discount'),
            DB::raw('IF(o.cash_ticket_platform = 0,ticket_denomination,0) AS business_cash'),
            DB::raw('IF(o.discount_ticket_platform = 0,ticket_discount,0) AS business_discount'),
            DB::raw("CASE WHEN o.status = 0 THEN '待付款' WHEN o.status = 1 THEN '未兑换' WHEN o.status = 2 THEN '已兑换' WHEN o.status = 3 THEN '已过期' WHEN o.status = 5 THEN '已退款' WHEN o.status = 6 THEN '已取消' END AS status"),
            'u.mobile',
            DB::raw('IF(o.convert_time > 0, FROM_UNIXTIME(o.convert_time),NULL) AS convert_time'),
            'bu.name as operator', 'cm.name as convert_machine',
        ];

        /* 条件筛选 */

        $status = intval($data['status']);
        // 订单状态筛选
        if ($status) {
            switch ($status) {
                case 1:
                    $builder->where('o.status', 0);
                    break;
                case 2:
                    $builder->where('o.status', 1);
                    break;
                case 3:
                    $builder->where('o.pay_date', '>=', 0)->whereIn('o.status', [1, 2, 3, 5]);;
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
        // 时间筛选
        if ($data['start_date'] && !$data['end_date']) {      // 只有开始时间
            $start_date = strtotime($data['start_date']);
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

        } elseif (!$data['start_date'] && $data['end_date']) { // 只有结束时间
            $end_date = strtotime($data['end_date']);
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

        } elseif ($data['start_date'] && $data['end_date']) {  // 有开始时间和结束时间
            $start_date = strtotime($data['start_date']);
            $end_date = strtotime($data['end_date']);
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
        // 门店名搜索
        if ($data['store']) {
            $storeIds = DB::table('bus_stores')
                ->whereIn('id', $this->storeIds)
                ->where('name', 'like', '%' . $request->get('store') . '%')
                ->lists('id');
            $builder->whereIn('o.store_id', $storeIds);
        } else {
            $builder->whereIn('o.store_id', $this->storeIds);
        }
        // 用户手机筛选
        if (!empty($data['mobile'])) {
            $builder->where('u.mobile', $data['mobile']);
        }
        // 订单号搜索
        if (!empty($data['order_no'])) {
            $builder->where('o.order_id', $data['order_no']);
        }
        // 交易号查询
        if (!empty($data['pay_no'])) {
            $builder->where('o.pay_no', $data['pay_no']);
        }
        //套餐名称筛选
        if (!empty($data['package_name'])) {
            $builder->where('p.name', 'like', '%' . $data['package_name'] . '%');
        }
        // 支付平台筛选
        if (!empty($data['payment_type'])) {
            $builder->where('o.payment_type', $data['payment_type']);
        }
        // 订单价格筛选
        if (!empty($data['price_start']) && empty($data['price_end'])) {
            $builder->where('o.price', '>=', $data['price_start']);
        } elseif (empty($data['price_start']) && !empty($data['price_end'])) {
            $builder->where('o.price', '<=', round(floatval($data['price_end']), 2));
        } elseif (!empty($data['price_start']) && !empty($data['price_end'])) {
            $builder->where('o.price', '>=', $data['price_start'])->where('o.price', '<=', round(floatval($data['price_end']), 2));
        }

        $res = $builder->select($fields)->orderBy('o.addtime', 'desc')->get();

        // 初始化导出处理器
        $handler = new ExcelProcessor();
        $header = [
            '创建时间', '支付渠道', '订单号', '交易号', '门店', '套餐名称', '原价（+元）', '实收（+元）', '平台现金券优惠（-元）',
            '平台积分抵扣（-元）', '门店现金券优惠（-元）', '门店折扣优惠（-元）', '状态', '买家', '兑换时间', '操作员', '提币机'
        ];
        $filename = date('Y\-m\-d H：i：s') . ' - 商品交易明细导出订单.xlsx';
        $handler->setHeader($header);
        $handler->setData($res);
        $handler->download($filename);
    }

    /**
     * 会员卡套餐订单导出
     * @param Request $request
     */
    public function memberOrders(Request $request)
    {
        $data = $request->only([
            'status', 'start_date', 'end_date', 'store', 'mobile', 'order_no', 'pay_no',
            'package_name', 'payment_type', 'price_start', 'price_end', 'exception_status'
        ]);

        $builder = DB::table('order as o')
            ->leftJoin('bus_stores as bs', 'bs.id', '=', 'o.store_id')
            ->leftJoin(config('tables.base') . '.users as u', 'u.id', '=', 'o.userid')
            ->where('o.type', 3)
            ->where('o.from', 1);

        $fields = [
            DB::raw('FROM_UNIXTIME(o.addtime) AS create_date'),
            DB::raw("CASE WHEN o.payment_type = 1 THEN '支付宝' WHEN o.payment_type = 2 THEN '微信APP支付' WHEN o.payment_type = 3 THEN '微信公众号支付' END AS payment_type"),
            'o.order_id', 'o.pay_no', 'bs.name as store_name', 'o.vip_package_name', 'o.price', 'o.pay_price',
            DB::raw('IF(o.cash_ticket_platform = 1,ticket_denomination,0) AS admin_cash'),
            DB::raw('ROUND(o.score_uses / 100) AS score_discount'),
            DB::raw('IF(o.discount_ticket_platform = 1,ticket_discount,0) AS admin_discount'),
            DB::raw('IF(o.discount_ticket_platform = 0,ticket_discount,0) AS business_discount'),
            DB::raw("CASE WHEN o.status = 0 THEN '待付款' WHEN o.status = 1 THEN '已支付' WHEN o.status = 2 THEN '已到账' WHEN o.status = 3 THEN '已过期' WHEN o.status = 5 THEN '已退款' WHEN o.status = 6 THEN '已取消' END AS status"),
            DB::raw("IF(o.sync_flag = 0, '正常', '异常') AS '异常状态'"),
            'u.mobile as username', 'o.member_number', 'o.member_name',
        ];

        /* 条件筛选 */
        $status = intval($data['status']);

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
        // 时间筛选
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
        // 门店名搜索
        if ($data['store']) {
            $storeIds = DB::table('bus_stores')
                ->where('name', 'like', '%' . $data['store'] . '%')
                ->whereIn('id', $this->storeIds)
                ->lists('id');
            $builder->whereIn('o.store_id', $storeIds);
        } else {
            $builder->whereIn('o.store_id', $this->storeIds);
        }
        // 用户名筛选
        if (!empty($data['mobile'])) {
            $builder->where('u.mobile', $data['mobile']);
        }
        // 订单号搜索
        if (!empty($data['order_no'])) {
            $builder->where('o.order_id', $data['order_no']);
        }
        // 交易号查询
        if (!empty($data['pay_no'])) {
            $builder->where('o.pay_no', $data['pay_no']);
        }
        //套餐名称筛选
        if (!empty($data['package_name'])) {
            $builder->where('vip_package_name', 'like', '%' . $data['package_name'] . '%');
        }
        // 支付平台筛选
        if (!empty($data['payment_type'])) {
            $builder->where('o.payment_type', $data['payment_type']);
        }
        // 订单价格筛选
        if (!empty($price_start) && empty($data['price_end'])) {
            $builder->where('o.price', '>=', $data['price_start']);
        } elseif (empty($data['price_start']) && !empty($data['price_end'])) {
            $builder->where('o.price', '<=', round(floatval($data['price_end']), 2));
        } elseif (!empty($data['price_start']) && !empty($data['price_end'])) {
            $builder->where('o.price', '>=', $data['price_start'])->where('o.price', '<=', round(floatval($data['price_end']), 2));
        }
        // 异常状态筛选
        if (intval($data['exception_status'])) {
            $exceptionStatus = $data['exception_status'];
            $builder->where('o.sync_flag', $exceptionStatus - 1);
        }

        $res = $builder->select($fields)->orderBy('o.addtime', 'desc')->get();

        // 初始化导出处理器
        $handler = new ExcelProcessor();
        $header = [
            '创建时间', '支付渠道', '订单号', '交易号', '门店', '套餐名称', '原价', '实收（+元）', '平台现金券优惠（-元）',
            '平台积分抵扣（-元）', '门店现金券优惠（-元）', '门店折扣优惠（-元）', '状态', '异常状态', '用户名', '会员卡号', '会员姓名'
        ];
        $filename = date('Y\-m\-d H：i：s') . ' - 会员卡交易明细导出订单.xlsx';
        $handler->setHeader($header)->setData($res)->download($filename);
    }

    /**
     * 交易汇总导出
     * @param Request $request
     */
    public function tradeSummary(Request $request)
    {
        $data = $request->only('start_date', 'end_date', 'store_name', 'payment_type');

        $fields = [
            DB::raw("CONCAT(b.name, '（ ', bs.name, ' ）') AS store_name"),   // 品牌（门店）
            DB::raw("ROUND(SUM(IF(o.type = 2, pay_price, 0)), 2) AS package_income"),   // 非会员/会员套餐总营收
            DB::raw("SUM(IF(o.type = 2, 1, null)) AS package_count"),   // 非会员/会员套餐总订单笔数
            DB::raw("ROUND(SUM(IF(o.type = 2 AND o.status = 2, o.pay_price, 0)), 2) AS converted_package_income"),//非会员套餐已兑换营收
            DB::raw("SUM(IF(o.type = 2 AND o.status = 2, 1, null)) AS converted_package_count"),//非会员套餐已兑换笔数
            DB::raw("ROUND(SUM(IF(o.type = 2 AND o.status = 1, o.pay_price, 0)), 2) AS unconverted_package_income"),//非会员套餐未兑换营收
            DB::raw("SUM(IF(o.type = 2 AND o.status = 1, 1, null)) AS unconverted_package_count"),//非会员套餐未兑换笔数
            DB::raw("ROUND(SUM(IF(o.type = 3 AND o.status = 2, o.pay_price, 0)), 2) AS member_package_income"),// 会员套餐已兑换营收
            DB::raw("SUM(IF(o.type = 3 AND o.status = 2, 1, null)) AS member_package_count"),// 会员套餐已兑换笔数
        ];

        $builder = DB::table('order as o')
            ->where('o.from', 1)
            ->whereIn('o.status', [1, 2])
            ->whereIn('o.type', [2, 3]);

        // 条件筛选
        if (strtotime($data['start_date']) && !strtotime($data['end_date'])) {
            $builder->where('o.addtime', '>=', strtotime($data['start_date']));
        } elseif (!strtotime($data['start_date']) && strtotime($data['end_date'])) {
            $builder->where('o.addtime', '<=', strtotime($data['end_date']));
        } elseif (strtotime($data['start_date']) && strtotime($data['end_date'])) {
            $builder->whereBetween('o.addtime', [strtotime($data['start_date']), strtotime($data['end_date'])]);
        }


        if ($data['store_name']) {
            $sids = DB::table('bus_stores')->whereIn('id', $this->storeIds)
                ->where('name', 'like', '%' . $data['store_name'] . '%')->lists('id');
            $builder->join('bus_stores as bs', function ($join) use ($sids) {
                $join->on('bs.id', '=', 'o.store_id')->whereIn('bs.id', $sids);
            });
        } else {
            $builder->join('bus_stores as bs', function ($join) {
                $join->on('bs.id', '=', 'o.store_id')->whereIn('bs.id', $this->storeIds);
            });
        }

        $builder->leftJoin(config('tables.base') . '.brand as b', 'b.id', '=', 'bs.brand_id');


        if (intval($data['payment_type'])) {
            $builder->where('o.payment_type', $data['payment_type']);
        }

        $res = $builder->select($fields)->groupBy('o.store_id')->get();
        $header = [
            '门店', '套餐总营收（元）', '套餐笔数', '套餐已兑换营收（元）', '套餐已兑换笔数',
            '套餐未兑换营收（元）', '套餐未兑换笔数（元）', '会员卡套餐总营收（元）', '会员卡套餐笔数'
        ];
        $filename = date('Y-m-d H：i：s') . ' - 交易明细.xlsx';

        $handler = new ExcelProcessor();
        $handler->setHeader($header)->setData($res)->download($filename);

    }

    /**
     * VR机台营收订单导出
     * @param Request $request
     */
    public function vrOrders(Request $request)
    {
        $data = $request->only('start_date', 'end_date', 'store_id');

        $fields = [
            'vo.create_date', 'vo.pay_date', 'vo.use_date', 'vo.no', 'vo.pay_no', 'vo.machine_name', 'vo.game_name',
            'bs.name as store_name', 'u.mobile as username',
            DB::raw("CASE WHEN vo.status = 0 THEN '未使用' WHEN vo.status = 1 THEN '已启动' WHEN vo.status = 2 THEN '游戏中' WHEN vo.status = 3 THEN '已使用' END AS status"),
            DB::raw('IF(vo.is_pay = 1, "是", "否") AS is_pay'),
            DB::raw('ROUND(vo.amount/100,2) AS amount'),
            DB::raw('ROUND(vo.consume_amount/100,2) AS consume_amount'),
            DB::raw('ROUND((vo.amount - vo.consume_amount)/100, 2)'),
            'vo.vr_charge',
            DB::raw('IF(vo.status = 3, (ROUND(vo.consume_amount/100,2) - vo.vr_charge), 0) AS real_earned_amount'),
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

        $list = $builder
            ->leftJoin(config('tables.base') . '.users as u', 'u.id', '=', 'vo.userid')
            ->leftJoin('bus_stores as bs', 'bs.id', '=', 'vo.store_id')
            ->select($fields)
            ->orderBy('vo.create_date', 'desc')->get();

        $handler = new ExcelProcessor();
        $header = [
            '创建时间', '支付时间', '消费时间', '订单号', '交易号', '机台名称', '游戏名称', '消费门店',
            '买家', '游戏状态', '是否已经支付', '订单金额', '已消费金额', '未消费金额', '服务费', '实收金额'
        ];
        $sheetTitle = 'VR机台营收';
        $filename = date('Y-m-d H：i：s') . ' - VR机台营收.xlsx';

        $handler->setHeader($header)->setSheetTitle($sheetTitle)->setData($list)->download($filename);
    }

    /**
     * 智联宝机台（线上）订单导出
     * @param Request $request
     */
    public function smartLinkOrders(Request $request)
    {
        $data = $request->only('start_date', 'end_date');

        $builder = DB::table('iot_order as io')
            ->where('io.is_pay', 1)
            ->whereIn('io.status', [2, 3])
            ->join('iot_machine as im', 'im.id', '=', 'io.machine_id')
            ->join('iot_product as ip', 'ip.id', '=', 'im.product_id')
            ->leftJoin('iot_dev as ide', 'ide.id', '=', 'im.dev_id')
            ->leftJoin(config('tables.base') . '.users as u', 'u.id', '=', 'io.userid')
            ->join('bus_stores as bs', function ($join) {
                $join->on('bs.id', '=', 'ip.store_id')->whereIn('bs.id', $this->storeIds);
            });

        $fields = [
            'bs.name as store_name', 'im.name as im.chine_name', 'ide.serial_no', 'io.no',
            DB::raw("CASE WHEN io.pay_type = 1 THEN '支付宝' WHEN io.pay_type = 2 THEN '微信APP' WHEN io.pay_type = 3 THEN '微信公众号' WHEN io.pay_type = 4 THEN '游币' END AS pay_type"),
            'io.pay_no',
            DB::raw("CASE io.status WHEN 1 THEN '待付款' WHEN 2 THEN '未使用' WHEN 3 THEN '已使用' END AS use_status"),
            'u.mobile',
            DB::raw('ROUND(io.coin_price / 100, 2) AS coin_price'),
            'io.coin_qty',
            DB::raw('ROUND(io.coin_price * io.coin_qty / 100, 2) AS round_price'),
            'io.num',
            DB::raw('io.coin_qty * io.num'),
            DB::raw('ROUND(io.coin_price * io.coin_qty * io.num / 100, 2) AS price'),
            'io.pay_date'
        ];

        if (strtotime($data['start_date']) && !strtotime($data['end_date'])) {
            $builder->where('io.create_date', '>=', $data['start_date']);
        } elseif (!strtotime($data['start_date']) && strtotime($data['end_date'])) {
            $builder->where('io.create_date', '<=', $data['end_date']);
        } elseif (strtotime($data['start_date']) && strtotime($data['end_date'])) {
            $builder->whereBetween('io.create_date', [$data['start_date'], $data['end_date']]);
        }

        $res = $builder->select($fields)->orderBy('io.id', 'desc')->get();
        $header = [
            '门店', '机台', '序列号', '订单号', '支付类型', '交易号', '使用状态',
            '买家', '币单价', '每局币数', '局单价', '局数', '投币数', '金额', '支付时间'
        ];
        $sheetTitle = '智联宝机台营收';
        $filename = date('Y-m-d H：i：s') . ' - 智联宝机台营收.xlsx';
        $handler = new ExcelProcessor();
        $handler->setHeader($header)->setSheetTitle($sheetTitle)->setData($res)->download($filename);

    }

    /**
     * 会员管理（明细）导出
     * @param Request $request
     */
    public function members(Request $request)
    {
        $sids = implode(',', $this->storeIds);
        $fields = [
            'u.mobile', 'sr.score', 'c.coin',
            DB::raw('(SELECT convert_time FROM `order` WHERE userid = u.id AND store_id IN(' . $sids . ') ORDER BY convert_time DESC LIMIT 1) AS last_consume_time'),
            'ut.update_date as last_login_time',
            't.total_consume_amount',
            't.total_consume_count',
            DB::raw('ROUND(t.total_consume_amount / t.total_consume_count, 2) AS average_consume'),
        ];

        $data = $request->only(['price_start', 'price_end', 'from', 'count_start', 'count_end',
            'card_bind', 'account', 'convert_start', 'convert_end'
        ]);

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

        $members = $builder->select($fields)->groupBy('u.id')->orderBy('u.id', 'desc')->get();
        $handler = new ExcelProcessor();
        $header = [
            '会员账号', '积分', '游币', '最后消费时间', '最后登录时间', '累计消费金额（元）', '累计消费次数', '平均消费金额（元）'
        ];
        $filename = date('Y-m-d H：i：s') . ' - 会员明细.xlsx';

        $handler->setHeader($header)->setData($members)->download($filename);

    }

    /**
     * 套餐分析导出
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function packageAnalysis(Request $request)
    {
        $data = $request->only('start_date', 'end_date', 'money', 'orders', 'payers');

        if (!$this->storeIds) {
            return view('business.package-analysis', [
                'start_date' => $data['start_date'] ? $data['start_date'] : '',
                'end_date' => $data['end_date'] ? $data['end_date'] : '',
                'packages' => [],
                'money' => 0,
                'orders' => 0,
                'payers' => 0,
            ]);
        }

        $sids = implode(',', $this->storeIds);

        $fields = [
            'p.name',
            DB::raw('CONCAT(b.name, "（", bs.name, "）") AS store_name'),
            'o2.order_sum',
            'o2.success_order_count',
            'o2.payed_order_count',
            'o2.unpayed_order_count',
            DB::raw("CONCAT((ROUND(( IF(o2.payed_order_count IS NOT NULL, o2.payed_order_count, 0) / o2.unpayed_order_count) * 100,2)),'%') AS pay_percent"),
        ];

        if ($data['start_date'] && !$data['end_date']) {
            $subCondition = 'AND addtime >= ' . strtotime($data['start_date']);
        } elseif (!$data['start_date'] && $data['end_date']) {
            $subCondition = 'AND addtime <= ' . strtotime($data['end_date']);
        } elseif ($data['start_date'] && $data['end_date']) {
            $subCondition = 'AND addtime BETWEEN ' . strtotime($data['start_date']) . ' AND ' . strtotime($data['end_date']);
        } else {
            $subCondition = '';
        }

        $joinSql3 = DB::raw('(
            SELECT
                good_id, addtime,
                ROUND(SUM(IF(`status` = 2,price,0)),2) AS order_sum,
                SUM(IF(`status` = 2,1,0)) AS success_order_count,
                SUM(IF(`status` = 1 OR `status` = 2 OR `status` = 5 OR (`status` = 3 AND `pay_date` > 0),1,0)) AS payed_order_count,
                SUM(1) AS unpayed_order_count
            FROM `order`
            WHERE `type` = 2 AND `status` IN(0,1,2)
            AND store_id IN(' . $sids . ')
            ' . $subCondition . '
            GROUP BY good_id) AS o2
        ');

        $builder = DB::table('packages as p')
            ->where('p.userid', $this->parentUserId)
            ->join('package_store_relation as psr', function ($join) {
                $join->on('psr.package_id', '=', 'p.id')
                    ->whereIn('psr.store_id', $this->storeIds);
            })
            ->leftJoin($joinSql3, 'o2.good_id', '=', 'p.id')
            ->leftJoin('bus_stores as bs', 'bs.id', '=', 'psr.store_id')
            ->leftJoin(config('tables.base') . '.brand as b', 'b.id', '=', 'bs.brand_id')
            ->select($fields);

        // 排序控制
        if (!$data['money'] && !$data['orders'] && !$data['payers']) {
            $builder->orderBy('p.id', 'desc');
        } else {
            if ($data['money'] && $data['money'] == 1) {
                $builder->orderBy('order_sum', 'asc');
            } elseif ($data['money'] && $data['money'] == 2) {
                $builder->orderBy('order_sum', 'desc');
            }

            if ($data['orders'] && $data['orders'] == 1) {
                $builder->orderBy('success_order_count', 'asc');
            } elseif ($data['orders'] && $data['orders'] == 2) {
                $builder->orderBy('success_order_count', 'desc');
            }

            if ($data['payers'] && $data['payers'] == 1) {
                $builder->orderBy('payed_order_count', 'asc');
            } elseif ($data['payers'] && $data['payers'] == 2) {
                $builder->orderBy('payed_order_count', 'desc');
            }
        }

        $res = $builder->get();
        $handler = new ExcelProcessor();
        $header = ['套餐名称', '门店名称', '成交金额', '成交笔数', '支付人数', '下单人数', '支付转化率'];
        $filename = date('Y-m-d H：i：s') . ' - 套餐分析.xlsx';

        $handler->setHeader($header)->setData($res)->download($filename);

    }

    /**
     * 卡券分析 - 优惠券分析 - 导出
     * @param Request $request
     */
    public function ticketAnalysis(Request $request)
    {
        $data = $request->only('start_date', 'end_date');
        if ($data['start_date'] || $data['end_date']) {
            $sd = strtotime($data['start_date']);
            $ed = strtotime($data['end_date']);
        }

        $fields = [
            't.name',
            DB::raw('COUNT(tgr.id) as got_count'),
            DB::raw('COUNT(o.discount_ticket) as used_count'),
            DB::raw("CONCAT( ROUND(COUNT(o.discount_ticket)/COUNT(tgr.id), 2), '%') AS percent"),
        ];

        $builder = DB::table('ticket as t')
            ->where('t.type', 2)
            ->where('t.is_delete', 0)
            ->where('t.userid', $this->parentUserId)
            ->join('ticket_extend as te', function ($join) {
                $join->on('te.ticket_id', '=', 't.id')->whereIn('te.store_id', $this->storeIds);
            })
            ->leftJoin('ticket_get_record as tgr', 'tgr.ticket_id', '=', 't.id')
            ->leftJoin('order as o', 'o.discount_ticket', '=', 't.id')
            ->groupBy('t.id');

        if (!empty($sd) && !empty($ed)) {
            $builder->whereBetween('t.addtime', [$sd, $ed]);
        }

        $list = $builder->select($fields)->get();
        $header = ['优惠券名称', '领取份数', '核销份数', '核销率'];
        $filename = date('Y-m-d H：i：s') . ' - 卡券分析.xlsx';
        $handler = new ExcelProcessor();

        $handler->setHeader($header)->setData($list)->download($filename);

    }

}

@extends('business.layouts.frame-parent')
@section('page-title','商品交易明细')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>商品交易查询</h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <form action="{{ route('business.order-list') }}" method="get">
                            <div class="col-sm-10">
                                <div class="row m-b-xs">
                                    <div class="col-sm-12">
                                        <div class="btn-group">
                                            <button type="button" data-id="0"
                                                    class="btn switch-status @if($status == 0) btn-success @else btn-white @endif ">
                                                全部
                                            </button>
                                            <button type="button" data-id="1"
                                                    class="btn switch-status @if($status == 1) btn-success @else btn-white @endif ">
                                                待付款
                                            </button>
                                            <button type="button" data-id="2"
                                                    class="btn switch-status @if($status == 2) btn-success @else btn-white @endif ">
                                                未兑换
                                            </button>
                                            <button type="button" data-id="3"
                                                    class="btn switch-status @if($status == 3) btn-success @else btn-white @endif ">
                                                已支付
                                            </button>
                                            <button type="button" data-id="4"
                                                    class="btn switch-status @if($status == 4) btn-success @else btn-white @endif ">
                                                已兑换
                                            </button>
                                            <button type="button" data-id="5"
                                                    class="btn switch-status @if($status == 5) btn-success @else btn-white @endif ">
                                                已过期
                                            </button>
                                            <button type="button" data-id="6"
                                                    class="btn switch-status @if($status == 6) btn-success @else btn-white @endif ">
                                                已退款
                                            </button>
                                            <button type="button" data-id="7"
                                                    class="btn switch-status @if($status == 7) btn-success @else btn-white @endif ">
                                                已取消
                                            </button>
                                            <input type="hidden" name="status" value="{{ $status }}">
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="row form-horizontal">
                                    <div class="col-sm-6 m-b-xs">
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">时间</label>
                                            <div class="col-sm-9">
                                                <div class="input-daterange input-group">
                                                    <input type="text" class="form-control" name="start_date"
                                                           @if(!empty($start_date)) value="{{ $start_date }}"
                                                           @endif id="get_start">
                                                    <span class="input-group-addon">至</span>
                                                    <input type="text" class="form-control" name="end_date"
                                                           @if(!empty($end_date)) value="{{ $end_date }}"
                                                           @endif id="get_end">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <button type="button" data-id="1"
                                                class="btn btn-range @if($range == 1) btn-success @else btn-white @endif ">
                                            今日
                                        </button>
                                        <button type="button" data-id="2"
                                                class="btn btn-range @if($range == 2) btn-success @else btn-white @endif ">
                                            昨日
                                        </button>
                                        <button type="button" data-id="3"
                                                class="btn btn-range @if($range == 3) btn-success @else btn-white @endif ">
                                            最近7天
                                        </button>
                                        <button type="button" data-id="4"
                                                class="btn btn-range @if($range == 4) btn-success @else btn-white @endif ">
                                            最近30天
                                        </button>
                                    </div>
                                </div>
                                <div class="">
                                    <div class="row form-horizontal">
                                        <div class="col-sm-6 m-b-xs">
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">门店</label>
                                                <div class="col-sm-9">
                                                    <input type="text" placeholder="搜索门店" name="store"
                                                           @if(!empty($store_name)) value="{{ $store_name }}"
                                                           @endif class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 m-b-xs">
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">用户</label>
                                                <div class="col-sm-9">
                                                    <input type="text" name="mobile" value="{{ $mobile }}"
                                                           placeholder="请输入用户手机号" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row form-horizontal">
                                        <div class="col-sm-6 m-b-xs">
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">订单号</label>
                                                <div class="col-sm-9">
                                                    <input type="text" name="order_no" value="{{ $order_no }}"
                                                           placeholder="" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 m-b-xs">
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">交易号</label>
                                                <div class="col-sm-9">
                                                    <input type="text" name="pay_no" value="{{ $pay_no }}"
                                                           placeholder="" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row form-horizontal">
                                        <div class="col-sm-6 m-b-xs">
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">套餐名称</label>
                                                <div class="col-sm-9">
                                                    <input type="text" name="package_name" value="{{ $package_name }}"
                                                           placeholder="" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 m-b-xs">
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">支付平台</label>
                                                <div class="col-sm-9">
                                                    <select class="form-control" name="payment_type">
                                                        <option value="0" @if($payment_type == 0) selected @endif >不限
                                                        </option>
                                                        <option value="1" @if($payment_type == 1) selected @endif >支付宝
                                                        </option>
                                                        <option value="2" @if($payment_type == 2) selected @endif >
                                                            微信支付
                                                        </option>
                                                        <option value="3" @if($payment_type == 3) selected @endif >
                                                            微信公众号
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row form-horizontal">
                                        <div class="col-sm-6 m-b-xs">
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">金额</label>
                                                <div class="col-sm-9">
                                                    <div class="input-daterange input-group">
                                                        <input type="text" name="price_start" value="{{ $price_start }}"
                                                               class="form-control">
                                                        <span class="input-group-addon">至</span>
                                                        <input type="text" name="price_end" value="{{ $price_end }}"
                                                               class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 m-b-xs">
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">兑换途径</label>
                                                <div class="col-sm-9">
                                                    <select class="form-control" name="convert_type">
                                                        <option value="0" @if($convert_type == 0) selected @endif>全部
                                                        </option>
                                                        <option value="1" @if($convert_type == 1) selected @endif>商户
                                                        </option>
                                                        <option value="2" @if($convert_type == 2) selected @endif>提币机
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="col-sm-2 m-b-xs text-right">
                                <button class="btn btn-primary btn-lg" type="submit">
                                    <i class="fa fa-search"></i>
                                </button>
                                <button id="export" data-url="{{ route('business.package-order-export') }}"
                                        class="btn btn-lg btn-success" type="button">
                                    <i class="fa fa-download"></i>
                                </button>
                            </div>
                        </form>
                    </div>


                    <div class="row">
                        <div class="col-sm-3">
                            <div class="widget yellow-bg">
                                <div class="row">
                                    <div class="col-xs-4">
                                        <i class="fa fa-area-chart fa-2x"></i>
                                    </div>
                                    <div class="col-xs-8 text-right">
                                        <span> 总金额 </span>
                                        <h2 class="font-bold">￥{{ $sum->total }}</h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="widget navy-bg">
                                <div class="row">
                                    <div class="col-xs-4">
                                        <i class="fa fa-hourglass-half fa-2x"></i>
                                    </div>
                                    <div class="col-xs-8 text-right">
                                        <span> 已兑换 </span>
                                        <h2 class="font-bold">￥{{ $sum->converted }}</h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="widget lazur-bg">
                                <div class="row">
                                    <div class="col-xs-4">
                                        <i class="fa fa-credit-card fa-2x"></i>
                                    </div>
                                    <div class="col-xs-8 text-right">
                                        <span> 未兑换 </span>
                                        <h2 class="font-bold">￥{{ $sum->unconverted }}</h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="widget yellow-bg">
                                <div class="row">
                                    <div class="col-xs-4">
                                        <i class="fa fa-pie-chart fa-2x"></i>
                                    </div>
                                    <div class="col-xs-8 text-right">
                                        <span> 记录数 </span>
                                        <h2 class="font-bold">{{ $sum->count }}</h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>创建时间</th>
                                <th>套餐/门店</th>
                                <th>单号</th>
                                <th>买家 / 购买数量</th>
                                <th>支付时间</th>
                                <th>订单金额(元)</th>
                                <th>状态</th>
                                <th>兑换</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($orders))
                                @foreach($orders as $order)
                                    <tr>
                                        <td>{{ date('Y-m-d',$order->addtime) }}<br/>{{ date('H:i:s',$order->addtime) }}
                                        </td>
                                        <td>{{ $order->good_name }}
                                            <br/>门店: {{ $order->store_name }}
                                        </td>
                                        <td>订单号:{{ $order->order_id }}
                                            <br/> 交易号: {{ $order->pay_no }}
                                            @if($order->payment_type == 1)
                                                <span class="label label-success">支付宝</span>
                                            @elseif($order->payment_type == 2)
                                                <span class="label label-success">微信支付</span>
                                            @elseif($order->payment_type == 3)
                                                <span class="label label-success">微信公众号</span>
                                            @endif
                                        </td>
                                        <td>
                                            买家：@if( !empty($order->username) ) {{ $order->username }}
                                            @elseif( !empty($order->mobile) ) {{ $order->mobile }}
                                            @endif
                                            <br>
                                            购买数量：{{ $order->number }}
                                        </td>
                                        <td>@if(!empty($order->pay_date)){{ date('Y-m-d H:i:s',$order->pay_date) }}@endif</td>
                                        <td>
                                            原价：{{ $order->price }}
                                            元&nbsp;&nbsp;&nbsp;&nbsp;平台现金券优惠：{{ $order->admin_cash }}<br/>
                                            实收：{{ $order->pay_price }}
                                            元&nbsp;&nbsp;&nbsp;&nbsp;门店现金券优惠：{{ $order->bus_cash }}<br/>
                                            优惠：{{ ($order->price - $order->pay_price) }} 元&nbsp;&nbsp;&nbsp;&nbsp;门店折扣优惠：{{ $order->bus_discount }}
                                            <br>积分优惠：{{ round($order->score_uses/100,2) }}
                                        </td>
                                        <td>
                                            @if($order->status == 0)待付款
                                            @elseif($order->status == 1)未兑换
                                            @elseif($order->status == 2)已兑换
                                            @elseif($order->status == 3)已过期
                                            @elseif($order->status == 5)已退款
                                            @elseif($order->status == 6)已取消
                                            @endif
                                        </td>
                                        <td>
                                            @if(!empty($order->convert_user)) 已兑换 @else 未兑换 @endif
                                            <br/>操作员：{{ $order->convert_user }}
                                            <br/>提币机：{{ $order->convert_machine }}
                                            <br/>时间：@if(!empty($order->convert_time)){{ date('Y-m-d H:i:s',$order->convert_time) }}@endif
                                        </td>
                                        <td>
                                            @if($order->status == 1)
                                                <a href="{{ route('business.order-deposit',['id'=>$order->id]) }}"
                                                   class="btn btn-sm btn-warning">退款</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="text-right">
                        @if(!empty($orders->links()))
                            {{ $orders->appends([
                                'start_date'=>$start_date,
                                'end_date'=>$end_date,
                                'store'=>$store_name,
                                'order_no'=>$order_no,
                                'package_name'=>$package_name,
                                'price_start'=>$price_start,
                                'price_end'=>$price_end,
                                'range'=>$range,
                                'mobile'=>$mobile,
                                'pay_no'=>$pay_no,
                                'payment_type'=>$payment_type,
                                'status'=>$status,
                                'convert_type'=>$convert_type
                            ])->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/business/js/plugins/layer/laydate/laydate.js"></script>
    <script src="/business/js/util.date.js"></script>
    <script type="text/javascript">
        $(function () {

            var get_start = {
                elem: "#get_start",
                format: "YYYY-MM-DD hh:mm:ss",
                min: '2010-01-01',
                max: "2099-06-16 23:59:59",
                istime: true,
                istoday: false,
                choose: function (datas) {
                    get_end.min = datas;
                    get_end.start = datas;
                }
            };
            var get_end = {
                elem: "#get_end",
                format: "YYYY-MM-DD hh:mm:ss",
                min: '2010-01-01',
                max: "2099-06-16 23:59:59",
                istime: true,
                istoday: false,
                choose: function (datas) {
                    get_start.max = datas
                }
            };
            laydate(get_start);
            laydate(get_end);

            // 今天、昨天...日期选择
            $('.btn-range').click(function () {
                $(this).siblings('.btn-range').removeClass('btn-success').addClass('btn-white');
                $(this).removeClass('btn-white').addClass('btn-success');
                var $startDate = $('input[name=start_date]');
                var $endDate = $('input[name=end_date]');
                var date = new Date();
                switch ($(this).data('id')) {
                    case 1:
                        $startDate.val(date.formatDate("yyyy-MM-dd") + ' 00:00:00');
                        $endDate.val(date.formatDate('yyyy-MM-dd 23:59:59'));
                        break;
                    case 2:
                        $startDate.val(new Date(date.getTime() - (24 * 3600 * 1000)).formatDate("yyyy-MM-dd") + ' 00:00:00');
                        $endDate.val(new Date(date.getTime() - (24 * 3600 * 1000)).formatDate("yyyy-MM-dd") + ' 23:59:59');
                        break;
                    case 3:
                        $startDate.val(new Date(date.getTime() - (7 * 24 * 3600 * 1000)).formatDate("yyyy-MM-dd") + ' 00:00:00');
                        $endDate.val(date.formatDate('yyyy-MM-dd 23:59:59'));
                        break;
                    case 4:
                        $startDate.val(new Date(date.getTime() - (30 * 24 * 3600 * 1000)).formatDate("yyyy-MM-dd") + ' 00:00:00');
                        $endDate.val(date.formatDate('yyyy-MM-dd 23:59:59'));
                        break;
                }
            });

            // 订单状态选择
            $('.switch-status').click(function () {
                $(this).siblings('.switch-status').removeClass('btn-success').addClass('btn-white');
                $(this).removeClass('btn-white').addClass('btn-success');
                $('input[name=status]').val($(this).data('id'));
            });

            // 订单导出
            $('#export').click(function () {
                $('form').attr('action', $(this).data('url')).submit().attr('action', location.href);
            });

        });
    </script>
@endsection


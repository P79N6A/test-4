@extends('business.layouts.frame-parent')
@section('page-title','交易汇总')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>交易汇总</h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <form action="{{ route('business.trade-summary') }}" method="get">
                            <div class="col-sm-10">
                                <div class="row form-horizontal">
                                    <div class="col-sm-6 m-b-xs">
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">订单创建时间</label>
                                            <div class="col-sm-9">
                                                <div class="input-daterange input-group">
                                                    <input type="text" class="form-control" name="start_date"
                                                           value="@if(!empty($startDate)){{ $startDate }}@endif"
                                                           id="get_start">
                                                    <span class="input-group-addon">至</span>
                                                    <input type="text" class="form-control" name="end_date"
                                                           value="@if(!empty($endDate)){{ $endDate }}@endif"
                                                           id="get_end">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <button type="button"
                                                class="btn btn-range @if(!empty($range) && $range == 1) btn-success @else btn-white @endif ">
                                            今日
                                        </button>
                                        <button type="button"
                                                class="btn btn-range @if(!empty($range) && $range == 2) btn-success @else btn-white @endif ">
                                            昨日
                                        </button>
                                        <button type="button"
                                                class="btn btn-range @if(!empty($range) && $range == 3) btn-success @else btn-white @endif ">
                                            最近7天
                                        </button>
                                        <button type="button"
                                                class="btn btn-range @if(!empty($range) && $range == 4) btn-success @else btn-white @endif ">
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
                                                    <input type="text" name="store_name"
                                                           value="@if(!empty($storeName)){{ $storeName }}@endif"
                                                           placeholder="搜索门店" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 m-b-xs">
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">支付平台</label>
                                                <div class="col-sm-6">
                                                    <select class="form-control" name="payment_type">
                                                        <option value="0" @if($paymentType == 0) selected @endif >不限
                                                        </option>
                                                        <option value="1" @if($paymentType == 1) selected @endif >支付宝
                                                        </option>
                                                        <option value="2" @if($paymentType == 2) selected @endif >
                                                            微信支付
                                                        </option>
                                                        <option value="3" @if($paymentType == 3) selected @endif >
                                                            微信公众号
                                                        </option>
                                                    </select>
                                                </div>
                                                <div class="col-sm-3">
                                                    <button id="export"
                                                            data-url="{{ route('business.trade-summary-export') }}"
                                                            class="btn btn-success" type="button">导出
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-2 m-b-xs text-right">
                                <button class="btn btn-primary btn-lg" type="submit"><i class="fa fa-search"></i>
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
                                        <h2 class="font-bold">￥@if(!empty($sum)){{ $sum->total }}@else 0 @endif</h2>
                                        套餐：￥@if(!empty($sum)){{ $sum->package_total }}@else 0 @endif<br>
                                        会员卡：￥@if(!empty($sum)){{ $sum->member_total }} @else 0 @endif
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
                                        <h2 class="font-bold">￥@if(!empty($sum)){{ $sum->converted }}@else 0 @endif</h2>
                                        套餐：￥@if(!empty($sum)){{ $sum->package_converted }}@else 0 @endif<br>
                                        会员卡：￥@if(!empty($sum)){{ $sum->member_converted }}@else 0 @endif
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
                                        <h2 class="font-bold">￥@if(!empty($sum)){{ $sum->unconverted }}@else
                                                0 @endif</h2>
                                        套餐：￥@if(!empty($sum)){{ $sum->package_unconverted }}@else 0 @endif<br>
                                        会员卡：￥@if(!empty($sum)){{ $sum->member_unconverted }}@else 0 @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>门店</th>
                                <th>总营收</th>
                                <th>已兑换</th>
                                <th>未兑换</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($summary))
                                @foreach($summary as $item)
                                    <tr>
                                        <td>{{ $item->brand_name }}（{{ $item->store_name }}）</td>
                                        <td>
                                            <p>
                                                <span class="label label-info">套餐</span> ￥{{ $item->package_total }}
                                                &nbsp;&nbsp;笔数：{{ $item->package_total_count }}
                                            </p>
                                            <p>
                                                <span class="label label-success">会员卡</span> ￥{{ $item->member_total }}
                                                &nbsp;&nbsp;笔数：{{ $item->member_total_count }}
                                            </p>
                                        </td>
                                        <td>
                                            <p>
                                                <span class="label label-info">套餐</span>￥{{ $item->package_converted }}
                                                &nbsp;&nbsp;笔数：{{ $item->package_converted_count }}
                                            </p>
                                            <p>
                                                <span class="label label-success">会员卡</span>￥{{ $item->member_converted }}
                                                &nbsp;&nbsp;笔数：{{ $item->member_converted_count }}
                                            </p>
                                        </td>
                                        <td>
                                            <p>
                                                <span class="label label-info">套餐</span>￥{{ $item->package_unconverted }}
                                                &nbsp;&nbsp;笔数：{{ $item->package_unconverted_count }}
                                            </p>
                                            <p>
                                                <span class="label label-success">会员卡</span>￥{{ $item->member_unconverted }}
                                                &nbsp;&nbsp;笔数：{{ $item->member_unconverted_count }}
                                            </p>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="text-right">
                        @if(!empty($summary))
                            {{ $summary->appends([
                                'start_date' => $startDate,
                                'end_date' => $endDate,
                                'range' => $range,
                                'store_name' => $storeName,
                                'payment_type' => $paymentType
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
                min: '2010-01-01 00:00:00',
                max: "2099-06-16 23:59:59",
                istime: true,
                istoday: false,
                choose: function (datas) {
                    get_end.min = datas;
                    get_end.start = datas
                }
            };
            var get_end = {
                elem: "#get_end",
                format: "YYYY-MM-DD hh:mm:ss",
                min: laydate.now(),
                max: "2099-06-16 23:59:59",
                istime: true,
                istoday: false,
                choose: function (datas) {
                    get_start.max = datas
                }
            };

            laydate(get_start);
            laydate(get_end);

            $('.btn-range').click(function () {
                $(this).siblings('.btn-range').removeClass('btn-success').addClass('btn-white');
                $(this).removeClass('btn-white').addClass('btn-success');
                var $startDate = $('input[name=start_date]');
                var $endDate = $('input[name=end_date]');
                var date = new Date();
                switch (($(this).index() + 1)) {
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

            $('#export').click(function () {
                $('form').attr('action', $(this).data('url')).submit().attr('action', location.href);
            });

        });
    </script>
@endsection

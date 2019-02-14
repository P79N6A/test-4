@extends('business.layouts.frame-parent')
@section('page-title','机台营收概况')
@section('main')
    <link rel="stylesheet" href="/admin/js/plugins/layer/laydate/skins/default/laydate.css">
    <div class="row">
        <div class="col-sm-4">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <span class="label label-success pull-right" id="total_machine"></span>
                    <h5>总机台数（绿色：在线；灰色：离线）</h5>
                </div>
                <div class="ibox-content">
                    <div class="text-center">
                        <div id="all"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>线上营收（昨天，今天，本月）</h5>
                </div>
                <div class="ibox-content">
                    <div class="flot-chart">
                        <div class="flot-chart-content" id="flot-bar-chart1"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>线下营收（昨天，今天，本月）</h5>
                </div>
                <div class="ibox-content">
                    <div class="flot-chart">
                        <div class="flot-chart-content" id="flot-bar-chart2"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{ csrf_field() }}
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>线上营收</h5>
                </div>
                <div class="ibox-content text-right">
                    <div class="form-group form-inline">
                        <input type="text" id="ons_date" name="ons_date" class="form-control" placeholder="开始时间"> -
                        <input type="text" id="one_date" name="one_date" class="form-control" placeholder="结束时间">
                    </div>
                </div>
            </div>
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <div class="flot-chart">
                        <div class="flot-chart-content" id="flot-line-chart1"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>线下营收</h5>
                </div>
                <div class="ibox-content text-right">
                    <div class="form-group form-inline">
                        <input type="text" id="offs_date" name="offs_date" class="form-control" placeholder="开始时间"> -
                        <input type="text" id="offe_date" name="offe_date" class="form-control" placeholder="结束时间">
                    </div>
                </div>
            </div>
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <div class="flot-chart">
                        <div class="flot-chart-content" id="flot-line-chart2"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <a name="mode"></a>
            <div class="btn-group btn-group-justified">
                <div class="btn-group">
                    <button type="button" class="btn btn-lg @if($mode == 1) btn-primary @else btn-white @endif btn-mode"
                            data-url="{{ route('business.machine-sales-overview',['mode'=>1]).'#mode' }}">时间
                    </button>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-lg @if($mode == 2) btn-primary @else btn-white @endif btn-mode"
                            data-url="{{ route('business.machine-sales-overview',['mode'=>2]).'#mode' }}">产品
                    </button>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-lg @if($mode == 3) btn-primary @else btn-white @endif btn-mode"
                            data-url="{{ route('business.machine-sales-overview',['mode'=>3]).'#mode' }}">机台
                    </button>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-lg @if($mode == 4) btn-primary @else btn-white @endif btn-mode"
                            data-url="{{ route('business.machine-sales-overview',['mode'=>4]).'#mode' }}">门店
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>每天营收统计营收</h5>
                </div>
                <div class="ibox-content text-right">
                    <div class="form-group form-inline">
                        <input type="text" id="sales_sdate" name="sales_sdate" value="{{ $s }}" class="form-control" placeholder="开始时间">
                        -
                        <input type="text" id="sales_edate" name="sales_edate" value="{{ $e }}" class="form-control" placeholder="结束时间">
                    </div>
                </div>
            </div>
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <table class="table table-bordered table-striped">
                        @if(!empty($res))
                            @if($mode == 1)
                                <thead>
                                <th>时间</th>
                                <th>交易数量（笔）</th>
                                <th>交易金额（元）</th>
                                </thead>
                                <tbody>
                                @foreach($res as $item)
                                    <tr>
                                        <td>{{ $item->create_date }}</td>
                                        <td>{{ $item->order_count }}</td>
                                        <td>{{ $item->price_sum }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            @elseif($mode == 2)
                                <thead>
                                <th>产品名称</th>
                                <th>交易数量（笔）</th>
                                <th>交易金额（元）</th>
                                </thead>
                                <tbody>
                                @foreach($res as $item)
                                    <tr>
                                        <td>{{ $item->product_name }}</td>
                                        <td>{{ $item->order_count }}</td>
                                        <td>{{ $item->price_sum }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            @elseif($mode == 3)
                                <thead>
                                <th>机台名称</th>
                                <th>机台序列号</th>
                                <th>交易数量（笔）</th>
                                <th>交易金额（元）</th>
                                </thead>
                                <tbody>
                                @foreach($res as $item)
                                    <tr>
                                        <td>{{ $item->machine_name }}</td>
                                        <td>{{ $item->serial_no }}</td>
                                        <td>{{ $item->order_count }}</td>
                                        <td>{{ $item->price_sum }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            @elseif($mode == 4)
                                <thead>
                                <th>门店名称</th>
                                <th>交易数量（笔）</th>
                                <th>交易金额（元）</th>
                                </thead>
                                <tbody>
                                @foreach($res as $item)
                                    <tr>
                                        <td>{{ $item->store_name }}</td>
                                        <td>{{ $item->order_count }}</td>
                                        <td>{{ $item->price_sum }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            @endif
                        @endif
                    </table>
                    <div class="text-right">
                        @if(!empty($res))
                            {{ $res->appends(['mode'=>$mode])->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/admin/js/plugins/sparkline/jquery.sparkline.min.js"></script>
    <script src="/admin/js/plugins/flot/jquery.flot.js"></script>
    <script src="/admin/js/plugins/layer/laydate/laydate.js"></script>
    <script>
        $(function () {

            $('.btn-mode').click(function () {
                location.href = $(this).data('url');
            });

            $.ajax({
                type: 'get',
                url: '{{ route('business.machine-sales-overview') }}',
                success: function (data) {
                    dealWithData(data);
                }
            });

            function dealWithData(data) {
                // 总机台数赋值
                $('#total_machine').text(data.percent[0].total_qty != undefined ? data.percent[0].total_qty : 0);

                // 饼状图
                $("#all").sparkline(
                    [
                        data.percent[0].online_qty != undefined ? data.percent[0].online_qty : 0,
                        data.percent[0].offline_qty != undefined ? data.percent[0].offline_qty : 0
                    ],
                    {
                        type: "pie",
                        width: '200',
                        height: "200",
                        sliceColors: ["#1ab394", "#F5F5F5"]
                    }
                );

                // 柱状图
                var option1 = {
                    series: {
                        bars: {
                            show: !0,
                            barWidth: .2,
                            fill: !0,
                            fillColor: {colors: [{opacity: .8}, {opacity: .8}]}
                        }
                    },
                    xaxis: {tickDecimals: 0},
                    yaxis: {min: 0},
                    colors: ["#1ab394"],
                    grid: {color: "#999999", hoverable: !0, clickable: !0, tickColor: "#D4D4D4", borderWidth: 0},
                    legend: {show: !1},
                    tooltip: !0,
                    tooltipOpts: {content: "x: %x, y: %y"}
                };
                var data1 = {
                    label: "bar", data: [
                        [1, data.yesterday[0].online_amount != undefined ? data.yesterday[0].online_amount : 0],
                        [2, data.today[0].online_amount != undefined ? data.today[0].online_amount : 0],
                        [3, data.thirtyDays[0].online_amount != undefined ? data.thirtyDays[0].online_amount : 0]
                    ]
                };
                $.plot($("#flot-bar-chart1"), [data1], option1);

                var option2 = {
                    series: {
                        bars: {
                            show: !0,
                            barWidth: .2,
                            fill: !0,
                            fillColor: {colors: [{opacity: .8}, {opacity: .8}]}
                        }
                    },
                    xaxis: {tickDecimals: 0},
                    yaxis: {min: 0},
                    colors: ["#1ab394"],
                    grid: {color: "#999999", hoverable: !0, clickable: !0, tickColor: "#D4D4D4", borderWidth: 0},
                    legend: {show: !1},
                    tooltip: !0,
                    tooltipOpts: {content: "x: %x, y: %y"}
                };
                var data2 = {
                    label: "bar", data: [
                        [1, data.yesterday[0].offline_qty != undefined ? data.yesterday[0].offline_qty : 0],
                        [2, data.today[0].offline_qty != undefined ? data.today[0].offline_qty : 0],
                        [3, data.thirtyDays[0].offline_qty != undefined ? data.thirtyDays[0].offline_qty : 0]
                    ]
                };
                $.plot($("#flot-bar-chart2"), [data2], option2);

                sales();
            }

            function sales() {
                var dataset3 = [];
                var dataset4 = [];
                $.ajax({
                    type: 'post',
                    url: "{{route('business.machine-sales-filter')}}",
                    data: {
                        _token: $('input[name=_token]').val(),
                        ons_date: $('#ons_date').val(),
                        one_date: $('#one_date').val(),
                        offs_date: $('#offs_date').val(),
                        offe_date: $('#offe_date').val()
                    },
                    success: function (data) {
                        if (data.online != undefined) {
                            $.each(data.online, function (index, value) {
                                dataset3.push([parseInt(value.day), value.total_amount]);

                                // 折线图
                                var option3 = {
                                    series: {
                                        lines: {
                                            show: !0,
                                            lineWidth: 2,
                                            fill: !0,
                                            fillColor: {colors: [{opacity: 0}, {opacity: 0}]}
                                        }
                                    },
                                    xaxis: {
                                        tickDecimals: 0,
                                    },
                                    yaxis:{
                                        min:0
                                    },
                                    colors: ["#1ab394"],
                                    grid: {
                                        color: "#999999",
                                        hoverable: !0,
                                        clickable: !0,
                                        tickColor: "#D4D4D4",
                                        borderWidth: 0
                                    },
                                    legend: {show: !1},
                                    tooltip: !0,
                                    tooltipOpts: {content: "x: %x, y: %y"}
                                };
                                var data3 = {
                                    label: "bar",
                                    data: dataset3
                                };
                                $.plot($("#flot-line-chart1"), [data3], option3);
                            });
                        }
                        if (data.offline != undefined) {
                            $.each(data.offline, function (index, value) {
                                dataset4.push([parseInt(value.day), value.total_qty]);

                                // 折线图
                                var option4 = {
                                    series: {
                                        lines: {
                                            show: !0,
                                            lineWidth: 2,
                                            fill: !0,
                                            fillColor: {colors: [{opacity: 0}, {opacity: 0}]}
                                        }
                                    },
                                    xaxis: {tickDecimals: 0},
                                    yaxis:{
                                        min:0
                                    },
                                    colors: ["#1ab394"],
                                    grid: {
                                        color: "#999999",
                                        hoverable: !0,
                                        clickable: !0,
                                        tickColor: "#D4D4D4",
                                        borderWidth: 0
                                    },
                                    legend: {show: !1},
                                    tooltip: !0,
                                    tooltipOpts: {content: "x: %x, y: %y"}
                                };
                                var data4 = {
                                    label: "bar",
                                    data: dataset4
                                };
                                $.plot($("#flot-line-chart2"), [data4], option4);
                            });
                        }

                    }
                });

            }


            // 日期选择
            var ons = {
                elem: "#ons_date",
                format: "YYYY-MM-DD hh:mm:ss",
                min: '2000-01-01 00:00:00',
                max: "2099-06-16 23:59:59",
                istime: true,
                istoday: false,
                choose: function (datas) {
                    one.min = datas
                    one.max = laydate.now()
                }
            };
            var one = {
                elem: "#one_date",
                format: "YYYY-MM-DD hh:mm:ss",
                min: '2010-01-01 00:00:00',
                max: laydate.now(),
                istime: true,
                istoday: false,
                choose: function (datas) {
                    sales();
                }
            };
            var offs = {
                elem: "#offs_date",
                format: "YYYY-MM-DD hh:mm:ss",
                min: '2000-01-01 00:00:00',
                max: "2099-06-16 23:59:59",
                istime: true,
                istoday: false,
                choose: function (datas) {
                    offe.min = datas;
                    offe.max = laydate.now()
                }
            };
            var offe = {
                elem: "#offe_date",
                format: "YYYY-MM-DD hh:mm:ss",
                min: '2000-01-01 00:00:00',
                max: laydate.now(),
                istime: true,
                istoday: false,
                choose: function (datas) {
                    sales();
                }
            };

            var sd = $('#sales_sdate').val();
            var saless = {
                elem: "#sales_sdate",
                format: "YYYY-MM-DD hh:mm:ss",
                min: '2000-01-01 00:00:00',
                max: "2099-06-16 23:59:59",
                istime: true,
                istoday: false,
                choose: function (datas) {
                    sd = datas;
                    salese.min = datas;
                    salese.max = laydate.now()
                }
            };
            var salese = {
                elem: "#sales_edate",
                format: "YYYY-MM-DD hh:mm:ss",
                min: '2000-01-01 00:00:00',
                max: laydate.now(),
                istime: true,
                istoday: false,
                choose: function (datas) {
                    var url = "{{ route('business.machine-sales-overview') }}";
                    url += '?sd=' + sd + '&ed=' + datas + '#mode';
                    location.href = url;
                }
            };
            laydate(ons);
            laydate(one);
            laydate(offs);
            laydate(offe);
            laydate(saless);
            laydate(salese);

        });
    </script>
@endsection
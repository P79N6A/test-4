@extends('business.layouts.frame-parent')
@section('page-title','机台营收概况')
@section('main')
    <link rel="stylesheet" href="/admin/js/plugins/layer/laydate/skins/default/laydate.css">
    <div class="row">
        <div class="col-sm-4">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <span class="label label-success pull-right" id="total_machine"></span>
                    <h5>总机台数</h5>
                </div>
                <div class="ibox-content">
                    <div class="text-center">
                        <div id="container1" style=""></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>线上营收</h5>
                </div>
                <div class="ibox-content">
                    <div id="container2"></div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>线下营收</h5>
                </div>
                <div class="ibox-content">
                    <div id="container3"></div>
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
                        <input type="text" id="start_date" name="start_date" class="form-control" placeholder="开始时间"> -
                        <input type="text" id="end_date" name="end_date" class="form-control" placeholder="结束时间">
                    </div>
                    <div id="container4"></div>
                    <div id="container5"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <a name="mode"></a>
            <div class="btn-group">
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
                    <h5>营收统计</h5>
                </div>
                <div class="ibox-content">
                    <div class="form-group form-inline text-right">
                        <button type="button" id="export"
                                data-url="{{ route('business.smart-link-orders-export') }}"
                                class="btn btn-sm btn-success">导出
                        </button>&nbsp;&nbsp;
                        <input type="text" id="sales_sdate" name="sales_sdate" value="{{ $s }}" class="form-control"
                               placeholder="开始时间">
                        -
                        <input type="text" id="sales_edate" name="sales_edate" value="{{ $e }}" class="form-control"
                               placeholder="结束时间">
                    </div>
                    <table class="table table-striped">
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
                            {{ $res->appends(['mode'=>$mode,'sd'=>$s,'ed'=>$e])->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/business/js/highcharts/highcharts.min.js"></script>
    <script src="/business/js/highcharts/exporting.js"></script>
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
                $('#container1').highcharts({
                    chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false
                    },
                    title: {
                        text: '在线：' + data.percent[0].online_qty + '    离线：' + data.percent[0].offline_qty
                    },
                    tooltip: {
                        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                                style: {
                                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                                }
                            }
                        }
                    },
                    series: [{
                        type: 'pie',
                        name: '在线离线比例',
                        data: [
                            ['在线', data.percent[0].online_qty / data.percent[0].total_qty],
                            ['离线', data.percent[0].offline_qty / data.percent[0].total_qty]
                        ]
                    }]
                });

                // 柱状图
                $('#container2').highcharts({
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: '线上营收状况'
                    },
                    xAxis: {
                        categories: [
                            '昨天',
                            '今天',
                            '最近30天'
                        ],
                        crosshair: true
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: '元'
                        }
                    },
                    tooltip: {
                        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                        '<td style="padding:0"><b>{point.y:.1f} 元</b></td></tr>',
                        footerFormat: '</table>',
                        shared: true,
                        useHTML: true
                    },
                    plotOptions: {
                        column: {
                            pointPadding: 0.2,
                            borderWidth: 0
                        }
                    },
                    series: [{
                        name: '线上营收状况',
                        data: [
                            parseFloat(data.yesterday[0].online_amount != undefined ? data.yesterday[0].online_amount : 0),
                            parseFloat(data.today[0].online_amount != undefined ? data.today[0].online_amount : 0),
                            parseFloat(data.thirtyDays[0].online_amount != undefined ? data.thirtyDays[0].online_amount : 0)
                        ]
                    }]
                });

                // 柱状图
                $('#container3').highcharts({
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: '线下营收状况'
                    },
                    xAxis: {
                        categories: [
                            '昨天',
                            '今天',
                            '最近30天'
                        ],
                        crosshair: true
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: '币'
                        }
                    },
                    tooltip: {
                        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                        '<td style="padding:0"><b>{point.y:.1f} 币</b></td></tr>',
                        footerFormat: '</table>',
                        shared: true,
                        useHTML: true
                    },
                    plotOptions: {
                        column: {
                            pointPadding: 0.2,
                            borderWidth: 0
                        }
                    },
                    series: [{
                        name: '线下营收状况',
                        data: [
                            parseFloat(data.yesterday[0].offline_qty != undefined ? data.yesterday[0].offline_qty : 0),
                            parseFloat(data.today[0].offline_qty != undefined ? data.today[0].offline_qty : 0),
                            parseFloat(data.thirtyDays[0].offline_qty != undefined ? data.thirtyDays[0].offline_qty : 0)
                        ]
                    }]
                });

                sales();
            }

            function sales() {
                var label = [];
                var offline = [];
                var online = [];
                $.ajax({
                    type: 'post',
                    url: "{{route('business.machine-sales-filter')}}",
                    data: {
                        _token: $('input[name=_token]').val(),
                        start_date: $('#start_date').val(),
                        end_date: $('#end_date').val()
                    },
                    success: function (data) {
                        if (data.length > 0) {
                            $.each(data, function (index, value) {
                                label.push(value.day);
                                online.push(parseFloat(value.total_amount));
                                offline.push(parseFloat(value.total_qty));
                            });

                            // 线上折线图
                            $('#container4').highcharts({
                                title: {
                                    text: '线上营收情况',
                                    x: -20 //center
                                },
                                xAxis: {
                                    categories: label
                                },
                                yAxis: {
                                    title: {
                                        text: '元'
                                    },
                                    plotLines: [{
                                        value: 0,
                                        width: 1,
                                        color: '#808080'
                                    }]
                                },
                                tooltip: {
                                    valueSuffix: '元'
                                },
                                legend: {
                                    layout: 'vertical',
                                    align: 'right',
                                    verticalAlign: 'middle',
                                    borderWidth: 0
                                },
                                series: [{
                                    name: '线上营收',
                                    data: online
                                }]
                            });

                            // 线下折线图
                            $('#container5').highcharts({
                                title: {
                                    text: '线下营收情况',
                                    x: -20 //center
                                },
                                xAxis: {
                                    categories: label
                                },
                                yAxis: {
                                    title: {
                                        text: '币'
                                    },
                                    plotLines: [{
                                        value: 0,
                                        width: 1,
                                        color: '#808080'
                                    }]
                                },
                                tooltip: {
                                    valueSuffix: '币'
                                },
                                legend: {
                                    layout: 'vertical',
                                    align: 'right',
                                    verticalAlign: 'middle',
                                    borderWidth: 0
                                },
                                series: [{
                                    name: '线下营收',
                                    data: offline
                                }]
                            });
                        }

                    }
                });
            }


            // 日期选择
            var start_date = {
                elem: "#start_date",
                format: "YYYY-MM-DD hh:mm:ss",
                min: '2000-01-01 00:00:00',
                max: "2099-06-16 23:59:59",
                istime: true,
                istoday: false,
                choose: function (datas) {
                    end_date.max = laydate.now();
                    sales();
                }
            };
            var end_date = {
                elem: "#end_date",
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
                    url += '?mode=' + '{{ $mode }}' + '&sd=' + sd + '&ed=' + datas + '#mode';
                    location.href = url;
                }
            };
            laydate(start_date);
            laydate(end_date);
            laydate(saless);
            laydate(salese);

            $('#export').click(function () {
                location.href = $(this).data('url') + '?start_date=' + $('#sales_sdate').val() + '&end_date=' + $('#sales_edate').val();
            });
        });
    </script>
@endsection
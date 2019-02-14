@extends('business.layouts.frame-parent')
@section('page-title','订单分析')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <div class="row">
                        <div class="col-sm-12">
                            <h5>@yield('page-title')</h5>
                        </div>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-sm-3">
                            <input class="form-control" placeholder="选择日期" id="date1">
                        </div>
                        <div class="col-sm-3">
                            <input class="form-control" placeholder="选择日期" id="date2">
                        </div>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-lg-12">
                            <div id="diagram" style="height:300px;"></div>
                        </div>
                        <div class="col-lg-6">
                            <div id="circle"></div>
                        </div>
                        <div class="col-lg-6">
                            <div id="payment-percent"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="/business/js/plugins/layer/laydate/laydate.js"></script>
    <script src="/business/js/highcharts/highcharts.min.js"></script>
    <script src="/business/js/highcharts/exporting.js"></script>
    <script>
        $(function () {
            var param1 = null, param2 = null;

            // 日期选择
            var date1 = {
                elem: "#date1",
                format: "YYYY-MM-DD",
                min: '2010-01-01',
                max: laydate.now(),
                istoday: true,
                choose: function (datas) {
                    param1 = datas;
                    if (param1 && param2) {
                        getData(param1, param2);
                    }
                }
            };
            var date2 = {
                elem: "#date2",
                format: "YYYY-MM-DD",
                min: '2000-01-01',
                max: laydate.now(),
                istoday: true,
                choose: function (datas) {
                    param2 = datas;
                    if (param1 && param2) {
                        getData(param1, param2);
                    }
                }
            };
            laydate(date1);
            laydate(date2);

            getData(param1, param2);

            // 曲线图
            function showDiagram(data) {
                var dates = [];
                var sums = [];
                var prices = [];
                var counts = [];

                $.each(data, function (index, value) {
                    dates.push(value.date);
                    sums.push(value.price_sum);
                    prices.push(value.price);
                    counts.push(value.order_count);
                });

                $('#diagram').highcharts({
                    chart: {
                        type: 'spline'
                    },
                    title: {
                        text: '每日订单增长趋势'
                    },
                    xAxis: {
                        categories: dates
                    },
                    yAxis: {
                        title: {
                            text: '统计结果：元/元/笔'
                        },
                        labels: {
                            formatter: function () {
                                return this.value + '';
                            }
                        }
                    },
                    tooltip: {
                        crosshairs: true,
                        shared: true
                    },
                    plotOptions: {
                        spline: {
                            marker: {
                                radius: 4,
                                lineColor: '#666666',
                                lineWidth: 1
                            }
                        }
                    },
                    series: [{
                        name: '收益金额',
                        marker: {
                            symbol: 'square'
                        },
                        data: sums
                    }, {
                        name: '客单价',
                        marker: {
                            symbol: 'diamond'
                        },
                        data: prices
                    }, {
                        name: '成交笔数',
                        marker: {
                            symbol: 'diamond'
                        },
                        data: counts
                    }]
                });
            }

            // 饼状图
            function showPercent(data) {
                $('#circle').highcharts({
                    chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false
                    },
                    title: {
                        text: '用户下单构成'
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
                        name: '用户比例',
                        data: [
                            ['新用户', data != undefined ? data.new_user : 0],
                            ['旧用户', data != undefined ? data.old_user : 0]
                        ]
                    }]
                });
            }

            function showPaymentPercent(data) {
                $('#payment-percent').highcharts({
                    chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false
                    },
                    title: {
                        text: '支付方式构成'
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
                        name: '支付方式构成比例',
                        data: [
                            ['支付宝', data != undefined ? parseInt(data.alipay) : 0],
                            ['微信支付', data != undefined ? parseInt(data.wechat) : 0],
                            ['微信公众号', data != undefined ? parseInt(data.wechat_public_account) : 0]
                        ]
                    }]
                });
            }

            // ajax 请求数据
            function getData(date1, date2) {
                $.ajax({
                    type: 'get',
                    url: '{{ route('business.order-analysis') }}',
                    data: {
                        start_date: date1,
                        end_date: date2
                    },
                    success: function (res) {
                        if (res.trend != undefined) {
                            showDiagram(res.trend);
                        }
                        if (res.percent != undefined) {
                            showPercent(res.percent);
                        }
                        if (res.paymentStructure != undefined) {
                            showPaymentPercent(res.paymentStructure);
                        }
                    }
                });

            }

        });
    </script>
@endsection
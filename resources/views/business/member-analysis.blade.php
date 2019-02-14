@extends('business.layouts.frame-parent')
@section('page-title','会员分析')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <div class="row">
                        <div class="col-sm-12">
                            <h5>选择时间段</h5>
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
            </div>
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>会员分析</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-sm-12">
                            <div id="diagram" style="height:300px;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <div class="row">
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label>行为分析</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-sm-6">
                            <table class="table table-responsive table-striped table-hover counts">
                                <caption><h4>消费次数</h4></caption>
                                <thead>
                                <tr>
                                    <th>消费次数</th>
                                    <th>人数</th>
                                    <th>占比</th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div class="col-sm-6">
                            <table class="table table-responsive table-striped table-hover sums">
                                <caption><h4>消费金额</h4></caption>
                                <thead>
                                <tr>
                                    <th>金额</th>
                                    <th>人数</th>
                                    <th>占比</th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
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
                    param2 = datas + ' 23:59:59';
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
                var amounts = [];
                for (var i = 0; i < data.length; i++) {
                    dates.push(data[i].date);
                    amounts.push(data[i].amount);
                }

                $('#diagram').highcharts({
                    chart: {
                        type: 'spline'
                    },
                    title: {
                        text: '每日用户访问统计'
                    },
                    xAxis: {
                        categories: dates
                    },
                    yAxis: {
                        title: {
                            text: '每日访客人数'
                        },
                        labels: {
                            formatter: function () {
                                return this.value + '人';
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
                        name: '每日访客人数',
                        marker: {
                            symbol: 'circle'
                        },
                        data: amounts
                    }
                    ]
                });
            }

            function showCount(data) {
                var $tbody = $('table.counts tbody').empty();
                var totalCount = 0;
                if (data != undefined) {
                    $.each(data, function (index, value) {
                        totalCount += value;
                    });
                    $.each(data, function (index, value) {
                        var $str = '<tr><td>'
                            + index
                            + '</td><td>'
                            + value
                            + '</td><td>'
                            + (value / totalCount * 100).toFixed(4) + '%</td></td>';
                        $tbody.append($str);
                    });
                }
            }

            function showSum(data) {
                var $tbody = $('table.sums tbody').empty();
                var totalCount = 0;
                if (data != undefined) {
                    $.each(data, function (index, value) {
                        totalCount += value;
                    });
                    $.each(data, function (index, value) {
                        var $str = '<tr><td>'
                            + index
                            + '</td><td>'
                            + value
                            + '</td><td>'
                            + (value / totalCount * 100).toFixed(4) + '%</td></td>';
                        $tbody.append($str);
                    });
                }
            }

            // ajax 请求数据
            function getData(date1, date2) {
                $.ajax({
                    type: 'get',
                    url: '{{ route('business.member-analysis') }}',
                    data: {
                        date1: date1,
                        date2: date2
                    },
                    success: function (res) {
                        if (res.users != undefined) {
                            showDiagram(res.users);
                        }
                        if (res.counts != undefined) {
                            showCount(res.counts);
                        }
                        if (res.sums != undefined) {
                            showSum(res.sums);
                        }
                    }
                });

            }


        });
    </script>
@endsection
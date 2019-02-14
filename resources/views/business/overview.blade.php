@extends('business.layouts.frame-parent')
@section('page-title','首页')
@section('main')
    <div class="row">
        <div class="col-sm-2">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>今日访客（人）</h5>
                </div>
                <div class="ibox-content">
                    <h1>{{ $visitors['today']['new'] + $visitors['today']['old'] }}</h1>
                    <div class="row">
                        <div class="col-sm-12">
                            <small class="pull-left">新用户：{{ $visitors['today']['new'] }}</small>
                            <small class="pull-right">旧用户：{{ $visitors['today']['old'] }}</small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <span class="label label-danger">较前一日：
                                {{ abs( $visitors['yesterday']['total'] - $visitors['today']['total'] ) }}
                                @if($visitors['yesterday']['total'] > $visitors['today']['total']) ↓
                                @elseif($visitors['yesterday']['total'] < $visitors['today']['total']) ↑
                                @endif
                            </span>
                            <a href="{{ route('business.member-analysis') }}"
                               class="label label-success pull-right">查看详情</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>今日消费人数</h5>
                </div>
                <div class="ibox-content">
                    <h1>{{ $consumers->today }}</h1>
                    <div class="row">
                        <div class="col-sm-12">
                            <small class="pull-left">&nbsp;</small>
                            <small class="pull-right">&nbsp;</small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <span class="label label-danger">较前一日：
                                {{ abs( $consumers->yesterday - $consumers->today ) }}
                                @if($consumers->yesterday > $consumers->today) ↓
                                @elseif($consumers->yesterday < $consumers->today) ↑
                                @endif
                            </span>
                            {{--<a class="label label-success pull-right">查看详情</a>--}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>今日非会员套餐收益金额（元）</h5>
                </div>
                <div class="ibox-content">
                    <h1>{{ $income['package']['today'] }}</h1>
                    <div class="row">
                        <div class="col-sm-12">
                            <small class="pull-left">&nbsp;</small>
                            <small class="pull-right">&nbsp;</small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            @if($income['package']['today'] > $income['package']['yesterday'])
                                <span class="label label-primary">
                                较前一日：{{ abs($income['package']['today'] - $income['package']['yesterday']) }} ↑
                                </span>
                            @elseif($income['package']['today'] < $income['package']['yesterday'])
                                <span class="label label-danger">
                                较前一日：{{ abs($income['package']['today'] - $income['package']['yesterday']) }} ↓
                                </span>
                            @endif
                            <a href="{{ route('business.order-list') }}" class="label label-success pull-right">查看详情</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>今日会员套餐收益金额（元）</h5>
                </div>
                <div class="ibox-content">
                    <h1>{{ $income['member']['today'] }}</h1>
                    <div class="row">
                        <div class="col-sm-12">
                            <small class="pull-left">&nbsp;</small>
                            <small class="pull-right">&nbsp;</small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                                @if($income['member']['today'] > $income['member']['yesterday'])
                                    <span class="label label-primary">
                                        较前一日：{{ round(abs($income['member']['today'] - $income['member']['yesterday']), 2) }} ↑
                                    </span>
                                @elseif($income['member']['today'] < $income['member']['yesterday'])
                                    <span class="label label-danger">
                                        较前一日：{{ round(abs($income['member']['today'] - $income['member']['yesterday']), 2) }} ↓
                                    </span>
                                @endif
                            <a href="{{ route('business.member-order-list') }}"
                               class="label label-success pull-right">查看详情</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>今日优惠券领取数（张）</h5>
                </div>
                <div class="ibox-content">
                    <h1>{{ $ticketCount['today'] }}</h1>
                    <div class="row">
                        <div class="col-sm-12">
                            <small class="pull-left">&nbsp;</small>
                            <small class="pull-right">&nbsp;</small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <span class="label label-danger">
                                较前一日：{{ abs($ticketCount['today'] - $ticketCount['yesterday']) }}
                                @if($ticketCount['today'] > $ticketCount['yesterday'])↑
                                @elseif($ticketCount['today'] < $ticketCount['yesterday'])↓
                                @endif
                            </span>
                            <a href="{{ route('business.ticket-analysis') }}"
                               class="label label-success pull-right">查看详情</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>订单</h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-sm-9">
                            <div id="container" style="height:300px;"></div>
                        </div>
                        <div class="col-sm-3">
                            <ul class="stat-list">
                                <li>
                                    <h2 class="no-margins"></h2>
                                    <small>访客总数</small>
                                    <div class="stat-percent">
                                        {{ $sum['allVisitorCount'] }}
                                    </div>
                                    <div class="progress progress-mini">
                                        <div style="width: 48%;" class="progress-bar"></div>
                                    </div>
                                </li>
                                <li>
                                    <h2 class="no-margins "></h2>
                                    <small>最近一个月交易笔数</small>
                                    <div class="stat-percent">
                                        {{ $sum['quantity'] }}
                                    </div>
                                    <div class="progress progress-mini">
                                        <div style="width: 60%;" class="progress-bar"></div>
                                    </div>
                                </li>
                                <li>
                                    <h2 class="no-margins "></h2>
                                    <small>最近一个月销售额</small>
                                    <div class="stat-percent">
                                        {{ $sum['amount'] }}
                                    </div>
                                    <div class="progress progress-mini">
                                        <div style="width: 22%;" class="progress-bar"></div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>消息</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link" href="{{ route('business.bus-msg-list') }}">
                            历史消息&nbsp;&nbsp;
                            <i class="fa fa-chevron-right"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content ibox-heading">
                    <h3><i class="fa fa-envelope-o"></i> 新消息</h3>
                    <small><i class="fa fa-tim"></i> 您有{{ $messageCount }}条未读消息</small>
                </div>
                <div class="ibox-content">
                    <div class="feed-activity-list">
                        @if(!empty($messages))
                            @foreach($messages as $message)
                                <div class="feed-element">
                                    <div>
                                        <div>
                                            {{ $message->title }}
                                            <a href="{{ route('business.read-msg',['id'=>$message->id]) }}"
                                               class="btn btn-default btn-xs">详细</a>
                                        </div>
                                        <small class="text-muted">{{ date('Y-m-d H:i',$message->receive_time) }}</small>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="/business/js/highcharts/highcharts.min.js"></script>
    <script src="/business/js/highcharts/exporting.js"></script>
    <script>
        $(function () {

            getData();

            function getData() {
                $.ajax({
                    type: 'get',
                    url: '{{ route('business.get-chart-data') }}',
                    success: function (data) {
                        var dates = [];
                        var amounts = [];
                        var quantities = [];
                        if (data.code = 200 && data.data !== undefined) {
                            $.each(data.data, function (index, value) {
                                dates.push(value.date);
                                amounts.push(value.amount);
                                quantities.push(value.quantity);
                            });
                            showBarData(dates, amounts, quantities);
                        }
                    }
                });
            }

            function showBarData(dates, amounts, quantiteis) {
                $('#container').highcharts({
                    chart: {
                        zoomType: 'xy'
                    },
                    title: {
                        text: '本月营收统计图'
                    },
                    xAxis: [{
                        categories: dates,
                        crosshair: true
                    }],
                    yAxis: [{ // Primary yAxis
                        labels: {
                            format: '{value}笔',
                            style: {
                                color: Highcharts.getOptions().colors[1]
                            }
                        },
                        title: {
                            text: '交易笔数',
                            style: {
                                color: Highcharts.getOptions().colors[1]
                            }
                        }
                    }, { // Secondary yAxis
                        title: {
                            text: '总金额',
                            style: {
                                color: Highcharts.getOptions().colors[0]
                            }
                        },
                        labels: {
                            format: '{value} 元',
                            style: {
                                color: Highcharts.getOptions().colors[0]
                            }
                        },
                        opposite: true
                    }],
                    tooltip: {
                        shared: true
                    },
                    legend: {
                        layout: 'vertical',
                        align: 'left',
                        x: 100,
                        verticalAlign: 'top',
                        y: -10,
                        floating: true,
                        backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
                    },
                    series: [{
                        name: '总金额',
                        type: 'column',
                        yAxis: 1,
                        data: amounts,
                        tooltip: {
                            valueSuffix: ' 元'
                        }
                    }, {
                        name: '交易笔数',
                        type: 'spline',
                        data: quantiteis,
                        tooltip: {
                            valueSuffix: '笔'
                        }
                    }]
                });
            }

        });
    </script>
@endsection

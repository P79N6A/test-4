@extends('business.layouts.frame-parent')
@section('page-title','卡券分析')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>@yield('page-title')</h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <form action="{{ route('business.ticket-analysis') }}" method="get">
                            <div class="col-sm-10">
                                <div class="row form-horizontal">
                                    <div class="col-sm-6 m-b-xs">
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">卡券创建时间段筛选</label>
                                            <div class="col-sm-9">
                                                <div class="input-daterange input-group">
                                                    <input type="text" class="form-control" id="start_date"
                                                           name="start_date"
                                                           value="{{ $start_date }}">
                                                    <span class="input-group-addon">至</span>
                                                    <input type="text" class="form-control" id="end_date"
                                                           name="end_date"
                                                           value="{{ $end_date }}">
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
                                <button id="export" class="btn btn-success btn-lg" data-url="{{ route('business.ticket-analysis-export') }}" type="button">
                                    <i class="fa fa-download"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="row">
                        <div class="col-lg-9 col-sm-12">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>优惠券名称</th>
                                        <th>领取份数</th>
                                        <th>核销份数</th>
                                        <th>核销率</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(!empty($tickets))
                                        @foreach($tickets as $ticket)
                                            <tr>
                                                <td>{{ $ticket->id }}</td>
                                                <td>{{ $ticket->name }}</td>
                                                <td>{{ $ticket->got_count }}</td>
                                                <td>{{ $ticket->used_count }}</td>
                                                <td>{{ $ticket->percent }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-12">
                            <div id="pie" style=""></div>
                        </div>
                    </div>
                    <div class="text-right">
                        @if(!empty($tickets))
                            {{ $tickets->appends([
                                'start_date' => $start_date,
                                'end_date' => $end_date
                            ])->links() }}
                        @endif
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
            var start_date = {
                elem: "#start_date",
                format: "YYYY-MM-DD hh:mm:ss",
                min: '2010-01-01 00:00:00',
                max: laydate.now(),
                istime: true,
                istoday: false,
                choose: function (datas) {
                    end_date.min = datas;
                }
            };
            var end_date = {
                elem: "#end_date",
                format: "YYYY-MM-DD hh:mm:ss",
                min: '2010-01-01 00:00:00',
                max: laydate.now(),
                istime: true,
                istoday: false,
                choose: function (datas) {
                    start_date.max = datas
                }
            };
            laydate(start_date);
            laydate(end_date);

            getPieDate();

            function getPieDate() {
                $.ajax({
                    type: 'get',
                    url: '{{ route('business.ticket-get-user-structure') }}',
                    success: function (data) {
                        dealWithData(data);
                    }
                });
            }

            function dealWithData(data) {
                // 饼状图
                $('#pie').highcharts({
                    chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false
                    },
                    title: {
                        text: '领取优惠券用户构成'
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
                        name: '新老用户比例',
                        data: [
                            ['新用户', parseInt('{{ $percent['newCount']/$percent['total'] }}')],
                            ['老用户', parseInt('{{ $percent['oldCount']/$percent['total'] }}')]
                        ]
                    }]
                });
            }

            $('#export').click(function(){
                $('form').attr('action',$(this).data('url')).submit().attr('action',location.href);
            });

        });
    </script>
@endsection

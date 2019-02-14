@extends('business.layouts.frame-parent')
@section('page-title','门店分析')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>@yield('page-title')</h5>
                </div>
                <div class="ibox-content">
                    <div class="row form-horizontal">
                        <form action="{{ route('business.store-analysis') }}">
                            <div class="col-sm-5">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">时间</label>
                                    <div class="col-sm-9">
                                        <div class="input-daterange input-group">
                                            <input type="text" class="form-control" name="start_date" value="{{ $params['start_date'] }}" id="start_date">
                                            <span class="input-group-addon">至</span>
                                            <input type="text" class="form-control" name="end_date" value="{{ $params['end_date'] }}" id="end_date">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">门店</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="keyword" value="{{ $params['keyword'] }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <button type="submit" class="btn  btn-primary">分析</button>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>门店</th>
                                <th>总访客数</th>
                                <th>消费人数</th>
                                <th>会员卡套餐</th>
                                <th>非会员卡套餐</th>
                                <th>智联宝机台收入</th>
                                <th>总收入</th>
                                <th>套餐交易笔数</th>
                                <th>套餐客单价</th>
                                <th>线下转入平台积分</th>
                                <th>彩票转入平台积分</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($list))
                                @foreach($list as $item)
                                    <tr>
                                        <td>{{ $item->store_name }}</td>
                                        <td>{{ $item->total_visitor_count }}</td>
                                        <td>{{ $item->consumer_count }}</td>
                                        <td>
                                            销量：{{ $item->member_package_count }}<br>
                                            金额：{{ $item->member_package_income }}
                                        </td>
                                        <td>
                                            销量：{{ $item->package_count }}<br>
                                            金额：{{ $item->package_income }}
                                        </td>
                                        <td>{{ $item->iot_order_income }}</td>
                                        <td>{{ $item->all_income }}</td>
                                        <td>{{ $item->all_package_count }}</td>
                                        <td>{{ $item->average_price }}</td>
                                        <td>
                                            {{ $item->scores }}
                                            @if(!empty($item->scores))
                                                <a href="{{route('business.get-bus-scores-exchange-log',['id'=>$item->store_id])}}" class="text-navy">
                                                    <small>详情</small>
                                                </a>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $item->ticket_transferred_scores }}
                                            @if(!empty($item->ticket_transferred_scores))
                                                <a href="{{route('business.get-bus-tickets-exchange-log',['id'=>$item->store_id])}}" class="text-navy">
                                                    <small>详情</small>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="text-right">
                        @if(!empty($list))
                            {{ $list->appends(['start_date'=>$params['start_date'],'end_date'=>$params['end_date'],'keyword'=>$params['keyword']])->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="/business/js/plugins/layer/laydate/laydate.js"></script>
    <script>
        var start_date = {
            elem: "#start_date",
            format: "YYYY-MM-DD hh:mm:ss",
            min: '2010-01-01',
            max: laydate.now(),
            istime: true,
            istoday: true,
            choose: function (datas) {

            }
        };
        var end_date = {
            elem: "#end_date",
            format: "YYYY-MM-DD hh:mm:ss",
            min: '2010-01-01',
            max: laydate.now(),
            istime: true,
            istoday: true,
            choose: function (datas) {
                end_date.max = laydate.now()
            }
        };
        laydate(start_date);
        laydate(end_date);

    </script>
@endsection

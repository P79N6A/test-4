@extends('admin.layouts.parent')
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
                        <form action="{{route('get-bus-tickets-exchange-log')}}">
                            <div class="col-sm-4">
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
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">手机号</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="mobile" value="{{ $params['mobile'] }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">会员编号</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="code" value="{{ $params['code'] }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <input class="form-control" type="hidden" name="id" value="{{ $params['id'] }}">
                                <button type="submit" class="btn  btn-primary">分析</button>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>时间</th>
                                <th>手机号</th>
                                <th>会员编码</th>
                                <th>会员卡转出彩票</th>
                                <th>APP转入积分</th>
                                <th>转换比率</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($list))
                                @foreach($list as $item)
                                    <tr>
                                        <td>{{ $item->create_date }}</td>
                                        <td>{{ $item->mobile }}</td>
                                        <td>{{ $item->member_card_no }}</td>
                                        <td>{{ $item->real_ticket }}</td>
                                        <td>{{ $item->ticket_transferred_scores }}</td>
                                        <td>{{ $item->rate }}</td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="text-right">
                        @if(!empty($list))
                            {{ $list->appends(['start_date'=>$params['start_date'],'end_date'=>$params['end_date'],'code'=>$params['code'],'mobile'=>$params['mobile'],'id'=>$params['id']])->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="/admin/js/plugins/layer/laydate/laydate.js"></script>
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

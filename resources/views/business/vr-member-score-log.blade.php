@extends('business.layouts.frame-parent')
@section('page-title','VR点数报表')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>@yield('page-title')</h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-sm-12">
                            <form role="form" action="{{ route('business.vr-member-score-log') }}" class="form-inline"
                                  method="get">
                                <div class="form-group">
                                    <label>门店名称：</label>
                                    <input class="form-control" name="store" value="{{ $params['store'] }}" placeholder="请输入门店名称">
                                </div>
                                <div class="form-group">
                                    <label>日期：</label>
                                    <input class="form-control" id="start-date" name="start_date" value="{{ $params['start_date'] }}" placeholder="开始日期">
                                    &nbsp;<input class="form-control" id="end-date" name="end_date" value="{{ $params['end_date'] }}" placeholder="结束日期">
                                </div>
                                <button type="submit" class="btn btn-sm btn-primary">搜索</button>
                            </form>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-sm-12">
                            <table class="table table-responsive table-stripped">
                                <thead>
                                <tr>
                                    <th>门店标识</th>
                                    <th>门店名称</th>
                                    <th>消费缘由</th>
                                    <th>充值/消费点数</th>
                                    <th>充值/消费时间</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(!empty($list))
                                    @foreach($list as $item)
                                        <tr>
                                            <td>{{ $item->id }}</td>
                                            <td>{{ $item->store_name }}</td>
                                            <td>{{ $item->reason }}</td>
                                            <td>{{ $item->score }}</td>
                                            <td>{{ $item->create_date }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                        <div class="col-sm-12">
                            <div class="text-right">
                                @if(!empty($list))
                                    {{ $list->appends([
                                        'store'=>$params['store'],
                                        'start_date'=>$params['start_date'],
                                        'end_date'=>$params['end_date']
                                    ])->links() }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="/business/js/plugins/layer/laydate/laydate.js"></script>
    <script>
        $(function () {
            var start_date = {
                elem: "#start-date",
                format: "YYYY-MM-DD hh:mm:ss",
                min: '2010-01-01 00:00:00',
                max: "2099-06-16 23:59:59",
                istime: true,
                istoday: false,
                choose: function (datas) {
                }
            };
            var end_date = {
                elem: "#end-date",
                format: "YYYY-MM-DD hh:mm:ss",
                min: '2010-01-01 00:00:00',
                max: "2099-06-16 23:59:59",
                istime: true,
                istoday: false,
                choose: function (datas) {
                    start_date.max = datas
                }
            };

            laydate(start_date);
            laydate(end_date);
        });
    </script>
@endsection
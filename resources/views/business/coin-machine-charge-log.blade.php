@extends('business.layouts.frame-parent')
@section('page-title','添币记录')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>@yield('page-title')</h5>
                    <a class="btn btn-xs btn-primary pull-right"
                       href="{{ route('business.add-coin-charge-log') }}">添加</a>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-sm-12">
                            <form class="form-inline" action="{{ route('business.coin-machine-charge-log') }}">
                                <div class="form-group">
                                    <input class="form-control" name="store" value="{{ $params['store'] }}" placeholder="搜索门店">
                                    <input class="form-control" name="serial" value="{{ $params['serial'] }}" placeholder="搜索机台序列号">
                                    <input class="form-control" name="operator" value="{{ $params['operator'] }}" placeholder="搜索操作员">
                                    <input class="form-control" name="sd" value="{{ $params['sd'] }}" id="sd" placeholder="开始日期"> -
                                    <input class="form-control" name="ed" value="{{ $params['ed'] }}" id="ed" placeholder="结束日期">
                                    <button type="submit" class="btn btn-sm btn-primary">搜索</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table class="table table-stripped">
                                    <thead>
                                    <th>门店</th>
                                    <th>机台序列号</th>
                                    <th>机台名称</th>
                                    <th>币数（枚）</th>
                                    <th>操作员</th>
                                    <th>添加时间</th>
                                    <th>操作</th>
                                    </thead>
                                    <tbody>
                                    @if(!empty($logs))
                                        @foreach($logs as $log)
                                            <tr>
                                                <td>{{ $log->store_name }}</td>
                                                <td>{{ $log->serial }}</td>
                                                <td>{{ $log->machine_name }}</td>
                                                <td>{{ $log->coin }}</td>
                                                <td>{{ $log->operator }}</td>
                                                <td>{{ $log->create_date }}</td>
                                                <td>
                                                    <a class="btn btn-sm btn-danger del-log"
                                                       data-url="{{ route('business.del-coin-charge-log') }}"
                                                       data-type="id" data-id="{{ $log->id }}">删除</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-right">
                                @if(!empty($logs))
                                    {{ $logs->appends([
                                        'store' => $params['store'],
                                        'serial' => $params['serial'],
                                        'operator' => $params['operator'],
                                        'sd' => $params['sd'],
                                        'ed' => $params['ed']
                                    ])->links() }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('business/js/plugins/layer/laydate/laydate.js') }}"></script>
    <script>
        $(function () {

            var sd = {
                elem: '#sd',
                event: 'click',
                format: 'YYYY-MM-DD hh:mm:ss',
                istime: true,
                isclear: true,
                istoday: true,
                min: '2010-01-01 00:00:00',
                max: '2099-12-31 23:59:59',
                start: laydate.now(),
                choose: function (dates) {
                    ed.start = dates;
                }
            };

            var ed = {
                elem: '#ed',
                event: 'click',
                format: 'YYYY-MM-DD hh:mm:ss',
                istime: true,
                isclear: true,
                istoday: true,
                min: '2010-01-01 00:00:00',
                max: '2099-12-31 23:59:59',
                start: laydate.now(),
                choose: function (dates) {
                    sd.max = dates;
                }
            };

            laydate(sd);
            laydate(ed);

            $('.del-log').click(function () {
                var $this = $(this);
                layer.msg('您确定要删除该记录吗？', {
                    time: 0,
                    btn: ['是', '否'],
                    yes: function (index) {
                        layer.close(index);
                        youyibao.httpSend($this, 'get', 1);
                    }
                });
            });
        });
    </script>
@endsection

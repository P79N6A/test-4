@extends('business.layouts.frame-parent')
@section('page-title','机台列表')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>@yield('page-title')</h5>
                    <a class="btn btn-xs btn-primary pull-right"
                       href="{{ route('business.add-coin-machine') }}">添加机台</a>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <form class="form-inline">
                                    <input class="form-control" name="store" value="{{ $params['store'] }}"
                                           placeholder="请输入门店关键字">
                                    <input class="form-control" name="keyword" value="{{ $params['keyword'] }}"
                                           placeholder="机台序列号/机台名称">
                                    <input class="form-control" id="sd" name="start_date"
                                           value="{{ $params['start_date'] }}" placeholder="请选择开始日期"> -
                                    <input class="form-control" id="ed" name="end_date"
                                           value="{{ $params['end_date'] }}" placeholder="请选择结束日期">
                                    <button type="submit" class="btn btn-sm btn-success">搜索</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-stripped">
                            <thead>
                            <th>门店</th>
                            <th>机台序列号</th>
                            <th>机台名称</th>
                            <th>状态</th>
                            <th>创建时间</th>
                            <th>操作</th>
                            </thead>
                            <tbody>
                            @if(!empty($machines))
                                @foreach($machines as $machine)
                                    <tr>
                                        <td>{{ $machine->store_name }}</td>
                                        <td>{{ $machine->serial }}</td>
                                        <td>{{ $machine->name }}</td>
                                        <td>
                                            @if($machine->usable == 0)<span class="label label-primary">启用</span>
                                            @elseif($machine->usable == 1)<span class="label label-danger">禁用</span>
                                            @endif
                                        </td>
                                        <td>{{ $machine->create_date }}</td>
                                        <td>
                                            <a href="{{ route('business.edit-coin-machine',['id'=>$machine->id]) }}"
                                               class="btn btn-sm btn-success">修改</a>
                                            @if($machine->usable == 0)
                                                <a data-url="{{ route('business.switch-coin-machine-status') }}"
                                                   data-type="id" data-id="{{ $machine->id }}"
                                                   class="btn btn-sm btn-warning switch-status">禁用</a>
                                            @else
                                                <a data-url="{{ route('business.switch-coin-machine-status') }}"
                                                   data-type="id" data-id="{{ $machine->id }}"
                                                   class="btn btn-sm btn-primary switch-status">启用</a>
                                            @endif
                                            <a data-url="{{ route('business.del-coin-machine') }}" data-type="id"
                                               data-id="{{ $machine->id }}" class="btn btn-sm btn-danger del-machine">删除</a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="text-right">
                        @if(!empty($machines))
                            {{ $machines->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="/business/js/plugins/layer/laydate/laydate.js"></script>
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

            $('.switch-status').click(function () {
                var $this = $(this);
                layer.msg('您确定要执行该操作吗？', {
                    time: 0,
                    btn: ['是', '否'],
                    yes: function (index) {
                        layer.close(index);
                        youyibao.httpSend($this, 'get', 1);
                    }
                });
            });

            $('.del-machine').click(function () {
                var $this = $(this);
                layer.msg('您确定要删除该机台吗？', {
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

@extends('business.layouts.frame-parent')
@section('page-title','VR机台营收明细')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>@yield('page-title')</h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <form action="{{ route('business.vr-machine-sales-flow') }}">
                            <div class="col-sm-10">
                                <input type="hidden" name="id" value="{{ $params['id'] }}">
                                <div class="row form-horizontal">
                                    <div class="col-sm-5 m-b-xs">
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <div class="input-daterange input-group">
                                                    <input type="text" class="form-control" name="start_date" value="{{ $params['start_date'] }}" id="start_date" placeholder="起始时间">
                                                    <span class="input-group-addon">至</span>
                                                    <input type="text" class="form-control" name="end_date" value="{{ $params['end_date'] }}" id="end_date" placeholder="结束时间">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <input class="form-control" name="game_name" value="{{ $params['game_name'] }}" placeholder="请输入游戏名称">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1">
                                        <div class="form-group">
                                            <button class="btn btn-sm btn-primary" type="submit">搜索</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>游戏名称</th>
                                <th>游戏ID</th>
                                <th>营收金额</th>
                                <th>消费日期</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($list))
                                @foreach($list as $item)
                                    <tr>
                                        <td>{{ $item->game_name }}</td>
                                        <td>{{ $item->game_id }}</td>
                                        <td>{{ $item->income }}</td>
                                        <td>{{ $item->use_date }}</td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <h4 class="text-right">总金额：{{ $sum }} 元</h4>
                        </div>
                    </div>
                    <div class="text-right">
                        @if(!empty($list))
                            {{ $list->appends(['start_date'=>$params['start_date'],'end_date'=>$params['end_date'],'game_name'=>$params['game_name']])->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="/business/js/plugins/layer/laydate/laydate.js"></script>
    <script>
        $(function (){
            var start_date = {
                elem: "#start_date",
                format: "YYYY-MM-DD hh:mm:ss",
                min: '2010-01-01 00:00:00',
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
                max: '2099-12-31 23:59:59',
                istime: true,
                istoday: true,
                choose: function (datas) {

                }
            };
            laydate(start_date);
            laydate(end_date);
        });
    </script>
@endsection


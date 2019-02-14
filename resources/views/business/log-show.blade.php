@extends('business.layouts.frame-parent')
@section('page-title','后台操作日志')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>后台操作日志</h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <form action="{{ route('log.show') }}" method="get">
                            <div class="col-sm-10">
                                <div class="row form-horizontal">
                                    <div class="col-sm-6 m-b-xs">
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">时间</label>
                                            <div class="col-sm-10">
                                                <div class="input-daterange input-group">
                                                    <input type="text" class="form-control" name="start_date"
                                                           @if(!empty($start_date)) value="{{ $start_date }}"
                                                           @endif id="get_start">
                                                    <span class="input-group-addon">至</span>
                                                    <input type="text" class="form-control" name="end_date"
                                                           @if(!empty($end_date)) value="{{ $end_date }}"
                                                           @endif id="get_end">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <button type="button" data-id="1"
                                                class="btn btn-range @if($range == 1) btn-success @else btn-white @endif ">
                                            今日
                                        </button>
                                        <button type="button" data-id="2"
                                                class="btn btn-range @if($range == 2) btn-success @else btn-white @endif ">
                                            昨日
                                        </button>
                                        <button type="button" data-id="3"
                                                class="btn btn-range @if($range == 3) btn-success @else btn-white @endif ">
                                            最近7天
                                        </button>
                                        <button type="button" data-id="4"
                                                class="btn btn-range @if($range == 4) btn-success @else btn-white @endif ">
                                            最近30天
                                        </button>
                                    </div>
                                </div>
                                @if(!empty($allow_users))
                                <div class="row form-horizontal">
                                    <div class="col-sm-6 m-b-xs">
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">用户</label>
                                            <div class="col-sm-10">
                                                <div class="form-group">
                                                    <select name="uid" class="form-control m-b">
                                                        <option value="0">全部</option> 
                                                    @foreach($allow_users as $allow_user)
                                                        @if($allow_user->id == $filter_user_id)
                                                        <option value="{{$allow_user->id}}" selected="selected">{{$allow_user->name}}</option> 
                                                        @else
                                                        <option value="{{$allow_user->id}}"">{{$allow_user->name}}</option> 
                                                        @endif
                                                    @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                            <div class="col-sm-2 m-b-xs text-right">
                                <button class="btn btn-primary btn-lg" type="submit">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>


                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>用户</th>
                                <th>IP</th>
                                <th>区域</th>
                                <th>说明</th>
                                <th>地址</th>
                                <th>方式</th>
                                <th>时间</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($logs as $log)
                                <tr>
                                <td>@if($log->userid !=0 ) {{$log->user->name}} - {{$log->user->description}} @else 无用户操作 @endif</td>

                                <td>{{$log->ip}}</td>
                                <td>{{$log->ip_area}}</td>
                                <td>{{$log->comment}}</td>
                                <td>{{$log->uri}}</td>
                                <td>{{$log->method}}</td>
                                <td>{{$log->create_at}}</td>
                                <td>
                                    <a href="{{route('log.detail',['id'=>$log->log_id])}}" class="btn btn-white btn-sm">
                                        <i class="fa fa-pencil"></i> 查看详情
                                    </a>
                                </td>

                                </tr>
                                    
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-right">
                        @if(!empty($logs->links()))
                            {{ $logs->appends([
                                'start_date'=>$start_date,
                                'end_date'=>$end_date,
                                'uid' => $filter_user_id
                            ])->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/business/js/plugins/layer/laydate/laydate.js"></script>
    <script src="/business/js/util.date.js"></script>
    <script type="text/javascript">
        $(function () {

            var get_start = {
                elem: "#get_start",
                format: "YYYY-MM-DD hh:mm:ss",
                min: '2010-01-01',
                max: "2099-06-16 23:59:59",
                istime: true,
                istoday: false,
                choose: function (datas) {
                    get_end.min = datas;
                    get_end.start = datas;
                }
            };
            var get_end = {
                elem: "#get_end",
                format: "YYYY-MM-DD hh:mm:ss",
                min: '2010-01-01',
                max: "2099-06-16 23:59:59",
                istime: true,
                istoday: false,
                choose: function (datas) {
                    get_start.max = datas
                }
            };
            laydate(get_start);
            laydate(get_end);

            // 今天、昨天...日期选择
            $('.btn-range').click(function () {
                $(this).siblings('.btn-range').removeClass('btn-success').addClass('btn-white');
                $(this).removeClass('btn-white').addClass('btn-success');
                var $startDate = $('input[name=start_date]');
                var $endDate = $('input[name=end_date]');
                var date = new Date();
                switch ($(this).data('id')) {
                    case 1:
                        $startDate.val(date.formatDate("yyyy-MM-dd") + ' 00:00:00');
                        $endDate.val(date.formatDate('yyyy-MM-dd 23:59:59'));
                        break;
                    case 2:
                        $startDate.val(new Date(date.getTime() - (24 * 3600 * 1000)).formatDate("yyyy-MM-dd") + ' 00:00:00');
                        $endDate.val(new Date(date.getTime() - (24 * 3600 * 1000)).formatDate("yyyy-MM-dd") + ' 23:59:59');
                        break;
                    case 3:
                        $startDate.val(new Date(date.getTime() - (7 * 24 * 3600 * 1000)).formatDate("yyyy-MM-dd") + ' 00:00:00');
                        $endDate.val(date.formatDate('yyyy-MM-dd 23:59:59'));
                        break;
                    case 4:
                        $startDate.val(new Date(date.getTime() - (30 * 24 * 3600 * 1000)).formatDate("yyyy-MM-dd") + ' 00:00:00');
                        $endDate.val(date.formatDate('yyyy-MM-dd 23:59:59'));
                        break;
                }
            });

        });
    </script>
@endsection


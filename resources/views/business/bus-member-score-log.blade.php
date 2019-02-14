@extends('admin.layouts.parent')
@section('page-title')
{{empty($user_info->nickname) ? $user_info->mobile : $user_info->nickname}}用户积分记录
@endsection
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>@yield('page-title')</h5>
                </div>
                <div class="ibox-content">
                    <div class="row form-horizontal">
                        <form action="{{route('get-bus-member-score-log')}}">
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
                                <th>积分项目</th>
                                <th>消耗/增加积分</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($list))
                                @foreach($list as $item)
                                    <tr>
                                        <td>{{ date('Y-m-d H:i:s',$item->add_time) }}</td>
                                        <td>{{ $item->msg }}</td>
                                        <td>{{ $item->score }}</td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="text-right">
                        @if(!empty($list))
                            {{ $list->appends(['start_date'=>$params['start_date'],'end_date'=>$params['end_date'],'id'=>$params['id']])->links() }}
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

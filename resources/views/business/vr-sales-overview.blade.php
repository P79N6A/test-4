@extends('business.layouts.frame-parent')
@section('page-title','VR机台营收概况')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>@yield('page-title')</h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <form action="{{ route('business.vr-sales-overview') }}">
                            <div class="col-sm-10">
                                <div class="row form-horizontal">
                                    <div class="col-sm-5 m-b-xs">
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">订单创建时间</label>
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
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <select name="store_id" class="form-control">
                                                    <option value="0">请选择门店</option>
                                                    @if(!empty($stores))
                                                        @foreach($stores as $store)
                                                            <option @if($store->id == $params['store_id']) selected @endif value="{{ $store->id }}">{{ $store->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <input class="form-control" name="machine_name" value="{{ $params['machine_name'] }}" placeholder="请输入机台名称">
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
                                <th>门店</th>
                                <th>机台名称</th>
                                <th>营收总金额</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($list))
                                @foreach($list as $item)
                                    <tr>
                                        <td>{{ $item->store_name }}</td>
                                        <td>{{ $item->machine_name }}</td>
                                        <td>{{ $item->sales }}</td>
                                        <td><a href="{{ route('business.vr-machine-sales-flow',['id'=>$item->machine_id]) }}" class="btn btn-sm btn-success">查看详情</a></td>
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
                            {{ $list->appends(['start_date'=>$params['start_date'],'end_date'=>$params['end_date'],'store_id'=>$params['store_id'],'machine_name'=>$params['machine_name']])->links() }}
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
                    end_date.max = laydate.now()
                }
            };
            laydate(start_date);
            laydate(end_date);
        });
    </script>
@endsection


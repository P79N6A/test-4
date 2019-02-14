@extends('business.layouts.frame-parent')
@section('page-title','套餐分析')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>@yield('page-title')</h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <form action="{{ route('business.package-analysis') }}" method="get">
                            <div class="col-sm-10">
                                <div class="row form-horizontal">
                                    <div class="col-sm-5 m-b-xs">
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">时间段筛选</label>
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
                                    <div class="col-sm-2">
                                        <div class="form-group form-inline">
                                            <label>成交总金额排序</label>
                                            <select name="money" class="form-control">
                                                <option @if($money == 0) selected @endif value="0">默认</option>
                                                <option @if($money == 1) selected @endif value="1">升序</option>
                                                <option @if($money == 2) selected @endif value="2">降序</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group form-inline">
                                            <label>成交笔数排序</label>
                                            <select name="orders" class="form-control">
                                                <option @if($orders == 0) selected @endif value="0">默认</option>
                                                <option @if($orders == 1) selected @endif value="1">升序</option>
                                                <option @if($orders == 3) selected @endif value="2">降序</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group form-inline">
                                            <label>支付人数排序</label>
                                            <select name="payers" class="form-control">
                                                <option @if($payers == 0) selected @endif value="0">默认</option>
                                                <option @if($payers == 1) selected @endif value="1">升序</option>
                                                <option @if($payers == 2) selected @endif value="2">降序</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-2 m-b-xs text-right">
                                <button class="btn btn-primary btn-lg" type="submit">
                                    <i class="fa fa-search"></i>
                                </button>
                                <button id="export" class="btn btn-success btn-lg" data-url="{{ route('business.package-analysis-export') }}" type="button">
                                    <i class="fa fa-download"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>序号</th>
                                <th>套餐名称</th>
                                <th>品牌（门店）</th>
                                <th>成交总金额</th>
                                <th>成交笔数</th>
                                <th>支付人数</th>
                                <th>下单人数</th>
                                <th>支付转化率</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($packages))
                                @foreach($packages as $package)
                                    <tr>
                                        <td>{{ $package->id }}</td>
                                        <td>{{ $package->name }}</td>
                                        <td>{{ $package->brand_name }}（{{ $package->store_name }}）</td>
                                        <td>{{ $package->order_sum }}</td>
                                        <td>{{ $package->success_order_count }}</td>
                                        <td>{{ $package->payed_order_count }}</td>
                                        <td>{{ $package->unpayed_order_count }}</td>
                                        <td>{{ $package->pay_percent }}</td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="text-right">
                        @if(!empty($packages))
                            {{ $packages->appends([
                                'start_date' => $start_date,
                                'end_date' => $end_date,
                                'money' => $money,
                                'orders' => $orders,
                                'payers' => $payers
                            ])->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="/business/js/plugins/layer/laydate/laydate.js"></script>
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

            $('select[name=money]').change(function (){
                if(location.href.match(/\?/)){
                    location.href += '&money=' + $(this).val();
                }else{
                    location.href += '?money=' + $(this).val();
                }
            });

            $('select[name=orders]').change(function (){
                if(location.href.match(/\?/)){
                    location.href += '&orders=' + $(this).val();
                }else{
                    location.href += '?orders=' + $(this).val();
                }
            });

            $('select[name=payers]').change(function (){
                if(location.href.match(/\?/)){
                    location.href += '&payers=' + $(this).val();
                }else{
                    location.href += '?payers=' + $(this).val();
                }
            });

            $('#export').click(function(){
                $('form').attr('action',$(this).data('url')).submit().attr('action',location.href);
            });

        });
    </script>
@endsection

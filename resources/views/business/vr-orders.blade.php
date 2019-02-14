@extends('business.layouts.frame-parent')
@section('page-title','VR机台订单执行情况表')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>@yield('page-title')</h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <form action="{{ route('business.vr-orders') }}">
                            <div class="col-sm-10">
                                <div class="row form-horizontal">
                                    <div class="col-sm-5 m-b-xs">
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">消费时间</label>
                                            <div class="col-sm-8">
                                                <div class="input-daterange input-group">
                                                    <input type="text" class="form-control" name="start_date" value="{{ $params['start_date'] }}" id="start_date">
                                                    <span class="input-group-addon">至</span>
                                                    <input type="text" class="form-control" name="end_date" value="{{ $params['end_date'] }}" id="end_date">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-5">
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">门店选择</label>
                                            <div class="col-sm-8">
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
                                            <button class="btn btn-sm btn-primary" type="submit">搜索</button>
                                            <button id="export" type="button" data-url="{{ route('business.vr-orders-export') }}"  class="btn btn-sm btn-success">导出</button>
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
                                <th>订单号/交易号</th>
                                <th>机台名称/游戏名称</th>
                                <th>消费门店/买家</th>
                                <th>创建时间/支付时间/消费时间</th>
                                <th>游戏状态</th>
                                <th>是否已经支付</th>
                                <th>订单金额/已消费金额/未消费金额</th>
                                <th>服务费/实收金额</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($orders))
                                @foreach($orders as $order)
                                    <tr>
                                        <td>
                                            订单号：{{ $order->no }} <br/>
                                            交易号：{{ $order->pay_no }}
                                        </td>
                                        <td>
                                            机台名称：{{ $order->machine_name }}<br/>
                                            游戏名称: {{ $order->game_name }}
                                        </td>
                                        <td>
                                            消费门店:{{ $order->store_name }}<br/>
                                            买家: {{ $order->mobile }}
                                        <td>
                                            创建时间：{{ $order->create_date }}<br>
                                            支付时间：{{ $order->pay_date }}<br>
                                            消费时间：{{ $order->use_date }}
                                        </td>
                                        <td>
                                            @if($order->status == 0)
                                                <span class="label label-default">未使用</span>
                                            @elseif($order->status == 1)
                                                <span class="label label-primary">已启动</span>
                                            @elseif($order->status == 2)
                                                <span class="label label-info">游戏中</span>
                                            @elseif($order->status == 3)
                                                <span class="label label-success">已使用</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($order->is_pay == 1)
                                                <span class="label label-success">是</span>
                                            @else
                                                <span class="label label-default">否</span>
                                            @endif
                                        </td>
                                        <td>
                                            订单金额：{{ $order->amount }}<br>
                                            已消费金额：@if($order->status == 3){{ $order->consume_amount }}@else 0 @endif <br>
                                            未消费金额：{{ $order->amount - $order->consume_amount - $order->vr_charge }}
                                        </td>
                                        <td>
                                            @if($order->charge_type != 3)
                                                <span>服务费：{{ $order->vr_charge }}</span><br>
                                                <span>实收金额：</span>
                                                @if($order->status == 3){{ $order->consume_amount - $order->vr_charge }} @else 0 @endif
                                            @else
                                                <span>服务费：{{ $order->game_charge }}</span><br>
                                                <span>实收金额：</span>
                                                @if($order->status == 3){{ $order->consume_amount - $order->game_charge }} @else 0 @endif
                                            @endif
                                        </td>
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
                        @if(!empty($orders))
                            {{ $orders->links() }}
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

            $('#export').click(function(){
                $('form').attr('action',$(this).data('url')).submit().attr('action',location.href);
            });
        });
    </script>
@endsection


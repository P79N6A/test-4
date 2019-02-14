@extends('business.layouts.frame-parent')
@section('page-title','订单管理')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>@yield('page-title')</h5>
                    <div class="ibox-tools">
                        <a href="{{ route('business.order-analysis') }}" class="btn btn-primary btn-xs">订单分析</a>
                    </div>
                </div>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <form action="{{ route('business.order-management') }}" method="get">
                        <div class="col-sm-10">
                            <div class="row form-horizontal">
                                <div class="col-sm-6 m-b-xs">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">下单时间</label>
                                        <div class="col-sm-9">
                                            <div class="input-daterange input-group">
                                                <input type="text" class="form-control" id="addtime_start" name="addtime_start"
                                                       value="{{ $addtime_start }}">
                                                <span class="input-group-addon">至</span>
                                                <input type="text" class="form-control" id="addtime_end" name="addtime_end"
                                                       value="{{ $addtime_end }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 m-b-xs">
                                    <label class="col-sm-3 control-label">兑换时间</label>
                                    <div class="col-sm-9">
                                        <div class="input-daterange input-group">
                                            <input type="text" class="form-control" id="converttime_start" name="convert_start"
                                                   value="{{ $convert_start }}">
                                            <span class="input-group-addon">至</span>
                                            <input type="text" class="form-control" id="converttime_end" name="convert_end"
                                                   value="{{ $convert_end }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row form-horizontal">
                                <div class="col-sm-6 m-b-xs">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">金额范围</label>
                                        <div class="col-sm-9">
                                            <div class="input-daterange input-group">
                                                <input type="text" class="form-control" name="price_start"
                                                       value="{{ $price_start }}">
                                                <span class="input-group-addon">至</span>
                                                <input type="text" class="form-control" name="price_end"
                                                       value="{{ $price_end }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 m-b-xs">
                                    <label class="col-sm-3 control-label">账号</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="account" value="{{ $account }}"
                                               class="form-control" placeholder="输入手机号码">
                                    </div>
                                </div>
                            </div>
                            <div class="row form-horizontal">
                                <div class="col-sm-6 m-b-xs">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">门店名称</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="store" value="{{ $store }}"
                                                   class="form-control" placeholder="门店关键字">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 m-b-xs">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">套餐名称</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="package" value="{{ $package }}"
                                                   class="form-control" placeholder="套餐关键字">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row form-horizontal">
                                <div class="col-sm-6 m-b-xs">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">订单号</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="order_no" value="{{ $order_no }}"
                                                   class="form-control" placeholder="输入订单号">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 m-b-xs">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">交易状态</label>
                                        <div class="col-sm-9">
                                            <select class="form-control" name="status">
                                                <option value="0" @if($status == 0) selected @endif >全部</option>
                                                <option value="1" @if($status == 1) selected @endif >待付款</option>
                                                <option value="2" @if($status == 2) selected @endif >未使用</option>
                                                <option value="3" @if($status == 3) selected @endif >已使用</option>
                                                <option value="4" @if($status == 4) selected @endif >已过期</option>
                                                <option value="6" @if($status == 6) selected @endif >已退款</option>
                                                <option value="7" @if($status == 7) selected @endif >已取消</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row form-horizontal">
                                <div class="col-sm-6 m-b-xs">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">支付平台</label>
                                        <div class="col-sm-9">
                                            <select class="form-control" name="pay_type">
                                                <option value="0" @if($pay_type == 0) selected @endif >全部</option>
                                                <option value="1" @if($pay_type == 1) selected @endif >支付宝</option>
                                                <option value="2" @if($pay_type == 2) selected @endif >微信APP</option>
                                                <option value="3" @if($pay_type == 3) selected @endif >微信公众号</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2 m-b-xs text-right">
                            <button class="btn btn-primary btn-lg" type="submit">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="table-responsive">
                    <div class="sm-col-12">
                        <span class="label label-primary">记录数：@if(!empty($orders)){{ count($orders) }} @else 0 @endif 条</span>
                        <span class="label label-primary">总金额： {{ round($sum,2) }}元</span>
                    </div>
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>订单号</th>
                            <th>账号</th>
                            <th>门店</th>
                            <th>套餐</th>
                            <th>
                                价格<br>
                                实际支付价格
                            </th>
                            <th>支付信息</th>
                            <th>下单时间</th>
                            <th>支付时间</th>
                            <th>兑换时间</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(!empty($orders))
                            @foreach($orders as $order)
                                <tr>
                                    <td>{{ $order->order_id }}</td>
                                    <td>{{ $order->mobile }}</td>
                                    <td>{{ $order->store_name }}</td>
                                    <td>{{ $order->package_name }}</td>
                                    <td>
                                        ￥{{ $order->price }}<br>
                                        ￥{{ $order->pay_price }}
                                    </td>
                                    <td>
                                        状态：
                                        @if($order->status == 0)待付款
                                        @elseif($order->status == 1)未使用
                                        @elseif($order->status == 2)已使用
                                        @elseif($order->status == 3)已过期
                                        @elseif($order->status == 5)已退款
                                        @elseif($order->status == 6)已取消
                                        @endif
                                        <br>
                                        平台：
                                        @if($order->payment_type == 0)
                                        @elseif($order->payment_type == 1)支付宝
                                        @elseif($order->payment_type == 2)微信APP
                                        @elseif($order->payment_type == 3)微信公众号
                                        @endif
                                        <br>
                                        交易号：{{ $order->pay_no }}<br>
                                    </td>
                                    <td>{{ date('Y-m-d H:i:s',$order->addtime) }}</td>
                                    <td>@if(!empty($order->pay_date)) {{ date('Y-m-d H:i:s',$order->pay_date) }} @endif</td>
                                    <td>@if(!empty($order->convert_time)) {{ date('Y-m-d H:i:s',$order->convert_time) }} @endif</td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
                <div class="text-right">
                    @if(!empty($orders))
                        {{ $orders->appends([
                            'addtime_start'=>$price_start,
                            'addtime_end'=>$price_end,
                            'covert_start'=>$convert_start,
                            'price_start'=>$price_start,
                            'price_end'=>$price_end,
                            'account'=>$account,
                            'order_no'=>$order_no,
                            'status'=>$status,
                            'pay_type'=>$pay_type
                        ])->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>
    <script src="/business/js/plugins/layer/laydate/laydate.js"></script>
    <script>
        $(function () {
            var addtime_start = {
                elem: "#addtime_start",
                format: "YYYY-MM-DD hh:mm:ss",
                min: '2010-01-01 00:00:00',
                max: laydate.now(),
                istime: true,
                istoday: false,
                choose: function(datas) {
                    addtime_end.min = datas;
                }
            };
            var addtime_end = {
                elem: "#addtime_end",
                format: "YYYY-MM-DD hh:mm:ss",
                min: '2010-01-01 00:00:00',
                max: laydate.now(),
                istime: true,
                istoday: false,
                choose: function(datas) {
                    addtime_start.max = datas
                }
            };
            var converttime_start = {
                elem: "#converttime_start",
                format: "YYYY-MM-DD hh:mm:ss",
                min: '2010-01-01 00:00:00',
                max: laydate.now(),
                istime: true,
                istoday: false,
                choose: function(datas) {
                    converttime_end.min = datas;
                }
            };
            var converttime_end = {
                elem: "#converttime_end",
                format: "YYYY-MM-DD hh:mm:ss",
                min: '2010-01-01 00:00:00',
                max: laydate.now(),
                istime: true,
                istoday: false,
                choose: function(datas) {
                    converttime_start.max = datas
                }
            };
            laydate(addtime_start);
            laydate(addtime_end);
            laydate(converttime_start);
            laydate(converttime_end);



        });
    </script>
@endsection

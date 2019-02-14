<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="favicon.ico"> <link href="merchant/css/bootstrap.min14ed.css?v=3.3.6" rel="stylesheet">
    <link href="merchant/css/font-awesome.min93e3.css?v=4.4.0" rel="stylesheet">
    <!-- Data Tables -->
    <link href="merchant/css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">
    <link href="merchant/css/animate.min.css" rel="stylesheet">
    <link href="merchant/css/style.min862f.css?v=4.1.0" rel="stylesheet">
    <link href="merchant/css/jquery.datetimepicker.css?v=4.1.0" rel="stylesheet">
</head>
<body>
    <div class="ibox">
        <div class="row">
            <div class="row">
                <div class="col-sm-4">
                    <a href="/member-orders" class="btn btn-sm @if(empty($range)) btn-info @endif">全部</a>
                    <a href="/member-orders?range=1" class="btn btn-sm @if(isset($range) && $range == 1 ) btn-info @endif">今天</a>
                    <a href="/member-orders?range=2" class="btn btn-sm @if(isset($range) && $range == 2 ) btn-info @endif">昨天</a>
                    <a href="/member-orders?range=3" class="btn btn-sm @if(isset($range) && $range == 3 ) btn-info @endif">最近7天</a>
                    <a href="/member-orders?range=4" class="btn btn-sm @if(isset($range) && $range == 4 ) btn-info @endif">最近30天</a>
                </div>
                <form action="/member-orders">
                    <div class="col-sm-8">
                        <span>订单创建时间&nbsp;&nbsp;</span>
                        <input id="start_date" name="start_date" @if(!empty($start_date)) value="{{ date('Y-m-d H:i:s',$start_date) }}" @endif> -
                        <input id="end_date" name="end_date" @if(!empty($end_date)) value="{{ date('Y-m-d H:i:s',$end_date) }}" @endif>
                        <input name="store" placeholder="搜索门店" @if(!empty($store_name)) value="{{ $store_name }}" @endif>
                        <button class="btn btn-primary btn-sm">搜索</button>
                    </div>
                </form>
            </div>
            <div class="row">
                <div class="col-sm-6 col-sm-push-1">
                    <ul class="pagination">
                        <li class=" @if( is_numeric($status) && $status == 1) active @endif"><span class="state-tabs" style="cursor:pointer;" data-url="/member-orders?s=1">待付款</span></li>
                        <li class=" @if( is_numeric($status) && $status == 2) active @endif"><span class="state-tabs" style="cursor:pointer;" data-url="/member-orders?s=2">成功</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="ibox">
        <table class="table table-striped table-bordered table-hover dataTables-example">
            <thead>
            <tr>
                <th>用户名</th>
                <th>门店名</th>
                <th>套餐名称</th>
                <th>充值币数</th>
                <th>支付状态</th>
                <th>付款方式</th>
                <th>充值金额</th>
                <th>下单时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($orders as $order)
                 <tr>
                     <td> {{ $order->username }}</td>
                     <td> {{ $order->store_name }}</td>
                     <td> {{ $order->package_name }}</td>
                     <td> {{ $order->recharge_coins }}</td>
                     <td>
                         @if($order->pay_status == 0)
                             未付款
                         @elseif($order->pay_status == 1)
                             已付款
                         @endif
                     </td>
                     <td>
                         @if($order->payment_type == 1)
                             支付宝
                         @elseif($order->payment_type == 2)
                            微信
                         @endif
                     </td>
                     <td>{{ $order->money }} 元</td>
                     <td>
                         @if($order->addtime)
                            {{ date('Y-m-d H:i:s',$order->addtime) }}
                         @endif
                     </td>
                     <td>
                         <a href="javascript:void(0);" class="btn btn-white btn-sm"><i class="fa fa-edit"></i>未定义</a> |
                         <a href="javascript:void(0);" class="btn btn-white btn-sm"><i class="fa fa-trash"></i>未定义</a>
                     </td>
                 </tr>
             @endforeach
            </tbody>
        </table>
    </div>
    <div class="col-lg-push-1">{!! $orders->appends(['status'=>$status,'range'=>$range])->links() !!}</div>
</body>
</html>
<script src="/merchant/js/jquery.min.js"></script>
<script type="text/javascript" src="/merchant/js/jquery.datetimepicker.full.min.js"></script>
<script>
    $(function(){
        $('.state-tabs').click(function(){
            location.href = $(this).data('url');
        });

        $('#start_date').click(function(){
            $(this).datetimepicker({
                format:'Y-m-d H:i:s'
            });
        });
        $('#end_date').click(function(){
            $(this).datetimepicker({
                format:'Y-m-d H:i:s'
            });
        });

    });
</script>

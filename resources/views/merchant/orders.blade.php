<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="favicon.ico"> <link href="merchant/css/bootstrap.min14ed.css?v=3.3.6" rel="stylesheet">
    <link href="merchant/css/font-awesome.min93e3.css?v=4.4.0" rel="stylesheet">
    <link href="merchant/css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">
    <link href="merchant/css/animate.min.css" rel="stylesheet">
    <link href="merchant/css/style.min862f.css?v=4.1.0" rel="stylesheet">
    <link href="merchant/css/jquery.datetimepicker.css?v=4.1.0" rel="stylesheet">
</head>
<body>
    <div class="ibox">
        <div class="row">
            <div class="col-sm-4">
                <a href="/orders" class="btn btn-sm @if($range == 0) btn-info @endif">全部</a>
                <a href="/orders?range=1" class="btn btn-sm @if($range == 1 ) btn-info @endif">今天</a>
                <a href="/orders?range=2" class="btn btn-sm @if($range == 2 ) btn-info @endif">昨天</a>
                <a href="/orders?range=3" class="btn btn-sm @if($range == 3 ) btn-info @endif">最近7天</a>
                <a href="/orders?range=4" class="btn btn-sm @if($range == 4 ) btn-info @endif">最近30天</a>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6 col-sm-push-1">
                <ul class="pagination">
                    <li class=" @if( is_numeric($status) && $status == 0) active @endif"><span class="state-tabs" style="cursor:pointer;" data-url="/orders?s=0">待付款</span></li>
                    <li class=" @if( is_numeric($status) && $status == 1) active @endif"><span class="state-tabs" style="cursor:pointer;" data-url="/orders?s=1">成功</span></li>
                    <li class=" @if( is_numeric($status) && $status == 2) active @endif"><span class="state-tabs" style="cursor:pointer;" data-url="/orders?s=2">已使用</span></li>
                    <li class=" @if( is_numeric($status) && $status == 3) active @endif"><span class="state-tabs" style="cursor:pointer;" data-url="/orders?s=3">已过期</span></li>
                    <li class=" @if( is_numeric($status) && $status == 4) active @endif"><span class="state-tabs" style="cursor:pointer;" data-url="/orders?s=4">退款中</span></li>
                    <li class=" @if( is_numeric($status) && $status == 5) active @endif"><span class="state-tabs" style="cursor:pointer;" data-url="/orders?s=5">已退款</span></li>
                </ul>
            </div>
        </div>
        <form action="" method="get">
                <div class="col-sm-8">
                    <span>订单创建时间&nbsp;&nbsp;</span>
                    <input id="start_date" name="start_date" @if(!empty($start_date)) value="{{ $start_date }}" @endif> -
                    <input id="end_date" name="end_date" @if(!empty($end_date)) value="{{ $end_date }}" @endif>
                    <input name="store" placeholder="搜索门店" @if(!empty($store_name)) value="{{ $store_name }}" @endif>
                    <button class="btn btn-primary btn-sm">搜索</button>
                </div>
        </form>
    </div>

    <div class="ibox">
        <table class="table table-bordered table-striped">
            <tr>
                <th>商家实收</th>
                <th>商家优惠</th>
                <th>订单金额</th>
            </tr>
            <tr>
                <td>{{ $summary['exact_earned'] }}</td>
                <td>{{ $summary['discount'] }}</td>
                <td>{{ $summary['total'] }}</td>
            </tr>
        </table>
    </div>

    <div class="ibox">
        <table class="table table-striped table-bordered table-hover dataTables-example">
            <thead>
            <tr>
                <th>订单号</th>
                <th>套餐名称</th>
                <th>买家</th>
                <th>订单价格</th>
                <th>状态</th>
                <th>下单时间</th>
                <th>门店</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($orders as $order)
                 <tr>
                     <td> <a href="/orderdetail?id={{ $order->order_id }}">{{ $order->order_id }}</a></td>
                     <td> {{ $order->good_name }}</td>
                     <td> {{ $order->username }}</td>
                     <td>
                         <span>{{ $order->price }} 元</span><br>
                         <span>商家实收：{{ $order->pay_price }} 元</span><br>
                         <span>商家优惠：{{ $order->price - $order->pay_price }} 元</span>
                     </td>
                     <td>
                         @if($order->status == 0)
                             待付款
                         @elseif($order->status == 1))
                             可使用
                         @elseif($order->status == 2)
                             已使用
                         @elseif($order->status == 3)
                             已过期
                         @elseif($order->status == 4)
                             退款中
                         @elseif($order->status == 5)
                             已退款
                         @endif
                     </td>
                     <td> {{ date('Y-m-d H:i:s',$order->addtime) }}</td>
                     <td>{{ $order->store_name }}</td>
                     <td>
                         <a href="#" class="btn btn-white btn-sm"><i class="fa fa-edit"></i>未定义</a> |
                         <a href="#" class="btn btn-white btn-sm"><i class="fa fa-trash"></i>未定义</a>
                     </td>
                 </tr>
             @endforeach
            </tbody>
        </table>
    </div>
    <div class="col-lg-push-1">{!! $orders->appends(['range'=>$range,'start_date'=>$start_date,'end_date'=>$end_date,'store'=>$store_name,'s'=>$status])->links() !!}</div>
</body>
</html>
<script type="text/javascript" src="/merchant/js/jquery.min.js"></script>
<script type="text/javascript" src="/merchant/js/jquery.datetimepicker.full.min.js"></script>
<script>
    $(function(){
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

        $('.state-tabs').click(function(){
            location.href = $(this).data('url');
        });


    });
</script>
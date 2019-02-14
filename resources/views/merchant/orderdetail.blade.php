<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body>
<div>
    <table>
        <tr><td>订单ID：</td><td>{{ $order->order_id}}</td></tr>
        <tr><td>套餐ID：</td><td>{{ $order->good_id}}</td></tr>
        <tr><td>套餐名称：</td><td>{{ $order->good_name}}</td></tr>
        <tr><td>门店：</td><td>{{ $order->store_name}}</td></tr>
        <tr><td>商品价格：</td><td>{{ $order->price }}</td></tr>
        <tr><td>实际付款价格：</td><td>{{ $order->pay_price }}</td></tr>
        <tr><td>购买用户ID：</td><td>{{ $order->userid}}</td></tr>
        <tr><td>购买用户名：</td><td>{{ $order->username}}</td></tr>
        <tr><td>订单时间：</td><td>{{ date('Y-m-d H:i:s',$order->addtime) }}</td></tr>
        <tr>
            <td>状态：</td>
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
                    已退款
                @endif
            </td>
        </tr>
    </table>
</div>
</body>
</html>
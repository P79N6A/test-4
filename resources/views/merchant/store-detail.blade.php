<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>门店详情 - {{ $store->name }}</title>
</head>
<body>
    <div>
        <table>
            <tr><td width="100">名称：</td><td>{{ $store->name }}</td></tr>
            <tr><td width="100">品牌：</td><td>{{ $store->brand_name }}</td></tr>
            <tr><td width="100">描述：</td><td>{{ $store->description }}</td></tr>
            <tr><td width="100">地址：</td><td>{{$store->province}}-{{$store->city}}-{{$store->county}} {{ $store->address }}</td></tr>
            <tr><td width="100">固话：</td><td>{{ $store->tel }}</td></tr>
            <tr><td width="100">手机：</td><td>{{ $store->mobile }}</td></tr>
            <tr>
                <td width="100">状态：</td>
                <td> @if($store->status == 1) 正常营业 @else 关停 @endif </td>
            </tr>
            <tr><td width="100">开业时间：</td><td> @if($store->addtime) {{ date('Y-m-d H:i:s',$store->addtime) }} @endif</td></tr>
            <tr>
                <td>二维码：</td>
                <td>
                    <img style="cursor:pointer;" data-url="{{ route('merchant.downloadQrCode',['type'=>11,'id'=>$store->id]) }}" class="qrcode-preview" src="{{ route('merchant.showQrCode',['type'=>11,'id'=>$store->id]) }}" title="下载二维码">
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
<script type="text/javascript" src="/merchant/js/jquery.min.js"></script>
<script>
    $(function(){
        $('.qrcode-preview').click(function(){
            location.href = $(this).attr('data-url');
        });
    });
</script>

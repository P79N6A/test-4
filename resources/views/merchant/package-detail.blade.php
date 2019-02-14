<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>套餐详情 - {{ $detail->name }}</title>
    <link rel="stylesheet" type="text/css" href="merchant/css/bootstrap.min.css">
</head>
<body>
<div>
    <table>
        <tr><td>套餐名字：</td><td>{{ $detail->name }}</td></tr>
        <tr><td>价格：</td><td>{{ $detail->price }} 元</td></tr>
        <tr>
            <td>可用门店：</td>
            <td>
                @foreach($availableStores as $store)
                    <p>{{ $store->name }}</p>
                @endforeach
            </td>
        </tr>
        <tr><td>套餐名字：</td><td>{{ $detail->name }}</td></tr>
        <tr><td>库存：</td><td>{{ $detail->stock }}</td></tr>
        <tr><td>销量：</td><td>{{ $detail->sales }}</td></tr>
        <tr><td>添加时间：</td><td>{{ date('Y-m-d H:i:s',$detail->addtime) }}</td></tr>
        <tr><td>过期时间：</td><td>{{ date('Y-m-d H:i:s',$detail->expire_date) }}</td></tr>

        @if(!empty($detail->sekill_data))
            <tr>
                <td>秒杀信息：</td>
                <td>
                    <table>
                        <tr>
                            <td>秒杀价格：</td>
                            <td>{{ $detail->sekill_data->price }}</td>
                        </tr>
                        <tr>
                            <td>秒杀库存：</td>
                            <td>{{ $detail->sekill_data->stock }}</td>
                        </tr>
                        <tr>
                            <td>秒杀限购：</td>
                            <td>{{ $detail->sekill_data->buy_limit }}</td>
                        </tr>
                        <tr>
                            <td>秒杀开始时间：</td>
                            <td>{{ date('Y-m-d H:i:s',$detail->sekill_data->start_date) }}</td>
                        </tr>
                        <tr>
                            <td>秒杀结束时间：</td>
                            <td>{{ date('Y-m-d H:i:s',$detail->sekill_data->end_date) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        @endif

        <tr>
            <td>二维码列表：</td>
            <td>
                <div>
                    @foreach($availableStores as $availableStore)
                        <div style="display:inline-block">
                            <a class="btn btn-info" title="下载该门店下本套餐的二维码" target="_blank" href="{{ route('merchant.downloadQrCode',['type'=>39,'store_id'=>$availableStore->id,'id'=>$detail->id]) }}">{{ $availableStore->name }}</a>
                        </div>
                    @endforeach

                </div>
            </td>
        </tr>
        <tr><td></td><td><a href="javascript:history.back(-1);" class="btn btn-s btn-info">返回</a></td></tr>
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
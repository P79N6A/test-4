<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>添加机台</title>
</head>
<body>
<div>
    <form class="form-add-machine" action="/add-machine" method="post">
        {{ csrf_field() }}
        <table>
            <tr>
                <td>名字：</td>
                <td><input name="name"></td>
            </tr>
            <tr>
                <td>产品类型：</td>
                <td>
                    <select name="product_id">
                        <option>请选择产品类型</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <td>设备：</td>
                <td>
                    <select name="dev_id">
                        <option>请选择设备</option>
                        @foreach($devices as $device)
                            <option value="{{ $device->id }}">{{ $device->name }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <td>是否可用：</td>
                <td>
                    <select name="usable">
                        <option value="1">是</option>
                        <option value="2">否</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>备注：</td>
                <td><textarea name="remarks"></textarea></td>
            </tr>
            <tr>
                <td></td>
                <td><button type="button" class="btn-add-machine">提交</button></td>
            </tr>
        </table>
    </form>
</div>
</body>
</html>
<script type="text/javascript" src="/merchant/js/jquery.min.js"></script>
<script type="text/javascript" src="/merchant/js/youyibao.js"></script>
<script type="text/javascript" src="/merchant/layer/layer/layer.js"></script>
<script>
    $(function(){
        $('.btn-add-machine').click(function(){
            youyibao.httpSend($('.form-add-machine'),'post',1);
        });




    });
</script>

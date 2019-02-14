<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>添加蓝牙设备</title>
</head>
<body>
<div>
    <form class="form-add-device" action="/add-bluetooth-device" method="post">
        <div>
            <select name="store_id">
                <option>请选择门店</option>
                @foreach($stores as $store)
                    <option value="{{ $store->id }}">{{ $store->name }}</option>
                @endforeach
            </select>
        </div>
        <label for="major">major：</label><input name="major"id="major">
        <label for="minor">minor：</label><input name="minor"id="minor">
        <label for="note">备注：</label><input name="note"id="note">
        <button type="button" class="btn-add-device">提交</button>
    </form>
</div>
</body>
</html>
<script type="text/javascript" src="/merchant/js/jquery.min.js"></script>
<script type="text/javascript" src="/merchant/js/youyibao.js"></script>
<script type="text/javascript" src="/merchant/layer/layer/layer.js"></script>
<script>
    $(function(){
        $('.btn-add-device').click(function(){
            youyibao.httpSend($('.form-add-device'),'post',1);
        });



    });
</script>

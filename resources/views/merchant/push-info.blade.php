<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>推送活动资讯</title>
</head>
<body>
<div>
    <form action="/push-info" method="post" class="form-push-info">
        {{ csrf_field() }}
        <input type="hidden" name="id" value="{{ $id }}">
        <div>
            <select name="store_id">
                <option>请选择门店</option>
                @foreach($stores as $store)
                    <option value="{{ $store->id }}">{{ $store->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <span>目标人群：</span>
            <p>
                <label for="consumer">在该门店消费过的用户：</label><input id="consumer" type="radio" name="target" value="1">
                <label for="visitor">门店访客：</label><input id="visitor" type="radio" name="target" value="2">
            </p>
            <div><button type="button" class="btn-push-info">提交</button></div>
        </div>
    </form>
</div>
</body>
</html>
<script type="text/javascript" src="/merchant/js/jquery.min.js"></script>
<script type="text/javascript" src="/merchant/js/youyibao.js"></script>
<script type="text/javascript" src="/merchant/layer/layer/layer.js"></script>
<script>
    $(function(){
        $('.btn-push-info').click(function(){
            youyibao.httpSend($('form.form-push-info'),'post',1);
        });


    });
</script>

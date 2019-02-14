<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>分配角色数据访问权限</title>
</head>
<body>
<div>
    <form action="/allocate-data-access-permission" method="post" class="form-allocate">
        <input type="hidden" name="id" value="{{ $role_id }}">
        <table border="1" cellspacing="0">
            <tr>
                <td>选择可访问哪些门店的数据：</td>
                <td>
                    @foreach($stores as $k=>$store)
                        <label for="store_{{ $k }}">{{ $store->name }}</label>
                        <input type="checkbox" id="store_{{ $k }}" name="store_ids[]" value="{{ $store->id }}" @if(in_array($store->id,$alloStores)) checked @endif>
                        <br>
                    @endforeach
                </td>
            </tr>
            <tr><td></td><td><button type="button" class="btn-allocate">提交</button></td></tr>
        </table>
    </form>
</div>
</body>
</html>
<script type="text/javascript" src="/merchant/js/jquery.min.js"></script>
<script type="text/javascript" src="/merchant/js/youyibao.js"></script>
<script type="text/javascript" src="/merchant/layer/layer/layer.js"></script>
<script>
    $(document).ready(function(){
        $('.btn-allocate').click(function(){
            youyibao.httpSend($('.form-allocate'),'post',1);
        });



    });
</script>

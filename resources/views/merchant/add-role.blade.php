<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>添加角色</title>
</head>
<body>
    <div>
        <form class="form-add-role" action="/add-role" method="post">
            {{ csrf_field() }}
            <table>
                <tr><td>角色名称：</td><td><input name="name"></td></tr>
                <tr><td>角色描述：</td><td><input name="description"></td></tr>
                <tr><td colspan="2"><button class="btn-add-role" type="button">提交</button></td></tr>
            </table>
        </form>
    </div>
</body>
</html>
<script type="text/javascript" src="merchant/js/jquery.min.js"></script>
<script type="text/javascript" src="merchant/js/youyibao.js"></script>
<script type="text/javascript" src="merchant/layer/layer/layer.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('.btn-add-role').click(function(){
            youyibao.httpSend($('.form-add-role'),'post',1);
        });
    });
</script>

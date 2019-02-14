<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>修改密码</title>
</head>
<body>
<div>
    <form action="/change-password" method="post" class="form-change-pwd">
        <table>
            <tr><td>旧密码：</td><td><input type="password" name="old_password" placeholder="旧密码"></td></tr>
            <tr><td>新密码：</td><td><input type="password" name="new_password" placeholder="新密码"></td></tr>
            <tr><td colspan="2"><button type="button" class="btn-change-pwd">修改</button></td></tr>
        </table>
    </form>
</div>
</body>
</html>
<script type="text/javascript" src="merchant/js/jquery.min.js"></script>
<script type="text/javascript" src="merchant/js/youyibao.js"></script>
<script type="text/javascript" src="/merchant/layer/layer/layer.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('.btn-change-pwd').click(function(){
            youyibao.httpSend($('form.form-change-pwd'),'post',1);
        });
    });

</script>

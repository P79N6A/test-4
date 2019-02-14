<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <div>
        <form class="form-add-permission" action="/store-permission" method="post">
            <table>
                <tr><td>名称：</td><td><input name="name" placeholder="唯一标识符"></td></tr>
                <tr><td>显示名称：</td><td><input name="display_name"></td></tr>
                <tr><td>描述：</td><td><input name="description"></td></tr>
                <tr><td colspan="2"><button type="button" class="btn-add-permission">提交</button> </td></tr>
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
        $('.btn-add-permission').click(function(){
            youyibao.httpSend($('form.form-add-permission'),'post',1);
        });
    });
</script>

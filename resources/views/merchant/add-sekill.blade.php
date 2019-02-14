<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>参与秒杀</title>
    <link rel="stylesheet" type="text/css" href="/merchant/css/jquery.datetimepicker.css">
</head>
<body>
<form action="/add-sekill" method="post" class="form-add-sekill">
    <table>
        <input type="hidden" name="package_id" value="{{ $package->id }}">
        <tr><td>秒杀价格：</td><td><input name="price"></td></tr>
        <tr><td>秒杀库存：</td><td><input name="stock"></td></tr>
        <tr><td>秒杀限购：</td><td><input name="buy_limit"></td></tr>
        <tr>
            <td>秒杀起始时间：</td>
            <td>
                <input id="start_date" name="start_date" placeholder="双击选择时间"> -
                <input id="end_date" name="end_date" placeholder="双击选择时间">
            </td>
        </tr>
        <tr><td></td><td><button type="button" class="btn-add-sekill">提交</button></td></tr>
    </table>
</form>
</body>
</html>
<script type="text/javascript" src="/merchant/js/jquery.min.js"></script>
<script type="text/javascript" src="/merchant/js/youyibao.js"></script>
<script type="text/javascript" src="/merchant/js/jquery.datetimepicker.full.min.js"></script>
<script type="text/javascript" src="merchant/layer/layer/layer.js"></script>
<script>
    $(function(){
        $('#start_date').click(function(){
            $(this).datetimepicker({
                format:'Y-m-d H:i:s'
            });
        });
        $('#end_date').click(function(){
            $(this).datetimepicker({
                format:'Y-m-d H:i:s'
            });
        });

        $('.btn-add-sekill').click(function(){
            youyibao.httpSend($('.form-add-sekill'),'post',1);
        });

    });
</script>

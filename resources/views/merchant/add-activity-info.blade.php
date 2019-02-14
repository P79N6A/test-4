<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body>
<div>
    <form class="form-add-activity-info" method="post" action="/store-activity-info">
        <table>
            <tr>
                <td>门店：</td>
                <td>
                    <select name="store_id">
                        <option value="">请选择门店</option>
                        @foreach($stores as $store)
                            <option value="{{ $store->id }}">{{ $store->name }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <td>标题</td>
                <td><input name="title"></td>
            </tr>
            <tr>
                <td>描述：</td>
                <td><textarea name="description"></textarea></td>
            </tr>
            <tr>
                <td>内容：</td>
                <td><textarea name="content"></textarea></td>
            </tr>
            <tr>
                <td>是否可以推送：</td>
                <td>
                    <select name="push_flag">
                        <option value="0">否</option>
                        <option value="1">是</option>
                    </select>
                </td>
            </tr>
            <tr><td colspan="2"><button type="button" class="btn-add-info">提交</button></td></tr>
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
        $('.btn-add-info').click(function(){
            youyibao.httpSend($('form.form-add-activity-info'),'post',1);
        });
    });
</script>
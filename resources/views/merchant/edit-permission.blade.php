<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <div>
        <form class="form-add-permission" action="/update-permission" method="post">
            <table>
                <input type="hidden" name="id" value="{{ $permission->id }}">
                <tr><td>名称：</td><td><input name="name" value="{{ $permission->name }}" placeholder="唯一标识符"></td></tr>
                <tr><td>显示名称：</td><td><input name="display_name" value="{{ $permission->display_name }}"></td></tr>
                <tr><td>描述：</td><td><input name="description" value="{{ $permission->description }}"></td></tr>
                <tr>
                    <td>状态：</td>
                    <td>
                        <select name="status">
                            <option value="1" @if($permission->status == 1) selected @endif >启用</option>
                            <option value="0" @if($permission->status == 0) selected @endif >禁用</option>
                        </select>
                    </td>
                </tr>
                <tr><td colspan="2"><button type="button" class="btn-add-permission">提交</button> </td></tr>
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
        $('.btn-add-permission').click(function(){
            youyibao.httpSend($('form.form-add-permission'),'post',1);
        });
    });
</script>

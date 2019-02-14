<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../merchant/css/style.min.css?v=2.1.0" rel="stylesheet">
</head>

<body class="gray-bg">
    <div id="modal-form" class="modal fade" aria-hidden="true">
        <div class="modal-content">
            <div class="row">
                <div class="col-sm-4 ">
                    <form role="form" class="form-editrole" method="post" action="/edit-role">
                        {{ csrf_field() }}
                        <input type="hidden" name="id" value="{{ $role->id }}" >
                        <div class="form-group">
                            <label>角色名称：</label>
                            <input placeholder="唯一标识角色的名称" name="name" value="{{ $role->name }}" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>角色描述：</label>
                            <input placeholder="角色描述（可选）" name="description"  value="{{ $role->description }}" class="form-control">
                        </div>
                        <div>
                            <label>状态：</label>
                            <select name="status">
                                <option value="0" @if($role->status == 0) selected @endif >禁用</option>
                                <option value="1" @if($role->status == 1) selected @endif >启用</option>
                            </select>
                        </div>
                        <div>
                            <button class="btn-sub btn btn-sm btn-primary pull-right m-t-n-xs" type="button">
                                <strong>提交</strong>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
<script type="text/javascript" src="merchant/js/jquery.min.js"></script>
<script type="text/javascript" src="merchant/js/youyibao.js"></script>
<script type="text/javascript" src="/merchant/layer/layer/layer.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('.btn-sub').click(function(){
            youyibao.httpSend($('form.form-editrole'),'post',1);
        });
    });
</script>
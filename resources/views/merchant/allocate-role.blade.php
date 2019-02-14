<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>权限列表</title>
    <link href="../merchant/css/bootstrap.min14ed.css?v=3.3.6" rel="stylesheet">
    <link href="../merchant/css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">
    <link href="../merchant/css/animate.min.css" rel="stylesheet">
    <link href="../merchant/css/style.min862f.css?v=4.1.0" rel="stylesheet">
</head>
<form method="post" class="form-allo-role" action="/allocate-role">
    <input type="hidden" name="uid" value="{{ $user_id }}">
    <div class="ibox">
        <table class="table table-striped table-bordered table-hover dataTables-example">
            <thead>
            <tr>
                <th>ID</th>
                <th>角色名称</th>
                <th>角色描述</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($roles as $role)
                <tr>
                    <td>{{ $role->id }}</td>
                    <td>{{ $role->name }}</td>
                    <td>{{ $role->description }}</td>
                    <td>
                        <input type="checkbox" name="ids[]" value="{{ $role->id }}" @if(in_array($role->id,$allocatedRoles)) checked @endif>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="row">
        <div class="col-sm-1 col-sm-push-11">
            <button type="button" class="btn btn-allo-role btn-sm btn-info" >提交</button>
        </div>
    </div>
</form>
</body>
</html>
<script type="text/javascript" src="merchant/js/jquery.min.js"></script>
<script type="text/javascript" src="merchant/js/youyibao.js"></script>
<script type="text/javascript" src="/merchant/layer/layer/layer.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('.btn-allo-role').click(function(){
            youyibao.httpSend($('.form-allo-role'),'post',1);
        });

    });

</script>

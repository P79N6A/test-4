<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>权限列表</title>
    <link href="merchant/css/bootstrap.min14ed.css?v=3.3.6" rel="stylesheet">
    <link href="merchant/css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">
    <link href="merchant/css/animate.min.css" rel="stylesheet">
    <link href="merchant/css/style.min862f.css?v=4.1.0" rel="stylesheet">
</head>
<form method="post" action="/alloperm">
    <input type="hidden" name="role_id" value="{{ $role_id }}">
    <div class="ibox">
        <table class="table table-striped table-bordered table-hover dataTables-example">
            <thead>
            <tr>
                <th>ID</th>
                <th>权限名称</th>
                <th>权限描述</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($perms as $perm)
                <tr>
                    <td> {{ $perm->id }}</td>
                    <td> {{ $perm->display_name }}</td>
                    <td> {{ $perm->description }}</td>
                    <td>
                        <input type="checkbox" name="ids[]" value=" {{ $perm->id }}" @if(in_array($perm->id,$myPerms)) checked @endif >
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="row">
        <div class="col-sm-1 col-sm-push-11">
            <input type="submit" class="btn btn-sm btn-info" value="提交">
        </div>
    </div>
</form>
</body>
</html>
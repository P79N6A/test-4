<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="favicon.ico"> <link href="merchant/css/bootstrap.min14ed.css?v=3.3.6" rel="stylesheet">
    <link href="merchant/css/font-awesome.min93e3.css?v=4.4.0" rel="stylesheet">
    <!-- Data Tables -->
    <link href="merchant/css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">
    <link href="merchant/css/animate.min.css" rel="stylesheet">
    <link href="merchant/css/style.min862f.css?v=4.1.0" rel="stylesheet">
</head>
<body>
    <div class="ibox">
        <table class="table table-striped table-bordered table-hover dataTables-example">
            <thead>
            <tr>
                <th>ID</th>
                <th>角色标识符</th>
                <th>角色显示名称</th>
                <th>角色描述</th>
                <th>创建日期</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($roles as $role)
                <tr>
                    <td>{{ $role->id }}</td>
                    <td>{{ $role->name }}</td>
                    <td>{{ $role->display_name }}</td>
                    <td>{{ $role->description }}</td>
                    <td>{{ $role->created_at }}</td>
                    <td>
                        <a href="/editrole?id={{ $role->id }}" class="btn btn-white btn-sm"><i class="fa fa-edit"></i>修改</a> |
                        <a href='javascript:void(0)' data-url="/delrole" data-type="id" data-id="{{ $role->id }}" class="delrole btn btn-white btn-sm"><i class="fa fa-trash"></i>删除</a> |
                        <a href="/attachperm?id={{ $role->id }}" class="btn btn-white btn-sm"><i class="fa fa-pencil"></i>分配菜单权限</a>
                        <a href="/attach-data-access-perm?id={{ $role->id }}" class="btn btn-white btn-sm"><i class="fa fa-pencil"></i>分配数据访问权限</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
<script type="text/javascript" src="merchant/js/jquery.min.js"></script>
<script type="text/javascript" src="merchant/js/youyibao.js"></script>
<script type="text/javascript" src="/merchant/layer/layer/layer.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('a.delrole').click(function(){
            youyibao.httpSend($(this),'get',1);
        });
    });
</script>
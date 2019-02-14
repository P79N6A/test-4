<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>权限列表</title>
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
                <th>权限标识符</th>
                <th>权限显示名称</th>
                <th>权限描述</th>
                <th>创建日期</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($permissions as $perm)
                <tr>
                    <td>{{  $perm->id }}</td>
                    <td>{{  $perm->name }}</td>
                    <td>{{  $perm->display_name }}</td>
                    <td>{{  $perm->description }}</td>
                    <td>{{  $perm->created_at }}</td>
                    <td>
                        <a href="editperm?id={{ $perm->id }}" class="btn btn-white btn-sm"><i class="fa fa-edit"></i>修改</a>
                        &nbsp;|&nbsp;
                        <a href='javascript:void(0)' data-url="/delperm" data-type='id' data-id="{{ $perm->id }}" class="btn btn-delperm btn-white btn-sm"><i class="fa fa-trash"></i>删除</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div>{!! $permissions->links() !!}</div>
</body>
</html>
<script type="text/javascript" src="merchant/js/jquery.min.js"></script>
<script type="text/javascript" src="merchant/js/youyibao.js"></script>
<script type="text/javascript" src="/merchant/layer/layer/layer.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('.btn-delperm').click(function(){
            youyibao.httpSend($(this),'get',1);
        });
    });
</script>
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
                <th>权限名称</th>
                <th>显示名称</th>
                <th>描述</th>
                <th>状态</th>
                <th>添加时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($permissions as $permission)
                <tr>
                    <td>{{ $permission->name }}</td>
                    <td>{{ $permission->display_name }}</td>
                    <td>{{ $permission->description }}</td>
                    <td>
                        @if($permission->status == 0)
                            禁用
                        @else
                            启用
                        @endif
                    </td>
                    <td>{{ date('Y-m-d H:i:s',$permission->addtime) }}</td>
                    <td>
                        <a href="/edit-permission?id={{ $permission->id }}" class="btn btn-white btn-sm"><i class="fa fa-edit"></i>修改</a> |
                        <a href='javascript:void(0)' data-url="/delete-permission" data-type="id" data-id="{{ $permission->id }}" class="del-permission btn btn-white btn-sm"><i class="fa fa-trash"></i>删除</a> |
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
        $('a.del-permission').click(function(){
            youyibao.httpSend($(this),'get',1);
        });
    });
</script>
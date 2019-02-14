<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理员列表</title>
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
                <th>名字</th>
                <th>邮件</th>
                <th>创建日期</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                 <tr>
                     <td> {{ $user->id }}</td>
                     <td> {{ $user->name }}</td>
                     <td> {{ $user->email }}</td>
                     <td> {{ date('Y-m-d H:i:s',$user->regtime) }}</td>
                     <td>
                         <a href="/edit-user?id={{  $user->id }}" class="btn btn-white btn-sm"><i class="fa fa-edit"></i>修改</a> |
                         <a href="javascript:;" data-url="/delete-user" data-type="id" data-id="{{ $user->id }}" class="btn btn-deluser btn-white btn-sm"><i class="fa fa-trash"></i>删除</a> |
                         <a href="/allocate-role?id={{  $user->id }}" class="btn btn-white btn-sm"><i class="fa fa-pencil"></i>分配角色</a>
                     </td>
                 </tr>
             @endforeach
            </tbody>
        </table>
    </div>
    <div class="col-lg-push-1">{!! $users->links() !!}</div>
</body>
</html>
<script type="text/javascript" src="merchant/js/jquery.min.js"></script>
<script type="text/javascript" src="merchant/js/youyibao.js"></script>
<script type="text/javascript" src="/merchant/layer/layer/layer.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('.btn-deluser').click(function(){
            youyibao.httpSend($(this),'geet',1);
        });
    });
</script>

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
                <th>名称</th>
                <th>产品类型</th>
                <th>设备类型</th>
                <th>是否可用</th>
                <th>在线状态</th>
                <th>创建时间</th>
                <th>创建用户</th>
                <th>备注</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($machines as $machine)
                 <tr>
                     <td> {{ $machine->name }}</td>
                     <td>{{ $machine->product_name }}</td>
                     <td> {{ $machine->dev_name }}</td>
                     <td> @if($machine->usable == 1) 是 @else 否 @endif</td>
                     <td> @if($machine->online_status == 1) 是 @else 否 @endif</td>
                     <td> {{ $machine->create_date }}</td>
                     <td> {{ $machine->create_user }}</td>
                     <td> {{ $machine->remarks }}</td>
                     <td>
                         <a href="javascript:;" class="btn btn-del-machine btn-sm btn-white" data-url="/del-machine?id={{ $machine->id }}">删除</a>
                     </td>
                 </tr>
             @endforeach
            </tbody>
        </table>
    </div>
    <div class="col-lg-push-1">@if(!empty($machines)){!! $machines->links() !!}@endif</div>
</body>
</html>
<script type="text/javascript" src="/merchant/js/jquery.min.js"></script>
<script type="text/javascript" src="/merchant/js/youyibao.js"></script>
<script type="text/javascript" src="/merchant/layer/layer/layer.js"></script>
<script type="text/javascript">
    $(function(){
        $('.btn-del-machine').click(function(){
            youyibao.httpSend($(this),'get',1);
        });


    });
</script>

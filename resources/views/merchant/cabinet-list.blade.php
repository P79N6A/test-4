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
                <th>门店</th>
                <th>硬件ID</th>
                <th>图片</th>
                <th>价格</th>
                <th>玩家数</th>
                <th>介绍</th>
                <th>玩法攻略</th>
                <th>是否推荐</th>
                <th>添加时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($cabinets as $cabinet)
                 <tr>
                     <td> {{ $cabinet->name }}</td>
                     <td> {{ $cabinet->store_id }}</td>
                     <td> {{ $cabinet->iot_id }}</td>
                     <td>
                         <img width="160" height="90" @if(!empty($cabinet->path)) src="{{ config('static.base_url').'/'.$cabinet->path }}" @endif alt="图片">
                     </td>
                     <td> {{ $cabinet->price }} 元</td>
                     <td> {{ $cabinet->players }}</td>
                     <td> {{ $cabinet->introduction }}</td>
                     <td> {{ $cabinet->guide }}</td>
                     <td> @if($cabinet->flag == 1) 是 @else 否 @endif </td>
                     <td> {{ date('Y-m-d H:i:s',$cabinet->addtime) }}</td>
                     <td>
                         <a href="/edit-cabinet?id={{ $cabinet->id }}" class="btn btn-white btn-sm"><i class="fa fa-edit"></i>修改</a> |
                         <a href="javascript:;" data-url="/delete-cabinet" data-type="id" data-id="{{ $cabinet->id }}" class="btn btn-del-cabinet btn-white btn-sm"><i class="fa fa-trash"></i>删除</a>
                     </td>
                 </tr>
             @endforeach
            </tbody>
        </table>
    </div>
    <div class="col-lg-push-1">@if(!empty($cabinets)){!! $cabinets->links() !!}@endif</div>
</body>
</html>
<script type="text/javascript" src="/merchant/js/jquery.min.js"></script>
<script type="text/javascript" src="/merchant/js/youyibao.js"></script>
<script type="text/javascript" src="/merchant/layer/layer/layer.js"></script>
<script type="text/javascript">
    $(function(){
        $('.btn-del-cabinet').click(function(){
            youyibao.httpSend($(this),'get',1);
        });
    });
</script>

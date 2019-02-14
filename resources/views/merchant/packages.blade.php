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
                <th>名字</th>
                <th>是否参与秒杀</th>
                <th>价格</th>
                <th>出币数</th>
                <th>库存</th>
                <th>销量</th>
                <th>状态</th>
                <th>过期时间</th>
                <th>添加时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($packages as $package)
                 <tr>
                     <td> {{ $package->id }}</td>
                     <td><a href="/package-detail?id={{ $package->id }}">{{ $package->name }}</a></td>
                     <td>@if($package->is_sekill == 1)是@else否@endif</td>
                     <td> {{ $package->price }} 元</td>
                     <td> {{ $package->coins }} </td>
                     <td> {{ $package->stock }} </td>
                     <td> @if($package->sales) {{ $package->sales }} @else 0 @endif </td>
                     <td>@if($package->expire_date < time()) 已过期 @else 未过期 @endif</td>
                     <td>
                         @if($package->expire_date)
                             {{ date('Y-m-d H:i:s',$package->expire_date) }}
                         @endif
                     </td>
                     <td>
                         @if($package->addtime)
                            {{ date('Y-m-d H:i:s',$package->addtime) }}
                         @endif
                     </td>
                     <td>
                         <a href="/editpackage?id={{ $package->id }}" class="btn btn-white btn-sm"><i class="fa fa-edit"></i>修改</a> |
                         <a data-url="/delpackage" data-type='id' data-id="{{ $package->id }}" class="del btn btn-white btn-sm"><i class="fa fa-trash"></i>删除</a>
                         @if($package->is_sekill == 0)
                            <a href="/add-sekill?id={{ $package->id }}" class="btn btn-white btn-sm"><i class="fa fa-trash"></i>参与秒杀</a>
                         @endif
                     </td>
                 </tr>
             @endforeach
            </tbody>
        </table>
    </div>
    <div class="col-lg-push-1">@if(!empty($packages)) {!! $packages->links() !!} @endif</div>
</body>
</html>
<script type="text/javascript" src="merchant/js/jquery.min.js"></script>
<script type="text/javascript" src="merchant/js/youyibao.js"></script>
<script type="text/javascript" src="/merchant/layer/layer/layer.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('a.del').click(function(){
            youyibao.httpSend($(this),'get',1);
        });
    });
</script>
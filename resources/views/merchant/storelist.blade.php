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
                <th>品牌</th>
                <th>地址</th>
                <th>蓝牙设备数</th>
                <th>状态</th>
                <th>添加时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($stores as $store)
                 <tr>
                     <td> {{ $store->id }}</td>
                     <td><a href="/store-detail?id={{ $store->id }}">{{ $store->name }}</a></td>
                     <td> {{ $store->brand_name }}</td>
                     <td> {{ $store->province }}-{{ $store->city }}-{{ $store->county }}&nbsp;&nbsp;{{ $store->address }}</td>
                     <td>
                         <a href=" @if($store->device_count > 0) /show-bluetooth-device?id={{ $store->id }} @else javascript:; @endif ">{{ $store->device_count }}</a>
                     </td>
                     <td>
                         @if($store->status == 1)
                             正常
                         @elseif($store->status == 2)
                            审核
                         @elseif($store->status == 3)
                            关停
                         @endif
                     </td>
                     <td>
                         @if($store->addtime)
                            {{ date('Y-m-d H:i:s',$store->addtime) }}
                         @endif
                     </td>
                     <td>
                         <a href="/store-detail?id={{ $store->id }}" class="btn btn-white btn-sm"><i class="fa fa-photo"></i>查看</a> |
                         <a href="/edit-store?id={{ $store->id }}" class="btn btn-white btn-sm"><i class="fa fa-edit"></i>修改</a> |
                         @if($store->status ==1 )
                             <a href="javascript:;" data-url="/operstore" data-type="id" data-id={{ $store->id }}&s=3" class="btn btn-oper btn-white btn-sm"><i class="fa fa-close"></i>关停</a> |
                         @elseif($store->status ==2 )
                             <a href="#" class="btn btn-white btn-sm"><i class="fa"></i>待审核</a> |
                         @else
                             <a href="javascript:;" data-url="/operstore" data-type="id" data-id={{ $store->id }}&s=1" class="btn btn-oper btn-white btn-sm"><i class="fa fa-edit"></i>重开业</a> |
                         @endif
                         <a href="javascript:;" data-url="/delstore" data-type="id" data-id="{{ $store->id }}" class="btn btn-delstore btn-white btn-sm"><i class="fa fa-trash"></i>删除</a>
                     </td>
                 </tr>
             @endforeach
            </tbody>
        </table>
    </div>
    <div class="col-lg-push-1">{!! $stores->links() !!}</div>
</body>
</html>
<script type="text/javascript" src="merchant/js/jquery.min.js"></script>
<script type="text/javascript" src="merchant/js/youyibao.js"></script>
<script type="text/javascript" src="/merchant/layer/layer/layer.js"></script>
<script type="text/javascript">
    $('.btn-delstore').click(function(){
        youyibao.httpSend($(this),'get',1);
    });
    $('.btn-oper').click(function(){
        youyibao.httpSend($(this),'get',1);
    });
</script>
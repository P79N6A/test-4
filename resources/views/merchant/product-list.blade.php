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
                <th>图片</th>
                <th>门店</th>
                <th>类别</th>
                <th>每局币数</th>
                <th>游戏介绍</th>
                <th>玩法攻略</th>
                <th>创建时间</th>
                <th>创建用户</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($products as $product)
                 <tr>
                     <td> {{ $product->name }}</td>
                     <td>
                         @if(!empty($product->path))
                            <img width="100"  src="{{ config('static.base_url').'/'.$product->path }}">
                         @endif
                     </td>
                     <td> {{ $product->store_name }}</td>
                     <td> {{ $product->product_type_name }}</td>
                     <td> {{ $product->coin_qty }}</td>
                     <td> {{ $product->introduction }}</td>
                     <td> {{ $product->guide }}</td>
                     <td> {{ $product->create_date }}</td>
                     <td> {{ $product->create_user }}</td>
                     <td>
                         <a href="javascript:;" class="btn btn-del-product btn-sm btn-white" data-url="/del-product?id={{ $product->id }}">删除</a>
                         &nbsp;|&nbsp;
                         <a class="btn btn-sm btn-white" href="/edit-product?id={{ $product->id }}">修改</a>
                     </td>
                 </tr>
             @endforeach
            </tbody>
        </table>
    </div>
    <div class="col-lg-push-1">@if(!empty($products)){!! $products->links() !!}@endif</div>
</body>
</html>
<script type="text/javascript" src="/merchant/js/jquery.min.js"></script>
<script type="text/javascript" src="/merchant/js/youyibao.js"></script>
<script type="text/javascript" src="/merchant/layer/layer/layer.js"></script>
<script type="text/javascript">
    $(function(){
        $('.btn-del-product').click(function(){
            youyibao.httpSend($(this),'get',1);
        });


    });
</script>

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
                <th>标题</th>
                <th>图片</th>
                <th>面额</th>
                <th>使用有效期</th>
                <th>可用门店</th>
                <th>添加时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($tickets as $ticket)
                 <tr>
                     <td> {{ $ticket->name }}</td>
                     <td> <img width="160" height="90" @if(!empty($ticket->path)) src="{{ config('static.base_url').'/'.$ticket->path }}" @endif alt="图片"></td>
                     <td> @if($ticket->denomination){{ $ticket->denomination }} 元 @endif</td>
                     <td>{{ date('Y-m-d H:i:s',$ticket->start_date) }} - {{ date('Y-m-d H:i:s',$ticket->expire_date) }}</td>
                     <td></td>
                     <td>{{ date('Y-m-d H:i:s',$ticket->addtime) }}</td>
                     <td>
                         <a href="/post-cash-coupon?id={{ $ticket->id }}" class="btn btn-white btn-sm"><i class="fa fa-edit"></i>发放</a>
                     </td>
                 </tr>
             @endforeach
            </tbody>
        </table>
    </div>
    <div class="col-lg-push-1">@if($tickets){!! $tickets->links() !!}@endif</div>
</body>
</html>
<script type="text/javascript" src="merchant/js/jquery.min.js"></script>
<script type="text/javascript" src="merchant/js/youyibao.js"></script>

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
                <th>类型</th>
                <th>面额</th>
                <th>折扣</th>
                <th>发放量</th>
                <th>使用有效期</th>
                <th>是否首页推荐</th>
                <th>使用说明</th>
                <th>发放时间</th>
                <th>状态</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($tickets as $ticket)
                 <tr>
                     <td> {{ $ticket->name }}</td>
                     <td><img width="160" height="90" src="{{ config('static.base_url').'/'.$ticket->path }}" alt="图片"></td>
                     <td> @if($ticket->type == 1) 现金券 @elseif($ticket->type == 2) 优惠券 @elseif($ticket->type == 3) 体验券 @endif </td>
                     <td> @if($ticket->denomination){{ $ticket->denomination }} 元 @endif</td>
                     <td> {{ $ticket->discount }}</td>
                     <td> {{ $ticket->circulation }}</td>
                     <td>{{ date('Y-m-d',$ticket->start_date) }} - {{ date('Y-m-d',$ticket->expire_date) }}</td>
                     <td> @if($ticket->flag == 1) 是 @else 否 @endif </td>
                     <td> {{ $ticket->instruction }}</td>
                     <td> {{ date('Y-m-d H:i:s',$ticket->addtime) }}</td>
                     <td>
                         @if($ticket->get_start_date > time())
                             未开放领取
                         @elseif($ticket->get_start_date <= time() && $ticket->expire_date >= time())
                             正在开放领取
                         @elseif($ticket->expire_date < time())
                             已过期
                         @endif
                     </td>
                     <td>
                         <a href="/edit-ticket?id={{ $ticket->id }}" class="btn btn-white btn-sm"><i class="fa"></i>修改</a> |
                         @if($ticket->get_start_date > time())
                         <a href="javascript:;" data-url="/delete-ticket" data-type="id" data-id="{{ $ticket->id }}" class="btn btn-del-ticket btn-white btn-sm"><i class="fa"></i>删除</a> |
                         @endif
                         @if($ticket->flag == 1)
                            <a href="javascript:;" data-url="/flag-ticket" data-type="id" data-id="{{ $ticket->id }}&f=0" class="btn btn-flag-ticket btn-white btn-sm" title="取消推荐到首页显示"><i class="fa"></i>取消推荐</a>
                         @elseif($ticket->flag == 0)
                             <a href="javascript:;" data-url="/flag-ticket" data-type="id" data-id="{{ $ticket->id }}&f=1" class="btn btn-flag-ticket btn-white btn-sm" title="推荐到首页显示"><i class="fa"></i>推荐</a>
                         @endif

                     </td>
                 </tr>
             @endforeach
            </tbody>
        </table>
    </div>
    <div class="col-lg-push-1">@if(!empty($tickets)){!! $tickets->links() !!} @endif</div>
</body>
</html>
<script type="text/javascript" src="merchant/js/jquery.min.js"></script>
<script type="text/javascript" src="merchant/js/youyibao.js"></script>
<script type="text/javascript" src="/merchant/layer/layer/layer.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('.btn-flag-ticket').click(function(){
            youyibao.httpSend($(this),'get',1);
        });

        $('.btn-del-ticket').click(function(){
            youyibao.httpSend($(this),'get',1);
        });

    });
</script>
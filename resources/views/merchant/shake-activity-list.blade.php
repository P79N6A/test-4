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
                <th>描述</th>
                <th>限摇次数</th>
                <th>中奖限制次数</th>
                <th>有效起始时间</th>
                <th>状态</th>
                <th>添加时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($activities as $activity)
                 <tr>
                     <td> {{ $activity->title }}</td>
                     <td> {{ $activity->description }}</td>
                     <td> {{ $activity->shake_limit }}</td>
                     <td> {{ $activity->win_limit }}</td>
                     <td> {{ date('Y-m-d H:i:s',$activity->start_date) }} - {{ date('Y-m-d H:i:s',$activity->end_date) }}</td>
                     <td>
                         @if($activity->start_date > time()) 未开始
                         @elseif($activity->start_date <= time() && time() <= $activity->end_date) 正在进行
                         @elseif($activity->end_date < time()) 已结束
                         @endif
                     </td>
                     <td> {{ date('Y-m-d H:i:s',$activity->addtime) }}</td>
                     <td>
                         @if($activity->start_date <= time() && time() <= $activity->end_date)
                         <a href="/publish-shake-gift?id={{ $activity->id }}" class="btn btn-white btn-sm"><i class="fa fa-edit"></i>投放奖品</a>
                         @endif
                     </td>
                 </tr>
             @endforeach
            </tbody>
        </table>
    </div>
    <div class="col-lg-push-1">{!! $activities->links() !!}</div>
</body>
</html>
<script type="text/javascript" src="merchant/js/jquery.min.js"></script>
<script type="text/javascript" src="merchant/js/youyibao.js"></script>
<script type="text/javascript" src="/merchant/layer/layer/layer.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('.btn-del-info').click(function(){
            youyibao.httpSend($(this),'get',1);
        });
    });
</script>
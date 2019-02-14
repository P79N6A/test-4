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
                <th>状态</th>
                <th>接收时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($messages as $message)
                 <tr>
                     <td> {{ $message->title }}</td>
                     <td>
                         @if($message->read_status == 0)
                             未读
                         @elseif($message->read_status == 1)
                             已读
                         @endif
                     </td>
                     <td> {{ date('Y-m-d H:i:s',$message->receive_time) }}</td>
                     <td>
                         @if($message->read_status == 0)
                            <a href="javascript:;" data-url="/mark-read" data-type="id" data-id="{{ $message->id }}" class="btn btn-mark-read btn-white btn-sm"><i class="fa fa-edit"></i>标记已读</a> |
                         @endif
                         <a href="javascript:;" data-url="/del-message" data-type="id" data-id="{{ $message->id }}" class="btn btn-del-message btn-white btn-sm"><i class="fa fa-trash"></i>删除</a>
                     </td>
                 </tr>
             @endforeach
            </tbody>
        </table>
    </div>
    <div class="col-lg-push-1">{!! $messages->links() !!}</div>
</body>
</html>
<script type="text/javascript" src="merchant/js/jquery.min.js"></script>
<script type="text/javascript" src="merchant/js/youyibao.js"></script>
<script type="text/javascript" src="/merchant/layer/layer/layer.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('.btn-mark-read').click(function(){
            youyibao.httpSend($(this),'get',1);
        });

        $('.btn-del-message').click(function(){
            youyibao.httpSend($(this),'get',1);
        });
    });
</script>
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
                <th>门店</th>
                <th>添加时间</th>
                <th>是否推送通知</th>
                <th>是否已发布</th>
                <th>是否已推送</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($infos as $info)
                 <tr>
                     <td> {{ $info->title }}</td>
                     <td> {{ $info->description }}</td>
                     <td> {{ $info->store_name }}</td>
                     <td> {{ date('Y-m-d H:i:s',$info->addtime) }}</td>
                     <td>
                         @if($info->push_flag == 0)
                             否
                         @else
                            是
                         @endif
                     </td>
                     <td>@if($info->flag == 1)是@else否@endif</td>
                     <td>@if($info->is_push == 1)是@else否@endif</td>
                     <td>
                         <a href="/edit-activity-info?id={{ $info->id }}" class="btn btn-white btn-sm"><i class="fa fa-edit"></i>修改</a> |
                         <a href="javascript:void(0);" data-url="/del-activity-info" data-type="id" data-id="{{ $info->id }}" class="btn btn-del-info btn-white btn-sm"><i class="fa fa-trash"></i>删除</a>
                         @if($info->flag == 0)
                            | <a href="javascript:;" data-url="/post-info" data-type="id" data-id="{{ $info->id }}" class="btn btn-post btn-white btn-sm">发布</a>
                         @endif
                         @if($info->is_push == 0 && $info->push_flag == 1)
                            | <a href="/push-info?id={{ $info->id }}" class="btn btn-white btn-sm">推送</a>
                         @endif
                     </td>
                 </tr>
             @endforeach
            </tbody>
        </table>
    </div>
    <div class="col-lg-push-1">{!! $infos->links() !!}</div>
</body>
</html>
<script type="text/javascript" src="merchant/js/jquery.min.js"></script>
<script type="text/javascript" src="merchant/js/youyibao.js"></script>
<script type="text/javascript" src="merchant/layer/layer/layer.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('.btn-del-info').click(function(){
            youyibao.httpSend($(this),'get',1);
        });

        $('.btn-post').click(function(){
            youyibao.httpSend($(this),'get',1);
        });
    });
</script>
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
                <th>类型</th>
                <th>封面</th>
                <th>位置</th>
                <th>是否已发布</th>
                <th>总浏览量</th>
                <th>月浏览量</th>
                <th>日浏览量</th>
                <th>最后访问时间</th>
                <th>添加时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($adds as $add)
                 <tr>
                     <td> {{ $add->title }}</td>
                     <td>
                         @if($add->type == 1) 图文广告
                         @elseif($add->type == 2) 外部链接广告
                         @elseif($add->type == 3) 商品/套餐广告
                         @endif
                     </td>
                     <td>
                         <img width="160" height="90" src="@if(!empty($add->path)) {{ config('upload.static_base_url') }}/{{ $add->path }} @endif" alt="图片">
                     </td>
                     <td>@if($add->place == 1)应用首页@elseif($add->place == 2) 启动页 @elseif($add->place == 3) 门店 @endif</td>
                     <td>@if($add->flag == 1)是@else 否 @endif</td>
                     <td>{{ $add->views }}</td>
                     <td>{{ $add->month_view }}</td>
                     <td>{{ $add->day_view }}</td>
                     <td>@if($add->last_view_time){{ date('Y-m-d H:i:s',$add->last_view_time) }} @endif</td>
                     <td> {{ date('Y-m-d H:i:s',$add->addtime) }}</td>
                     <td>
                         <a href="/edit-add?id={{ $add->id }}" class="btn btn-white btn-sm"><i class="fa fa-edit"></i>修改</a> |
                         <a href="#" data-url="/delete-add" data-type="id" data-id="{{ $add->id }}" class="btn btn-del-add btn-white btn-sm"><i class="fa fa-trash"></i>删除</a>
                         @if($add->place != 2)
                            @if($add->flag == 0)
                                | <a href="javascript:;" data-url="/posting-add" data-type="id" data-id="{{ $add->id }}" class="btn btn-posting-add btn-white btn-sm"><i class="fa fa-edit"></i>发布</a>
                            @else
                                | <a href="javascript:;" data-url="/revoke-add" data-type="id" data-id="{{ $add->id }}" class="btn btn-revoke-add btn-white btn-sm"><i class="fa fa-edit"></i>取消发布</a>
                            @endif
                         @endif
                     </td>
                 </tr>
             @endforeach
            </tbody>
        </table>
    </div>
    <div class="col-lg-push-1">{!! $adds->links() !!}</div>
</body>
</html>
<script type="text/javascript" src="merchant/js/jquery.min.js"></script>
<script type="text/javascript" src="merchant/js/youyibao.js"></script>
<script type="text/javascript" src="merchant/layer/layer/layer.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('.btn-del-add').click(function(){
            youyibao.httpSend($(this),'get',1);
        });
        $('.btn-posting-add').click(function(){
            youyibao.httpSend($(this),'get',1);
        });
        $('.btn-revoke-add').click(function(){
            youyibao.httpSend($(this),'get',1);
        });
    });
</script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>修改卡券</title>
</head>
<body>
<div>
    <form action="/edit-ticket" method="post" class="form-edit-ticket">
        <input type="hidden" name="id" value="{{ $ticket->id }}">
        <table border="1" cellspacing="0">
            <tr><td>名称：</td><td>{{ $ticket->name }}</td></tr>
            <tr>
                <td>类型：</td>
                <td>
                    <lable>现金券</lable><input type="radio" name="type" value="1" @if($ticket->type == 1) checked @endif disabled>&nbsp;
                    <lable>折扣券</lable><input type="radio" name="type" value="2" @if($ticket->type == 2) checked @endif disabled>&nbsp;
                    <lable>体验券</lable><input type="radio" name="type" value="3" @if($ticket->type == 3) checked @endif disabled>&nbsp;
                </td>
            </tr>
            <tr>
                <td>可用门店：</td>
                <td>
                    @foreach($stores as $store)
                        <label>{{ $store->name }}</label>
                        <input type="checkbox" @if(in_array($store->id,$availableStores)) checked @endif disabled>
                        <br>
                    @endforeach
                </td>
            </tr>
            <tr>
                <td>图片：</td>
                <td>
                    <input type="hidden" name="image" value="{{ $ticket->image }}">
                    <div id="picker">选择图片</div>
                    <div class="preview">
                        <img width="160" height="90" src="@if($ticket->path) {{ config('static.base_url').'/'.$ticket->path }} @endif" alt="图片">
                    </div>
                </td>
            </tr>
            <tr>
                <td>是否显示在首页：</td>
                <td>
                    <label>否</label><input name="flag" type="radio" value="0" @if($ticket->flag == 0) checked @endif disabled>
                    <label>是</label><input name="flag" type="radio" value="1" @if($ticket->flag == 1) checked @endif disabled>
                </td>
            </tr>
            @if($ticket->type == 1)
            <tr><td>面额：</td><td>{{ $ticket->denomination }}</td></tr>
            @endif
            @if($ticket->type ==2)
            <tr><td>折扣：</td><td>{{ $ticket->discount }}</td></tr>
            @endif
            <tr><td>发放量：</td><td><input name="circulation" value="{{ $ticket->circulation }}"></td></tr>
            <tr>
                <td>领取有效期：</td>
                <td>
                    {{ date('Y-m-d H:i:s',$ticket->get_start_date) }} -
                    {{ date('Y-m-d H:i:s',$ticket->get_end_date) }}
                </td>
            </tr>
            <tr>
                <td>使用有效期：</td>
                <td>
                    {{ date('Y-m-d H:i:s',$ticket->start_date) }} -
                    {{ date('Y-m-d H:i:s',$ticket->expire_date) }}
                </td>
            </tr>
            <tr><td>使用说明：</td><td>{{ $ticket->instruction }}</td></tr>
            <tr><td></td><td><button type="button" class="btn-edit-ticket">提交</button></td></tr>
        </table>
    </form>
</div>
</body>
</html>
<script type="text/javascript" src="/merchant/js/jquery.min.js"></script>
<script type="text/javascript" src="/merchant/js/webuploader.min.js"></script>
<script type="text/javascript" src="/merchant/js/youyibao.js"></script>
<script type="text/javascript" src="/merchant/layer/layer/layer.js"></script>
<script type="text/javascript">
    $(function(){

        // 图片上传
        var uploader = WebUploader.create({
            swf:'merchant/js/Uploader.swf',
            server:'/upload',
            pick:'#picker',
            resize:false,
            auto:true
        });
        uploader.on('uploadSuccess',function(file,response){
            $('.preview').empty().append($('<img>').attr('src',response.data[0].absolute_path).css({width:160,height:90}));
            $('input[name=image]').val(response.data[0].id);
        });

        // 表单提交
        $('.btn-edit-ticket').click(function(){
            youyibao.httpSend($('form.form-edit-ticket'),'post',1);
        });

    });
</script>

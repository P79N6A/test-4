<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>添加卡券</title>
    <link rel="stylesheet" type="text/css" href="/merchant/css/jquery.datetimepicker.css">
</head>
<body>
<div>
    <form action="/add-ticket" method="post" class="form-add-ticket">
        <table border="1" cellspacing="0">
            <tr><td>名称：</td><td><input name="name"></td></tr>
            <tr>
                <td>类型：</td>
                <td>
                    <lable>现金券</lable><input type="radio" name="type" value="1">&nbsp;
                    <lable>折扣券</lable><input type="radio" name="type" value="2">&nbsp;
                    <lable>体验券</lable><input type="radio" name="type" value="3">&nbsp;
                </td>
            </tr>
            <tr>
                <td>可用门店：</td>
                <td>
                    @foreach($stores as $k=>$store)
                        <label for="store[{{ $k }}]">{{ $store->name }}</label>
                        <input type="checkbox" id="store[{{ $k }}]" name="store_ids[]" value="{{ $store->id }}"><br>
                    @endforeach
                </td>
            </tr>
            <tr>
                <td>图片：</td>
                <td>
                    <input type="hidden" name="image">
                    <div id="picker">选择图片</div>
                    <div class="preview"></div>
                </td>
            </tr>
            <tr>
                <td>是否显示在首页：</td>
                <td>
                    <label>否</label><input name="flag" type="radio" value="0" checked>
                    <label>是</label><input name="flag" type="radio" value="1">
                </td>
            </tr>
            <tr class="denomination"><td>面额：</td><td><input name="denomination"></td></tr>
            <tr class="discount"><td>折扣：</td><td><input name="discount" ></td></tr>
            <tr><td>发放量：</td><td><input name="circulation"></td></tr>
            <tr>
                <td>领取有效期：</td>
                <td>
                    <input id="get_start_date" name="get_start_date"> -
                    <input id="get_end_date" name="get_end_date">
                </td>
            </tr>
            <tr>
                <td>使用有效期：</td>
                <td>
                    <input id="start_date" name="start_date"> -
                    <input id="expire_date" name="expire_date">
                </td>
            </tr>
            <tr><td>使用说明：</td><td><textarea name="instruction"></textarea></td></tr>
            <tr><td></td><td><button type="button" class="btn-add-ticket">提交</button></td></tr>
        </table>
    </form>
</div>
</body>
</html>
<script type="text/javascript" src="merchant/js/jquery.min.js"></script>
<script type="text/javascript" src="merchant/js/youyibao.js"></script>
<script type="text/javascript" src="merchant/js/webuploader.min.js"></script>
<script type="text/javascript" src="/merchant/js/jquery.datetimepicker.full.min.js"></script>
<script type="text/javascript" src="merchant/layer/layer/layer.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
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

        // 券类型控制
        $('input[name=type]').change(function(){
            var value = $(this).val();
            if(value == 1){
                $('.discount').hide();
                $('.denomination').show();
            }else if(value == 2){
                $('.denomination').hide();
                $('.discount').show();
            }else if(value == 3){
                $('.denomination').hide();
                $('.discount').hide();
            }
        });

        // 日期时间选择
        $('#get_start_date').click(function(){
            $(this).datetimepicker({
                format:'Y-m-d H:i:s'
            });
        });
        $('#get_end_date').click(function(){
            $(this).datetimepicker({
                format:'Y-m-d H:i:s'
            });
        });
        $('#start_date').click(function(){
            $(this).datetimepicker({
                format:'Y-m-d H:i:s'
            });
        });
        $('#expire_date').click(function(){
            $(this).datetimepicker({
                format:'Y-m-d H:i:s'
            });
        });

        // 提交表单
        $('.btn-add-ticket').click(function(){
            youyibao.httpSend($('form.form-add-ticket'),'post',1);
        });

    });
</script>

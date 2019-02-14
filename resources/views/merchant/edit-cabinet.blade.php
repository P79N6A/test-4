<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>修改机台</title>
</head>
<body>
<div>
    <form action="/edit-cabinet" method="post" class="form-edit-cabinet">
        <input type="hidden" name="id" value="{{ $cabinet->id }}"/>
        <table border="1" cellspacing="0">
            <tr><td>名称：</td><td><input name="name" value="{{ $cabinet->name }}"></td></tr>
            <tr><td>硬件ID：</td><td><input name="iot_id" value="{{ $cabinet->iot_id }}"></td></tr>
            <tr>
                <td>门店：</td>
                <td>
                    @foreach($stores as $k=>$store)
                        <label for="store_{{ $k }}">{{ $store->name }}</label>
                        <input id="store_{{$k}}" type="radio" name="store_id" value="{{$store->id}}" @if($cabinet->store_id == $store->id) checked @endif>
                        <br>
                    @endforeach
                </td>
            </tr>
            <tr>
                <td>图片：</td>
                <td>
                    <input type="hidden" name="image" value="{{ $cabinet->image }}">
                    <div id="image-picker">选择图片</div>
                    <div class="image-preview">
                        <img width="160" height="90" @if(!empty($cabinet->path)) src="{{ config('static.base_url').'/'.$cabinet->path }}" @endif >
                    </div>
                </td>
            </tr>
            <tr>
                <td>相册：</td>
                <td>
                    <input type="hidden" name="gallery" value="{{ $cabinet->gallery }}">
                    <div id="gallery-picker">选择图片</div>
                    <div class="gallery-preview">
                        @foreach($gallery as $item)
                            <img width="160" height="90" @if($item) src="{{ config('static.base_url').'/'.$item }}" @endif alt="相册图片">
                        @endforeach
                    </div>
                </td>
            </tr>
            <tr><td>价格：</td><td><input name="price" value="{{ $cabinet->price }}" placeholder="游币/局"> 游币/局</td></tr>
            <tr><td>介绍：</td><td><textarea name="introduction">{{ $cabinet->introduction }}</textarea></td></tr>
            <tr><td>玩法攻略：</td><td><textarea name="guide">{{ $cabinet->guide }}</textarea></td></tr>
            <tr><td></td><td><button type="button" class="btn-edit-cabinet">提交</button></td></tr>
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
        // 封面上传
        var imageUploader = WebUploader.create({
            auto:true,
            swf:'/merchant/js/Uploader.js',
            server:'/upload',
            pick:'#image-picker',
            accept:{
                title:'Images',
                extensions:'jpg,png,bmp,gif',
                mimeTypes:'image/*'
            }
        });
        imageUploader.on('uploadSuccess',function(file, response){
            $('.image-preview').empty().append($('<img>').attr('src',response.data[0].absolute_path).css({width:160,height:90}));
            $('input[name=image]').val(response.data[0].id);
        });

        // 相册上传
        var galleryUploader = WebUploader.create({
            auto:true,
            swf:'/merchant/js/Uploader.js',
            server:'/upload',
            pick:'#gallery-picker',
            accept:{
                title:'Images',
                extensions:'jpg,png,bmp,gif',
                mimeTypes:'image/*'
            }
        });
        var $galleryContainer = $('.gallery-preview');
        var gallery = '';
        galleryUploader.on('uploadSuccess',function(file, response){
            $galleryContainer.append($('<img>').attr('src',response.data[0].absolute_path).css({width:160,height:90}));
            gallery += (String(response.data[0].id) + ',');
            $('input[name=gallery]').val(gallery);
        });

        // 表单提交
        $('.btn-edit-cabinet').click(function(){
            youyibao.httpSend($('form.form-edit-cabinet'),'post',1);
        });



    });
</script>

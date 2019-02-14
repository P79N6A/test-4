<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>添加产品</title>
</head>
<body>
<div>
    <form action="/add-product" method="post" class="form-add-product">
        {{ csrf_field() }}
        <table>
            <tr>
                <td>选择门店：</td>
                <td>
                    <select name="store_id">
                        <option>请选择门店</option>
                        @foreach($stores as $store)
                            <option value="{{ $store->id }}">{{ $store->name }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <td>类别：</td>
                <td>
                    <select name="product_type_id">
                        <option>请选择类别</option>
                        @foreach($types as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <td>名称：</td>
                <td><input name="name"></td>
            </tr>
            <tr>
                <td>每局币数：</td>
                <td><input name="coin_qty"></td>
            </tr>
            <tr>
                <td>机台图片：</td>
                <td>
                    <input type="hidden" name="image">
                    <div id="logo-picker">选择图片</div>
                    <div id="logo-preview"></div>
                </td>
            </tr>
            <tr>
                <td>机台相册:</td>
                <td>
                    <input type="hidden" name="gallery">
                    <div id="gallery-picker">选择图片</div>
                    <div id="gallery-preview"></div>
                </td>
            </tr>
            <tr>
                <td>游戏介绍：</td>
                <td>
                    <textarea name="introduction"></textarea>
                </td>
            </tr>
            <tr>
                <td>玩法和攻略：</td>
                <td>
                    <textarea name="guide"></textarea>
                </td>
            </tr>
            <tr>
                <td>备注：</td>
                <td><textarea name="remarks"></textarea></td>
            </tr>
            <tr>
                <td></td>
                <td><button type="button" class="btn-add-product">提交</button></td>
            </tr>
        </table>
    </form>
</div>
</body>
</html>
<script type="text/javascript" src="/merchant/js/jquery.min.js"></script>
<script type="text/javascript" src="/merchant/js/youyibao.js"></script>
<script type="text/javascript" src="/merchant/layer//layer/layer.js"></script>
<script type="text/javascript" src="/merchant/js/webuploader.min.js"></script>
<script>
    $(function(){
        $('.btn-add-product').click(function(){
            youyibao.httpSend($('.form-add-product'),'post',1);
        });

        // logo 图片上传
        var imageUploader = WebUploader.create({
            swf:'/merchant/js/Uploader.swf',
            server:'/upload',
            pick:'#logo-picker',
            resize:false,
            auto:true
        });
        imageUploader.on('uploadSuccess',function(file,response){
            $('#logo-preview').empty().append($('<img>').attr('src',response.data[0].absolute_path).css({width:160,height:90}));
            $('input[name=image]').val(response.data[0].id);
        });

        // 相册图片上传
        var galleryUploader = WebUploader.create({
            swf:'merchant/js/Uploader.swf',
            server:'/upload',
            pick:'#gallery-picker',
            resize:false,
            auto:true
        });
        var $gallery = $('#gallery-preview').empty();
        var gallery = '';
        galleryUploader.on('uploadSuccess',function(file,response){
            $gallery.append($('<img>').attr('src',response.data[0].absolute_path).css({width:160,height:90}));
            gallery += (String(response.data[0].id) + ',');
            $('input[name=gallery]').val(gallery);
        });

    });
</script>

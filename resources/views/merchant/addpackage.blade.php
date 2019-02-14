<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/merchant/css/jquery.datetimepicker.css">
</head>
<body>
<div>
    <form action="storepackage" method="post" class="form-store-package">
        <table>
            <tr>
                <td>可用门店：</td>
                <td>
                    @foreach($stores as $k=>$store)
                        <label for="store_[{{ $k }}]">{{ $store->name }}</label>
                        <input type="checkbox" id="store_[{{ $k }}]" name="store_ids[]" value="{{ $store->id }}">
                        <br>
                    @endforeach
                </td>
            </tr>
            <tr><td>商品名称：</td><td><input name="name" value="会员套餐"></td></tr>
            <tr><td>价格：</td><td><input name="price"></td></tr>
            <tr><td>出币数量：</td><td><input name="coins"></td></tr>
            <tr>
                <td>封面图片：</td>
                <td>
                    <div id="picker">选择图片</div>
                    <div class="preview"></div>
                    <input type="hidden" name="image">
                </td>
            </tr>
            <tr>
                <td>相册：</td>
                <td>
                    <input type="hidden" name="gallery">
                    <div id="gallery-picker">选择图片</div>
                    <div class="gallery-preview"></div>
                </td>
            </tr>
            <tr><td>商品描述：</td><td><textarea name="description"></textarea></td></tr>
            <tr><td>过期时间：</td><td><input id="expire_date" name="expire_date"></td></tr>
            <tr><td>库存：</td><td><input name="stock"></td></tr>
            <tr><td>限购：</td><td><input name="buy_limit" placeholder="默认为0，不限购"></td></tr>
            <tr><td colspan="2"><button type="button" class="btn-store-package">提交</button></td></tr>
        </table>
    </form>
</div>
</body>
</html>
<script type="text/javascript" src="/merchant/js/jquery.min.js"></script>
<script type="text/javascript" src="/merchant/js/webuploader.min.js"></script>
<script type="text/javascript" src="/merchant/js/youyibao.js"></script>
<script type="text/javascript" src="/merchant/js/jquery.datetimepicker.full.min.js"></script>
<script type="text/javascript" src="/merchant/layer/layer/layer.js"></script>
<script type="text/javascript">
    $(function(){
        $('#expire_date').click(function(){
            $(this).datetimepicker({
                format:'Y-m-d H:i:s'
            });
        });

        var imageUploader = WebUploader.create({
            swf:'merchant/js/Uploader.swf',
            server:'/upload',
            pick:'#picker',
            resize:false,
            auto:true
        });
        imageUploader.on('uploadSuccess',function(file,response){
            $('.preview').empty().append($('<img>').attr('src',response.data[0].absolute_path).css({width:160,height:90}));
            $('input[name=image]').val(response.data[0].id);
        });

        var galleryUploader = WebUploader.create({
            swf:'merchant/js/Uploader.swf',
            server:'/upload',
            pick:'#gallery-picker',
            resize:false,
            auto:true
        });

        var gallery = '';
        var $gallery = $('.gallery-preview').empty();
        galleryUploader.on('uploadSuccess',function(file,response){
            $gallery.append($('<img>').attr('src',response.data[0].absolute_path).css({width:160,height:90}));
            gallery += (String(response.data[0].id) + ',');
            $('input[name=gallery]').val(gallery);
        });

        $('.btn-store-package').click(function(){
            youyibao.httpSend($('form.form-store-package'),'post',1);
        });

    });
</script>

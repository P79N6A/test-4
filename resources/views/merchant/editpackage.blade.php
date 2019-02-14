<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body>
<div>
    <form class="editpackage" action="updatepackage" method="post">
        <table>
            <input type="hidden" name="id" value="{{ $package->id }}">
            <tr>
                <td>可用门店：</td>
                <td>
                    @foreach($stores as $k=>$store)
                        <label for="store_[{{ $k }}]">{{ $store->name }}</label>
                        <input type="checkbox" id="store_[{{ $k }}]" name="store_ids[]" value="{{ $store->id }}" @if(in_array($store->id,$available_stores)) checked @endif >
                        <br>
                    @endforeach
                </td>
            </tr>
            <tr><td>商品名称：</td><td><input name="name" value="{{ $package->name }}"></td></tr>
            <tr><td>价格：</td><td><input name="price" value="{{ $package->price }}"></td></tr>
            <tr><td>出币数量：</td><td><input name="coins" value="{{ $package->coins }}"></td></tr>
            <tr>
                <td>封面图片：</td>
                <td>
                    <input type="hidden" name="image" value="{{ $package->image }}">
                    <div id="image-picker">选择图片</div>
                    <div class="image-preview">
                        <img width="160" height="90" src="{{ config('static.base_url').'/'.$package->path }}" alt="图片">
                    </div>
                </td>
            </tr>
            <tr>
                <td>相册：</td>
                <td>
                    <input type="hidden" name="gallery" value="{{ $package->gallery }}">
                    <div id="gallery-picker">选择图片</div>
                    <div class="gallery-preview">
                        @foreach($gallery as $item)
                            <img width="160" height="90" src="{{ config('static.base_url').'/'.$item }}" alt="相册">
                        @endforeach
                    </div>
                </td>
            </tr>
            <tr><td>商品描述：</td><td><textarea name="description" >{{ $package->description }}</textarea></td></tr>
            <tr><td>过期时间：</td><td><input name="expire_date" value="{{ date('Y-m-d H:i:s',$package->expire_date) }}"></td></tr>
            <tr><td>库存：</td><td><input name="stock" value="{{ $package->stock }}"></td></tr>
            <tr><td>限购：</td><td><input name="buy_limit" value="{{ $package->buy_limit }}" placeholder="默认为0，不限购"></td></tr>
            <tr><td colspan="2"><input type="button" id="btn-sub" value="提交"></td></tr>
        </table>
    </form>
</div>

</body>
</html>
<script type="text/javascript" src="merchant/js/jquery.min.js"></script>
<script type="text/javascript" src="/merchant/js/webuploader.min.js"></script>
<script type="text/javascript" src="merchant/js/youyibao.js"></script>
<script type="text/javascript" src="/merchant/layer/layer/layer.js"></script>
<script type="text/javascript">
    $(document).ready(function(){

        var imageUploader = WebUploader.create({
            swf:'merchant/js/Uploader.swf',
            server:'/upload',
            pick:'#image-picker',
            resize:false,
            auto:true
        });
        imageUploader.on('uploadSuccess',function(file,response){
            $('.image-preview').empty().append($('<img>').attr('src',response.data[0].absolute_path).css({width:160,height:90}));
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

        $('#btn-sub').click(function(){
            youyibao.httpSend($('form.editpackage'),'post',1);
        });
    });
</script>
@extends('business.layouts.frame-parent')
@section('page-title','添加产品')
@section('main')
    <link rel="stylesheet" href="/business/css/webuploader.css">
    <div class="row">
            <div class="col-sm-12">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>添加产品</h5>
                    </div>
                    <div class="ibox-content">
                        <form action="{{ route('business.add-product') }}" method="post" class="form-add-product">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>门店</label>
                                        <select class="form-control m-b" name="store_id">
                                            <option>请选择门店</option>
                                            @foreach($stores as $store)
                                                <option value="{{ $store->id }}">{{ $store->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>产品类别</label>
                                        <select class="form-control m-b" name="product_type_id">
                                            <option>请选择产品类别</option>
                                            @foreach($types as $type)
                                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>名称</label>
                                        <input type="text" name="name" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>每局币数</label>
                                        <input type="text" name="coin_qty" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>游戏介绍</label>
                                        <textarea name="introduction" class="form-control"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>玩法攻略</label>
                                        <textarea name="guide" class="form-control"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>备注</label>
                                        <textarea name="remarks" class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>产品图片（大小限制：327x353）</label>
                                        <div class="input-group">
                                            <input type="hidden" name="image">
                                            <div id="image-picker">上传</div>
                                            <div class="ibox-content no-padding border-left-right image-preview" style="margin-top:10px">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>相册图片（大小限制：327x353）</label>
                                        <div class="input-group">
                                            <div id="gallery-picker">上传</div>
                                            <div style="margin-top:10px" class="gallery-container"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <button class="btn btn-sm btn-primary btn-add-product" type="button">添加</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <script type="text/javascript" src="/business/js/webuploader.min.js"></script>
    <script>
        $(function(){
            // 图片上传
            var imageUploader = WebUploader.create({
                swf:'/business/js/Uploader.swf',
                server:'/upload',
                pick:'#image-picker',
                resize:false,
                auto:true
            });
            imageUploader.on('uploadSuccess',function(file,response){
                $('.image-preview').empty().append($('<img>').attr('src',response.data[0].absolute_path).addClass('img-preview-sm'));
                $('input[name=image]').val(response.data[0].id);
            });

            // 删除相册图片
            $('.gallery-container').delegate('.btn-del-photo','click',function(){
                $(this).parents('div.file-box').empty().remove();
            });

            // 相册图片上传
            var galleryUploader = WebUploader.create({
                swf:'/business/js/Uploader.swf',
                server:'/upload',
                pick:'#gallery-picker',
                resize:false,
                auto:true
            });
            var $gallery = $('.gallery-container');
            galleryUploader.on('uploadSuccess',function(file,response){
                var img = '<img src="' + response.data[0].absolute_path + '" class="img-preview-sm">';
                var $attid = '<input type="hidden" name="gallery_photos[]" value="' + response.data[0].id + '">';
                var $item = $('<div class="file-box"><div class="file"><span class="corner"></span><div class="image">' + $attid + img + '</div><div class="file-name text-center"><button class="btn btn-warning btn-circle btn-del-photo" type="button"><i class="fa fa-times"></i></button></div></div></div>');
                $gallery.append($item);
            });

            $('.btn-add-product').click(function(){
                youyibao.httpSend($('.form-add-product'),'post',1);
            });
        });
    </script>
@endsection

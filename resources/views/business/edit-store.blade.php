@extends('business.layouts.frame-parent')
@section('page-title','修改门店')
@section('main')
    <link rel="stylesheet" href="/business/css/webuploader.css">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>修改门店</h5>
                </div>
                <div class="ibox-content">
                    <form action="edit-store" method="post" class="form-add-store">
                        {{ csrf_field() }}
                        <input type="hidden" name="id" value="{{ $store->id }}">
                        <input type="hidden" name="referrer" value="{{ $referrer }}">
                        <div class="row">
                            <div class="col-sm-7">
                                <div class="form-group">
                                    <label>品牌</label>
                                    <div class="input-group">{{ $brand->name }}</div>
                                </div>
                                <div class="form-group">
                                    <label>门店名称</label>
                                    <input type="text" name="name" value="{{ $store->name }}" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>门店类型</label>
                                    <select name="system_type" class="input-sm form-control input-s-sm inline">
                                        <option value="0" @if($store->system_type == 0) selected @endif>世软</option>
                                        <option value="1" @if($store->system_type == 1) selected @endif>大拇指</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>门店系统ID</label>
                                    <input type="text" name="member_card_system_id"
                                           value="{{ $store->member_card_system_id }}" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>门店电话</label>
                                    <input type="text" name='mobile' value="{{ $store->mobile }}" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>币单价（分/个）</label>
                                    <input type="text" name='coin_price' value="{{ $store->coin_price }}"
                                           class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>门店描述</label>
                                    <textarea class="form-control" name="description"
                                              rows="3">{{ $store->description }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label>门店logo（建议大小：117x117）</label>
                                    <div class="input-group">
                                        <input type="hidden" name="logo" value="{{ $store->logo }}">
                                        <div id="logo-picker">上传</div>
                                        <div class="ibox-content no-padding border-left-right logo-preview"
                                             style="margin-top:10px">
                                            @if(!empty($store->path))
                                                <img class="img-preview-sm"
                                                     src="{{ config('static.base_url').'/'.$store->path }}">
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>门店首图（建议大小：225x225）</label>
                                    <div class="input-group">
                                        <input type="hidden" name="home_image"
                                               value="{{ $store->home_image }}">
                                        <div id="home-image-picker">上传</div>
                                        <div class="ibox-content no-padding border-left-right home-image-preview"
                                             style="margin-top:10px">
                                            @if(!empty($store->home_image_pic))
                                                <img class="img-preview-sm"
                                                     src="{{ config('static.base_url').'/'.$store->home_image_pic }}">
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>相册图片</label>
                                    <div class="input-group">
                                        <div id="gallery-picker">上传</div>
                                        <div style="margin-top:10px" class="gallery-container">
                                            @foreach($galleryImages as $item)
                                                <div class="file-box">
                                                    <div class="file">
                                                        <span class="corner"></span>
                                                        <div class="image">
                                                            <input type="hidden" name="gallery_photos[]"
                                                                   value="{{ $item->id }}">
                                                            <img alt="image" class="img-responsive"
                                                                 src="{{ config('static.base_url').'/'.$item->path }}">
                                                        </div>
                                                        <div class="file-name text-center">
                                                            <button class="btn btn-warning btn-circle btn-del-photo"
                                                                    type="button"><i class="fa fa-times"></i></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <button class="btn btn-sm btn-primary btn-add-store" type="button">修改</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="/business/js/webuploader.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {

            // 删除相册图片
            $('.btn-del-photo').click(function () {
                $(this).parents('div.file-box').empty().remove();
            });

            // 相册图片上传
            var galleryUploader = WebUploader.create({
                swf: '/business/js/Uploader.swf',
                server: '/upload',
                pick: '#gallery-picker',
                resize: false,
                auto: true
            });
            var $gallery = $('.gallery-container');
            galleryUploader.on('uploadSuccess', function (file, response) {
                var img = '<img src="' + response.data[0].absolute_path + '" class="img-preview-sm">';
                var attid = '<input type="hidden" name="gallery_photos[]" value="' + response.data[0].id + '">';
                var $item = $('<div class="file-box"><div class="file"><span class="corner"></span><div class="image">'
                    + attid + img
                    + '</div><div class="file-name text-center"><button class="btn btn-warning btn-circle btn-del-photo" type="button"><i class="fa fa-times"></i></button></div></div></div>');
                $gallery.append($item);

                $('.btn-del-photo').click(function () {
                    $(this).parents('div.file-box').empty().remove();
                });
            });

            // logo 图片上传
            var imageUploader = WebUploader.create({
                swf: '/business/js/Uploader.swf',
                server: '/upload',
                pick: '#logo-picker',
                resize: false,
                auto: true
            });
            imageUploader.on('uploadSuccess', function (file, response) {
                $('.logo-preview').empty().append($('<img>').attr('src', response.data[0].absolute_path).addClass('img-responsive'));
                $('input[name=logo]').val(response.data[0].id);
            });

            // 门店首图上传
            var homeImageUploader = WebUploader.create({
                swf: '/business/js/Uploader.swf',
                server: '/upload',
                pick: '#home-image-picker',
                resize: false,
                auto: true
            });
            homeImageUploader.on('uploadSuccess', function (file, response) {
                $('.home-image-preview').empty().append($('<img>').addClass('img-responsive').attr('src', response.data[0].absolute_path));
                $('input[name=home_image]').val(response.data[0].id);
            });

            // 表单提交
            $('.btn-add-store').click(function () {
                youyibao.httpSend($('form.form-add-store'), 'post', 1);
            });

        });
    </script>
@endsection

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body class="gray-bg">
<div id="modal-form" class="modal fade" aria-hidden="true">
    <div class="modal-content">
        <div class="row">
            <div class="col-sm-4 ">
                <form role="form" class="form-editstore" action="/edit-store" method="post">
                    {{ csrf_field() }}
                    <input type='hidden' name="id" value="{{ $store->id }}">
                    <div class="form-group">
                        <label>品牌：</label>
                        <select name="brand_id">
                            @foreach($brands as $brand)
                                <option value="{{$brand->id}}"
                                        @if($store->brand_id == $brand->id) selected @endif >{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>门店名称：</label>
                        <input placeholder="门店名称" name="name" value="{{ $store->name }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>门店系统ID：</label>
                        <input placeholder="线下门店系统ID" name="member_card_system_id" value="{{ $store->member_card_system_id }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>省：</label>
                        <select class="province">
                            @foreach($provinces as $province)
                                <option value="{{ $province->id }}">{{ $province->province }}</option>
                            @endforeach
                        </select>

                        <label>市：</label>
                        <select class="city">
                            <option>请选择城市</option>
                        </select>

                        <label>区：</label>
                        <select class="block" name="region_id">
                            <option>请选择县区</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>门店描述：</label>
                        <input name="description" value="{{ $store->description }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>详细地址：</label>
                        <input name="address" value="{{ $store->address }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>门店首图：</label>
                        <input type="hidden" name="home_image" value="{{ $store->home_image }}">
                        <div id="home-image-picker">选择图片</div>
                        <div class="home-image-preview">
                            <img width="160" height="90"
                                 src="{{ config('static.base_url').'/'.$store->home_image_pic }}" alt="门店首图">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>相册图片：</label>
                        <input type="hidden" name="gallery" class="form-control" value="1,2,3,4">
                        <div id="gallery-picker">选择图片</div>
                        <div class="gallery-preview">
                            @foreach($gallery as $item)
                                <img width="160" height="90" src="{{ config('static.base_url').'/'.$item }}" alt="相册">
                            @endforeach
                        </div>
                    </div>
                    <div class="form-group">
                        <label>门店电话：</label>
                        <input name="tel" value="{{ $store->tel }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>门店手机：</label>
                        <input name="mobile" value='{{ $store->mobile }}' class="form-control">
                    </div>
                    <div class="form-group">
                        <label>门店logo：</label>
                        <input type="hidden" name="logo" value="{{ $store->logo }}" class="form-control">
                        <div id="logo-picker">选择图片</div>
                        <div class="logo-preview">
                            <img width="160" height="90" src="{{ config('static.base_url').'/'.$store->path }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>经纬度：</label>
                        <input name="longitude" class="form-control" value="{{ $store->longitude }}">
                        <input name="latitude" class="form-control" value="{{ $store->latitude }}">
                    </div>
                    <div>
                        <button class="btn btn-editstore btn-sm btn-primary pull-right m-t-n-xs" type="button">
                            <strong>提交</strong>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>

</html>
<script type="text/javascript" src="merchant/js/jquery.min.js"></script>
<script type="text/javascript" src="merchant/js/webuploader.min.js"></script>
<script type="text/javascript" src="merchant/js/youyibao.js"></script>
<script type="text/javascript" src="/merchant/layer/layer/layer.js"></script>
<script type="text/javascript">
    $('.btn-editstore').click(function () {
        youyibao.httpSend($('form.form-editstore'), 'post', 1);
    });

    $('.province').change(function () {
        getCities($(this).val());
    });

    $('.city').change(function () {
        $('.block').empty();
        getBlocks($(this).val());
    });

    function getCities(pid, callback) {
        youyibao.getCities(pid, function (data) {
            var list = '';
            $.each(data.data, function (index, value) {
                list += '<option value=' + value.id + '>' + value.city + '</option>';
            });
            $('.city').empty().append($(list));
        });
        callback();
    }

    function getBlocks(cid) {
        youyibao.getBlocks(cid, function (data) {
            var blocks = '';
            $.each(data.data, function (index, value) {
                blocks += '<option value=' + value.id + '>' + value.county + '</option>';
            });
            $('.block').empty().append($(blocks));
        });
    }


    // logo上传
    var logoUploader = WebUploader.create({
        swf: 'merchant/js/Uploader.swf',
        server: '/upload',
        pick: '#logo-picker',
        resize: false,
        auto: true
    });
    logoUploader.on('uploadSuccess', function (file, response) {
        $('.logo-preview').empty().append($('<img>').attr('src', response.data[0].absolute_path).css({
            width: 160,
            height: 90
        }));
        $('input[name=logo]').val(response.data[0].id);
    });

    // 门店首图上传
    var logoUploader = WebUploader.create({
        swf: 'merchant/js/Uploader.swf',
        server: '/upload',
        pick: '#home-image-picker',
        resize: false,
        auto: true
    });
    logoUploader.on('uploadSuccess', function (file, response) {
        $('.home-image-preview').empty().append($('<img>').attr('src', response.data[0].absolute_path).css({
            width: 160,
            height: 90
        }));
        $('input[name=home_image]').val(response.data[0].id);
    });

    // 相册上传
    var galleryUploader = WebUploader.create({
        swf: 'merchant/js/Uploader.swf',
        server: '/upload',
        pick: '#gallery-picker',
        resize: false,
        auto: true
    });

    var gallery = '';
    var $gallery = $('.gallery-preview').empty();
    galleryUploader.on('uploadSuccess', function (file, response) {
        $gallery.append($('<img>').attr('src', response.data[0].absolute_path).css({width: 160, height: 90}));
        gallery += (String(response.data[0].id) + ',');
        $('input[name=gallery]').val(gallery);
    });

</script>
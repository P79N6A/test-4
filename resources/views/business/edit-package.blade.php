@extends('business.layouts.frame-parent')
@section('page-title','修改套餐')
@section('main')
    <link rel="shortcut icon" href="favicon.ico">
    <link href="/business/css/bootstrap.min14ed.css?v=3.3.6" rel="stylesheet">
    <link href="/business/css/font-awesome.min93e3.css?v=4.4.0" rel="stylesheet">
    <link href="/business/css/plugins/iCheck/custom.css" rel="stylesheet">
    <link href="/business/css/plugins/chosen/chosen.css" rel="stylesheet">
    <link href="/business/css/plugins/colorpicker/css/bootstrap-colorpicker.min.css" rel="stylesheet">
    <link href="/business/css/plugins/cropper/cropper.min.css" rel="stylesheet">
    <link href="/business/css/plugins/switchery/switchery.css" rel="stylesheet">
    <link href="/business/css/plugins/jasny/jasny-bootstrap.min.css" rel="stylesheet">
    <link href="/business/css/plugins/nouslider/jquery.nouislider.css" rel="stylesheet">
    <link href="/business/css/plugins/datapicker/datepicker3.css" rel="stylesheet">
    <link href="/business/css/plugins/ionRangeSlider/ion.rangeSlider.css" rel="stylesheet">
    <link href="/business/css/plugins/ionRangeSlider/ion.rangeSlider.skinFlat.css" rel="stylesheet">
    <link href="/business/css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css" rel="stylesheet">
    <link href="/business/css/plugins/clockpicker/clockpicker.css" rel="stylesheet">
    <link href="/business/css/animate.min.css" rel="stylesheet">
    <link href="/business/css/style.min862f.css?v=4.1.0" rel="stylesheet">
    <link rel="stylesheet" href="/business/css/webuploader.css">

    <div class="row">
        <div class="col-sm-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>修改套餐</h5>
                </div>
                <div class="ibox-content">
                    <form action="{{ route('business.edit-package') }}" method="post" class="form-add-package">
                        {{ csrf_field() }}
                        <input type="hidden" name="id" value="{{ $package->id }}">
                        <div class="row">
                            <div class="col-sm-5">
                                <div class="form-group">
                                    <label>商品名称</label>
                                    <input type="text" name="name" value="{{ $package->name }}" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>价格</label>
                                    <input type="text" name="price" value="{{ $package->price }}" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>库存</label>
                                    <input type="text" name="stock" value="{{ $package->stock }}" class="form-control">
                                </div>
                                <div class="form-group" id="data_1">
                                    <label class="font-noraml">过期时间</label>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <div class="input-group date">
                                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                    <input type="text" name="expire_date" value="{{ date('Y-m-d',$package->expire_date) }}" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <div class="input-group clockpicker" data-autoclose="true">
                                                    <span class="input-group-addon">
                                                        <span class="fa fa-clock-o"></span>
                                                    </span>
                                                    <input type="text" class="form-control" name="expire_time" value="{{ date('H:i',$package->expire_date) }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>限购</label>
                                    <input type="text" name="buy_limit" value="{{ $package->buy_limit }}" class="form-control">
                                </div>
                                <div class="form-group">
                                    <div class="tabs-container">
                                        <ul class="nav nav-tabs">
                                            <li class="tab @if($package->type == 1) active @endif tab-switch" data-id="1"><a data-toggle="tab" href="#tab-1" aria-expanded="true">游戏币</a></li>
                                            <li class="tab @if($package->type == 2) active @endif tab-switch" data-id="2"><a data-toggle="tab" href="#tab-2" aria-expanded="true">门票</a></li>
                                            <input type="hidden" name="type" value="{{ $package->type }}">
                                        </ul>
                                        <div class="tab-content">
                                            <div id="tab-1" class="tab-pane @if($package->type == 1) active @endif ">
                                                <div class="panel-body">
                                                    <div class="form-group">
                                                        <input name="coins" value="{{ $package->coins }}" class="form-control" placeholder="出币数">
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="tab-2" class="tab-pane @if($package->type == 2) active @endif ">
                                                <div class="panel-body">
                                                    <div class="form-group">
                                                        <input name="ticket_name" value="{{ $package->ticket_name }}" class="form-control" placeholder="门票名称，最长10个中文">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>商品描述</label>
                                    <textarea class="form-control" name="description" rows="3">{{ $package->description }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label>是否在前端展示</label>
                                    <select name="flag" class="form-control">
                                        <option @if($package->flag == 1) selected @endif value="1">是</option>
                                        <option @if($package->flag == 0) selected @endif value="0">否</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-7">
                                <div class="form-group">
                                    <label>封面图片（大小限制：346x193）</label>
                                    <div class="input-group">
                                        <div class="btn-group">
                                            <input type="hidden" name="image" value="{{ $package->image }}">
                                            <div id="logo-picker">上传</div>
                                            <button class="btn btn-sm btn-rounded btn-warning" type="button" data-toggle="modal" data-target="#sysImageModal">预设</button>
                                            <div class="modal fade" id="sysImageModal" tabindex="-1" role="dialog" aria-hidden="true">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content" style="z-index:3;">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                            <h4 class="modal-title">预设封面</h4>
                                                            <small class="font-bold">这里可以选择系统预设封面。</small>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="photos text-center">
                                                                <a href="#">
                                                                    <img alt="image" width="195" class="feed-photo" src="img/p1.jpg">
                                                                </a>
                                                                <a href="#">
                                                                    <img alt="image" width="195" class="feed-photo" src="img/p1.jpg">
                                                                </a>
                                                                <a href="#">
                                                                    <img alt="image" width="195" class="feed-photo" src="img/p1.jpg">
                                                                </a>
                                                                <a href="#">
                                                                    <img alt="image" width="195" class="feed-photo" src="img/p1.jpg">
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="ibox-content no-padding border-left-right logo-preview" style="margin-top:10px">
                                            <img src="{{ config('static.base_url').'/'.$package->path }}" class="img-preview-sm" alt="image">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>相册</label>
                                    <div class="input-group">
                                        <div id="gallery-picker">上传</div>
                                        <div style="margin-top:10px" class="gallery-container">
                                            @foreach($gallery as $g)
                                                <div class="file-box">
                                                    <div class="file">
                                                        <span class="corner"></span>
                                                        <div class="image">
                                                            <input type="hidden" name="gallery_photos[]" value="{{ $g->id }}">
                                                            <img alt="image" class="img-preview-sm" src="{{ config('static.base_url').'/'.$g->path }}">
                                                        </div>
                                                        <div class="file-name text-center">
                                                            <button class="btn btn-warning btn-circle btn-del-photo" type="button"><i class="fa fa-times"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>适用门店</label>
                                </div>
                                <div class="form-group">
                                    @foreach($stores as $k=>$store)
                                        <label class="checkbox-inline i-checks" for="store_[{{ $k }}]">
                                            <input type="checkbox" id="store_[{{ $k }}]" name="store_ids[]" value="{{ $store->id }}" @if(in_array($store->id,$available_stores)) checked @endif>{{ $store->name }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <button class="btn btn-sm btn-primary btn-add-package" type="button">保存</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="/business/js/plugins/chosen/chosen.jquery.js"></script>
    <script src="/business/js/plugins/jsKnob/jquery.knob.js"></script>
    <script type="text/javascript" src="/merchant/js/webuploader.min.js"></script>
    <script src="/business/js/plugins/jasny/jasny-bootstrap.min.js"></script>
    <script src="/business/js/plugins/datapicker/bootstrap-datepicker.js"></script>
    <script src="/business/js/plugins/prettyfile/bootstrap-prettyfile.js"></script>
    <script src="/business/js/plugins/nouslider/jquery.nouislider.min.js"></script>
    <script src="/business/js/plugins/switchery/switchery.js"></script>
    <script src="/business/js/plugins/ionRangeSlider/ion.rangeSlider.min.js"></script>
    <script src="/business/js/plugins/iCheck/icheck.min.js"></script>
    <script src="/business/js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="/business/js/plugins/colorpicker/bootstrap-colorpicker.min.js"></script>
    <script src="/business/js/plugins/clockpicker/clockpicker.js"></script>
    <script src="/business/js/plugins/cropper/cropper.min.js"></script>
    <script type="text/javascript">
        $(function(){

            $('.btn-del-photo').click(function(){
                $(this).parents('div.file-box').empty().remove();
            });

            var imageUploader = WebUploader.create({
                swf:'merchant/js/Uploader.swf',
                server:'/upload',
                pick:'#logo-picker',
                resize:false,
                auto:true
            });
            imageUploader.on('uploadSuccess',function(file,response){
                $('.logo-preview').empty().append($('<img>').attr('src',response.data[0].absolute_path).addClass('img-preview-sm'));
                $('input[name=image]').val(response.data[0].id);
            });

            $('.tab-switch').click(function(){
                $('input[name=type]').val($(this).data('id'));
            });

            var galleryUploader = WebUploader.create({
                swf:'merchant/js/Uploader.swf',
                server:'/upload',
                pick:'#gallery-picker',
                resize:false,
                auto:true
            });

            var $gallery = $('.gallery-container');
            galleryUploader.on('uploadSuccess',function(file,response){
                var attid = '<input type="hidden" name="gallery_photos[]" value="' + response.data[0].id +'">';
                var img = '<img alt="image" class="img-preview-sm" src="' + response.data[0].absolute_path + '">';
                var item = '<div class="file-box"><div class="file"><span class="corner"></span><div class="image">' + attid + img + '</div><div class="file-name text-center"><button class="btn btn-warning btn-circle btn-del-photo" type="button"><i class="fa fa-times"></i></button> </div></div></div>';
                $gallery.append($(item));
            });

            $('.btn-add-package').click(function(){
                youyibao.httpSend($('form.form-add-package'),'post',1);
            });

        });
    </script>

    <script type="text/javascript">
    $(document).ready(function() {
        $(".i-checks").iCheck({
            checkboxClass: "icheckbox_square-green",
            radioClass: "iradio_square-green",
        });
        $("#data_1 .input-group.date").datepicker({
            todayBtn: "linked",
            keyboardNavigation: !1,
            forceParse: !1,
            calendarWeeks: !0,
            autoclose: !0
        });
        $(".clockpicker").clockpicker();
    });
    </script>
@endsection


<div class="modal fade" id="sysImageModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="z-index:3;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">预设封面</h4>
                <small class="font-bold">这里可以选择系统预设封面。</small>
            </div>
            <div class="modal-body">
                <div class="photos text-center">
                    @foreach($reservedImgs as $img)
                        <a href="#" class="img-item">
                            <img onclick="javascript:chooseImg($(this));" data-id="{{ $img->id }}" alt="image" width="195" class="feed-photo reserved-image" src="{{ config('static.base_url').'/'.$img->path }}">
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function chooseImg($obj){
        $('input[name=image]').val($obj.data('id'));
        $('div.logo-preview').empty().append($obj.clone());
        $obj.parents($('.modal-dialog')).modal('hide');
    }
</script>

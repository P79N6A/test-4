<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>创建广告</title>
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
    <link href="/business/css/webuploader.css" rel="stylesheet">
</head>

<body class="gray-bg">
    <div class="row content-tabs">
        <nav class="page-tabs J_menuTabs">
            <div class="page-tabs-content">
                <a href="javascript:;" class="active J_menuTab" data-id="home.html">广告管理</a>
            </div>
        </nav>
    </div>
    <div class="wrapper wrapper-content animated fadeInUp">
        <div class="row">
            <form action="{{ route('business.add-ad') }}" method="post" class="form-add-ad">
                {{ csrf_field() }}
                <div class="col-sm-12">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>创建广告</h5>
                        </div>
                        <div class="ibox-content">
                            <div class="row">
                                <div class="col-sm-8">
                                    <div class="form-group">
                                        <label>名称</label>
                                        <input type="text" name="title" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>显示平台</label>
                                        <div>
                                            <div class="radio radio-info radio-inline">
                                                <input type="radio" id="qinlineRadio1" value="0" name="platform" checked="">
                                                <label for="qinlineRadio1">全平台</label>
                                            </div>
                                            <div class="radio radio-info radio-inline">
                                                <input type="radio" id="qinlineRadio2" value="1" name="platform">
                                                <label for="qinlineRadio2">IOS</label>
                                            </div>
                                            <div class="radio radio-info radio-inline">
                                                <input type="radio" id="qinlineRadio3" value="2" name="platform">
                                                <label for="qinlineRadio3">Android</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>广告内容</label>
                                        <div class="tabs-container">
                                            <ul class="nav nav-tabs">
                                                <li class="active switch-type" data-type-index="1">
                                                    <a data-toggle="tab" href="#tab-1" aria-expanded="true"> 图文</a>
                                                </li>
                                                <li class="switch-type" data-type-index="1">
                                                    <a data-toggle="tab" href="#tab-2" aria-expanded="false"> 超链接</a>
                                                </li>
                                                <li class="switch-type" data-type-index="3">
                                                    <a data-toggle="tab" href="#tab-3" aria-expanded="false"> 套餐</a>
                                                </li>
                                                <input type="hidden" name="type" value="1">
                                            </ul>
                                            <div class="tab-content">
                                                <div id="tab-1" class="tab-pane active">
                                                    <div class="panel-body">
                                                        <script id="content" name="content" type="text/plain"></script>
                                                    </div>
                                                </div>
                                                <div id="tab-2" class="tab-pane">
                                                    <div class="panel-body">
                                                        <div class="form-group">
                                                            <label>链接</label>
                                                            <input type="text" class="form-control" name="url">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="tab-3" class="tab-pane">
                                                    <div class="panel-body">
                                                        <div class="form-group">
                                                            <label>门店</label>
                                                            <select class="form-control m-b stores" name="store_id">
                                                                @if(!empty($stores))
                                                                    <option>请选择门店</option>
                                                                    @foreach($stores as $store)
                                                                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>套餐</label>
                                                            <select class="form-control m-b" name="package_id">
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group" id="stores">
                                        <label>投放门店</label>
                                        <div>
                                            @if(!empty($stores))
                                                @foreach($stores as $store)
                                                    <label class="checkbox-inline i-checks">
                                                        <input type="checkbox" name="store_ids[]" value="{{ $store->id }}">{{ $store->name }}</label>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>推荐</label>
                                        <div>
                                            <div class="switch">
                                                <div class="onoffswitch">
                                                    <input type="checkbox" class="onoffswitch-checkbox" id="rec1" name="flag" value="1">
                                                    <label class="onoffswitch-label" for="rec1">
                                                        <span class="onoffswitch-inner"></span>
                                                        <span class="onoffswitch-switch"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>封面图片（大小限制：750x420）</label>
                                        <div class="input-group">
                                            <div id="image-picker">上传</div>
                                            <input type="hidden" name="image">
                                            <div class="ibox-content no-padding border-left-right image-preview" style="margin-top:10px">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <button class="btn btn-sm btn-primary btn-add-ad" type="button">创建广告</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="sysImageModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">预设封面</h4>
                    <small class="font-bold">这里可以选择系统预设封面。</small>
                </div>
                <div class="modal-body">
                    <div class="photos text-center">
                        <a href="#">
                            <img alt="image" width="195" class="feed-photo" src="/business/img/p1.jpg">
                        </a>
                        <a href="#">
                            <img alt="image" width="195" class="feed-photo" src="/business/img/p1.jpg">
                        </a>
                        <a href="#">
                            <img alt="image" width="195" class="feed-photo" src="/business/img/p1.jpg">
                        </a>
                        <a href="#">
                            <img alt="image" width="195" class="feed-photo" src="/business/img/p1.jpg">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="/ueditor/ueditor.config.modified.js"></script>
    <script src="/ueditor/ueditor.all.js"></script>
    <script src="/business/js/jquery.min.js?v=2.1.4"></script>
    <script src="/business/js/bootstrap.min.js?v=3.3.6"></script>
    <script src="/business/js/content.min.js?v=1.0.0"></script>
    <script src="/business/js/plugins/chosen/chosen.jquery.js"></script>
    <script src="/business/js/plugins/jsKnob/jquery.knob.js"></script>
    <script src="/business/js/plugins/jasny/jasny-bootstrap.min.js"></script>
    <script src="/business/js/plugins/datapicker/bootstrap-datepicker.js"></script>
    <script src="/business/js/plugins/prettyfile/bootstrap-prettyfile.js"></script>
    <script src="/business/js/plugins/nouslider/jquery.nouislider.min.js"></script>
    <script src="/business/js/plugins/switchery/switchery.js"></script>
    <script src="/business/js/plugins/ionRangeSlider/ion.rangeSlider.min.js"></script>
    <script src="/business/js/plugins/iCheck/icheck.min.js"></script>
    <script src="/business/js/youyibao.js"></script>
    <script src="/business/js/webuploader.min.js"></script>
    <script src="/business/js/layer/layer/layer.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {
        $(".i-checks").iCheck({
            checkboxClass: "icheckbox_square-green",
            radioClass: "iradio_square-green",
        });
        var ue = UE.getEditor('content',{
            initialFrameHeight:400,
        });

        $('.switch-type').click(function(){
            var index = $(this).data('type-index');
            $('input[name=type]').val(index);

            if($('input[name=type]').val() == 3){
                $('#stores').hide();
            }else{
                $('#stores').show();
            }
        });

        // 图片上传
        var imageUploader = WebUploader.create({
            auto:true,
            swf:'/merchant/js/Uploader.swf',
            server:'/upload',
            pick:'#image-picker',
            accept:{
                title:'Images',
                extensions:'jpg,png,bmp,gif',
                mimeTypes:'image/*'
            }
        });
        imageUploader.on('uploadSuccess',function(file, response){
            $('.image-preview').empty().append($('<img class="img-preview-sm">').attr('src',response.data[0].absolute_path).css({width:160,height:90}));
            $('input[name=image]').val(response.data[0].id);
        });

        $('.stores').change(function(){
            if(parseInt($(this).val()) > 0){
                youyibao.getPackages($(this).val(),function(data){
                    if(data.length > 0){
                        var res = null;
                        $.each(data,function(index,value){
                            res += '<option value="' + value.id + '">' + value.name + '</opton>';
                        });
                        $('select[name=package_id]').empty().append(res);
                    }else{
                        var option = '<option>无可用套餐</option>';
                        $('select[name=package_id]').empty().append(option);
                    }
                });
            }
        });

        $('.btn-add-ad').click(function(){
            youyibao.httpSend($('form.form-add-ad'),'post',1);
        });

    });
    </script>
</body>

</html>

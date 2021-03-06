<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>创建卡券</title>
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
                <a href="javascript:;" class="active J_menuTab" data-id="home.html">卡券管理</a>
            </div>
        </nav>
    </div>
    <div class="wrapper wrapper-content animated fadeInUp">
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>创建卡券</h5>
                    </div>
                    <div class="ibox-content">
                        <form action="{{ route('business.add-ticket') }}" method="post" class="form-add-ticket">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>封面图片</label>
                                        <div class="input-group">
                                            <div id="logo-picker">上传</div>
                                            <input type="hidden" name="image">
                                            <div class="ibox-content no-padding border-left-right logo-preview" style="margin-top:10px"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-8">
                                    <div class="form-group">
                                        <label>类型</label>
                                        <div>
                                            <div class="radio radio-info radio-inline">
                                                <input type="radio" id="inlineRadio1" value="1" name="type" checked>
                                                <label for="inlineRadio1">现金券</label>
                                            </div>
                                            {{--<div class="radio radio-info radio-inline">--}}
                                                {{--<input type="radio" id="inlineRadio2" value="2" name="type">--}}
                                                {{--<label for="inlineRadio2">折扣券</label>--}}
                                            {{--</div>--}}
                                            <div class="radio radio-info radio-inline">
                                                <input type="radio" id="inlineRadio3" value="3" name="type">
                                                <label for="inlineRadio3">体验券</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group ticket-name">
                                        <label>名称</label>
                                        <input type="text" name="name" class="form-control" placeholder="20字以内">
                                    </div>
                                    <div class="form-group denominatioin-container">
                                        <label>面额</label>
                                        <input type="text" name="denomination" class="form-control" placeholder="单位：元">
                                    </div>
                                    {{--<div class="form-group discount-container">--}}
                                        {{--<label>折扣</label>--}}
                                        {{--<input type="text" name="discount" class="form-control" placeholder="1-9.9 折">--}}
                                    {{--</div>--}}
                                    <div class="form-group">
                                        <label>发放量</label>
                                        <input type="text" name="circulation" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>领取有效期</label>
                                        <div>
                                            <input placeholder="开始时间" name="get_start_date" class="form-control layer-date" id="get_start">
                                            <input placeholder="结束时间" name="get_end_date" class="form-control layer-date" id="get_end">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>使用有效期</label>
                                        <div>
                                            <input placeholder="开始时间" name="start_date" class="form-control layer-date" id="use_start">
                                            <input placeholder="结束时间" name="expire_date" class="form-control layer-date" id="use_end">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>使用说明</label>
                                        <textarea class="form-control" name="instruction" rows="3"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>是否显示在门店首页</label>
                                        <select name="visible" class="form-control">
                                            <option value="1">是</option>
                                            <option value="0">否</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>适用门店</label>
                                    </div>
                                    <div class="form-group">
                                        @foreach($stores as $store)
                                            <label class="checkbox-inline i-checks">
                                                <input type="checkbox" name="store_ids[]" value="{{ $store->id }}">{{ $store->name }}
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <button class="btn btn-sm btn-primary btn-add-ticket" type="button">创建卡券</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
    <script src="/business/js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="/business/js/plugins/colorpicker/bootstrap-colorpicker.min.js"></script>
    <script src="/business/js/plugins/clockpicker/clockpicker.js"></script>
    <script src="/business/js/plugins/cropper/cropper.min.js"></script>
    <script src="/business/js/plugins/layer/laydate/laydate.js"></script>
    <script src="/business/js/webuploader.min.js"></script>
    <script src="/business/js/youyibao.js"></script>
    <script src="/business/js/layer/layer/layer.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {
        $(".i-checks").iCheck({
            checkboxClass: "icheckbox_square-green",
            radioClass: "iradio_square-green",
        });
    });
    var get_start = {
        elem: "#get_start",
        format: "YYYY-MM-DD hh:mm:ss",
        min: laydate.now(),
        max: "2099-06-16 23:59:59",
        istime: true,
        istoday: false,
        choose: function(datas) {
            get_end.min = datas;
            get_end.start = datas
        }
    };
    var get_end = {
        elem: "#get_end",
        format: "YYYY-MM-DD hh:mm:ss",
        min: laydate.now(),
        max: "2099-06-16 23:59:59",
        istime: true,
        istoday: false,
        choose: function(datas) {
            get_start.max = datas
        }
    };
    var use_start = {
        elem: "#use_start",
        format: "YYYY-MM-DD hh:mm:ss",
        min: laydate.now(),
        max: "2099-06-16 23:59:59",
        istime: true,
        istoday: false,
        choose: function(datas) {
            use_end.min = datas;
            use_end.start = datas
        }
    };
    var use_end = {
        elem: "#use_end",
        format: "YYYY-MM-DD hh:mm:ss",
        min: laydate.now(),
        max: "2099-06-16 23:59:59",
        istime: true,
        istoday: false,
        choose: function(datas) {
            use_start.max = datas
        }
    };
    laydate(get_start);
    laydate(get_end);
    laydate(use_start);
    laydate(use_end);
    </script>

    <script>
        $(function(){
            var imageUploader = WebUploader.create({
                swf:'/business/js/Uploader.swf',
                server:'/upload',
                pick:'#logo-picker',
                resize:false,
                auto:true
            });
            imageUploader.on('uploadSuccess',function(file,response){
                $('.logo-preview').empty().append($('<img>')
                    .attr('src',response.data[0].absolute_path)
                    .attr('alt','image')
                    .addClass('img-preview-sm'));
                $('input[name=image]').val(response.data[0].id);
            });

            $('.discount-container').hide();
            $('.ticket-name').hide();

            $('input[name=type]').change(function(){
                if($(this).val() == 1){
                    $('.discount-container').hide();
                    $('.denominatioin-container').show();
                    $('.ticket-name').hide();
                }else if($(this).val() == 2){
                    $('.denominatioin-container').hide();
                    $('.discount-container').show();
                    $('.ticket-name').hide();
                }else if($(this).val() == 3){
                    $('.denominatioin-container').hide();
                    $('.discount-container').hide();
                    $('.ticket-name').show();
                }
            });

            // 提交表单
            $('.btn-add-ticket').click(function(){
                youyibao.httpSend($('form.form-add-ticket'),'post',1);
            });

        });
    </script>

</body>

</html>

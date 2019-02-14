<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>发放卡券</title>
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
                        <h5>发放卡券</h5>
                    </div>
                    <div class="ibox-content">
                        <form action="{{ route('business.posting-ticket') }}" method="post" class="form-posting-ticket">
                            {{ csrf_field() }}
                            <input type="hidden" name="id" value="{{ $ticket->id }}">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>选择门店</label>
                                        <div class="form-group">
                                            @foreach($stores as $store)
                                                <label class="checkbox-inline i-checks"><input type="checkbox" name="store_ids[]" value="{{ $store->id }}">{{ $store->name }}</label>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>数量统计</label>
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div class="widget style1 navy-bg">
                                                    <div class="row">
                                                        <div class="col-xs-4">
                                                            <i class="fa fa-users fa-5x"></i>
                                                        </div>
                                                        <div class="col-xs-8 text-right">
                                                            <span> 访客总数 </span>
                                                            <h2 class="font-bold">{{ $ticket->visitorCount }}</h2>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="widget style1 yellow-bg">
                                                    <div class="row">
                                                        <div class="col-xs-4">
                                                            <i class="fa fa-yen fa-5x"></i>
                                                        </div>
                                                        <div class="col-xs-8 text-right">
                                                            <span> 消费过的用户 </span>
                                                            <h2 class="font-bold">{{ $ticket->consumerCount }}</h2>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="widget style1 yellow-bg">
                                                    <div class="row">
                                                        <div class="col-xs-4">
                                                            <i class="fa fa-get-pocket fa-5x"></i>
                                                        </div>
                                                        <div class="col-xs-8 text-right">
                                                            <span> 已经发放 </span>
                                                            <h2 class="font-bold">{{ $ticket->postedCount }}</h2>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>目标人群</label>
                                        <div>
                                            <div class="radio radio-info radio-inline">
                                                <input type="radio" id="inlineRadio1" value="1" name="target" >
                                                <label for="inlineRadio1">门店访客</label>
                                            </div>
                                            <div class="radio radio-info radio-inline">
                                                <input type="radio" id="inlineRadio2" value="2" name="target">
                                                <label for="inlineRadio2">有消费过的用户</label>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <button class="btn btn-sm btn-primary btn-posting-ticket" type="button">确认发放</button>
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
    <script src="/business/js/youyibao.js"></script>
    <script src="/business/js/layer/layer/layer.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {
        $(".i-checks").iCheck({
            checkboxClass: "icheckbox_square-green",
            radioClass: "iradio_square-green",
        });
        $('.btn-posting-ticket').click(function(){
            youyibao.httpSend($('form.form-posting-ticket'),'post',1);
        });

    });
    </script>
</body>

</html>

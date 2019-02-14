<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="favicon.ico"> <link href="merchant/css/bootstrap.min14ed.css?v=3.3.6" rel="stylesheet">
    <link href="merchant/css/font-awesome.min93e3.css?v=4.4.0" rel="stylesheet">
    <link href="merchant/css/animate.min.css" rel="stylesheet">
    <link href="merchant/css/style.min862f.css?v=4.1.0" rel="stylesheet">

</head>

<body class="gray-bg">
    <div class="wrapper wrapper-content">
        <div class="row animated fadeInRight">
            <div class="col-sm-4">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>个人资料</h5>
                    </div>
                    <div>

                        <div class="ibox-content profile-content">
                            <h4><strong>姓名</strong></h4>
                            <p>{{ $profile->name }}</p>
                            <h5>邮箱</h5>
                            <p>{{ $profile->email }}</p>
                            <h5>手机</h5>
                            <p>{{ $profile->mobile }}</p>
                            <h5>地址</h5>
                            <p>{{ $profile->country }}-{{ $profile->city }}-{{ $profile->county }}</p>
                            <div class="user-button">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <button type="button" class="btn btn-primary btn-sm btn-block">修改资料</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="merchant/js/jquery.min.js?v=2.1.4"></script>
    <script src="merchant/js/bootstrap.min.js?v=3.3.6"></script>
    <script src="merchant/js/content.min.js?v=1.0.0"></script>
    <script src="merchant/js/plugins/peity/jquery.peity.min.js"></script>
    <script src="merchant/js/demo/peity-demo.min.js"></script>
    <script type="text/javascript" src="http://tajs.qq.com/stats?sId=9051096" charset="UTF-8"></script>
</body>
</html>

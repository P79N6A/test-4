<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>麦麦天空商户平台 - 登录</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link href="business/css/bootstrap.min14ed.css?v=3.3.6" rel="stylesheet">
    <link href="/business/css/font-awesome.min93e3.css?v=4.4.0" rel="stylesheet">
    <link href="/business/css/animate.min.css" rel="stylesheet">
    <link href="/business/css/style.min862f.css?v=4.1.0" rel="stylesheet">
    <style>
        body{
            background-image:url(/business/img/bus-login-bg.png);
            background-size:100%;
        }
    </style>
    <!--[if lt IE 9]>
    <meta http-equiv="refresh" content="0;ie.html"/>
    <![endif]-->
    <script>if (window.top !== window.self) {
            window.top.location = window.location;
        }</script>
</head>

<body>

<div class="middle-box text-center loginscreen  animated fadeInDown">
    <div>
        <div>
            <h1 class="logo-name">
                <div id="logo"><img src="/images/youyibao-logo.png"></div>
            </h1>
        </div>
        <h3>欢迎使用 麦麦天空商户平台</h3>

        <form role="form" method='post' action="/processlogin" class="m-t form-login">
            <div class="form-group">
                <input name="username" class="form-control" placeholder="用户名" required="">
            </div>
            <div class="form-group">
                <input type="password" name="password" class="form-control" placeholder="密码" required="">
            </div>
            <button type="submit" class="btn btn-login btn-success block full-width m-b">登 录</button>
        </form>
    </div>
</div>
<script src="/business/js/jquery.min.js?v=2.1.4"></script>
<script src="/business/js/bootstrap.min.js?v=3.3.6"></script>
<script src="/business/js/youyibao.js"></script>
<script src="/business/js/layer/layer/layer.js"></script>
<script>
    $(function () {
        $('form').submit(function (e) {
            e.preventDefault();
            youyibao.httpSend($(this), 'post', 1);
        });
    });
</script>
</body>
</html>

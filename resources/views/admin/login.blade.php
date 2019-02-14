<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>麦麦天空总后台登录</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link href="/admin/css/bootstrap.min14ed.css?v=3.3.6" rel="stylesheet">
    <link href="/admin/css/font-awesome.min93e3.css?v=4.4.0" rel="stylesheet">
    <link href="/admin/css/animate.min.css" rel="stylesheet">
    <link href="/admin/css/style.min862f.css?v=4.1.0" rel="stylesheet">
    <style>
        body{
            background-image:url(/admin/img/admin-login-bg.png);
            background-size:100% 100%;
        }
    </style>
    <script>if (window.top !== window.self) {
            window.top.location = window.location;
        }</script>
</head>
<body class="gray-bg">
<div class="middle-box text-center loginscreen  animated fadeInDown">
    <div>
        <div>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <h1 class="logo-name">
                <div id="logo"><img src="/images/youyibao-logo.png" width="100" height="100"></div>
            </h1>
        </div>
        <h3>麦麦天空管理后台</h3>
        <form class="m-t" role="form" method="post" action="{{ route('admin.login') }}">
            {{ csrf_field() }}
            <div class="form-group">
                <input type="text" class="form-control" placeholder="用户名" name="name" required="">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="密码" required="">
            </div>
            <button type="submit" class="btn btn-success block full-width m-b">登 录</button>
        </form>
    </div>
</div>
<script src="/admin/js/jquery.min.js?v=2.1.4"></script>
<script src="/admin/js/bootstrap.min.js?v=3.3.6"></script>
<script src="/admin/js/youyibao.js"></script>
<script src="/admin/js/layer/layer/layer.js"></script>
<script>
    $(function(){
        $('form').submit(function(e){
            e.preventDefault();
            youyibao.httpSend($(this),'post',1);
        });


    });
</script>
</body>
</html>

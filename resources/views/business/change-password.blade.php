<!DOCTYPE html>
<html>
<!-- Mirrored from www.zi-han.net/theme/hplus/form_basic.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 20 Jan 2016 14:19:15 GMT -->

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>修改密码</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link href="/business/css/bootstrap.min14ed.css?v=3.3.6" rel="stylesheet">
    <link href="/business/css/font-awesome.min93e3.css?v=4.4.0" rel="stylesheet">
    <link href="/business/css/plugins/iCheck/custom.css" rel="stylesheet">
    <link href="/business/css/animate.min.css" rel="stylesheet">
    <link href="/business/css/style.min862f.css?v=4.1.0" rel="stylesheet">
</head>

<body class="gray-bg">
    <div class="row content-tabs">
        <nav class="page-tabs J_menuTabs">
            <div class="page-tabs-content">
                <a href="javascript:;" class="active J_menuTab" data-id="home.html">修改密码</a>
            </div>
        </nav>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="ibox float-e-margins">
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-6 b-r">
                        <form role="form" class="form-change-pwd" action="/change-password">
                            <div class="form-group">
                                <label>旧密码</label>
                                <input type="password" placeholder="请输入当前密码" name="old_password" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>新密码</label>
                                <input type="password" placeholder="请输入新的密码" name="new_password" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>确认密码</label>
                                <input type="password" placeholder="请输入新的密码" id="confirm-password" class="form-control">
                            </div>
                            <div>
                                <button class="btn btn-sm btn-primary pull-right m-t-n-xs" type="button">
                                    <strong class="btn-change-pwd">确认</strong>
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="col-sm-6">
                        <p class="text-center animated fadeInRightBig">
                            <i class="fa fa-lock big-icon"></i>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="/business/js/jquery.min.js?v=2.1.4"></script>
    <script src="/business/js/bootstrap.min.js?v=3.3.6"></script>
    <script src="/business/js/content.min.js?v=1.0.0"></script>
    <script type="text/javascript" src="/business/js/youyibao.js"></script>
    <script type="text/javascript" src="/business/js/layer/layer/layer.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $('.btn-change-pwd').click(function(){
                var old = $('input[name=old_password]').val();
                var newPwd = $('input[name=new_password]').val();
                var confirm = $('#confirm-password').val();

                if(old.length <= 0){
                    layer.msg('请输入旧新密码',{icon:5});
                    return false;
                }
                if(newPwd.length <= 0){
                    layer.msg('请输入新密码',{icon:5});
                    return false;
                }
                if(confirm.length <= 0){
                    layer.msg('请输入确认密码',{icon:5});
                    return false;
                }

                if($('#confirm-password').val() != $('input[name=new_password]').val()){
                    layer.msg('两次密码不一致',{icon:5});
                    return false;
                }
                youyibao.httpSend($('form.form-change-pwd'),'post',1);
            });
        });

    </script>
</body>
</html>

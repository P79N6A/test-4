<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="merchant/css/style.min.css?v=2.1.0" rel="stylesheet">
</head>

<body class="gray-bg">
    <div id="modal-form" class="modal fade" aria-hidden="true">
        <div class="modal-content">
            <div class="row">
                <div class="col-sm-4 ">
                    <form role="form" class="form-edituser" action="/edit-user" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" name="id" value="{{ $user->id }}">
                        <div class="form-group">
                            <label>用户名称：</label>
                            <input placeholder="用户名称" name="name" value="{{ $user->name }}" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>邮件地址：</label>
                            <input placeholder="邮件地址（可选）" name="email" value="{{ $user->email }}"  class="form-control">
                        </div>
                        <div class="form-group">
                            <label>密码：</label>
                            <input type="password" placeholder="密码，为空则不修改" name="password" class="form-control">
                        </div>
                        <div>
                            <button class="btn btn-edituser btn-sm btn-primary pull-right m-t-n-xs" type="button">
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
<script type="text/javascript" src="merchant/js/youyibao.js"></script>
<script type="text/javascript" src="/merchant/layer/layer/layer.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('.btn-edituser').click(function(){
            youyibao.httpSend($('form.form-edituser'),'post',1);
        });
    });
</script>
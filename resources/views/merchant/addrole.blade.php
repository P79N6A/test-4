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
                    <form role="form" class='form-addrole' action="/storerole" method="post">
                        <div class="form-group">
                            <label>角色唯一标识符：</label>
                            <input placeholder="唯一标识角色的名称" name="name" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>角色显示名称：</label>
                            <input placeholder="人类可读的名称（可选）" name="display_name" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>角色描述：</label>
                            <input placeholder="角色描述（可选）" name="description" class="form-control">
                        </div>
                        <div>
                            <button id="btn-sub" class="btn btn-sm btn-primary pull-right m-t-n-xs" type="button">
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
<script type="text/javascript" src="merchant/layer/layer/layer.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
       $('#btn-sub').click(function(){
           youyibao.httpSend($('form.form-addrole'),'post',1);
       });
    });
</script>

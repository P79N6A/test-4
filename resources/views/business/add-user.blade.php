@extends('business.layouts.frame-parent')
@section('page-title','创建操作员')
@section('main')
    <div class="wrapper wrapper-content animated fadeInUp">
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>创建操作员</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <form action="/add-user" class="form-edit-user">
                                {{ csrf_field() }}
                                <input type="hidden" name="id" >
                                <div class="col-sm-6 col-md-4 col-lg-3">
                                    <div class="form-group">
                                        <label>登录名</label>
                                        <div class="input-group m-b">
                                            <span class="input-group-addon">{{ session('username') }}#</span>
                                            <input type="text" name="name" placeholder="数字或者小写字母" class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>操作员描述</label>
                                        <textarea class="form-control" name="description" placeholder="可选"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>密码</label>
                                        <input type="password" name="password" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>确认密码</label>
                                        <input type="password" id="confirm-pwd" class="form-control">
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <button class="btn btn-sm btn-primary btn-edit-user" type="submit">创建</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function(){
            $('.btn-edit-user').click(function(){
                if($('input[name=name]').val().length <= 0){
                    layer.msg('名字不能为空',{icon:5});
                    return false;
                }
                var old = $('input[name=password]').val();
                var confirmPwd = $('#confirm-pwd').val();
                if((old.length > 0) && (confirmPwd.length <= 0)){
                    layer.msg('请输入确认密码',{icon:5});
                    return false;
                }
                if(old !== confirmPwd){
                    layer.msg('两次密码不一致',{icon:5});
                    return false;
                }

                youyibao.httpSend($('form.form-edit-user'),'post',1);
            });
        });
    </script>
@endsection

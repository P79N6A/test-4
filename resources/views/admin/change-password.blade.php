@extends('admin.layouts.parent')
@section('page-title','修改密码')
@section('main')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="ibox float-e-margins">
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-6 b-r">
                        <form role="form" class="form-change-pwd" action="/change-password">
                            <div class="form-group">
                                <label>旧密码</label>
                                <input type="password" placeholder="请输入当前密码" name="old" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>新密码</label>
                                <input type="password" placeholder="请输入新的密码" name="new" class="form-control">
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
    
    <script type="text/javascript">
        $(document).ready(function(){
            $('.btn-change-pwd').click(function(){
                var old = $('input[name=old]').val();
                var newPwd = $('input[name=new]').val();
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

                if($('#confirm-password').val() != $('input[name=new]').val()){
                    layer.msg('两次密码不一致',{icon:5});
                    return false;
                }
                youyibao.httpSend($('form.form-change-pwd'),'post',1);
            });
        });

    </script>
@endsection
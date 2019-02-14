@extends('admin.layouts.parent')
@section('page-title','修改管理员')
@section('main')
    <div class="row">
            <div class="col-sm-12">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>修改操作员</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <form action="{{ route('admin.edit-user') }}" class="form-edit-user">
                                {{ csrf_field() }}
                                <input type="hidden" name="id" value="{{ $user->id }}">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>登录名</label>
                                        <input type="text" name="name" value="{{ $user->name }}" placeholder="数字或者小写字母" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>手机号</label>
                                        <input type="text" name="mobile" value="{{ $user->mobile }}" placeholder="请输入手机号码" class="form-control">
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
                        <button class="btn btn-sm btn-primary btn-edit-user" type="submit">保存</button>
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

@extends('admin.layouts.parent')
@section('page-title','修改密码')
@section('main')
    <div class="ibox float-e-margins">
        <div class="ibox-content">
            <div class="row">
                <div class="col-sm-6 b-r">
                    <form role="form" action="{{ route('admin.reset-bus-password') }}" method="post">
                        <div class="form-group">
                            <label>您将为以下商户账号重置密码</label>
                            <h4>
                                <input type="hidden" name="id" value="{{ $user->id }}">
                                <span>用户名：{{ $user->name }}</span><br>
                            </h4>
                            <h4>
                                {{ csrf_field() }}
                                <span>手机号：{{ $user->mobile }}</span>
                            </h4>
                        </div>
                        <div class="form-group">
                            <label>新密码</label>
                            <input type="password" name="password" placeholder="请输入新的密码" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>确认密码</label>
                            <input type="password" name="confirm" id="confirm" placeholder="请重复输入新密码" class="form-control">
                        </div>
                        <div>
                            <button class="btn btn-sm btn-primary pull-right m-t-n-xs" type="submit"><strong>确认</strong>
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
    <script>
        $(function () {
            $('form').submit(function (e) {
                e.preventDefault();

                var _new = $('input[name=password]').val();
                var confirm = $('#confirm').val();

                if (_new.length < 1) {
                    layer.msg('请输入新密码', {
                        icon: 5
                    });
                    return false;
                }
                if (_new.length < 6) {
                    layer.msg('新密码最少6个字符', {
                        icon: 5
                    });
                    return false;
                }
                if (confirm.length < 1) {
                    layer.msg('请输入确认密码', {
                        icon: 5
                    });
                    return false;
                }
                if (confirm != _new) {
                    layer.msg('新密码两次输入内容不一致', {
                        icon: 5
                    });
                    return false;
                }

                youyibao.httpSend($(this), 'post', 1);
            });


        });
    </script>
@endsection

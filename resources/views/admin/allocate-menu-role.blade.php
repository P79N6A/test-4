@extends('admin.layouts.parent')
@section('page-title','分配菜单角色')
@section('main')
    <link rel="stylesheet" href="/admin/css/plugins/iCheck/custom.css">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>@yield('page-title')</h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-sm-6 col-md-4">
                            <form action="{{ route('admin.allocate-menu-role') }}" method="post">
                                {{ csrf_field() }}
                                <input type="hidden" name="id" value="{{ $user->id }}">
                                <div class="form-group">
                                    <label>商户账号名称</label>
                                    <p>{{ $user->name }}</p>
                                </div>
                                <div class="form-group">
                                    <label>角色</label>
                                    <div>
                                        @if(!empty($roles))
                                            @foreach($roles as $role)
                                                <label class="checkbox-inline i-checks">
                                                    <input type="checkbox" name="role_ids[]" value="{{ $role->id }}"
                                                           @if(in_array($role->id,$myRoles)) checked @endif>
                                                    {{ $role->name }}
                                                </label>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>
                                <button type="submit" class="btn btn-sm btn-primary">提交</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="/admin/js/plugins/iCheck/icheck.min.js"></script>
    <script>
        $(function () {
            $(".i-checks").iCheck({checkboxClass: "icheckbox_square-green", radioClass: "iradio_square-green"});
            $('form').submit(function (e) {
                e.preventDefault();
                youyibao.httpSend($(this), 'post', 1);
            });
        });
    </script>
@endsection
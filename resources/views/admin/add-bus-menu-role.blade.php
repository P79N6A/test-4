@extends('admin.layouts.parent')
@section('page-title','创建商户菜单角色')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>@yield('page-title')</h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-sm-8 col-md-4">
                            <form action="{{ route('admin.add-bus-menu-role') }}" method="post">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label>角色名称</label>
                                    <input class="form-control" name="name" placeholder="请输入角色名称">
                                </div>
                                <div class="form-group">
                                    <label>角色描述</label>
                                    <textarea class="form-control" name="description" placeholder="请输入角色描述"></textarea>
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
    <script>
        $(function () {
            $('form').submit(function (e) {
                e.preventDefault();
                youyibao.httpSend($(this), 'post', 1);
            });
        });
    </script>
@endsection
@extends('admin.layouts.parent')
@section('page-title','修改菜单角色管理')
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
                            <form action="{{ route('admin.edit-bus-menu-role') }}" method="post">
                                {{ csrf_field() }}
                                <input type="hidden" name="id" value="{{ $role->id }}">
                                <div class="form-group">
                                    <label>角色名称</label>
                                    <input class="form-control" name="name" value="{{ $role->name }}"
                                           placeholder="请输入角色名称">
                                </div>
                                <div class="form-group">
                                    <label>角色描述</label>
                                    <textarea class="form-control" name="description"
                                              placeholder="请输入角色描述">{{ $role->description }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label>状态</label>
                                    <select name="status" class="form-control">
                                        <option value="1" @if($role->status == 1) selected @endif>启用</option>
                                        <option value="2" @if($role->status == 2) selected @endif>禁用</option>
                                    </select>
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
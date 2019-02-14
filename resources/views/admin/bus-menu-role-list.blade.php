@extends('admin.layouts.parent')
@section('page-title','商户菜单角色管理')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>@yield('page-title')</h5>
                    <a href="{{ route('admin.add-bus-menu-role') }}"
                       class="pull-right btn btn-xs btn-primary">创建菜单角色</a>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-sm-12">
                            <table class="table table-responsive table-stripped">
                                <thead>
                                <tr>
                                    <th>名称</th>
                                    <th>描述</th>
                                    <th>状态</th>
                                    <th>创建日期</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(!empty($roles))
                                    @foreach($roles as $role)
                                        <tr>
                                            <td>{{ $role->name }}</td>
                                            <td>{{ $role->description }}</td>
                                            <td>
                                                @if($role->status == 1)<span class="label label-primary">启用</span>
                                                @elseif($role->status == 2)<span class="label label-danger">禁用</span>
                                                @endif
                                            </td>
                                            <td>{{ $role->create_date }}</td>
                                            <td>
                                                <a href="{{ route('admin.edit-bus-menu-role',['id'=>$role->id]) }}"
                                                   class="btn btn-sm btn-success">修改</a>
                                                <a data-url="{{ route('admin.delete-bus-menu-role') }}"
                                                   data-type="id"
                                                   data-id="{{ $role->id }}"
                                                   class="btn btn-sm btn-danger delete-role">删除</a>
                                                <a href="{{ route('admin.allocate-menu-for-role',['id'=>$role->id]) }}"
                                                   class="btn btn-sm btn-primary">分配访问菜单</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(function () {
            $('.delete-role').click(function () {
                var $this = $(this);
                layer.msg('您确定要删除该角色吗？', {
                    time: 0,
                    btn: ['是', '否'],
                    yes: function (index) {
                        layer.close(index);
                        youyibao.httpSend($this, 'get', 1);
                    }
                });
            });
        });
    </script>
@endsection
@extends('admin.layouts.parent')
@section('page-title','角色列表')
@section('main')
    <div class="ibox">
        <div class="ibox-title">
            <h5>所有角色</h5>
            <div class="ibox-tools">
                <a href="{{ route('admin.add-role') }}" class="btn btn-primary btn-xs">创建角色</a>
            </div>
        </div>
        <div class="ibox-content">
            <p>你好，欢迎使用角色管理</p>
            <div class="row">
                <div class="col-lg-12">
                    <table class="table table-stripped table-hover">
                        <thead>
                        <th>名称</th>
                        <th>描述</th>
                        <th>状态</th>
                        <th>创建日期</th>
                        <th>操作</th>
                        </thead>
                        <tbody>
                        @if(!empty($roles))
                            @foreach($roles as $role)
                                <tr>
                                    <td>{{ $role->display_name }}</td>
                                    <td>{{ $role->description }}</td>
                                    <td>
                                        @if($role->status == 1)
                                            <span class="label label-primary">启用</span>
                                        @elseif($role->status == 0)
                                            <span class="label">禁用</span>
                                        @endif
                                    </td>
                                    <td>{{ $role->created_at }}</td>
                                    <td>
                                        <a href="{{ route('admin.edit-role',['id'=>$role->id]) }}"
                                           class="btn btn-sm btn-warning">修改</a>
                                        <a href="javascript:;" data-url="{{ route('admin.delete-role') }}"
                                           data-type="id" data-id="{{ $role->id }}"
                                           class="btn btn-sm btn-danger btn-del-role">删除</a>
                                        <a href="{{ route('admin.allocate-permission',['id'=>$role->id]) }}"
                                           class="btn btn-sm btn-primary">功能权限</a>
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
    <script type="text/javascript">
        $(document).ready(function () {
            $('a.btn-del-role').click(function () {
                var $this = $(this);
                layer.msg('您确定删除该角色吗？', {
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

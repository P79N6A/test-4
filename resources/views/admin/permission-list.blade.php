@extends('admin.layouts.parent')
@section('page-title','权限列表')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>所有权限</h5>
                    <div class="ibox-tools">
                        <a href="{{ route('admin.add-permission') }}" class="btn btn-primary btn-xs">创建权限</a>
                    </div>
                </div>
                <div class="ibox-content">
                    <p>你好，欢迎使用权限管理</p>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>唯一标识符</th>
                                <th>权限名称</th>
                                <th>显示状态</th>
                                <th>禁用状态（禁用状态不显示）</th>
                                <th>创建时间</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($permissions))
                                @foreach($permissions as $permission)
                                    <tr>
                                        <td>{{ $permission['id'] }}</td>
                                        <td>{{ $permission['name'] }}</td>
                                        <td>{{ $permission['display_name'] }}</td>
                                        <td>
                                            @if($permission['status'] == 1)<span class="label label-success">显示</span>
                                            @elseif($permission['status'] == 0)<span class="label label-default">隐藏</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($permission['disable'] == 1)<span class="label label-danger">禁用</span>
                                            @elseif($permission['disable'] == 0)<span class="label label-primary">启用</span>
                                            @endif
                                        </td>
                                        <td>{{ $permission['created_at'] }}</td>
                                        <td>
                                            <a href="{{ route('admin.edit-permission',['id'=>$permission['id']]) }}"
                                               class="btn btn-warning btn-sm"><i class="fa fa-pencil"></i> 修改 </a>
                                            <a href="javascript:;" data-url="{{ route('admin.delete-permission') }}"
                                               data-type="id" data-id="{{ $permission['id'] }}"
                                               class="btn btn-danger btn-sm btn-del-permission"><i class="fa fa-trash"></i> 删除
                                            </a>
                                        </td>
                                    </tr>
                                    @if(!empty($permission['children']))
                                        @foreach($permission['children'] as $child)
                                            <tr>
                                                <td>{{ $child['id'] }}</td>
                                                <td>&nbsp;&nbsp;&nbsp;&nbsp;|---- {{ $child['name'] }}</td>
                                                <td>&nbsp;&nbsp;&nbsp;&nbsp;|---- {{ $child['display_name'] }}</td>
                                                <td>
                                                    @if($child['status'] == 1)<span class="label label-success">显示</span>
                                                    @elseif($child['status'] == 0)<span class="label label-default">隐藏</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($child['disable'] == 1)<span class="label label-danger">禁用</span>
                                                    @elseif($child['disable'] == 0)<span class="label label-primary">启用</span>
                                                    @endif
                                                </td>
                                                <td>{{ $child['created_at'] }}</td>
                                                <td>
                                                    <a href="{{ route('admin.edit-permission',['id'=>$child['id']]) }}"
                                                       class="btn btn-warning btn-sm"><i class="fa fa-pencil"></i> 修改 </a>
                                                    <a href="javascript:;" data-url="{{ route('admin.delete-permission') }}"
                                                       data-type="id" data-id="{{ $child['id'] }}"
                                                       class="btn btn-danger btn-sm btn-del-permission"><i class="fa fa-trash"></i> 删除
                                                    </a>
                                                </td>
                                            </tr>
                                            @if(!empty($child['children']))
                                                @foreach($child['children'] as $tchild)
                                                    <tr>
                                                        <td>{{ $tchild['id'] }}</td>
                                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|---- {{ $tchild['name'] }}</td>
                                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|---- {{ $tchild['display_name'] }}</td>
                                                        <td>
                                                            @if($tchild['status'] == 1)<span class="label label-success">显示</span>
                                                            @elseif($tchild['status'] == 0)<span class="label label-default">隐藏</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($tchild['disable'] == 1)<span class="label label-danger">禁用</span>
                                                            @elseif($tchild['disable'] == 0)<span class="label label-primary">启用</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $tchild['created_at'] }}</td>
                                                        <td>
                                                            <a href="{{ route('admin.edit-permission',['id'=>$tchild['id']]) }}"
                                                               class="btn btn-warning btn-sm"><i class="fa fa-pencil"></i> 修改 </a>
                                                            <a href="javascript:;" data-url="{{ route('admin.delete-permission') }}"
                                                               data-type="id" data-id="{{ $tchild['id'] }}"
                                                               class="btn btn-danger btn-sm btn-del-permission"><i class="fa fa-trash"></i> 删除
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    @endif
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="text-right"></div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(function(){
            $('.btn-del-permission').click(function(){
                var $this = $(this);
                layer.msg('确定要删除该菜单吗？可能会导致平台某些功能异常！',{
                    time:0,
                    btn:['是','否'],
                    yes:function(index){
                        layer.close(index);
                        youyibao.httpSend($this,'get',1);
                    }
                });
            });

        });
    </script>
@endsection

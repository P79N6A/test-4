@extends('admin.layouts.parent')
@section('page-title','管理员列表')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>所有管理员</h5>
                    <div class="ibox-tools">
                        <a href="{{ route('admin.add-user') }}" class="btn btn-primary btn-xs">创建管理员</a>
                    </div>
                </div>
                <div class="ibox-content">
                    <p>你好，欢迎使用操作员管理</p>
                    <div class="row">
                        <div class="col-sm-3">
                            <form action="{{ route('admin.user-list') }}" method="get">
                                <div class="input-group">
                                    <input type="text" name="keyword" value="{{ $keyword }}" placeholder="请输入关键词"
                                           class="input-sm form-control"> <span class="input-group-btn">
                                    <button type="submit" class="btn btn-sm btn-primary"> 搜索</button> </span>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>登录名</th>
                                <th>角色</th>
                                <th>创建时间</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($users))
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>
                                            <a href="{{ route('admin.allocate-role',['id'=>$user->id]) }}"
                                               class="btn btn-white btn-sm"><i class="fa fa-users"></i> 管理 </a>
                                        </td>
                                        <td>{{ $user->created_at }}</td>
                                        <td>
                                            <a href="{{ route('admin.edit-user',['id'=>$user->id]) }}"
                                               class="btn btn-warning btn-sm"><i class="fa fa-pencil"></i> 修改 </a>
                                            <a href="javascript:;" data-url="{{ route('admin.delete-user') }}"
                                               data-type="id" data-id="{{ $user->id }}"
                                               class="btn btn-danger btn-sm btn-del-user"><i class="fa fa-trash"></i> 删除
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="text-right">
                        @if(!empty($users) && !empty($users->links()))
                            {{ $users->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(function(){
            $('.btn-del-user').click(function(){
                var $this = $(this);
                layer.msg('您确定要删除该管理员吗？',{
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

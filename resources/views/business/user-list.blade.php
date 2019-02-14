@extends('business.layouts.frame-parent')
@section('page-title','操作员列表')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>所有操作员</h5>
                    <div class="ibox-tools">
                        <a href="/add-user" class="btn btn-primary btn-xs">创建操作员</a>
                    </div>
                </div>
                <div class="ibox-content">
                    <p>你好，欢迎使用操作员管理</p>
                    <div class="row">
                        <form action="">
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <input type="text" name="keyword" value="{{ $keyword }}" placeholder="请输入关键词" class="input-sm form-control">
                                    <span class="input-group-btn">
                                    <button type="submit" class="btn btn-sm btn-primary"> 搜索</button> </span>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>登录名</th>
                                <th>角色</th>
                                <th>描述</th>
                                <th>创建时间</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $parentUserName }}#{{ $user->name }}</td>
                                    <td>{{ $user->roles }}</td>
                                    <td>{{ $user->description }}</td>
                                    <td>{{ date('Y-m-d H:i:s',$user->regtime) }}</td>
                                    <td>
                                        <a href="/edit-user?id={{ $user->id }}" class="btn btn-white btn-sm"><i class="fa fa-pencil"></i> 修改 </a>
                                        <a href="javascript:;" data-url="/delete-user?id={{ $user->id }}" class="btn btn-white btn-sm btn-deluser"><i class="fa fa-trash"></i> 删除 </a>
                                        <a href="/allocate-role?id={{ $user->id }}" class="btn btn-white btn-sm"><i class="fa fa-users"></i> 分配角色 </a>
                                        <a href="{{ route('business.allocate-store',['id'=>$user->id]) }}" class="btn btn-sm btn-white"><i class="fa fa-edit"> 分配管理门店</i></a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-right">
                        @if(!empty($users->links())) {{ $users->appends(['keyword'=>$keyword])->links() }} @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function(){
            $('.btn-deluser').click(function(){
                var $this = $(this);
                layer.msg('您确定要删除该操作员吗？',{
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

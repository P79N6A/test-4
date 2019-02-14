@extends('business.layouts.frame-parent')
@section('page-title','角色列表')
@section('main')
        <div class="ibox">
            <div class="ibox-title">
                <h5>所有角色</h5>
                <div class="ibox-tools">
                    <a href="/add-role" class="btn btn-primary btn-xs">创建角色</a>
                </div>
            </div>
            <div class="ibox-content">
                <p>你好，欢迎使用角色管理</p>
                <div class="row">
                    <div class="col-sm-12">
                        <table class="table table-responsive table-stripped">
                            <thead>
                            <tr>
                                <th>名称</th>
                                <th>描述</th>
                                <th>状态</th>
                                <th>创建时间</th>
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
                                            @elseif($role->status == 0)<span class="label">禁用</span>
                                            @endif
                                        </td>
                                        <td>{{ date('Y-m-d H:i:s',$role->addtime) }}</td>
                                        <td>
                                            <a href="/allocate-permission?id={{ $role->id }}" type="button" class="btn btn-success btn-sm"> 功能权限</a>
                                            <a href="/edit-role?id={{ $role->id }}" type="button" class="btn btn-warning btn-sm"> 修改</a>
                                            <a href="javascript:;" data-url="/delete-role?id={{ $role->id }}" type="button" class="btn btn-danger btn-sm btn-del-role">  删除</a>
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
        $(document).ready(function(){
            $('a.btn-del-role').click(function(){
                var $this = $(this);
                layer.msg('您确定要删除该角色吗？',{
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

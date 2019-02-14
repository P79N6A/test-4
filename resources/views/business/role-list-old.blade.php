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
                    @foreach($roles as $role)
                    <div class="col-sm-4">
                        <div class="contact-box">
                            <div class="col-sm-4">
                                <div class="text-center">
                                    <div class="icon">
                                        <i class="fa fa-users" style="font-size: 70px;"></i>
                                    </div>
                                    <div class="m-t-xs font-bold">{{ $role->name }}</div>
                                </div>
                            </div>
                            <div class="col-sm-8">
                                <p>
                                    @if($role->status == 1)
                                        <span class="label label-primary">启用</span>
                                    @elseif($role->status == 0)
                                        <span class="label">无效</span>
                                    @endif
                                </p>
                                <p>{{ date('Y-m-d H:i:s',$role->addtime) }}</p>
                                <address>
                                    <strong>描述</strong>
                                    <br> {{ $role->description }}
                                </address>
                            </div>
                            <div class="clearfix"></div>
                            <div class="hr-line-dashed"></div>
                            <div class="user-button">
                                <a href="/allocate-permission?id={{ $role->id }}" type="button" class="btn btn-primary btn-sm btn-block"> 功能权限</a>
                                <a href="/allocate-data-access-permission?id={{ $role->id }}" type="button" class="btn btn-primary btn-sm btn-block"> 门店权限({{ $role->store_count }})</a>
                                <a href="/edit-role?id={{ $role->id }}" type="button" class="btn btn-primary btn-sm btn-block"> 修改</a>
                                <a href="javascript:;" data-url="/delete-role?id={{ $role->id }}" type="button" class="btn btn-primary btn-sm btn-block btn-del-role">  删除</a>
                            </div>
                        </div>
                    </div>
                    @endforeach
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

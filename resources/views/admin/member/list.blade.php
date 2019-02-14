@extends('admin.layouts.parent')
@section('page-title','用户列表')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>所有用户</h5>
                    <!-- <div class="ibox-tools">
                        <a href="{{ route('admin.add-member') }}" class="btn btn-primary btn-xs">创建用户</a>
                    </div> -->
                </div>
                <div class="ibox-content">
                    <p>你好，欢迎使用用户管理</p>
                    <div class="row">
                        
                            <form role="form" class="form-inline" action="{{ route('admin.member-list') }}" method="get">
                                            <div class="form-group">
                                            <input type="text" name="keyword" value="{{ $keyword }}" placeholder="请输入用户名或手机号"
                                           class="input-sm form-control">
                                            </div>
                                            <div class="form-group" style="display:none">
                                                <label for="exampleInputPassword2">VIP：</label>
                                                <select class="form-control" name="vip">
                                                <option value="" @if ($vip == "") selected @endif >全部</option>
                                                <option value="1" @if ($vip == 1) selected @endif >是</option>
                                                <option value="0" @if ($vip == 0) selected @endif >否</option>
                                            </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="exampleInputPassword2" >角色：</label>
                                                <select class="form-control" name="role">
                                                <option value="" @if ($role == "") selected @endif >全部</option>
                                                <option value="2" @if ($role == 2) selected @endif>医生</option>
                                                <option value="3" @if ($role == 3) selected @endif>店员</option>
                                                <option value="1" @if ($role == 1) selected @endif>用户</option>
                                            </select>
                                            </div>  
                                            <button type="submit" class="btn btn-sm btn-primary"> 搜索</button>
                                        </form>
                       
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>微信openid</th>
                                <th>昵称</th>
                                <th>头像</th>
                                <th>手机</th>
                                <!-- <th>VIP</th> -->
                                <!-- <th>VIP到期期时间</th> -->
                                <th>角色</th>
                                <th>邀请人</th>
                                <th>创建时间</th>
                                <th>更新时间</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($members))
                                @foreach($members as $member)
                                    <tr>
                                        <td>{{ $member->id }}</td>
                                        <td>{{ $member->openid }}</td>
                                        <td>{{ $member->nickname }}</td>
                                        <td><img src="{{ $member->img }}" alt="{{ $member->nickname }}" width="50" high="50"></td>
                                        <td>{{ $member->mobile }}</td>
                                        <!-- <td>
                                            @if ($member->is_vip === 1)
                                                是
                                            @else
                                                否
                                            @endif
                                        </td> -->
                                        <!-- <td>{{ $member->vip_expire ?? '-' }}</td> -->
                                        <td>
                                            @if ($member->role === 1)
                                                会员
                                            @elseif ($member->role === 2)
                                                医生
                                            @elseif ($member->role === 3)
                                                店员
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $member->inviter ?? '-' }}</td>
                                        <td>{{ $member->created_at ?? '-' }}</td>
                                        <td>{{ $member->updated_at ?? '-' }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <button data-toggle="dropdown"
                                                        class="btn btn-primary btn-sm dropdown-toggle">操作
                                                    <span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    @if ($member->role === 1)
                                                        <li>
                                                            <a href="javascript:;" data-url="{{ route('admin.set-doctor') }}"
                                                            data-type="id" data-id="{{ $member->id }}"
                                                            class="btn btn-primary btn-sm btn-set-doctor"><i class="fa fa-check-square"></i> 设为医生
                                                            </a>
                                                        </li>
                                                    @endif
                                                    @if ($member->role === 2)
                                                        <li>
                                                            <a href="javascript:;" data-url="{{ route('admin.unset-doctor') }}"
                                                            data-type="id" data-id="{{ $member->id }}"
                                                            class="btn btn-white btn-sm btn-unset-doctor"><i class="fa fa-close"></i> 解绑医生
                                                            </a>
                                                        </li>
                                                    @endif
                                                    @if ($member->role === 3)
                                                        <li>
                                                            <a href="javascript:;" data-url="{{ route('admin.business.store.unbindStaff') }}"
                                                            data-type="id" data-id="{{ $member->id }}"
                                                            class="btn btn-white btn-sm btn-unbind-staff"><i class="fa fa-close"></i> 解绑店员
                                                            </a>
                                                        </li>
                                                    @endif
                                                    <li>
                                                        <a href="{{ route('admin.edit-member',['id'=>$member->id]) }}"
                                                    class="btn btn-warning btn-sm"><i class="fa fa-pencil"></i> 修改 </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:;" data-url="{{ route('admin.delete-member') }}"
                                                    data-type="id" data-id="{{ $member->id }}"
                                                    class="btn btn-danger btn-sm btn-del-user"><i class="fa fa-trash"></i> 删除
                                                    </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="text-right">
                        @if(!empty($members) && !empty($members->links()))
                            {{ $members->appends([
                                                'keyword'=>$keyword,
                                                'role'=>$role,
                                                'vip'=>$vip
                                                ])->links() 
                            }}
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
                layer.msg('您确定要删除该用户吗？',{
                    time:0,
                    btn:['是','否'],
                    yes:function(index){
                        layer.close(index);
                        youyibao.httpSend($this,'get',1);
                    }
                });
            });

            $('.btn-set-doctor').click(function(){
                var $this = $(this);
                layer.msg('您确定要设置该用户为医生吗？',{
                    time:0,
                    btn:['是','否'],
                    yes:function(index){
                        layer.close(index);
                        youyibao.httpSend($this,'get',1);
                    }
                });
            });

            $('.btn-unset-doctor').click(function(){
                var $this = $(this);
                layer.msg('您确定要解除绑定该用户的医生角色吗？',{
                    time:0,
                    btn:['是','否'],
                    yes:function(index){
                        layer.close(index);

                        var data = 'id=' + $this.attr('data-id');
                        youyibao.httpSendWithData('get', $this.attr('data-url'), data, 1);
                    }
                });
            });

            $('.btn-unbind-staff').click(function(){
                var $this = $(this);
                layer.msg('您确定要解除绑定该用户的店员角色吗？',{
                    time:0,
                    btn:['是','否'],
                    yes:function(index){
                        layer.close(index);
                        var data = 'id=' + $this.attr('data-id');
                        youyibao.httpSendWithData('get', $this.attr('data-url'), data, 1);
                    }
                });
            });

            $('.set-commission-rate').click(function(){
                var $this = $(this);
                
                layer.prompt({title: '请输入佣金比例(%)', formType: 0, value: $(this).attr('data-rate')}, function(rate, index){
                    layer.close(index);

                    var data = 'id=' + $this.attr('data-id') + '&rate=' + rate;
                    youyibao.httpSendWithData('get', $this.attr('data-url'), data, 1);
                });
            });
        });
    </script>
@endsection

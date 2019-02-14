@extends('admin.layouts.parent')
@section('page-title','医生列表')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>所有医生</h5>
                    <!-- <div class="ibox-tools">
                        <a href="{{ route('business.add-member') }}" class="btn btn-primary btn-xs">创建用户</a>
                    </div> -->
                </div>
                <div class="ibox-content">
                    <p>你好，欢迎使用医生管理</p>
                    <div class="row">
                        <div class="col-sm-3">
                            <form action="{{ route('business.doctor-list') }}" method="get">
                                <div class="input-group">
                                    <input type="text" name="keyword" value="{{ $keyword }}" placeholder="请输入用户名或手机号"
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
                                <th>微信openid</th>
                                <th>姓名</th>
                                <th>昵称</th>
                                <th>头像</th>
                                <th>手机</th>
                                <!-- <th>提现余额</th> -->
                                <!-- <th>奖金总额</th> -->
                                <!-- <th>佣金比例(%)</th> -->
                                <!-- <th>消费总金额</th> -->
                                <th>邀请码</th>
                                <th>小程序码</th>
                                <th>邀请注册人数</th>
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
                                        <td>{{ $member->realname }}</td>
                                        <td>{{ $member->nickname }}</td>
                                        <td><img src="{{ $member->img }}" alt="{{ $member->nickname }}" width="50" high="50"></td>
                                        <td>{{ $member->mobile }}</td>
                                        <!-- <th><a href="{{ route('business.doctor-money-list',['usersId'=>$member->id]) }}">{{ $member->money }}</a></th> -->
                                        <!-- <th><a href="{{ route('business.doctor-record-list',['usersId'=>$member->id]) }}">{{ $member->record_amount }}</a></th> -->
                                        <!-- <td>{{ $member->commission_rate * 100 ?? '-' }}</td> -->
                                        <!-- <th>{{ $member->order_amount }}</th> -->
                                        <td>{{ $member->invite_code ?? '-' }}</td>
                                        <td><img src="{{ $member->invite_code_path }}" alt="{{ $member->invite_code }}" width="50" high="50"></td>
                                        <td>{{ $member->invite_num ?? '0' }}</td>
                                        <td>{{ $member->created_at ?? '-' }}</td>
                                        <td>{{ $member->updated_at ?? '-' }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <button data-toggle="dropdown"
                                                        class="btn btn-primary btn-sm dropdown-toggle">操作
                                                    <span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <!-- <li>
                                                        <a href="javascript:;" data-url="{{ route('business.set-commission-rate') }}" data-type="id" data-id="{{ $member->id }}" 
                                                        data-rate="{{ $member->commission_rate * 100 ?? '-' }}" class="btn btn-white btn-sm set-commission-rate"><i class="fa fa-cny"></i> 设置佣金比例 </a>
                                                    </li> -->
                                                    <li>
                                                        <a href="javascript:;" data-url="{{ route('business.unset-doctor') }}"
                                                        data-type="id" data-id="{{ $member->id }}"
                                                        class="btn btn-white btn-sm btn-unset-doctor"><i class="fa fa-close"></i> 解绑医生
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ route('business.edit-doctor',['id'=>$member->id]) }}"
                                                    class="btn btn-warning btn-sm"><i class="fa fa-pencil"></i> 修改 </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:;" data-url="{{ route('business.delete-doctor') }}"
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
                            {{ $members->links() }}
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

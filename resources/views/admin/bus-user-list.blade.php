@extends('admin.layouts.parent')
@section('page-title','商户管理')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>所有商户</h5>
                    <div class="ibox-tools">
                        <a href="{{ route('admin.create-bus-user') }}" class="btn btn-primary btn-xs">创建商户账号</a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <form action="{{ route('admin.bus-user-list') }}" method="get">
                            <div class="col-sm-6 m-b-xs">
                                <select class="input-sm form-control input-s-sm inline" name="status">
                                    <option value="0" @if($status == 0) selected @endif >全部</option>
                                    <option value="1" @if($status == 1) selected @endif >正常</option>
                                    <option value="2" @if($status == 2) selected @endif >异常</option>
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <input type="text" placeholder="请输入关键词" name="keyword" value="{{ $keyword }}"
                                           class="input-sm form-control">
                                    <span class="input-group-btn">
                                    <button type="submit" class="btn btn-sm btn-primary"> 搜索</button>
                                </span>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped" style="margin-bottom:120px;">
                            <thead>
                            <tr>
                                <th>名字</th>
                                <th>手机号码</th>
                                <th>品牌</th>
                                <th>用户门店数</th>
                                <th>状态</th>
                                <th>注册时间</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($users))
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->mobile }}</td>
                                        <td>{{ $user->brand_name }}</td>
                                        <td><a href="{{ route('admin.associated-stores',['id'=>$user->id]) }}" class="btn btn-xs btn-success">{{ $user->store_count }}</a></td>
                                        <td>
                                            @if($user->status == 1)
                                                <span class="label label-primary"> 正常 </span>
                                            @elseif($user->status == 0)
                                                <span class="label label-warning"> 异常 </span>
                                            @endif
                                        </td>
                                        <td>{{ date('Y-m-d H:i:s',$user->regtime) }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <button data-toggle="dropdown" class="btn btn-primary btn-sm dropdown-toggle">操作 <span class="caret"></span></button>
                                                <ul class="dropdown-menu">
                                                    @if($user->status == 0)
                                                        <li>
                                                            <a href="javascript:;" data-url="{{ route('admin.bus-user-status') }}"
                                                               data-type="id" data-id="{{ $user->id }}"
                                                               class="btn-pass">恢复状态 </a>
                                                        </li>
                                                    @endif
                                                    <li><a href="{{ route('admin.associate-store',['id'=>$user->id]) }}">关联门店</a></li>
                                                    <li><a href="{{ route('admin.reset-bus-password',['id'=>$user->id]) }}">重置密码</a></li>
                                                    <li><a href="{{ route('admin.allocate-menu-role',['id'=>$user->id]) }}">分配角色</a></li>
                                                    <li><a href="{{ route('admin.reset-bus-mobile',['id'=>$user->id]) }}">修改手机号</a></li>
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
                        @if(!empty($users) && !empty($users->links()))
                            {{ $users->appends(['keyword'=>$keyword,'status'=>$status])->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(function () {
            $('.btn-pass').click(function () {
                youyibao.httpSend($(this), 'get', 1);
            });
        });
    </script>
@endsection

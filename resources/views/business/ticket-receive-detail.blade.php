@extends('business.layouts.frame-parent')
@section('page-title','卡券领取详情')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>@yield('page-title')</h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <form action="{{ route('business.ticket-receive-detail') }}" method="get">
                            <input type="hidden" name="id" value="{{ $ticket->id }}">
                            <div class="col-sm-2 m-b-xs">
                                <select class="input-sm form-control input-s-sm inline" name="status">
                                    <option value="0" @if($status == 0) selected @endif >全部</option>
                                    <option value="1" @if($status == 1) selected @endif >未使用</option>
                                    <option value="2" @if($status == 2) selected @endif >已使用</option>
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <div class="input-group">
                                    <input type="text" name="keyword" value="{{ $keyword }}" placeholder="请输入使用门店关键词"
                                           class="input-sm form-control"> <span class="input-group-btn">
                                        <button type="submit" class="btn btn-sm btn-primary"> 搜索</button> </span>
                                </div>
                            </div>
                        </form>
                    </div>
                    <br>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>用户名</th>
                                <th>领取时间</th>
                                <th>使用状态</th>
                                <th>使用时间</th>
                                <th>使用门店</th>
                                <th>套餐名称</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($users))
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->username }}</td>
                                        <td>{{ date('Y-m-d H:i:s',$user->get_time) }}</td>
                                        <td>
                                            @if($user->use_status == 1) <span class="label label-primary">已使用</span>
                                            @else <span class="label label-default">未使用</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(!empty($user->use_time)){{ date('Y-m-d H:i:s',$user->use_time) }}@endif
                                        </td>
                                        <td>{{ $user->store_name }}</td>
                                        <td>{{ $user->package_name }}</td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="text-right">
                        @if(!empty($users))
                            {{ $users->appends([
                                'id'=>$ticket->id,
                                'status'=>$status,
                                'keyword'=>$keyword
                            ])->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

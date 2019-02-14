@extends('admin.layouts.parent')
@section('page-title','消息中心')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>已推送消息列表</h5>
                    <div class="ibox-tools">
                        <a href="{{ route('business.push-message') }}" class="btn btn-primary btn-xs">推送新消息</a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>标题</th>
                                <th width="400">内容</th>
                                <th>推送时间</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($msgs))
                                @foreach($msgs as $msg)
                                    <tr>
                                        <td>{{ $msg->id }}</td>
                                        <td>{{ $msg->title }}</td>
                                        <td><a class="btn btn-xs btn-primary" href="{{ route('business.message-detail',['id'=>$msg->id]) }}">消息详情</a></td>
                                        <td>{{ date('Y-m-d H:i:s',$msg->addtime) }}</td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="text-right">
                        @if(!empty($msgs) && !empty($msgs->links()))
                            {{ $msgs->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>

    </script>
@endsection

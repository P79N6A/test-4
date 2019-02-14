@extends('business.layouts.frame-parent')
@section('page-title','公告列表')
@section('main')
    <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>消息</h5>
            </div>
            <div class="ibox-content">
                <div>
                    @if($msgs)
                        @foreach($msgs as $msg)
                        <div class="feed-element">
                            <div>
                                {{--<small class="pull-right text-navy">1月前</small>--}}
                                <div>
                                    {{ $msg->title }}
                                    <a href="{{ route('business.read-msg',['id'=>$msg->id]) }}" class="btn btn-default btn-xs">详细</a>
                                </div>
                                <small class="text-muted">{{ date('Y-m-d H:i:s',$msg->receive_time)  }}</small>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>
                <div class="text-right">
                    @if(!empty($msgs) && !empty($msgs->links()))
                        {{ $msgs->links() }}
                    @endif
                </div>
            </div>
        </div>
@endsection

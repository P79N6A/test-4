@extends('business.layouts.frame-parent')
@section('page-title','摇一摇活动')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>摇一摇活动列表</h5>
                    <div class="ibox-tools">
                        <a href="{{ route('business.add-shake-activity') }}" class="btn btn-primary btn-xs">新建摇一摇活动</a>
                        <a href="{{ route('business.history-shake-activity') }}"
                           class="btn btn-xs btn-primary">历史已过期活动</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        @if(!empty($activities))
            @foreach($activities as $activity)
                <div class="col-sm-4">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>{{ $activity->title }}</h5>
                        </div>
                        <div class="ibox-content">
                            <p>
                                共有 {{ $activity->win_count }} 个用户摇到奖品
                            </p>
                            <p>
                                @if($activity->start_date <= time() && time() < $activity->end_date)
                                    <small class="label label-primary"><i class="fa fa-clock-o"></i> 进行中</small>
                                @elseif($activity->end_date < time())
                                    <small class="label label-default"><i class="fa fa-clock-o"></i> 已结束</small>
                                @elseif($activity->start_date > time())
                                    <small class="label label-default"><i class="fa fa-clock-o"></i> 未开始</small>
                                @endif
                            </p>
                            <p>
                                <i class="fa fa-clock-o"></i> 开始时间: {{ date('Y-m-d H:i:s',$activity->start_date) }}
                            </p>
                            <p>
                                <i class="fa fa-clock-o"></i> 结束时间: {{ date('Y-m-d H:i:s',$activity->end_date) }}
                            </p>
                            <h4>举办门店</h4>
                            <p><span class="label label-primary">{{ $activity->store_name }}</span></p>
                            <h4>活动介绍</h4>
                            <p>
                                {{ $activity->description }}
                            </p>
                            <h4>中奖限制次数</h4>
                            <p>{{ $activity->win_limit }}</p>
                            <div class="user-button">
                                @if( ($activity->start_date <= time() && time() <= $activity->end_date) || $activity->start_date > time())
                                    <a href="{{ route('business.publish-shake-gift',['id'=>$activity->id]) }}"
                                       type="button"
                                       class="btn btn-primary btn-sm btn-block">
                                        <i class="fa fa-gift"></i> 投放奖品
                                    </a>
                                    <a href="{{ route('business.edit-shake-activity',['id'=>$activity->id]) }}"
                                       type="button" class="btn btn-sm btn-success btn-block">
                                        <i class="fa fa-edit"></i> 修改
                                    </a>
                                    <a data-url="{{ route('business.del-shake-activity') }}" data-type="id"
                                       data-id="{{ $activity->id }}"
                                       type="button" class="btn btn-sm btn-warning btn-block btn-del">
                                        <i class="fa fa-trash"></i> 删除
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
    <div class="text-right">
        @if(!empty($activities))
            {{ $activities->links() }}
        @endif
    </div>
    <script>
        $(function () {
            $('.btn-del').click(function () {
                var $this = $(this);
                layer.msg('您确定要删除该活动吗？', {
                    time: 0,
                    btn: ['是', '否'],
                    yes: function (index) {
                        layer.close(index);
                        youyibao.httpSend($this, 'get', 1);
                    }
                });
            });
        });
    </script>
@endsection


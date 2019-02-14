@extends('business.layouts.frame-parent')
@section('page-title','秒杀活动')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>@yield('page-title')</h5>
                    <div class="ibox-tools">
                        <a href="{{ route('business.add-sekill-activity') }}" class="btn btn-primary btn-xs">创建秒杀活动</a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>标题</th>
                                <th>描述</th>
                                <th>开始时间</th>
                                <th>结束时间</th>
                                <th>举行门店</th>
                                <th>状态</th>
                                <th>创建时间</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($activities))
                                @foreach($activities as $activity)
                                    <tr>
                                        <td>{{ $activity->title }}</td>
                                        <td>{{ $activity->description }}</td>
                                        <td>{{ $activity->start_date }}</td>
                                        <td>{{ $activity->end_date }}</td>
                                        <td>{{ $activity->store_name }}</td>
                                        <td>
                                            @if(strtotime($activity->start_date) > time())
                                                <span class="label label-default">未开始</span>
                                            @elseif(strtotime($activity->start_date) <= time() && time() <= strtotime($activity->end_date))
                                                @if($activity->status == 1)
                                                    <span class="label label-primary">正在进行</span>
                                                @else
                                                    <span class="label label-default">暂停</span>
                                                @endif
                                            @elseif(strtotime($activity->end_date) < time())
                                                <span class="label label-default">已结束</span>
                                            @endif
                                        </td>
                                        <td>{{ $activity->create_date }}</td>
                                        <td>
                                            @if($activity->end_date >= date('Y-m-d H:i:s'))
                                                <a href="{{ route('business.put-sekill-package',['id'=>$activity->id]) }}" class="btn btn-sm btn-white"><i class="fa fa-gift"> 投放套餐</i></a>
                                            @endif
                                            <a href="{{ route('business.edit-sekill-activity',['id'=>$activity->id]) }}"
                                               class="btn btn-white btn-sm">
                                                <i class="fa fa-pencil"></i> 修改
                                            </a>
                                            <a data-url="{{ route('business.del-sekill-activity') }}"
                                               data-type="id" data-id="{{ $activity->id }}"
                                               class="btn btn-white btn-sm btn-del"><i class="fa fa-trash"></i> 删除 </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="text-right">
                        @if( !empty($activities) )
                            {{ $activities->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.btn-del').click(function () {
                var $this = $(this);
                layer.msg('您确定要删除秒杀活动吗？', {
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

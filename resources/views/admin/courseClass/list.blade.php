@extends('admin.layouts.parent')
@section('page-title','课时列表')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>课时列表</h5>
                    <div class="ibox-tools">
                        <a href="{{ route('admin.course.class.add',['course_id'=>$course_id]) }}" class="btn btn-primary btn-xs">添加课时</a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>所属课程</th>
                                <th>课时名称</th>
                                <th>类型</th>
                                <th>最大开启游戏次数</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($list))
                                @foreach($list as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>
                                            {{ $item->course->name }}
                                        </td>
                                        <td>{{ $item->name }}</td>
                                        <td>
                                            @if($item->is_hot == 1)
                                            机台
                                            @else
                                            游乐
                                            @endif
                                        </td>
                                        <td>
                                            {{ $item->times }}
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.course.class.modify',['id'=>$item->id]) }}"
                                               class="btn btn-warning btn-sm"><i class="fa fa-pencil"></i> 修改 </a>
                                            <a href="javascript:;" data-url="{{ route('admin.course.class.delete') }}"
                                               data-type="id" data-id="{{ $item->id }}"
                                               class="btn btn-danger btn-sm btn-del-user"><i class="fa fa-trash"></i> 删除
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="text-right">
                        @if(!empty($list) && !empty($list->links()))
                            {{ $list->links() }}
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
                layer.msg('您确定要删除该课时吗？',{
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

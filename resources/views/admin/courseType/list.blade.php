@extends('admin.layouts.parent')
@section('page-title','课程类型列表')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>课程类型列表</h5>
                    <div class="ibox-tools">
                        <a href="{{ route('admin.course.type.add') }}" class="btn btn-primary btn-xs">添加类型</a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>类型名称</th>
                                <th>图标</th>
                                <th>停用</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($list))
                                @foreach($list as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>
                                            @if(isset($item->img))
                                            <img src="{{$item->img->path}}" width="80" />
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->disabled == 1)<span class="label label-danger">是</span>
                                            @else <span class="label label-primary">否</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.course.type.modify',['id'=>$item->id]) }}"
                                               class="btn btn-warning btn-sm"><i class="fa fa-pencil"></i> 修改 </a>
                                            <a href="javascript:;" data-url="{{ route('admin.course.type.delete') }}"
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
                layer.msg('您确定要删除该类型吗？',{
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

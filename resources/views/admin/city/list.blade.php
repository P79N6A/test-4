@extends('admin.layouts.parent')
@section('page-title','城市列表')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>城市列表</h5>
                    <div class="ibox-tools">
                        <a href="{{ route('admin.city.add') }}" class="btn btn-primary btn-xs">添加城市</a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>城市名称</th>
                                <th>首字母</th>
                                <th>热门城市</th>
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
                                            {{ $item->first_letter }}
                                        </td>
                                        <td>
                                            @if($item->is_hot == 1)<span class="label label-primary">是</span>
                                            @else <span class="label label-default">否</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.city.modify',['id'=>$item->id]) }}"
                                               class="btn btn-warning btn-sm"><i class="fa fa-pencil"></i> 修改 </a>
                                            <!-- <a href="javascript:;" data-url="{{ route('admin.city.modify') }}"
                                               data-type="id" data-id="{{ $item->id }}"
                                               class="btn btn-white btn-sm btn-del-user"><i class="fa fa-trash"></i> 删除
                                            </a> -->
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
                layer.msg('您确定要删除该管理员吗？',{
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

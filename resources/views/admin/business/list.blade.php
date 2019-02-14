@extends('admin.layouts.parent')
@section('page-title','商户列表')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>商户列表</h5>
                    <div class="ibox-tools">

                    </div>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>商户名</th>
                                <th>商户昵称</th>
                                <th>负责人电话</th>
                                <th>邮箱</th>
                                <!-- <th>所属城市</th> -->
                                <th>停用</th>
                                <th>创建时间</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($list))
                                @foreach($list as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{$item->nickname}}</td>
                                        <td>{{$item->mobile}}</td>
                                        <td>{{$item->email}}</td>
                                        <!-- <td>{{$item->city->name or '未知'}}</td> -->
                                        <td>
                                            @if($item->disabled == 1)<span class="label label-danger">是</span>
                                            @else <span class="label label-primary">否</span>
                                            @endif
                                        </td>
                                        <td>{{$item->created_at}}</td>
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

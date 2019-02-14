@extends('business.layouts.frame-parent')
@section('page-title','机台列表')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>所有机台</h5>
                    <div class="ibox-tools">
                        <a href="{{ route('business.add-machine') }}" class="btn btn-primary btn-xs">添加机台</a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>名称</th>
                                    <th>产品类型</th>
                                    <th>设备类型</th>
                                    <th>是否可用</th>
                                    <th>在线状态</th>
                                    <th>创建时间</th>
                                    <th>创建用户</th>
                                    <th>备注</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                            @if(!empty($machines))
                                @foreach($machines as $machine)
                                    <tr>
                                        <td> {{ $machine->name }}</td>
                                        <td>{{ $machine->product_name }}</td>
                                        <td> {{ $machine->dev_name }}</td>
                                        <td> @if($machine->usable == 1) 是 @else 否 @endif</td>
                                        <td> @if($machine->online_status == 'online') 是 @else 否 @endif</td>
                                        <td> {{ $machine->create_date }}</td>
                                        <td> {{ $machine->create_user }}</td>
                                        <td> {{ $machine->remarks }}</td>
                                        <td>
                                            <a href="{{ route('business.edit-machine',['id'=>$machine->id]) }}" class="btn btn-white btn-sm">
                                                <i class="fa fa-pencil"></i> 修改
                                            </a>
                                            <a href="#" data-url="{{ route('business.del-machine') }}" data-type="id" data-id="{{ $machine->id }}" class="btn btn-white btn-sm btn-del"><i class="fa fa-trash"></i> 删除 </a>
                                            <a class="btn btn-white btn-sm" href="{{ route('business.show-qrcode',['type'=>14,'serialNo'=>$machine->serial_no,'tag'=>0]) }}">查看二维码</a>
                                            <a class="btn btn-white btn-sm" href="{{ route('business.download-qrcode',['type'=>14,'serialNo'=>$machine->serial_no,'tag'=>0]) }}">下载二维码</a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="text-right">
                        @if(!empty($machines) && !empty($machines->links()) )
                            {{ $machines->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function(){
            $('.btn-del').click(function(){
                var $this = $(this);
                layer.msg('您确定要删除该机台吗？',{
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

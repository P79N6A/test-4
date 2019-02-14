@extends('admin.layouts.parent')
@section('page-title','医生提现管理')
@section('main')
    @extends('admin.layouts.doctor-money-tab')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>医生提现列表</h5>
                </div>
                <div class="ibox-content">
                    <p>你好，欢迎使用医生提现管理</p>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>流水号</th>
                                <th>医生昵称</th>
                                <th>医生手机</th>
                                <th>提现金额</th>
                                <th>申请时间</th>
                                <!-- <th>更新时间</th> -->
                                @if($status == 0)
                                    <th>操作</th>
                                @endif
                                @if($status == 1 || $status == -1)
                                    <th>审核备注</th>
                                    <th>审核人员</th>
                                    <th>审核时间</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($moneys))
                                @foreach($moneys as $money)
                                    <tr>
                                        <td>{{ $money->id }}</td>
                                        <td>{{ $money->record_id }}</td>
                                        <td>{{ $money->nickname }}</td>
                                        <td>{{ $money->mobile }}</td>
                                        <td>{{ $money->money }}</td>
                                        <td>{{ $money->created_at ?? '-' }}</td>
                                        <!-- <td>{{ $money->updated_at ?? '-' }}</td> -->
                                        @if($status == 0)
                                            <td>
                                                <div class="btn-group">
                                                    <button data-toggle="dropdown"
                                                            class="btn btn-primary btn-sm dropdown-toggle">操作
                                                        <span class="caret"></span>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a href="javascript:;" data-url="{{ route('admin.doctor-money-operate') }}" data-type="id" data-id="{{ $money->id }}" data-usersId="{{$usersId}}"
                                                        class="btn btn-success btn-sm approve"><i class="fa fa-check"></i> 审核通过</a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:;" data-url="{{ route('admin.doctor-money-operate') }}"
                                                            data-type="id" data-id="{{ $money->id }}"
                                                        class="btn btn-danger btn-sm reject"><i class="fa fa-close"></i> 拒绝
                                                        </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        @endif
                                        @if($status == 1 || $status == -1)
                                            <td>{{ $money->desc ?? '-' }}</td>
                                            <td>{{ $money->operator ?? '-' }}</td>
                                            <td>{{ $money->operated_at ?? '-' }}</td>
                                        @endif
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="text-right">
                        @if(!empty($moneys) && !empty($moneys->links()))
                            {{ $moneys->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(function(){
            $('.approve').click(function(){

                var $this = $(this);
                layer.prompt({title: '请输入审核备注', formType: 0, value: $(this).attr('data-rate')}, function(desc, index){
                    layer.close(index);

                    var data = 'id=' + $this.attr('data-id') + '&desc=' + desc + '&status=1&usersId='+$this.attr('data-usersId');
                    youyibao.httpSendWithData('get', $this.attr('data-url'), data, 1);
                });
            });

            $('.reject').click(function(){
                var $this = $(this);
                
                layer.prompt({title: '请输入审核备注', formType: 0, value: $(this).attr('data-rate')}, function(desc, index){
                    layer.close(index);

                    var data = 'id=' + $this.attr('data-id') + '&desc=' + desc + '&status=-1';
                    youyibao.httpSendWithData('get', $this.attr('data-url'), data, 1);
                });
            });
        });
    </script>
@endsection

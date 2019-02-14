@extends('admin.layouts.parent')
@section('page-title','奖金详细列表')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>奖金详细列表</h5>
                    <!-- <div class="ibox-tools">
                        <a href="{{ route('admin.add-member') }}" class="btn btn-primary btn-xs">创建用户</a>
                    </div> -->
                </div>
                <div class="ibox-content">
                  
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>流水号</th>
                                <th>昵称</th>
                                <th>头像</th>
                                <th>手机</th>
                                <th>奖金金额</th>
                                <th>订单金额</th>
                                <th>佣金比例(%)</th>
                                <th>创建时间</th>
                                <th>更新时间</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($list))
                                @foreach($list as $member)
                                    <tr>
                                        <td>{{ $member->id }}</td>
                                        <td>{{ $member->openid }}</td>
                                        <td>{{ $member->nickname }}</td>
                                        <td><img src="{{ $member->img }}" alt="{{ $member->nickname }}" width="50" high="50"></td>
                                        <td>{{ $member->mobile }}</td>
                                        <td>{{ $member->money }}</td>
                                        <td>{{ $member->order_amount }}</td>
                                        <td>{{ $member->commission_rate }}</td>
                                        <td>{{ $member->created_at ?? '-' }}</td>
                                        <td>{{ $member->updated_at ?? '-' }}</td>
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
                layer.msg('您确定要删除该用户吗？',{
                    time:0,
                    btn:['是','否'],
                    yes:function(index){
                        layer.close(index);
                        youyibao.httpSend($this,'get',1);
                    }
                });
            });

            $('.btn-set-doctor').click(function(){
                var $this = $(this);
                layer.msg('您确定要设置该用户为医生吗？',{
                    time:0,
                    btn:['是','否'],
                    yes:function(index){
                        layer.close(index);
                        youyibao.httpSend($this,'get',1);
                    }
                });
            });

            $('.set-commission-rate').click(function(){
                var $this = $(this);
                
                layer.prompt({title: '请输入佣金比例(%)', formType: 0, value: $(this).attr('data-rate')}, function(rate, index){
                    layer.close(index);

                    var data = 'id=' + $this.attr('data-id') + '&rate=' + rate;
                    youyibao.httpSendWithData('get', $this.attr('data-url'), data, 1);
                });
            });
        });
    </script>
@endsection

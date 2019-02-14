@extends('business.layouts.frame-parent')
@section('page-title','门店列表')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>所有门店</h5>
                    <div class="ibox-tools">
                        <a href="/add-store" class="btn btn-primary btn-xs">创建门店</a>
                    </div>
                </div>
                <div class="ibox-content">
                    <form action="/storelist" method="get">
                        <div class="row">
                            <div class="col-sm-6 m-b-xs">
                                <select name="status" class="input-sm form-control input-s-sm inline">
                                    <option value="0" @if($status == 0) selected @endif>全部</option>
                                    <option value="1" @if($status == 1) selected @endif>营业</option>
                                    <option value="2" @if($status == 2) selected @endif>待审核</option>
                                    <option value="3" @if($status == 3) selected @endif>关停</option>
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <input type="text" name="keyword" value="{{ $keyword }}" placeholder="请输入关键词"
                                           class="input-sm form-control">
                                    <span class="input-group-btn">
                                    <button type="submit" class="btn btn-sm btn-primary"> 搜索</button>
                                </span>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-striped" style="margin-bottom:200px;">
                            <thead>
                            <tr>
                                <th>所属品牌/门店名称/门店ID</th>
                                <th>地址/联系方式</th>
                                <th>门店状态</th>
                                <th>创建时间</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($stores as $store)
                                <tr>
                                    <td>
                                        {{ $store->brand_name }} ({{ $store->name }})
                                        <br/> {{ $store->id }}
                                    </td>
                                    <td>
                                        {{ $store->region.' '.$store->address }}
                                        <br/> {{ $store->mobile }}
                                    </td>
                                    <td>
                                        @if($store->status == 1)
                                            <span class="label label-primary"> 营业 </span>
                                        @elseif($store->status == 2)
                                            <span class="label label-warning"> 待审核 </span>
                                        @elseif($store->status == 3)
                                            <span class="label label-default"> 关停 </span>
                                        @endif
                                    </td>
                                    <td>2016-12-06 03:00:00</td>
                                    <td>
                                        <div class="btn-group">
                                            <button data-toggle="dropdown"
                                                    class="btn btn-primary btn-sm dropdown-toggle">操作
                                                <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a href="/store-detail?id={{ $store->id }}">查看 </a></li>
                                                <li><a href="/edit-store?id={{ $store->id }}">修改</a></li>
                                                @if($store->status ==1 )
                                                    <li>
                                                        <a href="javascript:;" data-url="/operstore" data-type="id"
                                                           data-id="{{ $store->id }}&s=3" class="btn-oper">关停</a>
                                                    </li>
                                                @elseif($store->status == 3)
                                                    <li>
                                                        <a href="javascript:;" data-url="/operstore" data-type="id"
                                                           data-id={{ $store->id }}&s=1" class="btn-oper">重开业</a> |
                                                    </li>
                                                @endif
                                                <li>
                                                    <a href="javascript:;" data-url="/delstore?id={{ $store->id }}"
                                                       class="btn-del-store">删除</a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('business.set-store-manager',['id'=>$store->id]) }}">分配管理员</a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('business.edit-store-address',['id'=>$store->id]) }}">修改地址信息</a>
                                                </li>
                                                @if($store->score_output_switch == 1)
                                                    <li>
                                                        <a href="{{ route('business.set-score-output-rate',['id'=>$store->id]) }}">修改积分转出率</a>
                                                    </li>
                                                @endif
                                                @if($store->ticket_output_switch == 1)
                                                    <li>
                                                        <a href="{{ route('business.set-ticket-output-rate',['id'=>$store->id]) }}">修改奖票转积分比率</a>
                                                    </li>
                                                @endif
                                                @if($store->member_flag == 1 && !empty($store->member_card_system_id))
                                                    <li>
                                                        <a class="check-server-status"
                                                           data-url="{{ route('business.check-store-server-status') }}"
                                                           data-type="id" data-id="{{ $store->id }}">
                                                            检测会员卡系统服务器状态</a>
                                                    </li>
                                                    <li>
                                                        <a class="update-member-plan"
                                                           data-url="{{ route('business.update-member-plan') }}"
                                                           data-type="id" data-id="{{ $store->id }}">
                                                            更新门店会员卡套餐</a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-right">
                        @if(!empty($stores->links())) {{ $stores->appends(['status'=>$status,'keyword'=>$keyword])->links() }} @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $('.btn-del-store').click(function () {
            var $this = $(this);
            layer.msg('您确定要删除该门店吗？', {
                time: 0,
                btn: ['是', '否'],
                yes: function (index) {
                    layer.close(index);
                    youyibao.httpSend($this, 'get', 1);
                }
            });
        });

        $('.check-server-status').click(function() {
            layer.msg('检测中',{
                icon:16,
                shade:0.01
            });
            youyibao.httpSend($(this),'get');
        });

        $('.update-member-plan').click(function () {
            layer.msg('更新中', {
                icon: 16
                , shade: 0.01
            });
            youyibao.httpSend($(this));
        });

        $('.btn-oper').click(function () {
            youyibao.httpSend($(this), 'get', 1);
        });
    </script>
@endsection

@extends('business.layouts.frame-parent')
@section('page-title','投放奖品')
@section('main')
    <link href="/business/css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css" rel="stylesheet">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>活动详细</h5>
                </div>
                <div class="ibox-content">
                    <h2>{{ $activity->title }}</h2>
                    <p>
                        共有 {{ $activity->prized_count }} 个用户已摇到奖品
                    </p>
                    <p>
                        @if($activity->start_date <= time() && time() <= $activity->end_date)
                            <small class="label label-primary"><i class="fa fa-clock-o"></i> 进行中</small>
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
                    <h4>介绍</h4>
                    <p>{{ $activity->description }}</p>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>已投放奖品</h5>
                </div>
                <div class="ibox-content">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>类型</th>
                            <th>名称</th>
                            <th>剩余份数</th>
                            <th>概率</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(!empty($addedPrizes))
                            @foreach($addedPrizes as $k=>$item)
                                <tr>
                                    <td>@if($item->type == 1)卡券@elseif($item->type == 2)商品@endif</td>
                                    <td>
                                        @if($item->type == 1){{ $item->ticket_name }}
                                        @elseif($item->type == 2){{ $item->package_name }}
                                        @endif
                                    </td>
                                    <td>{{ $item->stock }}</td>
                                    <td>{{ $item->probability * 100 }}%</td>
                                    <td>
                                        <a class="btn btn-sm btn-success change-stock"
                                           data-id="{{ $item->id }}"
                                           data-title="@if($item->type == 1){{ $item->ticket_name }}@elseif($item->type == 2){{ $item->package_name }}@endif"
                                           data-content="{{ $item->stock }}"
                                           data-expand="{{ $activity->id }}"
                                           data-toggle="modal" data-target="#myModal">修改数量</a>
                                        <a class="btn btn-sm btn-success change-rate"
                                           data-id="{{ $item->id }}"
                                           data-title="@if($item->type == 1){{ $item->ticket_name }}@elseif($item->type == 2){{ $item->package_name }}@endif"
                                           data-content="{{ $item->probability }}"
                                           data-expand="{{ $activity->id }}"
                                           data-toggle="modal" data-target="#rateModal">修改中奖概率</a>
                                        <a class="btn btn-sm btn-warning del-prize"
                                           data-url="{{ route('business.del-shake-gift') }}"
                                           data-type="activity_id"
                                           data-id="{{ $activity->id }}&prize_id={{ $item->id }}">删除</a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>投放奖品</h5>
            </div>
            <form action="{{ route('business.publish-shake-gift') }}" class="form-publish-gift">
                <input type="hidden" name="activity_id" value="{{ $activity->id }}">
                <div class="ibox-content">
                    <div class="form-group">
                        <label>投放数量</label>
                        <input type="text" name="stock" class="form-control" placeholder="将从总库存中扣除">
                    </div>
                    <div class="form-group">
                        <label>中奖概率</label>
                        <input type="text" name="probability" class="form-control" placeholder="小数点后一位小数，如：0.2">
                    </div>
                    <div class="tabs-container">
                        <ul class="nav nav-tabs">
                            <li class="active"><a class="switch-type" data-id="1" data-toggle="tab" href="#tab-1"
                                                  aria-expanded="true">卡券</a></li>
                            <li class=""><a class="switch-type" data-id="2" data-toggle="tab" href="#tab-2"
                                            aria-expanded="false">商品</a></li>
                            <input type="hidden" name="type" value="1">
                        </ul>
                        <div class="tab-content">
                            <div id="tab-1" class="tab-pane active">
                                <div class="panel-body">
                                    <table class="table table-striped">
                                        <thead>
                                        <tr>
                                            <th>卡券名称</th>
                                            <th>当前库存</th>
                                            <th>卡券类型</th>
                                            <th>选中</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($tickets as $ticket)
                                            <tr>
                                                <td class="prize_name">{{ $ticket->name }}</td>
                                                <td>{{ $ticket->stock }}</td>
                                                <td>
                                                    @if($ticket->type == 1)
                                                        现金券
                                                    @elseif($ticket->type == 2)
                                                        优惠券
                                                    @elseif($ticket->type == 3)
                                                        体验券
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="radio radio-info radio-inline">
                                                        <input type="radio" id="ticket{{ $ticket->id }}"
                                                               value="{{ $ticket->id }}" name="ticket_id">
                                                        <label for="ticket{{ $ticket->id }}">选中</label>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div id="tab-2" class="tab-pane">
                                <div class="panel-body">
                                    <table class="table table-striped">
                                        <thead>
                                        <tr>
                                            <th>商品名称</th>
                                            <th>当前库存</th>
                                            <th>价格</th>
                                            <th>选中</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($packages as $package)
                                            <tr>
                                                <td>{{ $package->name }}</td>
                                                <td>{{ $package->stock }}</td>
                                                <td>￥{{ $package->price }}</td>
                                                <td>
                                                    <div class="radio radio-info radio-inline">
                                                        <input type="radio" id="package{{ $package->id }}"
                                                               value="{{ $package->id }}" name="package_id">
                                                        <label for="package{{ $package->id }}">选中</label>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <button class="btn btn-sm btn-primary btn-publish-gift" type="button">确定投入</button>
                </div>
            </form>
        </div>
    </div>
    </div>

    {{-- 模态框--}}
    <div class="modal inmodal" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('business.change-shake-gift-stock') }}" id="stock-form" method="post">
                {{ csrf_field() }}
                <div class="modal-content animated bounceInDown">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span
                                    aria-hidden="true">&times;</span><span class="sr-only">关闭</span>
                        </button>
                        <h5 class="modal-title">修改奖品发放量</h5>
                    </div>
                    <input type="hidden" id="activity_id" name="activity_id" value="{{ $activity->id }}">
                    <input type="hidden" id="prize_id" name="prize_id" value="">
                    <div class="modal-body">
                        <div class="input-group m-b">
                            <span class="input-group-addon btn btn-sm btn-primary" id="stock-label">发放量</span>
                            <input type="text" name="stock" id="stock" placeholder="输入库存" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
                        <button type="submit" class="btn btn-primary">保存</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- 模态框--}}
    <div class="modal inmodal" id="rateModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('business.change-shake-gift-rate') }}" id="rate-form" method="post">
                {{ csrf_field() }}
                <div class="modal-content animated bounceInDown">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span
                                    aria-hidden="true">&times;</span><span class="sr-only">关闭</span>
                        </button>
                        <h5 class="modal-title">修改奖品中奖概率</h5>
                    </div>
                    <input type="hidden" id="activity_id" name="activity_id" value="{{ $activity->id }}">
                    <input type="hidden" id="prize_id" name="prize_id" value="">
                    <div class="modal-body">
                        <div class="input-group m-b">
                            <div class="input-group m-b">
                                <span class="input-group-addon" id="stock-label">中奖概率</span>
                                <input type="text" name="rate" id="rate" placeholder="输入中奖概率" class="form-control">
                                <span class="input-group-addon btn btn-sm">%</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
                        <button type="submit" class="btn btn-primary">保存</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(function () {
            $('a.switch-type').click(function () {
                $('input[name=type]').val(($(this).attr('data-id')));
            });

            $('.btn-publish-gift').click(function () {
                youyibao.httpSend($('form.form-publish-gift'), 'post', 1);
            });

            $('.change-stock').click(function () {
                $('#stock-label').text($(this).data('title'));
                $('input[name=prize_id]').val($(this).data('id'));
                $('#stock').val($(this).data('content'));
            });

            $('.change-rate').click(function () {
                $('#stock-label').text($(this).data('title'));
                $('input[name=prize_id]').val($(this).data('id'));
                $('#rate').val($(this).data('content') * 100);
            });

            $('#stock-form').submit(function (e) {
                e.preventDefault();
                youyibao.httpSend($(this), 'post', 1);
            });

            $('#rate-form').submit(function (e) {
                e.preventDefault();
                youyibao.httpSend($(this), 'post', 1);
            });

            $('.del-prize').click(function () {
                var $this = $(this);
                layer.msg('您确定要删除该奖品吗？', {
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

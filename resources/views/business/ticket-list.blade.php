@extends('business.layouts.frame-parent')
@section('page-title','卡券管理')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>所有卡券</h5>
                    <div class="ibox-tools">
                        <a href="{{ route('business.add-ticket') }}" class="btn btn-primary btn-xs">创建卡券</a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <form action="{{ route('business.ticket-list') }}" method="get">
                            <div class="col-sm-3 m-b-xs">
                                <select class="input-sm form-control input-s-sm inline" name="type">
                                    <option value="0" @if($type == 0) selected @endif >全部类型</option>
                                    <option value="1" @if($type == 1) selected @endif >现金券</option>
                                    <option value="2" @if($type == 2) selected @endif >优惠券</option>
                                    <option value="3" @if($type == 3) selected @endif >体验券</option>
                                </select>
                            </div>
                            <div class="col-sm-3 m-b-xs">
                                <select class="input-sm form-control input-s-sm inline" name="expire">
                                    <option value="0" @if($expire == 0) selected @endif >全部状态</option>
                                    <option value="1" @if($expire == 1) selected @endif >正在开放领取</option>
                                    <option value="2" @if($expire == 2) selected @endif >已过期</option>
                                </select>
                            </div>
                            <div class="col-sm-3 m-b-xs">
                                <select class="input-sm form-control input-s-sm inline" name="recommend">
                                    <option value="0" @if($recommend == 0) selected @endif >全部状态</option>
                                    <option value="1" @if($recommend == 1) selected @endif >已推荐到首页</option>
                                    <option value="2" @if($recommend == 2) selected @endif >未推荐到首页</option>
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <input type="text" name="keyword" value="{{ $keyword }}" placeholder="请输入关键词"
                                           class="input-sm form-control"> <span class="input-group-btn">
                                        <button type="submit" class="btn btn-sm btn-primary"> 搜索</button> </span>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped" style="margin-bottom:120px;">
                            <thead>
                            <tr>
                                <th>卡券</th>
                                <th>数量</th>
                                <th width="150">使用说明</th>
                                <th>发放时间/状态</th>
                                <th>门店显示状态</th>
                                <th>下架状态</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($tickets))
                                @foreach($tickets as $ticket)
                                    <tr>
                                        <td>
                                            @if($ticket->type == 1)
                                                <span class="badge badge-danger">现金券</span>
                                            @elseif($ticket->type == 2)
                                                <span class="badge badge-warning">优惠券</span>
                                            @elseif($ticket->type == 3)
                                                <span class="badge badge-success">体验券</span>
                                            @endif
                                            <br/>{{ $ticket->name }}
                                            <br/>ID:{{ $ticket->id }}
                                            @if($ticket->type == 1)
                                                <br/><span class="text-danger">面额: ￥{{ $ticket->denomination }}</span>
                                            @elseif($ticket->type == 2)
                                                <br/><span class="text-danger">折扣: {{ $ticket->discount * 10 }} 折</span>
                                            @endif
                                        </td>
                                        <td>
                                            库存 {{ $ticket->circulation }}<br/>
                                            已领取 {{ $ticket->got_count }}
                                        </td>
                                        <td>
                                            有效期：{{ date('Y-m-d H:i:s',$ticket->start_date) }}
                                            至 {{ date('Y-m-d H:i:s',$ticket->expire_date) }}
                                            <br/> {{ $ticket->instruction }}
                                        </td>
                                        <td>
                                            {{ date('Y-m-d H:i:s',$ticket->get_start_date) }} 开放领取<br/>
                                            @if($ticket->get_start_date <= time() && time() <= $ticket->expire_date)
                                                <span class="label label-primary">正在开放领取</span>
                                            @elseif($ticket->expire_date < time())
                                                <span class="label label-default">已过期</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($ticket->visible == 1)<span class="label label-primary">显示</span>
                                            @elseif($ticket->visible == 0)<span class="label label-default">隐藏</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($ticket->offline == 1) <span class="label label-default">已下架</span>
                                            @elseif($ticket->offline == 0) <span class="label label-primary">已上架</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($ticket->expire_date > time())
                                                <div class="btn-group">
                                                    <button data-toggle="dropdown"
                                                            class="btn btn-primary btn-sm dropdown-toggle">操作 <span
                                                                class="caret"></span></button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a href="{{ route('business.edit-ticket',['id'=>$ticket->id]) }}"
                                                               class="font-bold">修改</a></li>
                                                        @if($ticket->get_start_date <= time() && time() <= $ticket->expire_date)
                                                            <li>
                                                                <a href="{{ route('business.posting-ticket',['id'=>$ticket->id]) }}">发放</a>
                                                            </li>
                                                            <li><a class="offline-ticket"
                                                                   data-url="{{ route('business.offline-ticket') }}"
                                                                   data-type="id"
                                                                   data-id="{{ $ticket->id }}">@if($ticket->offline == 1)
                                                                        上架 @else 下架 @endif</a></li>
                                                        @endif
                                                        @if($ticket->get_start_date > time())
                                                            <li class="divider"></li>
                                                            <li><a data-url="{{ route('business.delete-ticket') }}"
                                                                   data-type="id" data-id="{{ $ticket->id }}"
                                                                   class="btn-del-ticket">删除</a></li>
                                                        @endif
                                                        <li>
                                                            <a href="{{ route('business.ticket-receive-detail',['id'=>$ticket->id]) }}">领取详情</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            @else
                                                <div class="btn-group">
                                                    <button data-toggle="dropdown"
                                                            class="btn btn-primary btn-sm dropdown-toggle">操作 <span
                                                                class="caret"></span></button>
                                                    <ul class="dropdown-menu">
                                                        <li><a data-url="{{ route('business.delete-ticket') }}"
                                                               data-type="id" data-id="{{ $ticket->id }}"
                                                               class="btn-del-ticket">删除</a></li>
                                                        <li>
                                                            <a href="{{ route('business.ticket-receive-detail',['id'=>$ticket->id]) }}">领取详情</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="text-right">
                        @if(!empty($tickets) && !empty($tickets->links()))
                            {{ $tickets->appends(['type'=>$type,'expire'=>$expire,'recommend'=>$recommend,'keyword'=>$keyword])->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(function () {
            $('.offline-ticket').click(function () {
                youyibao.httpSend($(this), 'get', 1);
            });

            $('.btn-del-ticket').click(function () {
                var $this = $(this);
                layer.msg('您确定要删除该卡券吗？', {
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

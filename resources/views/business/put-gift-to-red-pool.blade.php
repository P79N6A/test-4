<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>红包活动</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link href="/business/css/bootstrap.min14ed.css?v=3.3.6" rel="stylesheet">
    <link href="/business/css/font-awesome.min93e3.css?v=4.4.0" rel="stylesheet">
    <link href="/business/css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css" rel="stylesheet">
    <link href="/business/css/animate.min.css" rel="stylesheet">
    <link href="/business/css/style.min862f.css?v=4.1.0" rel="stylesheet">
    <link href="/business/css/plugins/iCheck/custom.css" rel="stylesheet">
</head>

<body class="gray-bg">
<div class="row content-tabs">
    <nav class="page-tabs J_menuTabs">
        <div class="page-tabs-content">
            <a href="javascript:;" class="active J_menuTab">红包活动</a>
        </div>
    </nav>
</div>
<div class="wrapper wrapper-content animated fadeInUp">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>红包活动</h5>
                </div>
                <div class="ibox-content">
                    <p>共有 {{ $gotCount }} 个用户已领取</p>
                    <p>
                        @if(1)
                            <small class="label label-primary"><i class="fa fa-clock-o"></i> 进行中</small>
                        @else
                            <small class="label label-default"><i class="fa fa-clock-o"></i> 未进行</small>
                        @endif
                    </p>
                    <h4>活动介绍</h4>
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
                            <th>领取群体</th>
                            <th>状态名称</th>
                            <th>领取门店</th>
                            <th>剩余数量</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(!empty($publishedRedPags))
                            @foreach($publishedRedPags as $item)
                                <tr>
                                    <td>
                                        @if($item->type == 1)积分
                                        @elseif($item->type == 2)游币
                                        @elseif($item->type == 3)现金券
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->range == 0)全部
                                        @elseif($item->range == 1)老用户
                                        @elseif($item->range == 2)新用户
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->type == 1){{ $item->item_id }} 积分
                                        @elseif($item->type == 2){{ $item->item_id }} 游币
                                        @else{{ $item->ticket_name }}@endif
                                    </td>
                                    <td>{{ $item->store_name }}</td>
                                    <td>{{ $item->stock }}</td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <form action="{{ route('business.put-gift-to-red-pool') }}" method="post">
            {{ csrf_field() }}
            <input type="hidden" name="id" value="{{ $activity->id }}">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>领取线上门店</h5>
                    </div>
                    <div class="ibox-content">
                        @if(!empty($stores))
                            @foreach($stores as $store)
                                <label class="checkbox-inline i-checks">
                                    <div class="icheckbox_square-green" style="position: relative;">
                                        <input type="checkbox" class="store_ids" name="store_ids[]"
                                               value="{{ $store->id }}"
                                               style="position: absolute; opacity: 0;">
                                        <ins class="iCheck-helper"
                                             style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; border: 0px; opacity: 0; background: rgb(255, 255, 255);">
                                        </ins>
                                    </div>{{ $store->name }}
                                </label>
                            @endforeach
                        @endif
                        <div class="hr-line-dashed"></div>
                        <div><a class="btn btn-sm btn-primary btn-filter">筛选卡券</a></div>
                    </div>
                </div>
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>投放奖品</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="tabs-container">
                            <div class="form-group">
                                <label>允许领取用户群</label>
                                <select name="range" class="form-control">
                                    <option value="0">全部</option>
                                    <option value="1">老用户</option>
                                    <option value="2">新用户</option>
                                </select>
                            </div>
                            <ul class="nav nav-tabs">
                                <li class="tab active"><a data-toggle="tab" href="#tab-1" aria-expanded="true">卡券</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div id="tab-1" class="tab-pane active">
                                    <div class="panel-body">
                                        <table class="table table-striped">
                                            <thead>
                                            <tr>
                                                <th>当前库存</th>
                                                <th>选中</th>
                                                <th>库存</th>
                                            </tr>
                                            </thead>
                                            <tbody class="ticket-container"></tbody>
                                        </table>
                                        <div><label class="label label-danger">注意：如果选择了一张卡券，右侧库存为必填项</label></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <button class="btn btn-sm btn-primary btn-confirm" type="button">确定</button>
            </div>
        </form>
    </div>
</div>
<script src="/business/js/jquery.min.js?v=2.1.4"></script>
<script src="/business/js/bootstrap.min.js?v=3.3.6"></script>
<script src="/business/js/content.min.js?v=1.0.0"></script>
<script src="/business/js/youyibao.js?v=1.0.0"></script>
<script src="/business/js/layer/layer/layer.js"></script>
<script src="/business/js/plugins/iCheck/icheck.min.js"></script>
<script>
    $(function () {
        var storeIds = [];
        $(".i-checks").iCheck({
            checkboxClass: "icheckbox_square-green",
            radioClass: "iradio_square-green"
        }).on('ifChecked', function (event) {
            storeIds.push(event.target.value);
        });
        ;

        $('.btn-filter').click(function () {
            var $stores = $('.store_ids:checked');
            var sids = [];
            $.each($stores, function (index, value) {
                sids.push(value.value);
            });
            $.get(
                "{{ route('business.filter-ticket') }}",
                {store_ids: sids}, function (data) {
                    dealWithData(data);
                }
            );
        });

        var stocks = [];
        var tickets = [];

        function dealWithData(data) {
            var $container = $('tbody.ticket-container').empty();
            $.each(data, function (index, value) {
                var $tr = $('<tr>');
                var $td1 = $('<td>').text(value.stock);
                var $td2 = $('<td>');
                var label = '<label class="checkbox-inline i-checks">'
                    + '<div class="icheckbox_square-green"'
                    + 'style="position: relative;">'
                    + '<input type="checkbox" class="ticket_ids" name="ticket_ids[]"'
                    + 'value="' + value.id + '"'
                    + 'style="position: absolute; opacity: 0;">'
                    + '<ins class="iCheck-helper"'
                    + 'style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; border: 0px; opacity: 0; background: rgb(255, 255, 255);">'
                    + '</ins>'
                    + '</div>' + value.name
                    + '</label>';
                $td2.append(label);
                var $td3 = $('<td>');
                var div = '<div class="form-group" style="width:60px;"><input type="text" id="stock_'+value.id+'" name="stock['+value.id+']" class="stocks form-control" maxlength="4"></div>';
                $td3.append(div);
                $tr.append($td1).append($td2).append($td3);
                $container.append($tr);

                $(".i-checks").iCheck({
                    checkboxClass: "icheckbox_square-green",
                    radioClass: "iradio_square-green"
                });

            });
        }

        $('.btn-confirm').click(function () {
            var $tickets = $('.ticket_ids:checked');
            stocks.splice(0,stocks.length);
            tickets.splice(0,tickets.length);

            var range = $('select[name=range]').val();
            $.each($tickets,function(index,value){
                var stock = $('#stock_'+value.value).val() ? $('#stock_'+value.value).val() : 0;
                tickets.push(value.value);
                stocks.push(stock);
            });
            $.ajax({
                type: 'post',
                url: $('form').attr('action'),
                data: {
                    _token: $('input[name=_token]').val(),
                    id: $('input[name=id]').val(),
                    store_ids: storeIds,
                    ticket_ids: tickets,
                    stock: stocks,
                    range: range
                },
                success: function (data) {
                    if (data.code != 200) {
                        layer.msg(data.msg, {icon: 5});
                    } else {
                        layer.msg(data.msg, {icon: 6}, function () {
                            setTimeout(function () {
                                location.href = data.url;
                            }, 2000)
                        });
                    }
                }
            });
        });

    });
</script>
</body>
</html>

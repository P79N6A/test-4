@extends('business.layouts.frame-parent')
@section('page-title','会员管理')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>@yield('page-title')</h5>
                    <div class="ibox-tools">
                        <a href="{{ route('business.member-analysis') }}" class="btn btn-primary btn-xs">会员分析</a>
                    </div>
                </div>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <form action="{{ route('business.member-management') }}" method="get">
                        <div class="col-sm-10">
                            <div class="row form-horizontal">
                                <div class="col-sm-6 m-b-xs">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">累计消费金额</label>
                                        <div class="col-sm-9">
                                            <div class="input-daterange input-group">
                                                <input type="text" class="form-control" name="price_start"
                                                       value="{{ $params['price_start'] }}">
                                                <span class="input-group-addon">至</span>
                                                <input type="text" class="form-control" name="price_end"
                                                       value="{{ $params['price_end'] }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 m-b-xs">
                                    <label class="col-sm-3 control-label">会员来源</label>
                                    <div class="form-group col-sm-9">
                                        <select class="form-control" name="from">
                                            <option value="0" @if($params['from'] == 0) selected @endif >全部</option>
                                            <option value="1" @if($params['from'] == 1) selected @endif >iOS</option>
                                            <option value="2" @if($params['from'] == 2) selected @endif >Android
                                            </option>
                                            <option value="3" @if($params['from'] == 3) selected @endif >微信</option>
                                            <option value="4" @if($params['from'] == 4) selected @endif >个人推广</option>
                                            <option value="5" @if($params['from'] == 5) selected @endif >门店二维码</option>
                                            <option value="6" @if($params['from'] == 6) selected @endif >24好玩</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="">
                                <div class="row form-horizontal">
                                    <div class="col-sm-6 m-b-xs">
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">累计消费次数</label>
                                            <div class="col-sm-9">
                                                <div class="input-daterange input-group">
                                                    <input type="number" class="form-control" min="0" name="count_start"
                                                           value="{{ $params['count_start'] }}">
                                                    <span class="input-group-addon">至</span>
                                                    <input type="number" class="form-control" min="0" name="count_end"
                                                           value="{{ $params['count_end'] }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 m-b-xs">
                                        <label class="col-sm-3 control-label">会员卡绑定</label>
                                        <div class="form-group col-sm-9">
                                            <select class="form-control" name="card_bind">
                                                <option value="0" @if($params['card_bind'] == 0) selected @endif >全部
                                                </option>
                                                <option value="1" @if($params['card_bind'] == 1) selected @endif >是
                                                </option>
                                                <option value="2" @if($params['card_bind'] == 2) selected @endif >否
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-horizontal">
                                    <div class="col-sm-6 m-b-xs">
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">账号</label>
                                            <div class="col-sm-9">
                                                <input type="text" name="account" value="{{ $params['account'] }}"
                                                       class="form-control" placeholder="输入手机号码">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="col-sm-3 control-label">消费时间</label>
                                        <div class="col-sm-9">
                                            <div class="input-daterange input-group">
                                                <input class="form-control" id="convert_start" name="convert_start"
                                                       value="{{ $params['convert_start'] }}">
                                                <span class="input-group-addon">至</span>
                                                <input class="form-control" id="convert_end" name="convert_end"
                                                       value="{{ $params['convert_end'] }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2 m-b-xs text-right">
                            <button class="btn btn-primary btn-lg" type="submit">
                                <i class="fa fa-search"></i>
                            </button>
                            <button type="button" class="btn btn-success btn-lg"
                                    id="export"
                                    data-url="{{ route('business.member-management-export') }}"
                                    title="导出数据到电子表格">
                                <i class="fa fa-download"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="table-responsive">
                    <div class="sm-col-12">
                        <span class="label label-primary">总会员数：{{ $count }} 人</span>
                    </div>
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>会员ID</th>
                            <th>会员账号</th>
                            <th>地区</th>
                            <th>积分</th>
                            <th>游币</th>
                            <th>最后消费时间</th>
                            <th>最后登录时间</th>
                            <th>累计消费金额</th>
                            <th>累计消费次数</th>
                            <th>平均消费金额</th>
                            <th>来源/是否绑定会员卡</th>
                            <th>账号状态</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(!empty($members))
                            @foreach($members as $member)
                                <tr>
                                    <td>{{ $member->id }}</td>
                                    <td>{{ $member->mobile }}</td>
                                    <td>{{ $member->region_name }}</td>
                                    <td><a href="{{route('business.get-bus-member-score-log',['id'=>$member->id])}}">{{ $member->score }}</a></td>
                                    <td>{{ $member->coin }}</td>
                                    <td>@if(!empty($member->last_consume_time)) {{ $member->last_consume_time }} @endif </td>
                                    <td>@if(!empty($member->last_login_time)) {{ $member->last_login_time }} @endif </td>
                                    <td>{{ round($member->total_consume_amount,2) }}</td>
                                    <td>{{ round($member->total_consume_count,2) }}</td>
                                    <td>{{ $member->average_consume_price }}</td>
                                    <td>来源：
                                        @if($member->from == 1)IOS
                                        @elseif($member->from == 2)安卓
                                        @elseif($member->from == 3)微信
                                        @elseif($member->from == 4)个人推广
                                        @elseif($member->from == 5)门店二维码
                                        @endif
                                        <br>
                                        绑定状态：@if($member->card_bind_status == 1) 是 @else 否 @endif
                                    </td>
                                    <td>@if($member->status == 1) 正常 @else 禁用 @endif </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
                <div class="text-right">
                    @if(!empty($members))
                        {{ $members->appends([
                            'price_start' => $params['price_start'],
                            'price_end' => $params['price_end'],
                            'from' => $params['from'],
                            'count_start' => $params['count_start'],
                            'count_end' => $params['count_end'],
                            'card_bind' => $params['card_bind'],
                            'account' => $params['account'],
                            'convert_start' => $params['convert_start'],
                            'convert_end' => $params['convert_end'],
                        ])->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>
    </div>
    <script src="/business/js/plugins/layer/laydate/laydate.js"></script>
    <script>
        $(function () {
            var convert_start = {
                elem: "#convert_start",
                format: "YYYY-MM-DD hh:mm:ss",
                min: '2010-01-01 00:00:00',
                max: laydate.now(),
                istime: true,
                istoday: false,
                choose: function (datas) {
                    convert_end.min = datas;
                }
            };
            var convert_end = {
                elem: "#convert_end",
                format: "YYYY-MM-DD hh:mm:ss",
                min: '2010-01-01 00:00:00',
                max: laydate.now(),
                istime: true,
                istoday: false,
                choose: function (datas) {
                    convert_start.max = datas
                }
            };
            laydate(convert_start);
            laydate(convert_end);

            $('#export').click(function () {
                $('form').attr('action', $(this).data('url')).submit().attr('action', location.href);
            });
        });
    </script>
@endsection


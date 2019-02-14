@extends('business.layouts.frame-clear-version')
@section('page-title')
{{$store_name}}
@endsection
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>当前操作门店：{{$store_name}}</h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="balance-box tooltip-demo">
                               <div class="clearfix">
                                   <a class="btn btn-nomarl btn-circle pull-right btn-outline" data-toggle="tooltip" data-placement="bottom" title="账户少于10元不能提现"><i class="fa fa-question"></i></a>
                                   <h4>可提现余额</h4>
                               </div>
                               <h1 class="balance-total">
                                    {{$result['available']}}
                                    @if($result['available'] > 10)
                                    <a class="btn btn-success btn-withdraw">提现</a>
                                    @endif
                                </h1>
                                <p class="text-danger" style="margin-top: 30px;"><i class="fa fa-exclamation-circle"></i> 截取为{{date('Y-m-d')}} 23:59:59前的账户余额</p>
                                <p class="text-danger"><i class="fa fa-exclamation-circle"></i> 因银行原因，如遇周末、节假日，付款时间可能会顺延至下一个工作日</p>
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <div class="balance-box tooltip-demo">
                                <div class="clearfix">
                                   <a class="btn btn-nomarl btn-circle pull-right btn-outline" data-toggle="tooltip" data-placement="bottom" title="实时交易后的账户余额"><i class="fa fa-question"></i></a>
                                   <h4>账户余额</h4>
                               </div>

                               <h1 class="balance-total">
                                    {{$result['balance']}} 
                                    <a class="btn btn-default btn-withdraw" href="{{route('business.store-balance-detail',['store_id'=>$store_id])}}">详情</a>
                                </h1>

                                <table width="95%" style="margin-top: 32px;margin-bottom: 10px;">
                                    <tr class="balance-info">
                                        <td width="8%" align="center">{{$result['balance']}}</td>
                                        <td width="2%"></td>
                                        <td width="8%" align="center">{{$result['user_pay']}}</td>
                                        <td width="2%"></td>
                                        <td width="8%" align="center">{{$result['platform_pay']}}</td>
                                        <td width="2%"></td>
                                        <td width="8%" align="center">{{$result['service_fee']}}</td>
                                        <td width="2%"></td>
                                        <td width="8%" align="center">{{$result['score_fee']}}</td>
                                        <td width="2%"></td>
                                        <td width="8%" align="center">{{$result['ticket_fee']}}</td>
                                    </tr>
                                    <tr class="balance-txt">
                                        <td align="center">账户余额</td>
                                        <td align="center">=</td>
                                        <td align="center">用户实付</td>
                                        <td align="center">+</td>
                                        <td align="center">平台补贴</td>
                                        <td align="center">-</td>
                                        <td align="center">服务费</td>
                                        <td align="center">-</td>
                                        <td align="center">积分转入费用</td>
                                        <td align="center">-</td>
                                        <td align="center">彩票转入费用</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-sm-12">
                            <form action="{{ route('log.show') }}" method="get">
                                <div class="row form-horizontal block-gray">
                                    <div class="col-sm-5">
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">时间</label>
                                            <div class="col-sm-10">
                                                <div class="input-daterange input-group">
                                                    <input type="text" class="form-control" name="start_date"
                                                           @if(!empty($start_date)) value="{{ $start_date }}"
                                                           @endif id="get_start">
                                                    <span class="input-group-addon">至</span>
                                                    <input type="text" class="form-control" name="end_date"
                                                           @if(!empty($end_date)) value="{{ $end_date }}"
                                                           @endif id="get_end">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-5">
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">提现状态：</label>
                                            <div class="col-sm-10">
                                                <div class="form-group">
                                                    <div class="btn-group">
                                                        <button class="btn @if($status < 0) btn-primary @else btn-white @endif" type="button">全部</button>
                                                        <button class="btn @if($status == 0) btn-primary @else btn-white @endif" type="button">处理中</button>
                                                        <button class="btn @if($status == 1) btn-primary @else btn-white @endif" type="button">成功</button>
                                                        <button class="btn @if($status == 2) btn-primary @else btn-white @endif" type="button">失败</button>
                                                        <input type="hidden" name="status" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <div class="row m-t-md">
                        <div class="col-sm-12">
                            <p class="table-top no-margins p-sm">
                            提款总额：<span class="text-warning">￥{{$withdraw_sum}}</span>
                            <span class="m-l">提款记录：<span class="text-warning">{{$list_count}}</span></span> 
                            </p>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>提现时间</th>
                                    <th>提现金额</th>
                                    <th>状态</th>
                                    <th>备注</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($list as $item)
                                <tr>
                                    <td>{{date('Y-m-d H:i:s',$item->create_at)}}</td>
                                    <td>{{$status_select[$item->status]}}</td>
                                    <td>{{round($item->amount,2)}}</td>
                                    <td>{{$item->remark}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="text-right">
                        @if(!empty($list->links()))
                            {{ $list->appends([
                                'start_date'=>$start_date,
                                'end_date'=>$end_date,
                                'status'=>$status,
                                'store_id'=>$store_id
                            ])->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/business/js/plugins/layer/laydate/laydate.js"></script>
    <script src="/business/js/util.date.js"></script>
    <script type="text/javascript">
        $(function () {

            var get_start = {
                elem: "#get_start",
                format: "YYYY-MM-DD hh:mm:ss",
                min: '2010-01-01',
                max: "2099-06-16 23:59:59",
                istime: true,
                istoday: false,
                choose: function (datas) {
                    get_end.min = datas;
                    get_end.start = datas;
                }
            };
            var get_end = {
                elem: "#get_end",
                format: "YYYY-MM-DD hh:mm:ss",
                min: '2010-01-01',
                max: "2099-06-16 23:59:59",
                istime: true,
                istoday: false,
                choose: function (datas) {
                    get_start.max = datas
                }
            };
            laydate(get_start);
            laydate(get_end);

        });
    </script>
@endsection


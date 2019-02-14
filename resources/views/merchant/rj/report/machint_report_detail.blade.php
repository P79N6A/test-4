@extends('admin.layouts.parent')
@section('page-title','营收详情')
@section('main')

 <link href="/admin/css/new-add.css" rel="stylesheet">
 <style>
	 .css-display{ display:none; }
	 .page-tabs-content{margin: 0 5px;  color: #fff;  background-color: #16cfe2;}
	 .row{margin-top:20px;}
	 button, input, select, textarea{    height: 32px; border:0px; width:172px;     padding: 6px 12px; }
	 .z_inputbox label{ font-weight: bold; }
	 select.input-sm{ height: 34px;}
 </style>
@extends(env('Merchant_view').'.layouts.report_menu')
    <div class="row" >
		<!--新加的-->
		<div class="col-sm-12 ">
			<div class="z_bar-gray z_border_t z_border_b clearfix">
				<form action="{{ route('business.rj_machint_report_detail',request()->input()) }}" method="post">
                    <div class="col-sm-8 m-b-xs z_pt10">
                        <div class="clearfix z_mb10">
                            <div class="z_inputbox fl z_mr30">
                                <label>支付类型:</label>
                                <select name="payment_type" class="z_w172 z_h24 input-sm form-control input-s-sm inline">
                                    <option value="0" @if($payment_type == 0) selected @endif>全部</option>
                                    <option value="1" @if($payment_type == 1) selected @endif>游币支付</option>
                                    <option value="2" @if($payment_type == 2) selected @endif>线下投币</option>
                                </select>
                            </div>
                            <div class="z_inputbox fl z_mr30">
                                <label>支付用户：</label>
                                <input type="text" name="user_name" value="{{$user_name}}" />
                            </div>
                        </div>
                    </div>
					<div class="col-sm-8 m-b-xs z_pt10">
						<div class="clearfix z_mb10">
							<div class="z_inputbox fl z_mr30">
								<label>营收日期:</label>
								<input type="text" name="starttime" value="" class=" layer-date" id="start_date" placeholder="开始时间"/>
								--
								<input type="text" name="endtime"   value="" class=" layer-date" id="end_date" placeholder="结束时间" />
							</div>
						</div>
					</div>
					<div class="col-sm-1 tc z_pt10" style=" width: 80px; ">
						<button class="btn z_bar-blue btn-xs z_color_white z_w60 z_mb5" style=" height: 33px;" >查询</button>
					</div>
                    {{ csrf_field() }}
				</form>
			</div>
		</div>
		<!--机台详情-->
    
        <div class="col-sm-12">
         <!-- 门店资料开始 -->
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>机台营收详情</h5>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
	                            <tr> 
	                            	<th>订单编号</th>
	                                <th>机台名称</th>
                                    <th>需支付币数</th>
                                    <th>机台投币数</th>
	                                <th>支付方式</th>
                                    <th>支付用户</th>
                                    <th>手机号</th>
                                    <th>支付时间</th>
	                            </tr>
                            </thead>
                            <tbody>
	                            @if(!empty($ads))
		                            @foreach($ads as $val)
		                               	<tr>
		                               	    <td>{{$val->refno}}</td>
											<td>{{$val->m_name}}</td>
                                            <td>{{$val->pay_coins}}</td>
											<td>{{$val->pay_price}}</td>
											<td><?php if($val->payment_type=='1'){ echo '游币支付';}else{echo '线下投币';}?></td>
                                            <td>{{$val->nickname?$val->nickname:$val->username}}</td>
                                            <td>{{$val->mobile?$val->mobile:$val->mobile}}</td>
                                            <td>{{date('Y-m-d H:i:s',$val->pay_date)}}</td>
		                                </tr>
		                             @endforeach 
		                         @endif
                             </tbody>
                        </table>
                    </div>
                    
                    <div class="text-right">
                    	@if(!empty($ads) && !empty($ads->links()))
                            {{ $ads->appends([
                                'starttime'=>$starttime,
                                'endtime'=>$endtime,
                                'payment_type'=>$payment_type,
                                'user_name'=>$user_name,
                                'machine_id'=>$machine_id
				               ])->links() }}
                        @endif
                    </div>
                </div>
            </div>
            <!-- 门店资料结束 -->

        </div>
    </div>
	{{ csrf_field() }}


 <script src="/admin/js/plugins/layer/laydate/laydate.js"></script>
 <script src="/admin/js/youyibao.js"></script>
 <script type="text/javascript">
     var start_date = {
         elem: "#start_date",
         format: "YYYY-MM-DD hh:mm:ss",
         max: laydate.now(),
         istime: true,
         istoday: false,
         choose: function(datas) {
             end_date.min = datas;
             end_date.start = datas
         }
     };
     var end_date = {
         elem: "#end_date",
         format: "YYYY-MM-DD hh:mm:ss",
         max: laydate.now(),
         istime: true,
         istoday: false,
         choose: function(datas) {
             start_date.max = datas
         }
     };
     laydate(start_date);
     laydate(end_date);
 </script>

 <script>
     $(function(){
         // 提交表单
         $('form.form-add').submit(function(e){
             e.preventDefault();
             youyibao.httpSend($(this),'post',1);
         });

     });
 </script>

@endsection

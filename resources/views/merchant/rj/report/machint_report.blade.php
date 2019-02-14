@extends('admin.layouts.parent')
@section('page-title','机台营收 ')
@section('main')

 <link href="/admin/css/new-add.css" rel="stylesheet">
 <style>
	 .css-display{ display:none; }
	 .page-tabs-content{margin: 0 5px;  color: #fff;  background-color: #16cfe2;}
	 .row{margin-top:20px;}
	 button, input, select, textarea{    height: 32px; border:0px; width:172px; }
	 .z_inputbox label{ font-weight: bold; }
	 select.input-sm{ height: 34px;}
 </style>
@extends(env('Merchant_view').'.layouts.report_menu')
    <div class="row" >
          	<!--新加的-->
        	<div class="col-sm-12 ">
        		<div class="z_bar-gray z_border_t z_border_b clearfix">
        		 	<form action="{{ route('business.rj_machint_report') }}" method="get">
		        		<div class="col-sm-8 m-b-xs z_pt10">
		        			<div class="clearfix z_mb10">
			        			<div class="z_inputbox fl z_mr30">
			        				<label>机台编号：</label>
									<input type="text" name="machine_id" value="{{$machine_id}}"/>
			        			</div>
								<div class="z_inputbox fl z_mr30">
									<label>机台名称：</label>
									<input type="text" name="m_name" value="{{$m_name}}"/>
								</div>
								<div class="z_inputbox fl z_mr30">
									<label>硬件编号：</label>
									<input type="text" name="serial_no" value="{{$serial_no}}"/>
								</div>
		        			</div>
							<div class="clearfix z_mb10">
								<div class="z_inputbox fl z_mr30">
									<label>机台类型：</label>
									<select name="m_type" class="z_w172 z_h24 input-sm form-control input-s-sm inline">
										<option value="0" >全部</option>
										<option value="2" @if($m_type==2) selected @endif>新机台</option>
										<option value="1" @if($m_type==1) selected @endif >老机台</option>
									</select>
								</div>
								<div class="z_inputbox fl z_mr30">
									<label>商家名称：</label>
									<input type="text" name="bus_name" value="{{$bus_name}}" />
								</div>
								<div class="z_inputbox fl z_mr30">
									<label>门店名称：</label>
									<input type="text" name="stores_name" value="{{$stores_name}}" />
								</div>
							</div>
							<div class="z_inputbox fl z_mr30">
								<label>日期：</label>
								<input type="text" name="m_starttime" value="{{$m_starttime}}" class=" layer-date" id="start_date" placeholder="开始时间"/>
								--
								<input type="text" name="m_endtime"   value="{{$m_endtime}}" class=" layer-date" id="end_date" placeholder="结束时间" />
							</div>
		        		</div>
		        		<div class="col-sm-1 tc z_pt10" style=" width: 80px; ">
		        			<button class="btn z_bar-blue btn-xs z_color_white z_w60 z_mb5" style=" height: 33px;" >查询</button>
		        		</div>
	        		</form>
        		</div>
        	</div>
        	<!--机台详情-->

        <div class="col-sm-12">
         <!-- 门店资料开始 -->
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>机台营收列表</h5>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
	                            <tr> 
	                            	<th>机台编号</th>
									<th>机台名称</th>
	                                <th>门店名称</th>
									<th>游币支付</th>
									<th>云端投币</th>
									<th>线下投币</th>
									<th>总投币数</th>
									<th>出票数</th>
									<th>出票率(票/币)</th>
	                                <th>操作</th>
	                            </tr>
                            </thead>
                            <tbody>
	                            @if(!empty($order_lists))
		                            @foreach($order_lists as $val)
		                               	<tr>
											<td>{{$val->machine_id}}</td>
											<td>{{$val->m_name}}</td>
											<td>{{$val->stores_name}}</td>
											<td>{{$val->shang_pay_price}}</td>
											<td>{{($val->pay_price)-($val->xia_pay_price)}}</td>
											<td>{{$val->xia_pay_price}}</td>
											<td>{{$val->pay_price}}</td>
											<td>{{$val->ticket?$val->ticket:0}}</td>
											<td><?php
                                                if($val->pay_price == '0'){
                                                    echo '0';
                                                }else{
                                                    echo sprintf('%.2f',($val->ticket/$val->pay_price));
                                                }
                                                ?>
											</td>
											<td>
												<a href="{{ route('business.rj_machint_report_detail',['machine_id'=>$val->machine_id,'m_starttime'=>$m_starttime,'m_endtime'=>$m_endtime]) }}" >查看机台营收历史</a>
		                                    </td>
		                                </tr>
		                             @endforeach 
		                         @endif
                             </tbody>
                        </table>
                    </div>
                    <div class="text-right">
                    	@if(!empty($ads) && !empty($ads->links()))
                            {{
								$ads->appends([
										'machine_id'=>$m_name,
										'm_name'=>$m_name,
										'serial_no'=>$serial_no,
										'm_type'=>$m_type,
										'bus_name'=>$bus_name,
										'stores_name'=>$stores_name,
										'm_starttime'=>$m_starttime,
										'm_endtime'=>$m_endtime
								])->links()
							 }}
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
@endsection

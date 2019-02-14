@extends('admin.layouts.parent')
@section('page-title','订单管理')
@section('main')

 <link href="/admin/css/new-add.css" rel="stylesheet">
 <style>
	 .css-display{ display:none; }
	 .page-tabs-content{margin: 0 5px;  color: #fff;  background-color: #16cfe2;}
	 .row{margin-top:20px;}
	 button, input, select, textarea{    height: 32px; border:0px; width:172px; padding: 0px 5px; }
	 .z_inputbox label{ font-weight: bold; }
	 select.input-sm{ height: 34px;}
 </style>
    <div class="row" >
          	<!--新加的-->
        	<div class="col-sm-12 ">
        		<div class="z_bar-gray z_border_t z_border_b clearfix">
        		 	<form action="{{ route('business.rj_order_index') }}" method="get">
		        		<div class="col-sm-10 m-b-xs z_pt10">
		        			<div class="clearfix z_mb10">
		        				<div class="z_inputbox fl z_mr30">
			        				<label>订单号 ： </label>
			        				<input type="text" name="refno" value="{{$refno}}"/>
			        			</div>
								<div class="z_inputbox fl z_mr30">
									<label>机台名称:</label>
									<input type="text" name="m_name" value="{{$m_name}}"/>
								</div>
								<div class="z_inputbox fl z_mr30">
									<label>硬件编号:</label>
									<input type="text" name="serial_no" value="{{$serial_no}}"/>
								</div>
		        			</div>
							<div class="clearfix z_mb10">
								<div class="z_inputbox fl z_mr30">
									<label>门店名称:</label>
									<input type="text" name="stores_name" value="{{$stores_name}}"/>
								</div>
								<div class="z_inputbox fl z_mr30">
									<label>门店 id：</label>
									<input type="text" name="store_id" value="{{$store_id}}"/>
								</div>
								<div class="z_inputbox fl z_mr30">
									<label>支付金额:</label>
									<input type="text" name="pay_price" value="{{$pay_price}}"/>
								</div>
							</div>
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
									<label>交易状态:</label>
									<select name="status" class="z_w172 z_h24 input-sm form-control input-s-sm inline">
										<option value="-1" @if($status == (-1)) selected @endif>全部</option>
										<option value="0" @if($status == 0) selected @endif>待付款</option>
										<option value="1" @if($status == 1) selected @endif>游戏中</option>
										<option value="2" @if($status == 2) selected @endif>已使用</option>
										<option value="3" @if($status == 3) selected @endif>已过期</option>
									</select>
								</div>
								<div class="z_inputbox fl z_mr30">
									<label>订单异常:</label>
									<select name="machine_status" class="z_w172 z_h24 input-sm form-control input-s-sm inline">
										<option value="0" @if($machine_status == 0) selected @endif>全部</option>
										<option value="1" @if($machine_status == 1) selected @endif>正常</option>
										<option value="2" @if($machine_status == 2) selected @endif>异常</option>
									</select>
								</div>
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
        
         <!-- 列表开始 -->
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>订单列表</h5>
					<div class="ibox-tools">
						<a href="{{route('business.rj_order_export_tow')}}" class="btn btn-primary btn-xs">导出列表 </a>
					</div>
                </div>
                <div class="ibox-content">
					<div class="table-responsive">
						<table class="table table-striped">
                            <thead>
	                            <tr>
	                            	<th>订单号</th>
	                                <th>门店名称</th>
	                                <th>门店ID</th>
	                                <th>机台名称</th>
	                                <th>硬件编号</th>
	                                <th>交易状态</th>
	                                <th>支付方式</th>
	                                {{--<th>用户编号</th>--}}
									<th>用户手机号</th>
	                                <th>支付币数</th>
									<th>下单时间</th>
									<th>订单来源</th>
									<th>是否异常</th>
	                                <th>操作</th>
	                            </tr>
                            </thead>
                            <tbody>
	                            @if(!empty($ads))

		                            @foreach($ads as $val)
		                               	<tr>
		                               	   <td>{{$val->refno}}</td>
			                               <td>{{$val->stores_name}}</td>
			                               <td>{{$val->store_id}}</td>
			                               <td>{{$val->m_name}} </td>
			                               <td>{{$val->serial_no}}</td>
											<th>
											    @if($val->status=='0') 待付款
											    @elseif($val->status=='1') 游戏中
											    @elseif($val->status=='2') 已使用
											    @elseif($val->status=='3') 已过期
											    @endif
										   </th>
			                               <th> @if($val->payment_type=='1') 游币支付 @else 线下投币 @endif </th>
			                               {{--<th>{{$val->userid}}</th>--}}
											<th>{{$val->mobile}}</th>
											<th>{{$val->pay_coins}}</th>
											<th>{{$val->addtime}}</th>
											<th>@if($val->orgin=='2') 微信  @elseif($val->orgin=='1') App  @else 未知  @endif </th>
											<th> @if($val->machine_status=='1') 正常 @elseif($val->machine_status=='2') 异常 @endif </th>
		                                   <td>
											   <a class="btn btn-white btn-sm" href="{{route('business.rj_order_detail',array('id'=>$val->id))}}"> 详情 </a>
											   <div class="btn btn-white btn-sm" onclick="del(this,{{$val->id}})"> 删除 </div>
										   </td>
		                                </tr>
		                             @endforeach
		                         @endif
                             </tbody>
                        </table>
                    </div>

                    <div class="text-right">
                    	@if(!empty($ads) && !empty($ads->links()))
                            {{ $ads->appends([
                             					'refno'=>$refno,
					                            'm_name'=>$m_name,
					                            'serial_no'=>$serial_no,
					                            'stores_name'=>$stores_name,
					                            'store_id'=>$store_id,
					                            'pay_price'=>$pay_price,
					                            'status'=>$status,
					                            'payment_type'=>$payment_type,
					                            'machine_status'=>$machine_status
                            ])->links() }}
                        @endif
                    </div>
                </div>
            </div>
            <!-- 列表结束 -->
            
            
        </div>
    </div>
	{{ csrf_field() }}
    <script>
    	var _token=$('input[name="_token"]').val();
    	function del(obj,id){
    		layer.confirm('您确定要删除订单？', {
    			  btn: ['确定','取消'] //按钮
    			}, function(){
    				data = {'_token':_token,'id':id}
    				$.post('{{route("business.rj_order_del")}}',data,function(res){
    					
    					if(res.code==200){
    						$(obj).parent().parent().remove();
    						layer.msg(res.msg,{icon: 1});
    					}else{
    						layer.msg(res.msg,{icon: 5});
    					}
    				});
    			}, function(){
    			 
    			});
        	
        }
    </script>
    
    <script>
        $(function(){
            $('.switch-publish').click(function(){
                youyibao.httpSend($(this),'get',1);
            });
            $('.btn-del-ad').click(function(){
                youyibao.httpSend($(this),'get',1);
            });
        });
    </script>
@endsection

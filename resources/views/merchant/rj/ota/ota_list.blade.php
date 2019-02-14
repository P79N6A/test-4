@extends('admin.layouts.parent')
@section('page-title','OTA升级')
@section('main')

 <link href="/admin/css/new-add.css" rel="stylesheet">
@extends(env('Merchant_view').'.layouts.ota_menu')
    <div class="row" >
          	<!--新加的-->
        	<div class="col-sm-12 ">
        		<div class="z_bar-gray z_border_t z_border_b clearfix">
        		 	<form action="{{ route('business.rj_ota_list') }}" method="get">
		        		<div class="col-sm-8 m-b-xs z_pt10">
							<div class="clearfix z_mb10">
								<div class="z_inputbox fl z_mr30">
									<label>品牌名称:</label>
									<input type="text" name="brand_name" value="{{$brand_name}}"/>
								</div>
								<div class="z_inputbox fl z_mr30">
									<label>机台型号：</label>
									<input type="text" name="model" value="{{$model}}" />
								</div>
							</div>
		        			<div class="clearfix z_mb10">
		        				<div class="z_inputbox fl z_mr30">
			        				<label>机台ID ：</label>
			        				<input type="text" name="id" value="{{$id}}"/>
			        			</div>
			        			<div class="z_inputbox fl z_mr30">
			        				<label>门店名称：</label>
			        				<input type="text" name="stores_name" value="{{$stores_name}}"/>
			        			</div>
		        			</div>
		        			<div class="clearfix z_mb10">
			        			<div class="z_inputbox fl z_mr30">
			        				<label>机台名称:</label>
			        				<input type="text" name="m_name" value="{{$m_name}}" />
			        			</div>
			        			<div class="z_inputbox fl z_mr30">
			        				<label>固件版本号</label>
			        				<input type="text" name="firmware_sn" value="{{$firmware_sn}}" />
			        			</div>
		        			</div>
		        			<div class="clearfix z_mb10">
			        			<div class="z_inputbox fl z_mr30">
			        				<label>硬件编号:</label>
			        				<input type="text" name="code" value="{{$code}}" />
			        			</div>
			        			<div class="z_inputbox fl z_mr30">
			        				<label>硬件版本号</label>
			        				<input type="text" name="hardware_sn" value="{{$hardware_sn}}" />
			        			</div>
		        			</div>
		        		</div>
		        		<div class="col-sm-1 tc z_mt54" style="width: 80px;">
		        			<button class="btn z_bar-blue btn-xs z_color_white z_w60 z_mb5">查询</button>
		        		</div>
	        		</form>
        		</div>
        	</div>
        	<!--机台详情-->
    
        <div class="col-sm-12">
        
         <!-- 门店资料开始 -->
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>升级列表</h5>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
	                            <tr> 
	                            	<th>机台ID</th>              
	                                <th>门店</th>
	                                <th>硬件编号</th>
	                                <th>机台名称</th>
	                                <th>硬件版本号</th>
	                                <th>固件版本号</th>
	                                <th>升级方式</th>
	                                <th>升级状态</th>
	                                <th>升级时间</th>
	                                <th>操作</th>
	                            </tr>
                            </thead>
                            <tbody>
	                            @if(!empty($ads))
		                            @foreach($ads as $val)
		                               	<tr>
		                               	   <td>{{$val->m_id}}</td>
			                               <td>{{$val->stores_name}}</td>
			                               <td>{{$val->code}}</td>
			                               <td>{{$val->m_name}} </td>
			                               <td>{{$val->hardware_sn}}</td>
			                               <td>{{$val->firmware_sn}}</td>
			                               <th> @if($val->upgrade_type=='1') 开机升级 @elseif($val->upgrade_type=='2') 指定时间升级 @else 立即升级  @endif </th>
			                               <th> @if($val->is_upgrade=='1') 已升级 @else 待升级 @endif </th>
			                               <th>{{$val->u_date}}</th>
		                                   <td>
		                                   		<button class="btn btn-white btn-sm" onclick="del(this,{{$val->u_id}})"> 移除 </button>                                         
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
					                            'id'=>$id,
								    			'stores_name'=>$stores_name,
								    			'm_name'=>$m_name,
								    			'firmware_sn'=>$firmware_sn,
								    			'code'=>$code,
								    			'hardware_sn'=>$hardware_sn
				                            ])->links() }}
                        @endif
                    </div>
                </div>
            </div>
            <!-- 门店资料结束 -->
            
            
        </div>
    </div>
	{{ csrf_field() }}
    <script>
    	var _token=$('input[name="_token"]').val();
    	function del(obj,id){
    		layer.confirm('您确定要移除升级机台？', {
    			  btn: ['确定','取消'] //按钮
    			}, function(){
    				data = {'_token':_token,'id':id}
    				$.post('{{route("business.rj_ota_list_del")}}',data,function(res){
    					
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

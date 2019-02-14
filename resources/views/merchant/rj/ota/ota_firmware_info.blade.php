@extends('admin.layouts.parent')
@section('page-title','版本详情')
@section('main')
<link href="/admin/css/webuploader.css" rel="stylesheet">
 <link href="/admin/css/new-add.css" rel="stylesheet">
    <div class="row" >
        <div class="col-sm-12">
        
         <!-- 门店资料开始 -->
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                <div class="z_form">
	                	{{ csrf_field() }}
		        		<div class="z_inputbox z_mt10 z_mb10">
	        				<label class="z_w120-tr"><span class="z_color_red">*</span>固件版本号:</label>
	        				<input type="text" disabled name="firmware_sn" value="{{$info['firmware_sn']}}" class="z_w335"/>
	        			</div>
	        			<div class="z_inputbox  z_mb10">
	        				<label class="z_w120-tr"><span class="z_color_red">*</span>硬件版本号:</label>
	        				<input type="text" disabled name="hardware_sn" value="{{$info['hardware_sn']}}" class="z_w335"/>
	        			</div>
	        			<div class="z_inputbox  z_mb10">
	        				<label class="z_w120-tr"><span class="z_color_red">*</span>上传固件版本:</label>
	        				<input type="text" disabled id="firmware_id" value="{{env('STATIC_BASE_URL').'/'.$info['firmware_url']}}" class="z_w335"/>
	        			
	        			</div>
	        			<div class="z_inputbox z_mb10">
	        				<label class="z_w120-tr">备注信息:</label>
	        				<span>{{$info['ramrk']}}</span>
	        			</div>
	        		
        			
        			<div class="z_title z_font16 z_border_b z_pb10">
        				<div class="clearfix clear">
	        				<span class="z_fontb">该版本下升级机台:</span>
							<button class="btn z_bar-blue btn-xs z_color_white fr" onclick="history.go(-1)" >返回上一页</button>
        				</div>
        			</div>
        			<div class="z_border z_mt20">
        				<table class="z_table2 js-z_table2">
		        			<thead class="z_fontb">
		        				<tr>
		        					<th class="">序号(升级)</th>
		        					<th class="">品牌</th>
		        					<th style="width: 11%;">门店</th>
		        					<th class="">机台名称</th>
		        					<th class="">机台型号</th>
		        					<th class="">硬件版本号</th>
		        					<th class="">固件版本号</th>
		        					<th class="">升级方式</th>
		        					<th>操作</th>
		        				</tr>
		        			</thead>
		        			<tbody>
			        			@if(!empty($ads))
			        				@foreach($ads as $val)
			        				<tr>
			        					<td>{{$val->u_id }}</td>
			        					<td>{{$val->brand_id}}</td>
			        					<td>{{$val->stores_name}}</td>
			        					<td>{{$val->m_name}}</td>
			        					<td>{{$val->model}}</td>
			        					<td>{{$val->hardware_sn}}</td>
			        					<td>{{$val->firmware_sn}}</td>
			        					<td> @if($val->upgrade_type=='1') 开机升级 @elseif($val->upgrade_type=='2') 指定时间升级 @else 立即升级  @endif </td>
			        					<td>
			        						<button class="btn btn-white btn-sm" onclick="del(this,{{$val->u_id}})">移除  </button>  
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
            <!-- 门店资料结束 -->
            
            
        </div>
    </div>
	<script src="/admin/js/webuploader.min.js"></script>	
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

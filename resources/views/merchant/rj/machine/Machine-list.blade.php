@extends('admin.layouts.parent')
@section('page-title','机台管理')
@section('main')

 <link href="/admin/css/new-add.css" rel="stylesheet">

    <div class="row" >
          	<!--新加的-->
        	<div class="col-sm-12 ">
        		<div class="z_bar-gray z_border_t z_border_b clearfix">
        		 	<form action="{{ route('business.rj_machine_list') }}" method="get">
		        		<div class="col-sm-10 m-b-xs z_pt10">
		        			<div class="clearfix z_mb10">
			        			<div class="z_inputbox fl z_mr30">
			        				<label>品牌名称:</label>
			        				<input type="text" name="brand_name" value="{{$brand_name}}"/>
			        			</div>
			        			<div class="z_inputbox fl z_mr30">
			        				<label>门店名称：</label>
			        				<input type="text" name="stores_name" value="{{$stores_name}}"/>
			        			</div>
			        			<div class="z_inputbox fl z_mr30">
			        				<label>禁用状态:</label>
			        				<select name="is_disable" class="z_w172 z_h24 input-sm form-control input-s-sm inline">
			        					<option value="0" @if($is_disable == 0) selected @endif>全部</option>
			        					<option value="1" @if($is_disable == 1) selected @endif>禁用</option>
			        					<option value="2" @if($is_disable == 2) selected @endif>开启</option>
			        				</select>
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
			        			<div class="z_inputbox fl z_mr30">
			        				<label>在线状态:</label>
			        				<select name="is_open" class="z_w172 z_h24 input-sm form-control input-s-sm inline">
			        					
			        					<option value="2" @if($is_open==='0') selected @endif>离线</option>
			        					<option value="1" @if($is_open == 1) selected @endif>在线</option>
			        					<option value="" @if($is_open==='') selected @endif>全部</option>
			        				</select>
			        			</div>
		        			</div>
		        			<div class="clearfix z_mb10">
			        			<div class="z_inputbox fl z_mr30">
			        				<label>机台型号:</label>
			        				<input type="text" name="model" value="{{$model}}" />
			        			</div>
			        			<div class="z_inputbox fl z_mr30">
			        				<label>硬件版本号</label>
			        				<input type="text" name="hardware_sn" value="{{$hardware_sn}}" />
			        			</div>
							<!--
			        			<div class="z_inputbox fl z_mr30">
			        				<label>激活状态:</label>
			        				<select name="is_activate" class="z_w172 z_h24 input-sm form-control input-s-sm inline">
			        					<option value="0" @if($is_activate == 0) selected @endif>全部</option>
			        					<option value="1" @if($is_activate == 1) selected @endif >未激活</option>
			        					<option value="2" @if($is_activate == 2) selected @endif >已激活</option>
			        				</select>
			        			</div>
								-->
								<div class="z_inputbox fl z_mr30">
									<label>硬件编号:</label>
									<input type="text" name="code" value="{{$code}}" />
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
                    <h5>机台列表</h5>
                </div>
              
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped"  style="font-size: 13px;">
                            <thead>
	                            <tr>                        
	                                <th>序号</th>
	                                <th>品牌</th>
	                                <th>门店</th>
	                                <th>机台型号</th>
	                                <th>硬件编号</th>
	                                <th>机台名称</th>
	                                <th>固件版本</th>
	                                <th>硬件版本</th>
	                                <th>是否在线</th>
	                                <th>是否激活</th>
	                                <th>是否禁用</th>
	                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($ads))
                              @foreach($ads as $k=>$dev)
                           		<tr>
                                   <td>{{$dev->m_id}}</td>
	                               <td>
		                               <?php 
				                              $brand_id = $dev->brand_id ? $dev->brand_id : 0 ;
				                               if(!empty($brand_id)){
					                               	$name = DB::table(config('tables.base').'.brand')->where('id', "$brand_id")->value('name');
					                               	echo $name?$name:'暂无';
				                               }else{
				                               		echo '暂无';
				                               }
				                               $brand_id = 0;
		                               ?>
	                               </td>
	                               <td>{{$dev->stores_name?$dev->stores_name:'暂无'}}</td>
	                               <td>{{$dev->model}}</td>
	                               <td>{{$dev->code}}</td>
	                               <td>{{$dev->m_name}}@if($dev->m_type == '1') (简易型) @else (智能型) @endif</td>
	                               <td>{{$dev->firmware_sn}}</td>
	                               <td>{{$dev->hardware_sn}}</td>
	                               <td>{{$dev->is_open?'在线':'离线'}}</td>
	                               <td>{{$dev->is_activate=='1'?'未激活':'已激活'}}</td>
	                               <td>{{$dev->is_disable=='1'?'禁用':'正常'}}</td>
                                   <td>
									   <a href="{{ route('business.rj_machine_info',['id'=>$dev->m_id]) }}" class="btn btn-white btn-sm"><i class="fa fa-pencil"></i>  详情</a>
									   <a href="{{ route('business.rj_machine_qrcode',['code'=>$dev->code]) }}" class="btn btn-white btn-sm"><i class="fa fa-pencil"></i>  二维码</a>
									 <!--
									     <a onClick="machine_un_bundling(this,{{$dev->m_id}})" class="btn btn-white btn-sm"><i class="fa fa-pencil"></i>  解绑</a>
									   <a onClick="machine_del(this,{{$dev->m_id}})" class="btn btn-white btn-sm"><i class="fa fa-pencil"></i>  删除</a>
									   -->
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
					                            'brand_name'=>$brand_name,
					                            'stores_name'=>$stores_name,
					                            'is_disable'=>$is_disable,
					                            'm_name'=>$m_name,
					                            'firmware_sn'=>$firmware_sn,
					                            'is_open'=>$is_open,
					                            'model'=>$model,
					                            'hardware_sn'=>$hardware_sn,
					                            'is_activate'=>$is_activate
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
     function machine_un_bundling(obj,id){

         layer.confirm('您确定解绑门店？', {
             btn: ['确定','取消'] //按钮
         }, function(){

             var url = "{{ route('business.rj_machine_un_bundling') }}";
             $.post(url,{id:id,_token:_token},function(res){
                 if(res.code=='200'){
                     layer.msg(res.msg,{icon:1},function(){
                         //	location.reload();
                         $(obj).parent().parent().remove();
                     });
                 }else{
                     layer.msg(res.msg,{icon:5});
                 }
             })
         }, function(){
             return false;
         });
     }
 </script>
 <script>
     var _token=$('input[name="_token"]').val();
     function machine_del(obj,id){
         layer.confirm('您确定删除机台？', {
             btn: ['确定','取消'] //按钮
         }, function(){
             var url = "{{ route('business.rj_machine_del') }}";
             $.post(url,{id:id,_token:_token},function(res){
                 if(res.code=='200'){
                     layer.msg(res.msg,{icon:1},function(){
                         //	location.reload();
                         $(obj).parent().parent().remove();
                     });
                 }else{
                     layer.msg(res.msg,{icon:5});
                 }
             })
         }, function(){
             return false;
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

@extends('admin.layouts.parent')
@section('page-title','添加活动')
@section('main')

<link rel="stylesheet" href="/admin/css/webuploader.css">
<link href="/admin/css/new-add.css" rel="stylesheet"> 

<style>
.css-display{ display:none; }
.border_padding{ border: 1px solid #a6a3a3;   padding: 10px 27px 10px 17px; margin-left:15px;margin-right:15px; }
.font_weight{font-weight: bold;}
.margin_left_50{margin-left:50px;}
 .bgcolor{background-color:#a7a3a3!important}
 .c_p{cursor: pointer; background:#f8f9e6;}
 .webuploader-pick{    background-color: #f3f3f4; padding:0px};
 .z_fix_img{ height:30px; background-image:none;  }
 .z_off{top:5px; right:5px;}
 .bgwh{padding: 5px 10px; background-color:rgba(22, 155, 213, 1); border-radius: 3px; }
 
 *{padding: 0px;margin: 0px;}
ul,ol{list-style: none;}
 
 	/*select下拉框 end*/
			.z_border{border: 1px solid #e0e0e0!important;}
			.z-hide{display: none;}
			.z_select_div{width: 100%;position: relative;}
			.z_select_div p{ margin:0;font-size: 14px;text-indent: 1em;line-height: 28px;margin-right: 30px;cursor:pointer; overflow: hidden;white-space: nowrap;text-overflow: ellipsis;color: #999;margin-top: 0px;}
			.z_tubiao{width: 21px;height: 30px;background-image: url(./tb12a.png);background-repeat: no-repeat;display: block;position: absolute;top: 0px;right: 10px;background-position: center;z-index: 4;cursor: pointer;}
			.z_select_div ul{position: absolute;top: 30px;border: 1px solid #E0E0E0;border-bottom-left-radius: 5px;border-bottom-right-radius: 5px;left: 0px;z-index: 7;max-height: 150px;overflow-x: hidden;background-color: #fff;width: 100%;}
			.z_select_div ul li{padding: 0px 10px;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;font-size: 14px;line-height: 40px;border-bottom: 1px solid #ccc;cursor: pointer;position: relative;}
			.z_select_div ul li:last-child{border-bottom: none;}
			.z_select_div ul li.active{background-color: #03A9F4;color: #fff;}
			.z_select_div ul li:hover{background-color: #06A6F2;color: #fff;}
 
 
</style>


    <div class="row" >
    	<form id="form1" action="{{ route('business.insert_schedule',['id'=>$a_id,'game_type'=>$_GET['game_type']]) }}" method="post">
			{{ csrf_field() }}
    			<!--创建赛程-->
	        	<div class="z_border_b z_font18 z_color_red z_lineh40 z_mb10 font_weight">
	        		@if($_GET['type']!=1)
	        			编辑赛程
	        			<button class="btn z_bar-blue  z_color_white z_w120 fr z_mr20 font_weight" type="submit">保存</button>
	        		@else
	        			查看赛程
	        		@endif
					(单机)
	        	</div>
	        	<!--创建赛程 end-->
	        	
        		<!-- 赛程一  start-->
        		<?php if(!empty($scduhele_list)){ $i=0;?>
        		
        		<?php foreach($scduhele_list as $key=>$val){  $i++; ?>
		        	<div class="z_mb10  posr border_padding">
		        		<div class="z_inputbox z_mt10 z_mb10 clearfix">
		    				<label class="z_lineh34 fl z_mr15 z_w100 tr "><span class="z_color_red">*</span>赛程名称:</label>
		    				<input type="text" class="z_w120 form-control"  name="data[{{$key}}][s_name]" value="{{$val['s_name']}}">
		    				<input type="hidden" class="z_w120 form-control"  name="data[{{$key}}][id]" value="{{$val['id']}}">   <!-- 赛程ID -->
		    			</div>
						<div class="z_inputbox z_mt10 z_mb10 clearfix">
							<label class="z_lineh34 fl z_mr15 z_w100 tr "><span class="z_color_red">*</span>玩家价(游币):</label>
							<input type="text" class="z_w120 form-control"  name="data[{{$key}}][price]" value="{{$val['price']}}" >
						</div>
		    			<div class="z_inputbox z_mb10 clearfix">
		    				<label class="z_lineh34 fl z_mr15 z_w100 tr"><span class="z_color_red">*</span>添加子账号:</label>
		    				<span class="z_lineh34 fl z_mr15  tr" style="color:red;">(对应关联总商户账号下的多个子账号，选择即可关联)</span>
		    			</div>
						<div class="z_inputbox z_mb10 clearfix" style=" clear: both; ">
							<ul class="z_ul clearfix margin_left_50">
								@if(!empty($merchant))
									@foreach($merchant as $list)
										<li id="{{$list['id']}}" title="{{$list['description']}}" class="@if(in_array($list['id'],$val['b_id'])) bgcolor @endif" >
											{{$list['name']}}
											<input type="checkbox" name="data[{{$key}}][merchant_id][]" value="{{$list['id']}}"
											 @if(in_array($list['id'],$val['b_id'])) checked @endif style="display:none;">
										</li>
									@endforeach	
								@endif
							</ul>
						</div>
		    			<div class="z_inputbox z_mb10 clearfix">
	        				<label class="z_lineh34 fl z_mr15 z_w100 tr"><span class="z_color_red">*</span>添加机台:</label>
	        				<span class="z_cursor js-addjixing fl" data-value="{{$key}}">
	        					<img src="/admin/img/u294.png" width="29" height="29"/>
	        				</span>
	        			</div>
	        			<div class="z_border  z_mb20 margin_left_50">
	        				<table class="z_table2 js-z_table2 ">
			        			<thead>
			        				<tr>
			        					<th class="" >门店</th>
			        					<th class="" >机台型号</th>
			        					<th class="" >硬件编号</th>
			        					<th class="" >机台名称</th>
			        					<th class="" >固件版本</th>
			        					<th class="" >硬件版本</th>
			        					<th class="" >操作</th>
			        				</tr>
			        			</thead>
			        			<tbody data-value="{{$key}}">
			        			@if(!empty($val['m_id']))
			        				<?php foreach($val['m_id'] as $m_key=>$m_val){ ?>
			        					<tr>
				        					<td>{{$m_val['stores_name']}}</td>
				        					<td>{{$m_val['model']}}</td>
				        					<td>{{$m_val['code']}}</td>
				        					<td>{{$m_val['m_name']}}</td>
				        					<td>{{$m_val['firmware_sn']}}</td>
				        					<td>{{$m_val['hardware_sn']}}</td>
				        					<td> 
				        						<span class="btn btn-white btn-sm z_color_red" onclick="del(this)">删除</span> 
				        						<input type="hidden" name="data[{{$key}}][machine_id][]" value="{{$m_val['m_id']}}">
				        					</td>
				        				</tr>
				        			<?php }?>
				        		@endif
			        			</tbody>
			        		</table>
	        			</div>
	        			<div class="z_inputbox z_mb10 clearfix">
	        				<label class="z_lineh34 fl z_mr15 z_w100 tr"><span class="z_color_red">*</span>奖品设置:</label>
	        				<span class="z_cursor js-z_add fl"  data-value="{{$key}}">
	        					<img src="/admin/img/u294.png" width="29" height="29"/>
	        				</span>
	        			</div>
	        			<div class="z_border z_mb20 margin_left_50">
	        				<table class="z_table2 js-z_table2a">
			        			<thead>
			        				<tr>
			        					<th class="">奖励类型</th>
			        					<th class="">奖品名称</th>
			        					<th class="z_w180">奖品图片</th>
			        					<th class="">奖品数量</th>
			        					<th class="">奖励排名</th>
			        					<th class="">操作</th>
			        				</tr>
			        			</thead>
			        			<tbody>
			        			@if(!empty($val['p_id']))
			        				<?php foreach($val['p_id'] as $p_key=>$p_val){ ?>
			        					<tr>
				        					<td>
				        						<select class="z_w120 input-sm form-control input-s-sm inline prize_value" name="data[{{$key}}][prize][{{$p_key}}][option]" onchange='btnChange(this);'>
						        					<option value="1" @if($p_val['type']==1) selected  @endif>线下礼品</option>
						        					<option value="2" @if($p_val['type']==2) selected  @endif>奖票</option>
													<!--
													<option value="3" @if($p_val['type']==3) selected  @endif>积分</option>
													-->
						        				</select>
				        					</td>
				        					<td><input type="text" class="z_w100 z_mr10" name="data[{{$key}}][prize][{{$p_key}}][name]" value="{{$p_val['item_name']}}" ></td>
				        					<td>
				        						<div class=" z_mtb10 z_mlrauto gallery-picker @if(!empty($p_val['itme_img'])) hide @endif" data-value="1232342131" >
				        							<div class="bgwh">
				        								上传<input type="hidden" name="data[{{$key}}][prize][{{$p_key}}][pic]" value="{{$p_val['itme_img']}}" />
				        							</div>
				        						</div>
				        						<div  style="position: relative;" class=" @if(empty($p_val['itme_img'])) hide @endif">
				        							<img class="z_fix_img z_mtb10 z_mlrauto" src="{{env('STATIC_BASE_URL').'/'.$p_val['itme_img']}}">
				        							<span class="z_off js-z_off"></span>
				        						</div>
				        					</td>
				        					<td><input type="text" name="data[{{$key}}][prize][{{$p_key}}][num]" value="{{$p_val['num']}}" ></td>
				        					<td>
						        				<div class="bar-white z_border " style="width: 120px; background:#fff;     margin: 0 auto; ">
													<input  value="@if(!empty($p_val['rank'])){{$p_val['rank']}}@else{{0}}@endif" 
															name="data[{{$key}}][prize][{{$p_key}}][ranking]"
															type="hidden" placeholder="" class="z_input ranking" 
													/>
													<div class="z_select_div  js-z_select_div">
														<p class="z_color_3">
															@if(!empty($p_val['rank'])) NO-{{$p_val['rank']}} @else 请选择 @endif
														</p>
														<ul class="z-hide js-z_select_ul">
															<li data-value = "1">NO-1</li>
															<li data-value = "2">NO-2</li>
															<li data-value = "3">NO-3</li>
															<li data-value = "4">NO-4</li>
															<li data-value = "5">NO-5</li>
															<li data-value = "6">NO-6</li>
															<li data-value = "7">NO-7</li>
															<li data-value = "8">NO-8</li>
															<li data-value = "9">NO-9</li>
															<li data-value = "10">NO-10</li>
														</ul>
														<span class="z_tubiao js-z_tubiao">
															<!--图标-->
														</span>
													</div>
												</div>
				        					</td>
				        					
				        					<td>
				        						<span class="btn btn-white btn-sm z_color_red js-z_off2" >删除</span>
				        						<input type="hidden" name="data[{{$key}}][prize][{{$p_key}}][id]" value="{{$p_val['id']}}" >
				        					</td>
				        				</tr>
				        			<?php }?>
				        		@endif
			        			</tbody>
			        		</table>
	        			</div>
	        			<!--删除赛程按钮-->
	        			<span  class="btn btn-white btn-sm posa z_top-30 z_right-30 c_p" >
	        				<span href="javascript:void(0);" class=" z_color_red z_w80 z_font14 font_weight " onclick="scduhele_del(this)">删除赛程</span>
	        			</span>
	        			<!--删除赛程按钮 end-->
	        			
		        	</div>
		        <?php };?>
	        	<?php }else{ ?>
		        	<div class="z_mb10  posr border_padding">
		        		<div class="z_inputbox z_mt10 z_mb10 clearfix">
		    				<label class="z_lineh34 fl z_mr15 z_w100 tr "><span class="z_color_red">*</span>赛程名称:</label>
		    				<input type="text" class="z_w120 form-control"  name="data[0][s_name]">
		    			</div>
						<div class="z_inputbox z_mt10 z_mb10 clearfix">
							<label class="z_lineh34 fl z_mr15 z_w100 tr "><span class="z_color_red">*</span>玩家价(游币):</label>
							<input type="text" class="z_w120 form-control"  name="data[0][price]" value="0" >
						</div>
		    			<div class="z_inputbox z_mb10 clearfix">
		    				<label class="z_lineh34 fl z_mr15 z_w100 tr"><span class="z_color_red">*</span>添加子账号:</label>
		    				<span class="z_lineh34 fl z_mr15  tr" style="color:red;">(对应关联总商户账号下的多个子账号，选择即可关联)</span>
		    			</div>
						<div class="z_inputbox z_mb10 clearfix" style=" clear: both; ">
							<ul class="z_ul clearfix margin_left_50">
								@if(!empty($merchant))
									@foreach($merchant as $list)
										<li id="{{$list['id']}}" title="{{$list['description']}}" >
											{{$list['name']}}
											<input type="checkbox" name="data[0][merchant_id][]" value="{{$list['id']}}" style="dispaly:none;">
										</li>
									@endforeach	
								@endif
							</ul>
						</div>
		    			<div class="z_inputbox z_mb10 clearfix">
	        				<label class="z_lineh34 fl z_mr15 z_w100 tr"><span class="z_color_red">*</span>添加机台:</label>
	        				<span class="z_cursor js-addjixing fl" data-value="0">
	        					<img src="/admin/img/u294.png" width="29" height="29"/>
	        				</span>
	        			</div>
	        			<div class="z_border  z_mb20 margin_left_50">
	        				<table class="z_table2 js-z_table2 ">
			        			<thead>
			        				<tr>
			        					<th class="" >门店</th>
			        					<th class="" >机台型号</th>
			        					<th class="" >硬件编号</th>
			        					<th class="" >机台名称</th>
			        					<th class="" >固件版本</th>
			        					<th class="" >硬件版本</th>
			        					<th class="" >操作</th>
			        				</tr>
			        			</thead>
			        			<tbody data-value="0">
			        				<!-- 机台列表 -->
			        			</tbody>
			        		</table>
	        			</div>
	        			<div class="z_inputbox z_mb10 clearfix">
	        				<label class="z_lineh34 fl z_mr15 z_w100 tr"><span class="z_color_red">*</span>奖品设置:</label>
	        				<span class="z_cursor js-z_add fl"  data-value="0">
	        					<img src="/admin/img/u294.png" width="29" height="29"/>
	        				</span>
	        			</div>
	        			<div class="z_border z_mb20 margin_left_50">
	        				<table class="z_table2 js-z_table2a">
			        			<thead>
			        				<tr>
			        					<th class="">奖励类型</th>
			        					<th class="">奖品名称</th>
			        					<th class="z_w180">奖品图片</th>
			        					<th class="">奖品数量</th>
			        					<th class="">奖励排名</th>
			        					<th class="">操作</th>
			        				</tr>
			        			</thead>
			        			<tbody>
			        				<!-- 奖品列表 -->
			        			</tbody>
			        		</table>
	        			</div>
	        			<!--删除赛程按钮-->
	        			<span  class="btn btn-white btn-sm posa z_top-30 z_right-30 c_p" >
	        				<span href="javascript:void(0);" class=" z_color_red z_w80 z_font14 font_weight " onclick="scduhele_del(this)">删除赛程</span>
	        			</span>
	        			<!--删除赛程按钮 end-->
	        			
		        	</div>
	        	<?php };?>
	        	<!--赛程一 end-->
		
			
			
        	
		</form>
		
		<!-- 添加新赛程 -->
		<div class="z_inputbox z_mb10 z_mb30 tc">
			<a href="javascript:void(0);" class="btn z_bar-blue z_color_white z_w100  z_mt40 font_weight " onClick="schedule_add(this)">添加新赛程</a>
			<input type="hidden" name="schedule_num" value="{{!empty($i)?$i-1:0}}">
		</div>
    </div>
    
    <!--阴影和弹出框-->
    <div class="shadow" style=""> </div>
    <!-- 选择机台商户  开始 -->
    <div class="z_tankuan z_border  z_w776 hide js-addjixingbox" style="top: 60px;">
    	<span class="z_off2 js-z_off3"></span>   	
    	<div class="clearfix">
    		<div class="clearfix col-sm-12 z_bar-orange z_ptb20">
    			<form id="form1" action="" method="get">
        		 	<input type="hidden" name="bus_user_id" value="<?php echo $activity->merchint_id?>">
		        		<div class="col-sm-10 m-b-xs z_pt10">
		        			<div class="clearfix z_mb10">
			        			<div class="z_inputbox fl z_mr30">
			        				<label>门店名称：</label>
			        				<input type="text" name="stores_name" value=""/>
			        			</div>
			        			<div class="z_inputbox fl z_mr30">
			        				<label>机台名称：</label>
			        				<input type="text" name="m_name" value="" />
			        			</div>
		        			</div>
		        			<div class="clearfix z_mb10">
			        			<div class="z_inputbox fl z_mr30">
			        				<label>硬件版本号</label>
			        				<input type="text" name="hardware_sn" value="" />
			        			</div>
			        			<div class="z_inputbox fl z_mr30">
			        				<label>固件版本号</label>
			        				<input type="text" name="firmware_sn" value="" />
			        			</div>
		        			</div>
		        			<div class="clearfix z_mb10">
			        			<div class="z_inputbox fl z_mr30">
			        				<label>机台型号：</label>
			        				<input type="text" name="model" value="" />
			        			</div>
		        			</div>
		        		</div>
		        		<div class="col-sm-1 tc z_mt54" style="width: 80px;">
	        			<a class="btn z_bar-blue btn-xs z_color_white z_w60 z_mb5" onclick="search_machint();" >查询</a>
	        		</div>
        		</form>
    		</div>
    		<div class="clear"></div>
    		<div class="z_mtb20 z_border" style=" max-height: 460px; overflow-y: auto;">
    			<table class="z_table2 js-z_table2_select">
        			<thead>
        				<tr>
							<th >选择机台</th>
							<th class="">门店</th>
							<th class="">机台型号</th>
							<th class="">硬件编号</th>
							<th class="">机台名称</th>
							<th class="">固件版本</th>
							<th class="">硬件版本</th>
						</tr>
        			</thead>
        			<tbody>
        			<!-- 机台列表 -->
        				
        			</tbody>
        		</table>
    		</div>
			<div class="z_inputbox z_mb10 z_mb30 tc">
				<a href="javascript:void(0);" class="btn z_bar-blue  z_color_white z_w100 js-z_table2_add">添加</a>
			</div>
    </div>
  </div>  
  <!-- 选择机台商户  结束 -->
  
  <script type="text/javascript" src="/admin/js/webuploader.min.js"></script>
  <script src="/admin/js/template.js"></script>
  <script>
  	  var _token=$('input[name="_token"]').val();
  </script>

  
  <!-- 添加赛程 -->
  <script  id="schedule_html" type="text/html">
		<div class="z_mb10  posr border_padding">
			<div class="z_inputbox z_mt10 z_mb10 clearfix">
				<label class="z_lineh34 fl z_mr15 z_w100 tr "><span class="z_color_red">*</span>赛程名称:<!--{key}--></label>
				<input type="text" class="z_w120 form-control" name="data[<!--{key}-->][s_name]">
			</div>
			<div class="z_inputbox z_mt10 z_mb10 clearfix">
				<label class="z_lineh34 fl z_mr15 z_w100 tr "><span class="z_color_red">*</span>玩家价(游币):</label>
				<input type="text" class="z_w120 form-control"  name="data[<!--{key}-->][price]" value="0" >
			</div>
			<div class="z_inputbox z_mb10 clearfix">
				<label class="z_lineh34 fl z_mr15 z_w100 tr"><span class="z_color_red">*</span>添加子账号:</label>
				<span class="z_lineh34 fl z_mr15  tr" style="color:red;">(对应关联总商户账号下的多个子账号，选择即可关联)</span>
			</div>
			<div class="z_inputbox z_mb10 clearfix" style=" clear: both; ">
				<ul class="z_ul clearfix margin_left_50">
					@if(!empty($merchant))
						@foreach($merchant as $list)
							<li id="{{$list['id']}}" title="{{$list['description']}}" >
								{{$list['name']}}
								<input type="checkbox" name="data[<!--{key}-->][merchant_id][]" value="{{$list['id']}}" style="display:none;" >
							</li>
						@endforeach	
					@endif
				</ul>
			</div>
			<div class="z_inputbox z_mb10 clearfix">
				<label class="z_lineh34 fl z_mr15 z_w100 tr"><span class="z_color_red">*</span>添加机台:</label>
				<span class="z_cursor js-addjixing fl"  data-value="<!--{key}-->">
					<img src="/admin/img/u294.png" width="29" height="29"/>
				</span>
			</div>
			<div class="z_border  z_mb20 margin_left_50">
				<table class="z_table2 js-z_table2 ">
					<thead>
		        				<tr>
		        					<th class="" >门店</th>
		        					<th class="" >机台型号</th>
		        					<th class="" >硬件编号</th>
		        					<th class="" >机台名称</th>
		        					<th class="" >固件版本</th>
		        					<th class="" >硬件版本</th>
		        					<th class="" >操作</th>
		        				</tr>
		        			</thead>
					<tbody data-value="<!--{key}-->">
						<!-- 机台列表 -->
					</tbody>
				</table>
			</div>
			<div class="z_inputbox z_mb10 clearfix">
				<label class="z_lineh34 fl z_mr15 z_w100 tr"><span class="z_color_red">*</span>奖品设置:</label>
				<span class="z_cursor js-z_add fl" data-value="<!--{key}-->">
					<img src="/admin/img/u294.png" width="29" height="29"/>
				</span>
			</div>
			<div class="z_border z_mb20 margin_left_50">
				<table class="z_table2 js-z_table2a">
					<thead>
						<tr>
							<th class="">奖励类型</th>
							<th class="">奖品名称</th>
							<th class="z_w180">奖品图片</th>
							<th class="">奖品数量</th>
							<th class="">奖励排名</th>
							<th class="">操作</th>
						</tr>
					</thead>
					<tbody>
						<!-- 奖品列表 -->
					</tbody>
				</table>
			</div>
			<!--删除赛程按钮-->
			<span  class="btn btn-white btn-sm posa z_top-30 z_right-30 c_p" >
				<span href="javascript:void(0);" class=" z_color_red z_w80 z_font14 font_weight " onclick="scduhele_del(this)">删除赛程</span>
			</span>
			<!--删除赛程按钮 end-->
			
		</div>
  </script>
  
  
  <!-- 添加奖品 -->
  <script id="list"  type="text/html">
		<tr>
			<td>
				<select class="z_w120 input-sm form-control input-s-sm inline prize_value" name="data[<!--{schedule_key}-->][prize][<!--{prize_key}-->][option]" onchange='btnChange(this);'>
					<option value="1">线下礼品</option>
					<option value="2">奖票</option>
					<!--
					<option value="3">积分</option>
					-->
				</select>
			</td>
			<td><input type="text" class="z_w100 z_mr10" name="data[<!--{schedule_key}-->][prize][<!--{prize_key}-->][name]" ></td>
			<td>
				<div class=" z_mtb10 z_mlrauto gallery-picker " data-value="1232342131"  >
					<div class="bgwh">
						上传<input type="hidden" name="data[<!--{schedule_key}-->][prize][<!--{prize_key}-->][pic]" value="" />
					</div>
				</div>
				<div  style="position: relative;" class="hide">
					<img class="z_fix_img z_mtb10 z_mlrauto" src="/admin/img/profile_small.jpg">
					<span class="z_off js-z_off"></span>
				</div>
			</td>
			<td><input type="text" name="data[<!--{schedule_key}-->][prize][<!--{prize_key}-->][num]" value="1" ></td>
			
			<td>
				<div class="bar-white z_border " style="width: 120px; background:#fff;     margin: 0 auto; ">
					<input  value="0" 
							name="data[<!--{schedule_key}-->][prize][<!--{prize_key}-->][ranking]"
							type="hidden" placeholder="" class="z_input ranking" 
					/>
					<div class="z_select_div  js-z_select_div">
						<p class="z_color_3">
							 请选择 
						</p>
						<ul class="z-hide js-z_select_ul">
							<li data-value = "1">NO-1</li>
							<li data-value = "2">NO-2</li>
							<li data-value = "3">NO-3</li>
							<li data-value = "4">NO-4</li>
							<li data-value = "5">NO-5</li>
							<li data-value = "6">NO-6</li>
							<li data-value = "7">NO-7</li>
							<li data-value = "8">NO-8</li>
							<li data-value = "9">NO-9</li>
							<li data-value = "10">NO-10</li>
						</ul>
						<span class="z_tubiao js-z_tubiao">
							<!--图标-->
						</span>
					</div>
				</div>
			</td>
			<td>
				<span class="btn btn-white btn-sm z_color_red js-z_off2"  >删除</span>
			</td>
		</tr>
  </script>
  
 <!-- 选择机台 -->
 <script id="mechint_list" type="text/html">
<!--{each list as val}-->
	<tr>
		<td>
			<input type="checkbox" name="machint_id[]" value="<!--{val.m_id}-->" />
		</td>
		<td><!--{val.stores_name}--></td>
		<td><!--{val.model}--></td>
		<td><!--{val.code}--></td>
		<td><!--{val.m_name}--></td>
		<td><!--{val.firmware_sn}--></td>
		<td><!--{val.hardware_sn}--></td>
	</tr>
<!--{/each}-->
</script>
 
 <!-- 选中的机台 -->
 <script id="mechint_list_select" type="text/html">
<!--{each list as val}-->
	 <tr>
		<td><!--{val.stores_name}--></td>
		<td><!--{val.model}--></td>
		<td><!--{val.code}--></td>
		<td><!--{val.m_name}--></td>
		<td><!--{val.firmware_sn}--></td>
		<td><!--{val.hardware_sn}--></td>
		<td> 
			<span class="btn btn-white btn-sm z_color_red" onclick="del(this)">删除</span> 
			<input type="hidden" name="data[<!--{key}-->][machine_id][]" value="<!--{val.m_id}-->">
		</td>
	</tr>
<!--{/each}-->
 </script>
  
  <script>


		function schedule_ajax(data){
			$.post('{{route("business.machine_user")}}',data,function(res){
				if(res.code=='200'){
					
					data={'list':res.data};
					var html =template('mechint_list',data);
					$('.js-z_table2_select').children('tbody').html(html);
	//				layer.msg(res.msg,{icon:1});
	
					//出现位置
					var _h = $(window).scrollTop();+30;
					$(".js-addjixingbox").css('top',_h);
					
					//阴影显示
					$(".js-addjixingbox").removeClass("hide");
					$(".shadow").show();
					
				}else{
					layer.msg(res.msg,{icon:5});
				}
			})
		}
		
		//获取机台
		$("body").delegate(".js-addjixing",'click',function(){
			//获取当前赛程下标
			var schedule_num = $(this).attr('data-value');
			console.log(schedule_num);
	// size();
			//获取当前赛程已经添加的机台id ( 查询  not in  )
			var strID='';
			$('input[name="data['+schedule_num+'][machine_id][]"]').each(function(){
				strID+= $(this).val()+',';
			});
	//		console.log('获取当前赛程已经添加的机台id '+strID);

			$('.js-z_table2_add').attr('data-schedule_num',schedule_num);	//传递当前赛程
			$('.js-z_table2_add').attr('data-strID',strID);	//传递当前赛程已选中的机台

			data={'bus_user_id':<?php echo $activity->merchint_id?>,'array_m_id_notin':strID,'_token':_token};
			schedule_ajax(data);
		});

		//搜索机台
		function search_machint(){
			
			var strID = $('.js-z_table2_add').attr('data-strID');	//获取当前赛程已选中的机台
			var stores_name = $('input[name="stores_name"]').val();	//门店名称
			var m_name = $('input[name="m_name"]').val();			//机台名臣
			var hardware_sn = $('input[name="hardware_sn"]').val();	//硬件版本号
			var firmware_sn = $('input[name="firmware_sn"]').val(); //固件版本号
			var model = $('input[name="model"]').val();				//机台型号
			
			data={
					'_token':_token,
					'bus_user_id':<?php echo $activity->merchint_id?>,
					'array_m_id_notin':strID,
					'stores_name':stores_name,
					'm_name':m_name,
					'hardware_sn':hardware_sn,
					'firmware_sn':firmware_sn,
					'model':model
				};
			schedule_ajax(data);
		};
		

		

		//选中的机台
		$('body').delegate('.js-z_table2_add','click',function(){
			
			var strID='';	//选中的机台放入html中
			$('input[name="machint_id[]"]:checked').each(function(){
				strID+= $(this).val()+',';
			});
			console.log(strID);
			
			var schedule_num = $(this).attr('data-schedule_num');	//定位当前赛程

			$.post('{{route("business.machine_user")}}',{'bus_user_id':<?php echo $activity->merchint_id?>,'array_m_id':strID,'_token':_token},function(res){
				if(res.code=='200'){
					data={'list':res.data,'key':schedule_num};
					var html =template("mechint_list_select",data);	
					console.log(html);
					console.log('schedule_num'+schedule_num);
						//插入
						 $('.js-z_table2').children('tbody').each(function(){
							 var _val = $(this).attr('data-value');
							 console.log('_val'+_val);
							 if(_val==schedule_num){
							 	 $(this).append(html);
							 }
							 
						 });
					
			//		layer.msg(res.msg,{icon:1});
					
					//阴影显示
					$(".js-addjixingbox").addClass("hide");
					$(".shadow").hide();
				}else{
					layer.msg(res.msg,{icon:5});
				}
			})
			
		});

		//删除机台
		function del(obj){
			layer.confirm('确定要删除机台吗？', {
				  btn: ['确定','取消'] 
				}, function(){
					layer.closeAll('dialog');
					$(obj).parent().parent().html('');
				}, function(){
					return true;
				});
		}	

		 
		//关闭
		$(".js-z_off3").click(function(){
			$(this).parent().addClass("hide");
			//阴影
			$(".shadow").hide();
		});
		
		//左右剧中定位
		function juzhong(b){
			var w2 =$("body").width();
			var w =$(b).width();
		    var w3 =(w2-w)/2;
		    $(b).css("left",w3);
		};
		juzhong(".js-addjixingbox")
		
		//添加赛程
		function schedule_add(obj){
			var schedule_num = $(obj).next().val();
			var key = parseInt(schedule_num)+1;
			
			data = {'key':key};
			console.log('data');
			var html =template('schedule_html',data);		console.log(html);
			
			$('#form1').append(html);
			$(obj).next().val(key);

			merchant_list();

		}
			
			//添加奖品 js-z_add		
			$('body').delegate(".js-z_add",'click',function(){
				//获取当前赛程下标
				var schedule_num = $(this).attr('data-value');
	//			console.log(schedule_num);
				
				//获取当前赛程 奖品 最大下标
				var prize_key=0;
				prize_key  =$(this).parent().next().children().children('tbody').children('tr').size();
	//			console.log(prize_key);
				
				data = {'schedule_key':schedule_num,'prize_key':prize_key};
				var html =template('list',data);
			//	console.log(html);
				$(this).parent().next().children().find('tbody').append(html);
				//绑定事件
				off2();
				Delete2();
		  		upload();
			});
    </script>
     <script >
    	 //下拉框选择改变事件
		function btnChange(e) {
			//var values =e.selectedIndex.value;
			var values = $(e).children('option:selected').val();
			//显示第二个下拉框
			if (values == "1") {
			  $(e).parent().next().find("span").addClass("hide");
			
			}
			else {
			  $(e).parent().next().find("span").removeClass("hide");
			}
		}
		
		
			
		//删除奖品项
		function off2(){
			$(".js-z_off2").click(function(){
				var obj = this;

				layer.confirm('确定要删除奖品吗？', {
					  btn: ['确定','取消'] 
					}, function(){
						layer.closeAll('dialog');
						$(obj).parent().parent().remove();
					}, function(){
						return true;
					});
			});
		}
		off2();
		
		//删除赛程
		function scduhele_del(obj){
			layer.confirm('确定要删除该赛程吗？', {
				  btn: ['确定','取消'] 
				}, function(){
					layer.closeAll('dialog');
					$(obj).parent().parent().remove();
				}, function(){
					return true;
				});
		}


		//选择商户
		function merchant_list(){
			$('.z_ul li').click(function(){
				$(this).toggleClass('bgcolor');
				
				var _class_bgcolor = $(this).hasClass('bgcolor');
				if(_class_bgcolor){
					$(this).find('input').attr('checked',true);
				}else{
					$(this).find('input').attr('checked',false);
				}
			});
		}
		
		merchant_list();

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
    
       <!-- 机台相册上传 -->
    <script>
	   //删除奖品图
		function Delete2(){
			$(".js-z_off").click(function(){
				
				var obj = this;
				layer.confirm('确定要删除奖品图吗？', {
					  btn: ['确定','取消'] 
					}, function(){
						layer.closeAll('dialog');
						$(obj).parent().prev().children('.webuploader-pick').children('.bgwh').children('input').val('');
						$(obj).parent().prev().removeClass('hide');
						$(obj).parent().addClass('hide');
					}, function(){
						return true;
					});
				
			});
		}	
		Delete2();

	
		function upload(){
			   // 相册图片上传
		        var galleryUploader = WebUploader.create({
		            swf: '/admin/js/Uploader.swf',
		            server: '/upload',
		            pick: '.gallery-picker',
		            resize: false,
		            auto: true,
		            duplicate :true  
		        });
		        var $gallery = $('.gallery-container');
		        galleryUploader.on('uploadSuccess', function (file, response) {
		            console.log(file);
		            console.log(response);
	
		           var uploaderId = '#rt_'+file.source.ruid;	//获取上传按钮 的id
		          //  console.log(uploaderId);
		            
		            if(response.data.length>0){
		            	$(uploaderId).parent().children('.webuploader-pick').children('.bgwh').children('input').val(response.data[0].relative_path);
		            	$(uploaderId).parent().addClass('hide');
						$(uploaderId).parent().next().removeClass('hide');
						$(uploaderId).parent().next().find('img').attr('src',response.data[0].absolute_path);
	
						console.log(response.data[0].absolute_path);
		            	
		            	Delete2();
		            }else{
		            	 layer.msg(response.msg[0], {icon: 5});
		            }
				
		        });
		}
		upload();
    </script>
    
      
  <script type="text/javascript">
			//弹出下拉框
			$("body").delegate('.js-z_select_div p','click',function(){
				console.log(123456);
				obj=this;
				var has = $(obj).next().hasClass('z-hide');
				$('.js-z_select_ul').addClass('z-hide');
				if(has){
					$(obj).next().removeClass('z-hide');
				}else{
					$(obj).next().addClass('z-hide');
				}
				select_chane(obj);
			});
			$("body").delegate('.js-z_tubiao','click',function(){
				obj=this;
				var has = $(obj).prev().hasClass('z-hide');
				$('.js-z_select_ul').addClass('z-hide');
				if(has){
					$(obj).prev().removeClass('z-hide');
				}else{
					$(obj).prev().addClass('z-hide');
				}
				select_chane(obj);
			});

			function select_chane(obj){	

				var base = ["1","2","3","4","5","6","7","8","9","10"];	
				var ranking = new Array();
				var z_input = $(obj).parent().parent().find('.ranking');
				var _val = z_input.val();		//当前值
//	console.log('_val '+_val);	

				var $select_val = $(obj).parent().parent().parent().parent().parent('tbody');

				var prize_value = $(obj).parent().parent().parent().parent().find('.prize_value').val();	//奖品类型		
//	console.log('prize_value '+prize_value);

				$select_val.find('.ranking').each(function(){
					 var prize_val = $(this).parent().parent().parent().find('.prize_value').val();
					 if(prize_val==prize_value){
					 	ranking.push($(this).val()); 
					 } 
				});

//	console.log(ranking);
	 
			    for (var j = 0; j < ranking.length; j++) {  
			    	base.splice($.inArray(ranking[j], base),1); 
			    } 
//	console.log(base);  

				var option = '';
				for(var i = 0 ; i < base.length; i++){
					option = option+'<li data-value = "'+base[i]+'">NO-'+base[i]+'</li>';
				}
				$(obj).parent().children('.js-z_select_ul').html(option);   
			}
			
			//给input设置值
			$("body").delegate('.js-z_select_ul li','click',function(){
				var txt =$(this).text();
				var dataValue = $(this).attr('data-value');
				obj = this;
				$(obj).addClass("active").siblings().removeClass("active");
				$(obj).parent().prev("p").html(txt);
				$(obj).parent().prev("p").css("color","#333");
				$(obj).parent().parent().prev("input").val(dataValue);
				$(obj).parent().addClass('z-hide');
				
			});
  </script>
    <script>
	  //提交数据
	    $('form').submit(function(e){
	        e.preventDefault();
	        youyibao.httpSend($(this),'post',1);
	    });
    </script>
  
    
    
@endsection

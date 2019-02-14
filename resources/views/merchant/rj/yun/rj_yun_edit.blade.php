<?php if($_GET['game_type']=='1'){$title =  '编辑固定积分模式'; }else{$title ='编辑积分累计模式';}?>
@extends('admin.layouts.parent')
@section('page-title',$title )
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
		.row{margin-right: 0px!important; margin-left: 0px!important;}
		select.input-sm{ height:33px; }

	</style>


	<div class="row" >
		<form id="form1" action="{{route('business.rj_yun_edit')}}" method="post">
		{{ csrf_field() }}
		<!--创建赛程-->
			<div class="z_border_b z_font18 z_color_red z_lineh40 z_mb10 font_weight">
				{{$title}}
				<input type="hidden" name="id" value="{{$first['id']}}">
				<input type="hidden" name="game_type" value="{{$_GET['game_type']}}">
			</div>
			<!--创建赛程 end-->

			<!-- 赛程一  start-->
			<div class="z_mb10  posr border_padding">

				<div class="z_inputbox z_mt10 z_mb10 clearfix">
					<label class="z_lineh34 fl z_mr15 z_w100 tr "><span class="z_color_red">*</span>活动名称:</label>
					<input type="text" class="z_w200 form-control"  name="name" value="{{$first['name']}}" placeholder="请输入活动名称" >
				</div>
				<div class="z_inputbox z_mt10 z_mb10 clearfix">
					<label class="z_lineh34 fl z_mr15 z_w100 tr "><span class="z_color_red">*</span>活动规则:</label>
					<textarea rows="3" cols="20"  class="z_w200 form-control" name="rule"  placeholder="请输入活动规则" >{{$first['rule']}}</textarea>
				</div>
				@if($_GET['game_type']=='1')
					<div class="game_type_one"><!--固定模式-->
						<div class="z_inputbox z_mt10 z_mb10 clearfix">
							<label class="z_lineh34 fl z_mr15 z_w100 tr ">开奖模式:</label>
							<select class="z_w120 input-sm form-control input-s-sm inline prize_value" name="lottery_type" onchange='lotteryType(this);'>
								<option value="1" <?php if($first['lottery_type']=='1') echo 'selected';?> >周期模式</option>
								<option value="2" <?php if($first['lottery_type']=='2') echo 'selected';?> >自定义时间</option>
							</select>
						</div>
						<div class="border_padding z_mb10 lottery_type_1  <?php if($first['lottery_type']=='2') echo 'hide';?> ">
							<div class="z_inputbox z_mt10 z_mb10 clearfix ">
								<label class="z_w120-tr z_mr10 z_fontb">开奖周期:</label>
								<label class="z_mr10">
									<input type="radio" name="lottery_cycle" value="1" @if($first['lottery_cycle']=='1') checked @endif >日
								</label>
								<label class="z_mr10">
									<input type="radio" name="lottery_cycle" value="2" @if($first['lottery_cycle']=='2') checked @endif >周
								</label>
								<label class="z_mr10">
									<input type="radio" name="lottery_cycle" value="3" @if($first['lottery_cycle']=='3') checked @endif >月
								</label>
							</div>
							<div class="z_inputbox z_mt10 z_mb10 clearfix ">
								<label class="z_w120-tr z_mr10 z_fontb fl">奖励次数:</label>
								<input type="number" class="z_w120 form-control" name="total_awards[0]" value="{{$first['total_awards']}}">
							</div>
							<div class="z_inputbox z_mt10 z_mb10 clearfix ">
								<label class="z_w120-tr z_mr10 z_fontb fl">活动开始时间:</label>
								<input type="text" class="z_w200 form-control" name="start_time_one[0]" id="get_start_one" value="{{$first['start_time']?date('Y-m-d H:i:s',$first['start_time']):''}}" placeholder="请选择开始时间">
							</div>
						</div>
						<div class="border_padding z_mb10 lottery_type_2  <?php if($first['lottery_type']=='1') echo 'hide';?> ">
							<div class="z_inputbox z_mt10 z_mb10 clearfix ">
								<label class="z_w120-tr z_mr10 z_fontb fl">自定义活动时间:</label>
								<input type="text"  class="z_w200 form-control fl" name="start_time" id="get_start" value="{{$first['start_time']?date('Y-m-d H:i:s',$first['start_time']):''}}" placeholder="请选择开始时间">
								<span class="fl">至</span>
								<input type="text"  class="z_w200 form-control " name="end_time" id="get_end" value="{{$first['end_time']?date('Y-m-d H:i:s',$first['end_time']):''}}" placeholder="请选择结束时间">
							</div>
						</div>
					</div>
				@else
					<div class="game_type_two  ">
						<div class="z_inputbox z_mt10 z_mb10 clearfix ">
							<label class="z_w120-tr z_mr10 z_fontb fl">奖励次数:</label>
							<input type="number" class="z_w120 form-control" name="total_awards[1]" value="{{$first['total_awards']}}">
						</div>
						<div class="z_inputbox z_mt10 z_mb10 clearfix ">
							<label class="z_w120-tr z_mr10 z_fontb fl">活动开始时间:</label>
							<input type="text" class="z_w200 form-control" name="start_time_one[1]" id="get_start_two" value="{{$first['start_time']?date('Y-m-d H:i:s',$first['start_time']):''}}" placeholder="请选择开始时间">
						</div>
						<div class="z_inputbox z_mt10 z_mb10 clearfix ">
							<label class="z_w120-tr z_mr10 z_fontb fl">云积分初始值:</label>
							<input type="number" class="z_w120 form-control" name="initial" value="{{$first['initial']}}">
						</div>
						<div class="z_inputbox z_mt10 z_mb10 clearfix ">
							<label class="z_w120-tr z_mr10 z_fontb fl">云积分增值:</label>
							<input type="number" class="z_w120 form-control" name="increase" value="{{$first['increase']}}">
						</div>
						<div class="z_inputbox z_mt10 z_mb10 clearfix ">
							<label class="z_w120-tr z_mr10 z_fontb fl">云积分增值币数:</label>
							<input type="number" class="z_w120 form-control" name="coin" value="{{$first['coin']}}">
						</div>
						<div class="z_inputbox z_mt10 z_mb10 clearfix ">
							<label class="z_w120-tr z_mr10 z_fontb fl">云积分封顶值:</label>
							<input type="number" class="z_w120 form-control" name="cap" value="{{$first['cap']}}}">
						</div>
					</div>
				@endif
				<div class="z_inputbox z_mb10 clearfix">
					<label class="z_lineh34 fl z_mr15 z_w100 tr"><span class="z_color_red">*</span>参与门店:</label>
					<span class="z_cursor js-addstore fl" data-value="2">
					<img src="/admin/img/u294.png" width="29" height="29"/>
				</span>
				</div>
				<div class="  z_mb20 margin_left_50 js-z_store">
					@foreach($first['bususer_item_id'] as $key=>$val)
						<div class="clearfix z_pb10 brand_store_id_{{$val['id']}}" >
							<span class="fl z_ptb5"> {{$val['name']}}：</span>

							@foreach($val['store'] as $kk=>$vv)
								<div class="fl z_border z_ml10 z_border_ra-5 z_mb5" style="padding: 5px 2px;">
									{{$vv['name']}} <span class="z_color_red del_id" style="cursor:pointer ">X</span>
									<input type="hidden" name="select_store_id[]" value="{{$vv['id']}}">
								</div>
							@endforeach
						</div>
					@endforeach
				</div>
				<div class="z_inputbox z_mb10 clearfix">
					<label class="z_lineh34 fl z_mr15 z_w100 tr"><span class="z_color_red">*</span>添加机台:</label>
					<span class="z_cursor js-addjixing fl" data-value="2">
					<img src="/admin/img/u294.png" width="29" height="29"/>
				</span>
				</div>
				<div class="  z_mb20 margin_left_50 ">
					<div class="clearfix z_pb10 js-z_table2">
						@foreach($first['machine_item_id'] as $m_key=>$m_val)
							<div class="fl z_border z_ml10 z_border_ra-5 z_mb5" style="padding: 5px 2px;">
								{{$m_val['m_name']}} <span class="z_color_red del_id" style="cursor:pointer ">X</span>
								<input type="hidden" name="select_machine_id[]" value="{{$m_val['m_id']}}">
							</div>
						@endforeach
					</div>
				</div>
				<div class="z_inputbox z_mb10 clearfix">
					<label class="z_lineh34 fl z_mr15 z_w100 tr"><span class="z_color_red">*</span>奖品设置:</label>
					<span class="z_cursor js-z_add fl"  data-value="2">
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
						@foreach($first['prize_item_id'] as $p_key=>$p_val)
							<tr>
								<input type="hidden" name="data[prize][{{$p_key}}][id]" value="{{$p_val['id']}}" >
								<td>
									<select class="z_w120 input-sm form-control input-s-sm inline prize_value" name="data[prize][{{$p_key}}][option]" onchange='btnChange(this);'>
										@if($first['game_type'] == '2')
											<option value="2" @if($p_val['type']=='2') selected @endif >奖票</option>
										@else
											<option value="1" @if($p_val['type']=='1') selected @endif >线下礼品</option>
											<option value="2" @if($p_val['type']=='2') selected @endif >奖票</option>
											{{--<option value="3" @if($p_val['type']=='3') selected @endif >积分</option>--}}
										@endif
									</select>
								</td>
								<td><input type="text" class="z_w100 z_mr10" name="data[prize][{{$p_key}}][name]"  value="{{$p_val['item_name']}}" ></td>
								<td>
									<div class=" z_mtb10 z_mlrauto gallery-picker  {{$p_val['itme_img']?'hide':''}} " data-value="1232342131" >
										<div class="bgwh">
											上传<input type="hidden" name="data[prize][{{$p_key}}][pic]" value="{{$p_val['itme_img']?$p_val['itme_img']:''}}" />
										</div>
									</div>
									<div  style="position: relative;" class="{{$p_val['itme_img']?'':'hide'}} ">
										<img class="z_fix_img z_mtb10 z_mlrauto" src="{{$p_val['itme_img']?$p_val['itme_img']:''}}">
										<span class="z_off js-z_off"></span>
									</div>
								</td>
								<td><input type="text" name="data[prize][{{$p_key}}][num]" value="{{$p_val['num']}}" ></td>
								<td>
									<div class="bar-white z_border " style="width: 120px; background:#fff;     margin: 0 auto; ">
										<input value="@if(!empty($p_val['rank'])){{$p_val['rank']}}@else{{0}}@endif"
											   name="data[prize][{{$p_key}}][ranking]"
											   type="hidden" placeholder=""
											   class="z_input ranking"
										/>
										<div class="z_select_div  js-z_select_div">
											<p class="z_color_3">
												@if(!empty($p_val['rank'])) NO-{{$p_val['rank']}} @else 请选择 @endif
											</p>
											<ul class="z-hide js-z_select_ul">
												@for($i=1;$i<=50;$i++)
													<li data-value = "{{$i}}">NO-{{$i}}</li>
												@endfor
												{{--<li data-value = "1">NO-1</li>--}}
												{{--<li data-value = "2">NO-2</li>--}}
												{{--<li data-value = "3">NO-3</li>--}}
												{{--<li data-value = "4">NO-4</li>--}}
												{{--<li data-value = "5">NO-5</li>--}}
												{{--<li data-value = "6">NO-6</li>--}}
												{{--<li data-value = "7">NO-7</li>--}}
												{{--<li data-value = "8">NO-8</li>--}}
												{{--<li data-value = "9">NO-9</li>--}}
												{{--<li data-value = "10">NO-10</li>--}}
											</ul>
											<span class="z_tubiao js-z_tubiao">
												<!--图标-->
											</span>
										</div>
									</div>
								</td>

								<td>
									<span class="btn btn-white btn-sm z_color_red js-z_off2" >删除</span>
								</td>
							</tr>
						@endforeach
						</tbody>
					</table>
				</div>
			</div>
			<!--赛程一 end-->

			<div class="z_inputbox z_mb10 z_mb30 tc">
				<button class="btn z_bar-blue z_color_white z_w100  z_mt40 font_weight" type="submit">保存</button>
			</div>

		</form>

	</div>
	<!--阴影和弹出框-->
	<div class="shadow" style=""> </div>
	<!-- 选择门店  开始 -->
	<div class="z_tankuan z_border  z_w776 hide js-addstorebox" style="top: 60px;">
		<span class="z_off2 js-z_off3"></span>
		<div class="clearfix">
			<div class="clearfix col-sm-12 z_bar-orange z_ptb20">
				<form id="form1" action="" method="get">
					<input type="hidden" name="bus_user_id" value="">
					<div class="col-sm-10 m-b-xs z_pt10">
						<div class="clearfix z_mb10">
							<div class="z_inputbox fl z_mr30">
								<label>品牌：</label>
								<select class="z_w120 input-sm form-control input-s-sm inline prize_value" name="brand_name" >
									<option value="0" >不选择</option>
									@foreach($barnd as $barnd_key=>$barnd_list)
										<option value="{{$barnd_key}}" >{{$barnd_list}}</option>
									@endforeach
								</select>
							</div>
							<div class="z_inputbox fl z_mr30">
								<label>门店：</label>
								<input type="text" class="z_w120 input-sm form-control input-s-sm inline prize_value" name="stores_name" value="" />
							</div>
						</div>
					</div>
					<div class="col-sm-1 tc " style="width: 80px;    margin-top: 10px;">
						<a class="btn z_bar-blue btn-xs z_color_white z_w60 z_mb5" onclick="search_store();" >查询</a>
					</div>
				</form>
			</div>
			<div class="clear"></div>
			<div class="z_mtb20 z_border" style=" max-height: 460px; overflow-y: auto;">
				<table class="z_table2 z_stroe js-z_stroe_select">
					<thead>
					<tr>
						<th ><input type="checkbox" name="checkbox[]" class="checkbox" onclick="allSelect(this,'store_id');" style="line-height: 50px; margin: 0px auto;" >选择门店</th>
						<th class="">品牌</th>
						<th class="">门店</th>
					</tr>
					</thead>
					<tbody>
					<!-- 门店列表 -->

					</tbody>
				</table>
			</div>
			<div class="z_inputbox z_mb10 z_mb30 tc">
				<a href="javascript:void(0);" class="btn z_bar-blue  z_color_white z_w100 js-z_store_add">添加</a>
			</div>
		</div>
	</div>
	<!-- 选择门店  结束 -->

	<!-- 选择机台  开始 -->
	<div class="z_tankuan z_border  z_w776 hide js-addjixingbox" style="top: 60px;">
		<span class="z_off2 js-z_off3"></span>
		<div class="clearfix">
			<div class="clearfix col-sm-12 z_bar-orange z_ptb20">
				<form id="form1" action="" method="get">
					<input type="hidden" name="bus_user_id" value="">
					<div class="col-sm-10 m-b-xs z_pt10">
						<div class="clearfix z_mb10">
							<div class="z_inputbox fl z_mr30">
								<label>机台名称：</label>
								<input type="text" name="m_name" value="" />
							</div>
						</div>
					</div>
					<div class="col-sm-1 tc " style="width: 80px;    margin-top: 10px;">
						<a class="btn z_bar-blue btn-xs z_color_white z_w60 z_mb5" onclick="search_machint();" >查询</a>
					</div>
				</form>
			</div>
			<div class="clear"></div>
			<div class="z_mtb20 z_border" style=" max-height: 460px; overflow-y: auto;">
				<table class="z_table2 js-z_table2_select">
					<thead>
					<tr>
						<th ><input type="checkbox" name="checkbox[]" class="checkbox" onclick="allSelect(this,'machint_id');" style="line-height: 50px; margin: 0px auto;" >选择机台</th>
						<th class="">机台名称</th>
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
	<!-- 选择机台  结束 -->

	<script type="text/javascript" src="/admin/js/webuploader.min.js"></script>
	<script src="/admin/js/template.js"></script>
	<script>
        var _token=$('input[name="_token"]').val();
	</script>



	<!-- 添加奖品 -->
	<script id="list"  type="text/html">
		<tr>
			<td>
				<select class="z_w120 input-sm form-control input-s-sm inline prize_value" name="data[prize][<!--{prize_key}-->][option]" onchange='btnChange(this);'>
					@if($first['game_type'] == 2)
						<option value="2" >奖票</option>
					@else
						<option value="1">线下礼品</option>
						<option value="2" >奖票</option>
					@endif
					{{--<option value="3">积分</option>--}}
				</select>
			</td>
			<td><input type="text" class="z_w100 z_mr10" name="data[prize][<!--{prize_key}-->][name]" ></td>
			<td>
				<div class=" z_mtb10 z_mlrauto gallery-picker " data-value="1232342131"  >
					<div class="bgwh">
						上传<input type="hidden" name="data[prize][<!--{prize_key}-->][pic]" value="" />
					</div>
				</div>
				<div  style="position: relative;" class="hide">
					<img class="z_fix_img z_mtb10 z_mlrauto" src="/admin/img/profile_small.jpg">
					<span class="z_off js-z_off"></span>
				</div>
			</td>
			<td><input type="text" name="data[prize][<!--{prize_key}-->][num]" value="1" ></td>

			<td>
				<div class="bar-white z_border " style="width: 120px; background:#fff;     margin: 0 auto; ">
					<input  value="0"
							name="data[prize][<!--{prize_key}-->][ranking]"
							type="hidden" placeholder="" class="z_input ranking"
					/>
					<div class="z_select_div  js-z_select_div">
						<p class="z_color_3">
							请选择
						</p>
						<ul class="z-hide js-z_select_ul">
							@for($i=1;$i<=50;$i++)
								<li data-value = "{{$i}}">NO-{{$i}}</li>
							@endfor
							{{--<li data-value = "1">NO-1</li>--}}
							{{--<li data-value = "2">NO-2</li>--}}
							{{--<li data-value = "3">NO-3</li>--}}
							{{--<li data-value = "4">NO-4</li>--}}
							{{--<li data-value = "5">NO-5</li>--}}
							{{--<li data-value = "6">NO-6</li>--}}
							{{--<li data-value = "7">NO-7</li>--}}
							{{--<li data-value = "8">NO-8</li>--}}
							{{--<li data-value = "9">NO-9</li>--}}
							{{--<li data-value = "10">NO-10</li>--}}
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


	<!-- 选择门店 -->
	<script id="store_list" type="text/html">
		<!--{each list as val kk}-->
		<tr>
			<td>
				<input type="checkbox" name="store_id[]" value="<!--{val.store_id}-->" />
			</td>
			<td><!--{ if val.brand_name }-->
				<!--{val.brand_name}-->
				<!--{else}-->
				--
				<!--{/if}-->
			</td>
			<td><!--{val.store_name}--></td>
		</tr>
		<!--{/each}-->
	</script>
	<!-- 选中的门店 -->
	<script id="store_list_select" type="text/html">
		<!--{each list as val}-->
		<div>

		</div>
		<!--{/each}-->
	</script>

	<!-- 选择机台 -->
	<script id="mechint_list" type="text/html">
		<!--{each list as val}-->
		<tr>
			<td>
				<input type="checkbox" name="machint_id[]" value="<!--{val.m_id}-->" />
			</td>
			<td><!--{val.m_name}--></td>
		</tr>
		<!--{/each}-->
	</script>

	<!-- 选中的机台 -->
	<script id="mechint_list_select" type="text/html">
		<div class="fl z_border z_ml10 z_border_ra-5 z_mb5" style="padding: 5px 2px;">
			<!--{m_name}--> <span class="z_color_red del_id" style="cursor:pointer ">X</span>
			<input type="hidden" name="select_machine_id[]" value="<!--{m_id}-->">
		</div>
	</script>

	<!--添加选中门店到html中-->
	<script id="js-z_store_add_lists" type="text/html">
		<!--{each store as val}-->
		<div class="fl z_border z_ml10 z_border_ra-5 z_mb5" style="padding: 5px 2px;">
			<!--{val.name}--> <span class="z_color_red del_id" style="cursor:pointer ">X</span>
			<input type="hidden" name="select_store_id[]" value="<!--{val.id}-->">
		</div>
		<!--{/each}-->
	</script>

	<script>
        //提交数据
        $('form').submit(function(e){
            e.preventDefault();
            youyibao.httpSend($(this),'post',1);
        });

        //积分模式
        function gameType(obj){
            var _val = $(obj).val();
            console.log(_val);
            if(_val=='1'){
                $('.game_type_one').removeClass('hide');
                $('.game_type_two').addClass('hide');
            }else{
                $('.game_type_one').addClass('hide');
                $('.game_type_two').removeClass('hide');
            }
        }

        //固定模式 开奖模式
        function lotteryType(obj){
            var _val = $(obj).val();
            console.log(_val);
            if(_val=='1'){
                $('.lottery_type_1').removeClass('hide');
                $('.lottery_type_2').addClass('hide');
            }else{
                $('.lottery_type_1').addClass('hide');
                $('.lottery_type_2').removeClass('hide');
            }
        };


	</script>

	<script>
        function addstore_ajax(data){
            var index = layer.load(0, {shade: false});
            $.post('{{route("business.rj_yun_storeLists")}}',data,function(res){
                if(res.code=='200'){
                    data={'list':res.data};
                    var html =template('store_list',data);
                    $('.js-z_stroe_select').children('tbody').html(html);
                    //				layer.msg(res.msg,{icon:1});

                    //出现位置
                    var _h = $(window).scrollTop();+30;
                    $(".js-addstorebox").css('top',_h);

                    //阴影显示
                    $(".js-addstorebox").removeClass("hide");
                    $(".shadow").show();

                }else{
                    layer.msg(res.msg,{icon:5});
                }
                layer.closeAll('loading');
            })

        }
        //获取门店
        $('body').delegate('.js-addstore','click',function(){
            var selectID = ''; //之前选中的门店
            $('input[name="select_store_id[]"]').each(function(){
                selectID+= $(this).val()+',';
            });
            console.log(selectID);

            data={'arr_store_id':selectID,'_token':_token};
            addstore_ajax(data);
        });
        //搜索门店
        function search_store(){
            var brand_id = $('select[name="brand_name"]').val();
            var store_name = $('input[name="stores_name"]').val();

            console.log(brand_id);
            console.log(store_name);

            var selectID = ''; //之前选中的门店
            $('input[name="select_store_id[]"]').each(function(){
                selectID+= $(this).val()+',';
            });
            console.log(selectID);

            data={'_token':_token,'arr_store_id':selectID,'brand_id':brand_id,'store_name':store_name};


            addstore_ajax(data);
        }


        //添加选中的门店
        $('body').delegate('.js-z_store_add','click',function(){
            var index = layer.load(0, {shade: false});

            var strID='';	//本次选中的门店
            $('input[name="store_id[]"]:checked').each(function(){
                strID+= $(this).val()+',';
            });
            console.log(strID);

            $.post('{{route("business.rj_yun_brand_store")}}',{'arr_store_id':strID,'_token':_token},function(res){
                if(res.code=='200'){
                    var _data = res.data;

                    $.each(_data,function(index,value){
                        var _res = $('.js-z_store').children('div').hasClass('brand_store_id_'+value.id);
                        if(_res==false){
                            var _store_html = '<div class="clearfix z_pb10 brand_store_id_'+value.id+'" > <span class="fl z_ptb5">'+ value.name+'：</span></div>';
                            $('.js-z_store').append(_store_html);
                        }

                        data = {'store':value.store};
                        var html =template('js-z_store_add_lists',data);
                        $('.brand_store_id_'+value.id).append(html);

                    });

                    //阴影显示
                    $(".js-addstorebox").addClass("hide");
                    $(".shadow").hide();

                }else{
                    layer.msg(res.msg,{icon:5});
                }
            })
            layer.closeAll('loading');
        });


	</script>



	<script type="text/javascript">
        function machint_ajax(m_name){
            var index = layer.load(0, {shade: false});

            var selectID = ''; //之前选中的门店
            $('input[name="select_store_id[]"]').each(function(){
                selectID+= $(this).val()+',';
            }); //console.log(selectID);

            if(selectID==''){
                layer.alert('请先选择门店');  layer.closeAll('loading'); return false;
            }

            //      selectID = '3099,3454,3589,';

            //之前选中的机台
            var select_machine_id = '';
            $('input[name="select_machine_id[]"]').each(function(){
                select_machine_id+= $(this).val()+',';
            }); //console.log(select_machine_id);


            data={'arr_store_id':selectID,'arr_machine_id':select_machine_id,'m_name':m_name,'_token':_token};

            $.post('{{route("business.rj_yun_machine_list")}}',data,function(res){
                if(res.code=='200'){
                    data={'list':res.data};
                    var html =template('mechint_list',data);
                    //  console.log(html);
                    $('.js-z_table2_select').children('tbody').html(html);

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

            layer.closeAll('loading');
        }

        //获取机台
        $("body").delegate(".js-addjixing",'click',function(){
            machint_ajax('');
        });

        //搜索机台
        function search_machint(){
            var m_name = $('input[name="m_name"]').val();			//机台名臣

            machint_ajax(m_name);
        };



        //添加选中的机台
        $('body').delegate('.js-z_table2_add','click',function(){

            var strID='';	//选中的机台放入html中
            $('input[name="machint_id[]"]:checked').each(function(){

                var m_name = $(this).parent('td').next('td').html();
                data={'m_id':$(this).val(),'m_name':m_name};
                var html =template('mechint_list_select',data);
                //  console.log(html);
                $('.js-z_table2').append(html);

            });


            //阴影显示
            $(".js-addjixingbox").addClass("hide");
            $(".shadow").hide();

        });


	</script>

	<script type="text/javascript">

        //机台门店 通用删除
        $('body').delegate('.del_id','click',function(){
            var _obj = this;
            layer.confirm('确定要删除吗？', {
                btn: ['确定','取消']
            }, function(){
                layer.closeAll('dialog');
                $(_obj).parent('div').remove();
            }, function(){
                return true;
            });
        })

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
        juzhong(".js-addjixingbox");

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
        //添加奖品 js-z_add
        $('body').delegate(".js-z_add",'click',function(){
            //获取当前赛程下标
            var schedule_num =1;
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
            var base = [
                "1","2","3","4","5","6","7","8","9","10",
                "11","12","13","14","15","16","17","18","19","20",
                "21","22","23","24","25","26","27","28","29","30",
                "31","32","33","34","35","36","37","38","39","40",
                "41","42","43","44","45","46","47","48","49","50",
                "51"
            ];
            var ranking = new Array();
            var z_input = $(obj).parent().parent().find('.ranking');
            var _val = z_input.val();		//当前值
//	console.log('_val '+_val);

            var $select_val = $(obj).parent().parent().parent().parent().parent('tbody');

            var prize_value = $(obj).parent().parent().parent().parent().find('.prize_value').val();	//奖品类型
//	console.log('prize_value '+prize_value);

            $select_val.find('.ranking').each(function(){
//                var prize_val = $(this).parent().parent().parent().find('.prize_value').val();
//                if(prize_val==prize_value){
//                    ranking.push($(this).val());
//                }
                ranking.push($(this).val());
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

        //全选与不全选
        function allSelect(obj,name) {
            if ($(obj).attr("checked") != "checked") {
                $(obj).attr("checked", "checked");
                $('input[name="'+name+'[]"]').prop("checked", "checked");
            }else {
                $(obj).removeAttr("checked");
                $('input[name="'+name+'[]"]').removeAttr("checked");
            }
        }

	</script>
	<script src="/admin/js/plugins/layer/laydate/laydate.js"></script>
	<script type="text/javascript">
            <?php if($_GET['game_type']=='2'){?>
        var get_start_two = {
                elem: "#get_start_two",
                format: "YYYY-MM-DD hh:mm:ss",
                //       min: laydate.now(),
                max: "2099-06-16 23:59:59",
                istime: true,
                istoday: false,
            };
        laydate(get_start_two);
            <?php }else{ ?>

        var get_start_one = {
                elem: "#get_start_one",
                format: "YYYY-MM-DD hh:mm:ss",
                //       min: laydate.now(),
                max: "2099-06-16 23:59:59",
                istime: true,
                istoday: false,
            };
        laydate(get_start_one);


        var get_start = {
            elem: "#get_start",
            format: "YYYY-MM-DD hh:mm:ss",
            //        min: laydate.now(),
            max: "2099-06-16 23:59:59",
            istime: true,
            istoday: false,
            choose: function(datas) {
                get_end.start = datas
            }
        };
        var get_end = {
            elem: "#get_end",
            format: "YYYY-MM-DD hh:mm:ss",
            //       min: laydate.now(),
            max: "2099-06-16 23:59:59",
            istime: true,
            istoday: false,
            choose: function(datas) {
                get_start.max = datas
            }
        };
        laydate(get_start);
        laydate(get_end);
        <?php }?>
	</script>


@endsection

@extends('admin.layouts.parent')
@section('page-title','云活动详情')
@section('main')

	<link href="/admin/css/plugins/datapicker/datepicker3.css" rel="stylesheet">
	<link href="/admin/css/new-add.css" rel="stylesheet">
	<style type="text/css">
		body{font-size:12px;font-family: "微软雅黑";color: #666;}
		a{text-decoration: none;}
		a:active,a:focus,a:visited{outline:none;text-decoration:none;}
		img{border: none; vertical-align:middle;}
		ul,ol{list-style: none;}
		table{border-collapse: collapse;}
		em,i{font-style:normal;}
		.clearfix:after{content: "";clear: both;display: block;height: 0;zoom: 1;}
		.z_mb10{margin-bottom: 10px;}

		.z_nav_a li{min-width: 60px;padding: 0px 10px;border: 1px solid #ccc;border-radius: 5px;cursor: pointer;line-height: 40px;text-align: center;font-size: 14px;color: #333;margin-right: 10px;margin-bottom: 10px;float: left;display: inline-block;}
		.z_nav_a li:last-child{margin-right: 0px;}
		.z_nav_a li.active{border:1px solid red;color: #F00B00;}
		.z_box{display: none;}
		.z_bold,.z_bold th{    font-size: 14px; font-weight: normal; 	margin-bottom: 0px; font-weight: bold; }
	</style>

	<div class="wrapper wrapper-content animated fadeInUp">
		<div class="row">
			<form action="{{ route('business.rj_yun_details') }}" method="get">
				<div class="clearfix z_mb10">
					<div class="z_inputbox fl z_mr30">
						<input type="hidden" name="id" value="{{$_GET['id']}}">
						<label>活动次数:</label>
						<select name="frequency" class="z_w172 z_h24 input-sm form-control input-s-sm inline" >
							<option value="0" @if($frequency == 0) selected @endif >0</option>
							@for($i=1;$i<=$info['total_awards'];$i++)
								<option value="{{$i}}" @if($frequency == $i) selected @endif >{{$i}} </option>
							@endfor
						</select>
						<button class="btn z_bar-blue btn-xs z_color_white z_w60 z_mb5" type="submit">查询</button>
					</div>
				</div>
			</form>
			<div class="z_border_t z_mt10 z_mb30 posr">
				<div class="z_inputbox z_mt10 z_mb10 clearfix">
					<label class="z_lineh34 fl z_mr15 z_w100 tr"><span class="z_color_red">*</span>活动名称:</label>
					<span class="z_lineh34 fl " >{{$info['name']}}</span>
				</div>
				<div class="z_inputbox z_mb10 clearfix">
					<label class="z_lineh34 fl z_mr15 z_w100 tr"><span class="z_color_red">*</span>活动时间:</label>
					@if(!empty($info['end_time']))
						<span class="z_lineh34 fl " >{{date('Y-m-d H:i:s',$info['start_time'])}}&nbsp;至&nbsp;{{date('Y-m-d H:i:s',$info['end_time'])}}</span>
					@else
						<span class="z_lineh34 fl " >{{date('Y-m-d H:i:s',$info['start_time'])}}</span>
					@endif
				</div>
				<div class="z_inputbox z_mb10 clearfix">
					<label class="z_lineh34 fl z_mr15 z_w100 tr"><span class="z_color_red">*</span>活动状态:</label>
					@if(!empty($info['activity_type'] == 1))
						<span class="z_lineh34 fl " >开启</span>
					@else
						<span class="z_lineh34 fl " >关闭</span>
					@endif
				</div>
				<div class="z_inputbox z_mb10 clearfix">
					<label class="z_lineh34 fl z_mr15 z_w100 tr"><span class="z_color_red">*</span>总奖次数:</label>
					<span class="z_lineh34 fl " >{{$info['total_awards']}}</span>
				</div>
				<div class="z_inputbox z_mb10 clearfix">
					<label class="z_lineh34 fl z_mr15 z_w100 tr"><span class="z_color_red">*</span>已奖次数:</label>
					<span class="z_lineh34 fl " >{{$info['execute_awards']}}</span>
				</div>
				@if(!empty($info['game_type'] == 2))
					<div class="z_inputbox z_mb10 clearfix">
						<label class="z_lineh34 fl z_mr15 z_w100 tr"><span class="z_color_red">*</span>累积值:</label>
						<span class="z_lineh34 fl " >{{$info['accumulate_num']}}</span>
					</div>
					<div class="z_inputbox z_mb10 clearfix">
						<label class="z_lineh34 fl z_mr15 z_w100 tr"><span class="z_color_red">*</span>当次投币累积数:</label>
						<span class="z_lineh34 fl " >{{$info['accumulate_coin']}}</span>
					</div>
				@endif
				@if($info['game_type'] == 1)
				<!--兑换奖品按钮-->
					{{ csrf_field() }}
					<a onClick="push_code(this)" class=" btn z_bar-blue z_color_white  posa z_top-30  z_font14 " style="display:none; right: 120px;">
						推送兑换码
					</a>
					<a href="javascript:void(0);" class=" btn z_bar-blue z_color_white  posa z_top-30 z_right-30 z_font14 js-duhuan_btn">
						兑换奖品
					</a>
					<!--兑换奖品按钮 end-->
				@endif
			</div>
			<!--单体列表-->
			@if(!empty($YunGameRankList))
				<div class="z_border_t">
					<!--表格-->
					<div class="z_mb10">
						<table class="z_table3 z_w60p z_mb30 z_ml20">
							<thead>
							<tr class="z_border_b">
								<th>排名</th>
								<th>用户</th>
								<th>手机号</th>
								<th>分数/奖票</th>
								<th>奖励类型</th>
								<th>奖励名称</th>
								<th>奖励数量</th>
								<th>兑换状态</th>
							</tr>
							</thead>
							<tbody >
							@foreach($YunGameRankList as $key=>$val)
								<tr>
									<td>{{$val['rank']}}</td>
									<td>{{$val['username']}}</td>
									<td>{{$val['mobile']}}</td>
									<td>{{$val['sum']}}</td>
									<td>{{$val['prize_type']}}</td>
									<td>{{$val['prize_item_name']}}</td>
									<td>{{$val['prize_sum']}}</td>
									@if($val['prize_type'] == '线下礼品')
										@if($val['exchange'] == 1)
											<td>已兑换</td>
										@else
											<td>未兑换</td>
										@endif
									@else
										<td>- -</td>
									@endif
								</tr>
							@endforeach
							</tbody>
						</table>
					</div>
					<!--表格 end-->
				</div>

		@endif
		<!--单体列表 end-->
		</div>
	</div>
	<!--阴影和弹出框-->
	<div class="shadow" style=""> </div>
	<!--兑换-->
	<div class="z_tankuan z_border  z_w420 hide js-duhuan" style="top: 60px;">
		<span class="z_off2 js-z_off3"></span>
		<div class="z_border_b z_lineh40 z_pl16">
			兑换奖品
		</div>
		<div class="z_mt50 z_mb50">
			<input type="text" name="code" placeholder="输入兑换码" class="z_w200 form-control z_mlrauto tc" >
		</div>
		<div class="tc">
			<a href="javascript:void(0);" onClick='exchange(this)' class="btn z_bar-blue z_color_white z_w120 z_mb20 z_mlrauto">兑换</a>
		</div>
	</div>
	<!--兑换 end-->
	<script src="js/jquery.min.js?v=2.1.4"></script>
	<script src="js/bootstrap.min.js?v=3.3.6"></script>
	<script src="js/plugins/peity/jquery.peity.min.js"></script>
	<script src="js/content.min.js?v=1.0.0"></script>
	<script src="js/template.js"></script>


	<script type="text/javascript" >
        //导航 js-z_nav_a
        $(".js-z_nav_a li").click(function(){
            var _index =$(this).index();
            $(this).addClass("active").siblings().removeClass("active");
            $(this).parent().parent().next().find(".z_box").hide().eq(_index).show();
        });
        var _token=$('input[name="_token"]').val();
        function exchange(){
            var url = "{{ route('business.rj_yun_code') }}";
            var code = $('input[name="code"]').val();
            if(code == ''){
                layer.msg('请传入兑换码');
                return false;
            }
            var mydata = {code:code,'_token':_token}
            console.log(url);
            console.log(mydata);
            $.ajax({
                url : url,
                dataType:"json",
                data:mydata,
                type : 'post',
                timeout: 5000,
                success : function(res){
                    if(res.code=='200'){
                        layer.msg(res.msg,{icon:1},function(){
                            location.reload();
                        });
                    }else{
                        layer.msg(res.msg,{icon:5});
                    }
                },
                error: function (XMLHttpRequest, textStatus, errorThrown){
                    layer.msg('ajax格式出错    '+textStatus,{icon:5});
                }
            });
        }
        //关闭
        $(".js-z_off3").click(function(){
            $(this).parent().addClass("hide");
            //阴影
            $(".shadow").hide();
        });
        //详情 js-duhuan
        function duhuan(){
            $(".js-duhuan_btn").click(function(){
                $(".js-duhuan").removeClass("hide");
                //阴影显示
                $(".shadow").show();
            });
        }
        duhuan();
        //左右剧中定位
        function juzhong(b){
            var w2 =$("body").width();
            var w =$(b).width();
            var w3 =(w2-w)/2;
            $(b).css("left",w3);
        };
        juzhong(".js-duhuan")
	</script>

@endsection

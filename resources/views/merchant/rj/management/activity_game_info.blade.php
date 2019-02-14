@extends('admin.layouts.parent')
@section('page-title','活动详情')
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
	        	<div class="z_border_t z_mt10 z_mb30 posr">
	        		<div class="z_inputbox z_mt10 z_mb10 clearfix">
	    				<label class="z_lineh34 fl z_mr15 z_w100 tr"><span class="z_color_red">*</span>活动名称:</label>
	    				<span class="z_lineh34 fl " >{{$activity_info['name']}}</span>
	    			</div>
	    			<div class="z_inputbox z_mb10 clearfix">
	    				<label class="z_lineh34 fl z_mr15 z_w100 tr"><span class="z_color_red">*</span>活动时间:</label>
	    				<span class="z_lineh34 fl " >{{date('Y-m-d H:i:s',$activity_info['start_time'])}}&nbsp;至&nbsp;{{date('Y-m-d H:i:s',$activity_info['end_time'])}}</span>
	    			</div>
	    			<div class="z_inputbox z_mb10 clearfix">
	    				<label class="z_lineh34 fl z_mr15 z_w100 tr"><span class="z_color_red">*</span>游戏名称:</label>
	    				<span class="z_lineh34 fl " > {{$activity_info['game_name']}}</span>
	    			</div>
	    			<!--兑换奖品按钮-->
	    			{{ csrf_field() }}
	    			<a onClick="push_code(this)" class=" btn z_bar-blue z_color_white  posa z_top-30  z_font14 " style="display:none; right: 120px;">	 
	    				推送兑换码 
	    			</a>
        			<a href="javascript:void(0);" class=" btn z_bar-blue z_color_white  posa z_top-30 z_right-30 z_font14 js-duhuan_btn">
        				兑换奖品
        			</a>
        				
        			<!--兑换奖品按钮 end-->
	        	</div>
				<!--单体列表-->
				@if(!empty($activity_info['scduhele']))
					@if($game_type=='1')
						@foreach($activity_info['scduhele'] as $k=>$v)

							<div class="z_border_t">
								<div class="z_font14 z_color_3 z_lineh40">
									<span class="z_mr10 z_ml20">赛程 ：</span><span >{{$v['name']}}</span>
								</div>
								<!--表格-->

								<div class="z_mb10">
									<table class="z_table3 z_w60p z_mb30 z_ml20">
										<thead>
										<tr class="z_border_b">
											<th class="tl">用户名称</th>
											<th class="z_w80">排名</th>
											<th>分数/彩票</th>
											<th>奖励奖品</th>
											<th>兑换状态</th>
										</tr>
										</thead>
										<tbody>
                                        <?php foreach($v['list'] as $l_key=>$l_val){ ?>
										<tr>
											<td class="tl">@if(!empty($l_val['nickname'])) {{$l_val['nickname']}} @else -- @endif</td>
											<td>{{$l_key+1}}</td>
											<td>{{$l_val['num']}}</td>
											<td>{{$l_val['pirze_item_name']}}</td>
											<td><span class="z_color_red">@if($l_val['exchange_type']==2) 已兑换 @elseif($l_val['exchange_type']==0) 未中奖  @else 未兑换  @endif</span></td>
										</tr>
                                        <?php } ?>
										</tbody>
									</table>
								</div>

								<!--表格 end-->
							</div>
						@endforeach

					@else
						@foreach($activity_info['scduhele'] as $k=>$v)
							<div class="z_border_t">
								<div class="z_font14 z_color_3 z_lineh40 z_bold">
									<span class="z_mr10 z_ml20 ">赛程 ：</span><span >{{$v['ScduheleName']}}</span>
								</div>
								<!--表格-->
								<div class="z_mb10">
									<ul class="clearfix z_nav_a js-z_nav_a">
                                        <?php $i=0; $j=0;?>
                                        <?php foreach($v['TeamRanking'] as $kkk=>$vvv){ ?>
										@if(empty($i))
											<li class="active">场次{{$kkk}}</li>
										@else
											<li >场次{{$kkk}}</li>
										@endif
                                        <?php $i++; } ?>
									</ul>
								</div>
								<!--盒子-->
								<div>
                                    <?php foreach($v['TeamRanking'] as $kkk=>$vvv){ ?>
									@if(empty($j))
										<div class="z_box" style="display: block;">
											@else
												<div class="z_box">
													@endif
													<div class="z_mb10 z_ml20">
                                                        <?php foreach($vvv as $kkey=>$vval){ ?>
														<div><span class="z_bold">团队名称：</span> {{$kkey}} &nbsp;&nbsp;<span class="z_bold">团队总分：</span> {{$vval['num']}} </div>
														<table class="z_table3 z_w60p z_mb30 z_ml30">
															<thead>
															<tr class="z_border_b z_bold" >
																<th class="tl">用户名称</th>
																<th class="z_w80">排名</th>
																<th>分数/彩票</th>
																<th>奖励奖品</th>
																<th>兑换状态</th>
															</tr>
															</thead>
															<tbody>
                                                            <?php foreach($vval['log'] as $kkkk=>$vvvv){ ?>
															<tr>
																<td>{{$vvvv['u_id'].'---'.$vvvv['id']}}</td>
																<td>{{$kkkk+1}}</td>
																<td>{{$vvvv['num']}}</td>
																<td></td>
																<td></td>
															</tr>
                                                            <?php $j++; } ?>
															</tbody>
														</table>
                                                        <?php  } ?>
													</div>
												</div>

                                                <?php $j++; } ?>
										</div>
								</div>
							@endforeach

							@endif
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
    <script type="text/javascript">
    var _token=$('input[name="_token"]').val();
    	function push_code(obj){
        	var url = "{{ route('business.push_redeem_code') }}";
			var mydata = {id:"{{$_GET['id']}}",'_token':_token}
			  $.ajax({
					url : url,
					dataType:"json", 
					data:mydata,
					type : 'post',
					timeout: 50000,
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

        function exchange(){
        	var url = "{{ route('business.Write_off_code') }}";
        	var code = $('input[name="code"]').val();
        	if(code==''){
				layer.msg('请传入兑换码');
				return false;
            }
			var mydata = {code:code,'_token':_token}
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
		//编辑详情 js-duhuan
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

	<script type="text/javascript" >
        //导航 js-z_nav_a
        $(".js-z_nav_a li").click(function(){
            var _index =$(this).index();
            $(this).addClass("active").siblings().removeClass("active");
            $(this).parent().parent().next().find(".z_box").hide().eq(_index).show();
        });
	</script>

@endsection

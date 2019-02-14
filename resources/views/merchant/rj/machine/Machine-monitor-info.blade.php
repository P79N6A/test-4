@extends('admin.layouts.parent')
@section('page-title','机台监控详情')
@section('main')

 <link href="/admin/css/new-add.css" rel="stylesheet">
<style>
.clearfix{ margin: 15px 0px;}
.m_start{color:#000; margin:15px 0px; font-size:20px;font-weight:bold;}
.m_start span{color:red;}
.expention{border-bottom: 1px solid #e0e0e0;    clear: both;  margin-top:10px;height:30px;}
.expention_left{float:left;width:120px;;display:inline-block;font-weight:bold; margin-left:120px;}
.expention_right{float:left;width:auto; margin-left:40px;}
.expention_right ul li{ position: relative;height:22px;}
.expention_right ul li span{     top: -3px;  right: -5px;}
</style>
   		<div class="row">
           <!--奖品设置-->
        	<div class="col-sm-12">
        		<div class="z_form">
        			<div class="m_start">
						当前机台状态：
						<span>
							<?php if(empty($log)){
                                echo '正常运行中';
                            }else{
                                if($log[0]['status']==1){
                                    echo '机台故障';
                                }else{
                                    echo '正常运行中';
                                }

                            }?>
						</span>
					</div>
        			<div class="z_border clearfix">
        				
        			</div>
        			<div class="z_inputbox z_mb10 width30 fl">
        				<label class="z_w120-tr z_mr10 z_fontb"><span class="z_color_red">*</span>品牌:</label>
        				<span class="z_font14 z_color-3"><?php echo $brand_name;?></span>
        			</div>
        			<div class="z_inputbox z_mb10 width30 fl">
        				<label class="z_w120-tr z_mr10 z_fontb"><span class="z_color_red">*</span>门店:</label>
        				<span class="z_font14 z_color-3"><?php echo $info['stores_name'];?></span>
        			</div>
					<div class="z_inputbox z_mb10 width30 fl">
						<label class="z_w120-tr z_mr10 z_fontb"><span class="z_color_red">*</span>机台型号:</label>
						<span class="z_font14 z_color-3"><?php echo $info['model'];?></span>
					</div>
					<div class="z_inputbox z_mb10 width30 fl">
						<label class="z_w120-tr z_mr10 z_fontb"><span class="z_color_red">*</span>机台名称:</label>
						<span class="z_font14 z_color-3"><?php echo $info['m_name']?></span>
					</div>
					<div class="z_inputbox z_mb10 width30 fl">
						<label class="z_w120-tr z_mr10 z_fontb"><span class="z_color_red">*</span>支付方式:</label>
						<span class="z_font14 z_color-3"><?php if(strstr($info['pay_type'],'1')){ echo '线上支付'; } ?> <?php if(strstr($info['pay_type'],'2')){ echo '线下支付'; } ?></span>
					</div>
					<div class="z_inputbox z_mb10 width30 fl">
						<label class="z_w120-tr z_mr10 z_fontb"><span class="z_color_red">*</span>激活状态:</label>
						<span class="z_font14 z_color-3"><?php if($info['is_activate']=='2'){ echo '已激活';}else{echo '未激活';}?></span>
					</div>
					<div class="z_inputbox z_mb10 width30 fl">
						<label class="z_w120-tr z_mr10 z_fontb"><span class="z_color_red">*</span>在线状态:</label>
						<span class="z_font14 z_color-3"><?php if($info['is_open']=='1'){ echo '在线';}else{echo '离线';}?></span>
					</div>
					<div class="z_inputbox z_mb10 width30 fl">
						<label class="z_w120-tr z_mr10 z_fontb"><span class="z_color_red">*</span>固件版本号:</label>
						<span class="z_font14 z_color-3"><?php echo $info['firmware_sn'];?></span>
					</div>

					<div class="z_inputbox z_mb10 width30 fl">
						<label class="z_w120-tr z_mr10 z_fontb"><span class="z_color_red">*</span>硬件版本号:</label>
						<span class="z_font14 z_color-3"><?php echo $info['hardware_sn'];?></span>
					</div>
					<div class="z_inputbox z_mb10  fl">
						<label class="z_w120-tr z_mr10 z_fontb"><span class="z_color_red">*</span>硬件编号:</label>
						<span class="z_font14 z_color-3"><?php echo $info['code'];?></span>
					</div>
        			
        			<div class="z_inputbox  width100 fl">
        				<label class="z_w120-tr z_mr10 z_fontb"><span class="z_color_red">*</span>异常详情:</label>
                        <?php if(empty($log)){
                            echo '<span class="z_font14 z_color-3">当前无故障</span>';
                        }else{
                            if($log[0]['status']==1){
                                $code = [];
                                foreach($log as $key=>$val){
                                    if($val['status']==1){
                                        $arr_code = explode(',',$val['code']);
                                        foreach($arr_code as $key=>$val){
                                            $code[]=$val;
                                        }
                                    }else{
                                        break;
                                    }
                                }

                                $str_code = implode(',',array_unique($code));
                                echo '<span class="z_font14 z_color-3">当前故障码：'.$str_code.'</span>';
                            }else{
                                echo '<span class="z_font14 z_color-3">当前无故障</span>';
                            }

                        }
                        ?>

					</div>

					<div class="z_border clearfix fl width100">
						<div class="expention">
							<div class="expention_left" style="    font-weight: bold; font-size: 18px;" >异常码</div>
							<div class="expention_right">
								<ul style="margin-bottom:0px;">
									<li style="    font-weight: bold; font-size: 18px;" >异常信息</li>
								</ul>
							</div>
							<div class="expention_right" style="    font-weight: bold; font-size: 18px;"> 发生时间 </div>
						</div>
						@foreach($log as $k=>$v)
							<div class="expention">
								<div class="expention_left" >{{$v['code']}}</div>
								<div class="expention_right">
									<ul style="margin-bottom:0px;">
										<li>{{$v['desc']}}　&nbsp;<span class="z_off2 js-z_off2" ></span></li>
									</ul>
								</div>
								<div class="expention_right">
									{{date('Y-m-d H:i:s',$v['create_time'])}}
								</div>
							</div>
						@endforeach
					</div>
	        	</div>
        	</div>
        	
        	<!--奖品设置-->
        </div>
    
    
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

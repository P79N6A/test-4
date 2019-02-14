@extends('admin.layouts.parent')
@section('page-title','订单详情')
@section('main')
 <link rel="stylesheet" href="/admin/css/webuploader.css">
 <link href="/admin/css/new-add.css" rel="stylesheet">
<style>
.inline_block{    width:100px;  display:inline-block;  }
.img-responsive{width:200px;}
</style>

  

     <div class="wrapper wrapper-content animated fadeInUp">
        <div class="row">
        	<!--奖品设置-->
        	{{ csrf_field() }}
        	<div class="col-sm-12">
        		<div class="z_form">
					<div class="z_inputbox z_mb10">
						<label class="z_w120-tr z_mr10 z_fontb"><span class="z_color_red">*</span>下单时间:</label>
						<span class="z_font14 z_color-3"><?php echo $info->addtime;?></span>
					</div>
        			<div class="z_inputbox z_mb10">
        				<label class="z_w120-tr z_mr10 z_fontb"><span class="z_color_red">*</span>订单号:</label>
        				<span class="z_font14 z_color-3"><?php echo $info->refno;?></span>
        			</div>
        			<div class="z_inputbox z_mb10">
        				<label class="z_w120-tr z_mr10 z_fontb"><span class="z_color_red">*</span>用户编号:</label>
        				<span class="z_font14 z_color-3"><?php echo $info->userid;?></span>
        			</div>
        			<div class="z_inputbox z_mb10">
        				<label class="z_w120-tr z_mr10 z_fontb"><span class="z_color_red">*</span>门店ID:</label>
        				<span class="z_font14 z_color-3"><?php echo $info->store_id;?></span>
        			</div>
					<div class="z_inputbox z_mb10">
						<label class="z_w120-tr z_mr10 z_fontb"><span class="z_color_red">*</span>门店名称:</label>
						<span class="z_font14 z_color-3"><?php echo $info->stores_name;?></span>
					</div>
					<div class="z_inputbox z_mb10">
						<label class="z_w120-tr z_mr10 z_fontb"><span class="z_color_red">*</span>机台ID:</label>
						<span class="z_font14 z_color-3"><?php echo $info->machine_id;?></span>
					</div>
        			<div class="z_inputbox z_mb10">
        				<label class="z_w120-tr z_mr10 z_fontb"><span class="z_color_red">*</span>机台名称:</label>
        				<span class="z_font14 z_color-3"><?php echo $info->m_name;?></span>
        			</div>
					<div class="z_inputbox z_mb10">
						<label class="z_w120-tr z_mr10 z_fontb"><span class="z_color_red">*</span>硬件编号:</label>
						<span class="z_font14 z_color-3"><?php echo $info->serial_no;?></span>
					</div>
        			<div class="z_inputbox z_mb10">
        				<label class="z_w120-tr z_mr10 z_fontb"><span class="z_color_red">*</span>支付类型:</label>
        				<span class="z_font14 z_color-3">
							<?php if($info->payment_type=='1'){ echo '游币支付';}else{echo '线下投币';}?></span>
        			</div>
        			<div class="z_inputbox z_mb10">
        				<label class="z_w120-tr z_mr10 z_fontb"><span class="z_color_red">*</span>订单状态:</label>
        				<span class="z_font14 z_color-3">
							@if($info->status=='0') 待付款
							@elseif($info->status=='1') 游戏中
							@elseif($info->status=='2') 已使用
							@elseif($info->status=='3') 已过期
							@endif
						</span>
        			</div>
        			<div class="z_inputbox z_mb10">
        				<label class="z_w120-tr z_mr10 z_fontb"><span class="z_color_red">*</span>游戏状态:</label>
        				<span class="z_font14 z_color-3">
							@if($info->machine_status=='1') 正常 @elseif($info->machine_status=='2') 异常 @endif
						</span>
        			</div>
					<div class="z_inputbox z_mb10">
						<label class="z_w120-tr z_mr10 z_fontb"><span class="z_color_red">*</span>支付时间:</label>
						<span class="z_font14 z_color-3"><?php echo date('Y-m-d H:i:s',$info->pay_date);?></span>
					</div>
					<div class="z_inputbox z_mb10">
						<label class="z_w120-tr z_mr10 z_fontb"><span class="z_color_red">*</span>备注:</label>
						<span class="z_font14 z_color-3"><?php echo $info->remark;?></span>
					</div>
	        	</div>
        	</div>

        </div>
    </div>

   
@endsection

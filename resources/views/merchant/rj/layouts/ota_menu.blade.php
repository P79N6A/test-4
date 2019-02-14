	<style>
		.css-display{ display:none; }
		.page-tabs-content{  background: #eaeef1;}
	</style>
	<?php $action = request()->route()->getAction();  ?>

	<div class="row content-tabs">
	    <nav class="page-tabs J_menuTabs">

	        <div class="page-tabs-content">
	            <a href="{{ route('business.rj_ota_list') }}" class="<?php if($action['as']=='business.rj_ota_list'){echo 'active';} ?> J_menuTab" data-id="home.html">OTA升级详情</a>
	        </div>
	        
	        <div class="page-tabs-content">
	            <a href="{{ route('business.rj_ota_firmware') }}" class="<?php if($action['as']=='business.rj_ota_firmware'){echo 'active';} ?> J_menuTab" data-id="home.html">固件管理</a>
	        </div>
	    </nav>
	</div>

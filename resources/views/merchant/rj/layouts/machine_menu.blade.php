	<style>
		.css-display{ display:none; }
		.page-tabs-content{  background: #eaeef1;}
	</style>
	<?php $action = request()->route()->getAction();  ?>

	<div class="row content-tabs">
	    <nav class="page-tabs J_menuTabs">

	        <div class="page-tabs-content">
	            <a href="{{ route('business.rj_machine_list') }}" class="<?php if($action['as']=='business.rj_machine_list'){echo 'active';} ?> J_menuTab" data-id="home.html">已激活机台</a>
	        </div>
	        <div class="page-tabs-content">
	            <a href="{{ route('business.rj_machine_list_wei') }}" class="<?php if($action['as']=='business.rj_machine_list_wei'){echo 'active';} ?> J_menuTab" data-id="home.html">未激活机台</a>
	        </div>
	        <div class="page-tabs-content">
	            <a href="{{ route('business.rj_machine_model') }}" class="<?php if($action['as']=='business.rj_machine_model'){echo 'active';} ?> J_menuTab" data-id="home.html">机台类型管理</a>
	        </div>
	    </nav>
	</div>

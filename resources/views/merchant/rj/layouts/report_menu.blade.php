	<style>
		.css-display{ display:none; }
		.page-tabs-content{  background: #eaeef1;}
	</style>
	<?php $action = request()->route()->getAction();  ?>

	<div class="row content-tabs">
	    <nav class="page-tabs J_menuTabs">

	        <div class="page-tabs-content">
	            <a href="{{ route('business.rj_store_report') }}" class="<?php if($action['as']=='business.rj_store_report'||$action['as']=='business.rj_store_report_detail'){echo 'active';} ?> J_menuTab" data-id="home.html">门店营收</a>
	        </div>
	        
	        <div class="page-tabs-content">
	            <a href="{{ route('business.rj_machint_report') }}" class="<?php if($action['as']=='business.rj_machint_report'||$action['as']=='business.rj_machint_report_detail'){echo 'active';} ?> J_menuTab" data-id="home.html">机台营收</a>
	        </div>
	    </nav>
	</div>

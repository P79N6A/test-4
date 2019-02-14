	<style>
		.css-display{ display:none; }
		.page-tabs-content{  background: #eaeef1;}
	</style>
	<?php $status = (int) request()->input('status');  ?>

	<div class="row content-tabs">
	    <nav class="page-tabs J_menuTabs">
	        <div class="page-tabs-content">
	            <a href="{{ route('admin.doctor-money-list', ['status'=>0,'usersId'=>$usersId]) }}" class="<?php if($status === 0){echo 'active';} ?> J_menuTab" data-id="home.html">待审核</a>
	        </div>
	        <div class="page-tabs-content">
	            <a href="{{ route('admin.doctor-money-list', ['status'=>1,'usersId'=>$usersId]) }}" class="<?php if($status === 1){echo 'active';} ?> J_menuTab" data-id="home.html">已审核</a>
	        </div>
	        <div class="page-tabs-content">
	            <a href="{{ route('admin.doctor-money-list', ['status'=>-1,'usersId'=>$usersId]) }}" class="<?php if($status === -1){echo 'active';} ?> J_menuTab" data-id="home.html">审核失败</a>
	        </div>
	    </nav>
	</div>
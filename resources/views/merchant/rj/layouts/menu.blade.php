
<?php $action = request()->route()->getAction();  ?>
<div class="row content-tabs " >
    <nav class="page-tabs J_menuTabs">
        <div class="page-tabs-content">
            <a href="{{ route('admin.rj_activity_list') }}" class="<?php if($action['as']=='admin.rj_team_list'){echo 'active';} ?> J_menuTab" data-id="home.html">活动管理</a>
        </div>
    </nav>
</div>

<?php $action = request()->route()->getAction();  ?>
<div class="row content-tabs " >
    <nav class="page-tabs J_menuTabs">
        <div class="page-tabs-content">
            <a href="{{ route('business.rj_ranking') }}" class="<?php if($action['as']=='business.rj_ranking'){echo 'active';} ?> J_menuTab" data-id="home.html">怪兽猎人</a>
        </div>
    </nav>
</div>
<?php
/**
 * User: Arcy
 * Date: 2017/9/6
 * Time: 17:12
 */
/* 商家路由 */
Route::group(['domain' => config('domain.bus_domain')], function () {
	//Log命名空间，merchant.auth中间
	Route::group(['namespace' => 'Merchant','middleware' => ['merchant.auth','merchant.rbac'],'as'=>'log.'], function () {
		Route::get('log-show',['uses'=>'LogController@show','as'=>'show']);
		Route::get('log-detail',['uses'=>'LogController@detail','as'=>'detail']);
	});
});

/* 总后台路由 */
Route::group(['domain' => config('domain.admin_domain')], function () {
	Route::group(['namespace' => 'Admin','middleware' => ['admin.auth'],'as'=>'log.'], function () {
		Route::get('log-show',['uses'=>'LogController@show','as'=>'show']);
		Route::get('log-detail',['uses'=>'LogController@detail','as'=>'detail']);
	});
});

/* 测试推送路由 */
Route::match(['post','get'],'test/show_push_log','Merchant\TestController@show_push_log');
Route::match(['post','get'],'test/test_push','Merchant\TestController@test_push');
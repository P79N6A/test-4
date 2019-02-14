<?php
/**
 * Created by PhpStorm.
 * User: AIMPER
 * Date: 2016/12/26
 * Time: 16:40
 */

/**
 * 第三方授权通用入口
 */
Route::group(['domain' => env('DOMAIN_OPENAUTH', 'auth.universal-space.cn'), 'namespace' => 'OpenAuth'], function() {
    Route::controller('alipay', 'AlipayController');
    Route::controller('wechat', 'WechatController');
});

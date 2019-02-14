<?php
/**
 * Created by PhpStorm.
 * User: AIMPER
 * Date: 2016/12/29
 * Time: 16:36
 */

// 总后台
Route::group(['domain'=>config('domain.admin_domain'),'middleware'=>['web','admin.auth']],function(){
    // 上传附件接口
    Route::get('upload/save',['as'=>'admin.upload','uses'=>'UploadController@create']);
    Route::post('upload/save',['as'=>'admin.upload','uses'=>'UploadController@store']);
});

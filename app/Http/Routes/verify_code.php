<?php

// 发送验证码
Route::post('send-verify-code', ['as' => 'send-verify-code', 'uses' => 'VerifyCodeController@sendCode']);
// 校验验证码

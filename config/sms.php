<?php

return [

    // 互亿短信平台
    'ihuyi' => [
        'username'  =>  'cf_baitian',
        'password'  =>  '1234567',
        'api_url'   =>  'http://106.ihuyi.cn/webservice/sms.php?method=Submit',
    ],

    'aliyun' => [
        'key' => env('SMS_KEY'),
        'secret' => env('SMS_SECRET')
    ],



];

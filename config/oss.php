<?php

/* 阿里云存储 OSS 配置 */
return [

    'default' => [
        'oss_access_id'=>env('OSS_ACCESS_ID',''),
        'oss_access_key'=>env('OSS_ACCESS_KEY',''),
        'oss_endpoint'=>env('OSS_ENDPOINT',''),
        'oss_bucket'=>env('OSS_BUCKET'),
        'oss_test_bucket'=>env('OSS_TEST_BUCKET','')
    ],

];

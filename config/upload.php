<?php
/**
 * Created by PhpStorm.
 * User: AIMPER
 * Date: 2016/10/26
 * Time: 11:25
 */
return [
    'root_path'         =>  env('UPLOAD_ROOT'),         // 上传根目录
    'allow_extention'   =>  env('ALLOW_EXTENSION'),     // 允许的扩展名
    'max_size'          =>  env('MAX_SIZE'),            // 允许最大上传大小,该值不大于服务器和PHP设置的值
    'auto_subdir'       =>  env('AUTO_SUBDIR'),         // 是否开启按日期自动创建子目录

    'static_base_url'   =>  env('STATIC_BASE_URL'),     // 静态文件访问基URL
];
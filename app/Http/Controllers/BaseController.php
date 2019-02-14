<?php
namespace App\Http\Controllers;
use OSS\OssClient;
use OSS\Core\OssException;

class BaseController extends Controller {

    /**
     * 生成随机字符串
     * @param int $len 字符串长度
     * @return string 随机串
    */
    public function genRandomString($len = 6){
        $template = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $str = '';

        for($i=0; $i<$len; $i++){
            $pos = rand(0,42);
            $str .= substr($template,$pos,1);
        }
        return $str;
    }

    /**
     * 统一输出接口返回数据方法
     * @param array $data 输入数据
     * @return string 格式化成 json 数据
    */
    public function output($data){
        return response()->json($data);
    }

}

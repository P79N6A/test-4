<?php

namespace App\Services;
use Illuminate\Http\Request;
use EasyWeChat\Foundation\Application;
use App\Helper;
use EasyWeChat\Payment\Order;

class WechatService{
    public static function getAccessToken()
    {
        $options = [
            'debug'     => true,
            'app_id'    => config('wechat.app_id'),
            'secret'    => config('wechat.secret'),
            'log' => [
                'level' => 'debug',
                'file'  => '/tmp/easywechat.log',
            ],
        ];
        
        $app = new Application($options);
        
        // 获取 access token 实例
        $accessToken = $app->access_token; // EasyWeChat\Core\AccessToken 实例
        return $accessToken->getToken(); // token 字符串
    }

    /**
     * 生成小程序二维码
     * 接口B：适用于需要的码数量极多的业务场景
     * $api-link https://developers.weixin.qq.com/miniprogram/dev/api/qrcode.html?search-key=%E4%BA%8C%E7%BB%B4%E7%A0%81
     * 
     * @param string $scene 最大32个可见字符，只支持数字，大小写英文以及部分特殊字符：!#$&'()*+,/:;=?@-._~，其它字符请自行编码为合法字符（因不支持%，中文无法使用 urlencode 处理，请使用其他编码方式）
     * @param string $page 	必须是已经发布的小程序存在的页面（否则报错），例如 "pages/index/index" ,根路径前不要填加'/',不能携带参数（参数请放在scene字段里），如果不填写这个字段，默认跳主页面
     * @param string $width 二维码的宽度
     */
    public static function getWxaCodeUnlimit($scene, $page = '', $width = 430, $save = 0)
    {
        $param = [];
        $param['scene'] = $scene;
        if(!empty($page)) $param['page'] = $page;
        $param['width'] = $width;

        $param = json_encode($param);
        $url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=" . static::getAccessToken();

        $resp = Helper::curl($url, $param, 1, 1);

        if($save){
            $path = '/upload/qrcode/'. $scene . '.jpg';
            file_put_contents('.' . $path, $resp);

            return $path;
        } else {
            header('content-type:image/jpg');
            return $resp;
        }
    }

     /**
     * 生成微信支付信息
     * 
     * $api-link https://api.mch.weixin.qq.com/pay/unifiedorder 
     * 
     * @param string $openid
     * @param Array $data($data->name,$data->price)
     * @return $config
     */
    public static function getWcPayment($data,$openid,$order_num)
    {
         $options =config('wechat.options');

        $app = new Application($options);
        $payment = $app->payment;

        $attributes = [
            'trade_type'       => 'JSAPI', // JSAPI，NATIVE，APP...
            'body'             => $data->name,
            'detail'           => $data->name,
            'out_trade_no'     => $order_num,
            'total_fee'        => $data->price,//$course->price // 单位：分
            'notify_url'       => $options['payment']['notify_url'], // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'openid'           => $openid, // trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识，
            // ...
        ];

        $order = new Order($attributes);
        $result = $payment->prepare($order);
        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
            $prepayId = $result->prepay_id;
            $config = $payment->configForJSSDKPayment($prepayId); 
        } else {
            // [TODO] 发起不成功处理
            
        }
        $config['out_trade_no'] = $order_num;
        return $config;
    }

    /*
    *查询微信支付订单
    */
    public static function getPaymentOrder($orderNo){
        $options = config('wechat.options');
        $app = new Application($options);
        $payment = $app->payment;
        return $payment->query($orderNo);
    }
}
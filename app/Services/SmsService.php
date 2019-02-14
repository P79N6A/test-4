<?php
/**
 * User: Arcy
 * Date: 2018/7/20
 * Time: 10:07
 */

namespace App\Services;
use Aliyun\SMS\Client;
use App\Models\SmsCodeModel;
use Aliyun\Sms\Request\V20170525\SendSmsRequest;
class SmsService{
    private $client = null;

    public function __construct(){
        $key = config('sms.aliyun.key');
        $secret = config('sms.aliyun.secret');

        $this->client = Client::getClient($key,$secret);
    }

    /**
     * 发送短信
     * @return stdClass
     */
    public function sendSms($mobile,$param) {

        // 初始化SendSmsRequest实例用于设置发送短信的参数
        $request = new SendSmsRequest();

        //可选-启用https协议
        //$request->setProtocol("https");

        // 必填，设置短信接收号码
        $request->setPhoneNumbers($mobile);

        // 必填，设置签名名称，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $request->setSignName("麦麦天空");

        // 必填，设置模板CODE，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $request->setTemplateCode("SMS_139976833");

        // 可选，设置模板参数, 假如模板中存在变量需要替换则为必填项
        $request->setTemplateParam(json_encode($param, JSON_UNESCAPED_UNICODE));

        // 可选，设置流水号
        // $request->setOutId("yourOutId");

        // 选填，上行短信扩展码（扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段）
        // $request->setSmsUpExtendCode("1234567");

        // 发起访问请求
        $acsResponse = $this->client->getAcsResponse($request);

        if(isset($acsResponse) && isset($acsResponse->Code) && $acsResponse->Code == "OK"){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 检测验证码
     */
    public static function checkCode($code ,$mobile){
        $expire = (time() - (30 * 60));
        $expire = date('Y-m-d H:i:s',$expire); //30分钟内有效期
        $where[] = ['code',$code];
        $where[] = ['mobile',$mobile];
        $where[] = ['is_use',0]; //未被使用
        $where[] = ['created_at',">=" ,$expire];

        $model = SmsCodeModel::where($where)->first();
        if(!empty($model)){
            $model->is_use = 1; //设置验证码为已使用
            $model->save();
            return true;
        }else{
            return false;
        }
    }
}

<?php
namespace App\Http\Controllers\Api;

use App\Helper;
use App\Models\SmsCodeModel;
use App\Services\SmsService;
use Illuminate\Http\Request;

class SmsController extends Controller
{

    /**
     * 发送验证码
     */
    public function send(Request $request)
    {
        $mobile = $request->input('mobile');

        if (!Helper::isMobile($mobile)) {
            return $this->error('手机号码不正确！');
        }

        //获取数据库未过期的验证码
        $expire = (time() - (30 * 60));
        $expire = date('Y-m-d H:i:s', $expire);
        $where[] = ['is_use', 0];
        $where[] = ['mobile', $mobile];
        $where[] = ['created_at', ">=", $expire];
        $code_model = SmsCodeModel::where($where)->first();

        if (empty($code_model)) {
            $code = Helper::getRandomString(4, false, false);
        } else {
            $code = $code_model->code;
        }

        $param['code'] = $code;
        $sms = new SmsService();

        if ($sms->sendSms($mobile, $param)) { //发送成功
            if (empty($code_model)) {
                //如果是重新生成验证码，则记录进数据库
                $code_model = new SmsCodeModel();
                $code_model->mobile = $mobile;
                $code_model->code = $code;
                $code_model->save();
            }

            return $this->success([]);
        } else {
            return $this->error('发送失败！');
        }
    }
}

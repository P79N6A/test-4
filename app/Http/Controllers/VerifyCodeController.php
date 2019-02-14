<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Helper;

class VerifyCodeController extends Controller
{
    /**
     * 发送验证码
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendCode(Request $request)
    {
        if ($request->isMethod('post')) {
            $mobile = $request->get('mobile');

            if (!$mobile) {
                return $this->response(403, '请提供手机号码');
            }

            if (!Helper::isMobile($mobile)) {
                return $this->response(403, '手机号码格式不正确');
            }

            $url = config('misc.code_url') . $mobile;
            $client = new Client();
            $res = $client->post($url);

            if ($res->getStatusCode() !== 200) {
                return $this->response(500, '发送验证码出错，请稍后重试');
            }

            $content = json_decode($res->getBody()->getContents());

            if ($content->retCode != 0) {
                return $this->response(500, $content->retMsg);
            }

            return $this->response(200, '验证码发送成功，请注意查收');

        }
    }
}

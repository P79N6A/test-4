<?php
namespace App\Http\Controllers\Api;

use App\Helper;
use App\Models\UsersModel;
use App\Models\UserTokenModel;
use App\Services\DecodeService;
use App\Services\SmsService;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private $validate = [
        'mobile' => 'required|mobile',
        'img' => 'required',
        'nickname' => 'required|between:1,80',
    ];
    private $messages = [
        'required' => ':attribute 不能为空',
        'between' => ':attribute 长度必须在 :min 和 :max 之间',
    ];
    private $attributes = [
        'mobile' => '手机号码',
        'img' => '用户头像',
        'nickname' => '用户昵称',
    ];
    /**
     * 记录OPENID在库
     * 如果存在用户，则判断是否已绑定信息
     * 如果不存在用户，则提示绑定相关信息
     */
    public function getCode(Request $request)
    {
        $code = $request->input('code');
        $inviteCode = $request->input('invite_code', ''); // 邀请码
        if (empty($code)) {
            return $this->error('code为空！');
        }

        $appid = config('wechat.app_id');
        $secret = config('wechat.secret');
        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid=' . $appid . '&secret=' . $secret . '&js_code=' . $code . '&grant_type=authorization_code';

        $client = new Client();
        $res = $client->request('GET', $url);

        $data = $res->getBody();
        $data = json_decode($data, true);

        if (isset($data['errorcode'])) {
            return $this->error('获取错误！');
        }

        if (!isset($data['openid'])) {
            return $this->error('获取失败！');
        }

        $user = UsersModel::where('openid', $data['openid'])->first();
        if (empty($user)) { //不存在用户则建立新用户
            //添加openid到数据库
            $user = new UsersModel();
            $user->openid = $data['openid'];
            if (!empty($inviteCode)) {
                $inviteParentId = UsersModel::where('invite_code', $inviteCode)->value('id');
                if (!empty($inviteParentId)) {
                    $user->invite_parent_id = $inviteParentId;
                }
                // 记录邀请关系
            }

            $user->save();
        }

        $update = [
            'user_id' => $user->id,
            'token' => Helper::makeToken(),
            'expire' => date('Y-m-d H:i:s'),
        ];

        try {
            $user_token = UserTokenModel::updateOrCreate(['user_id' => $user->id], $update);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }

        if (empty($user_token->token)) {
            return $this->error('生成Token失败，请重新尝试！');
        }

        $return['token'] = $user_token->token;
        $return['openid'] = $data['openid'];
        $return['session_key'] = $data['session_key'];
        //判断用户是否绑定
        $return['is_bind'] = empty($user->mobile) ? 0 : 1;

        return $this->success($return);
    }

    /**
     * 根据OPENID 获取code
     */
    public function getTokenByOpenid(Request $request)
    {
        $openid = $request->input('openid');
        if (empty($openid)) {
            return $this->error("获取出错！");
        }

        $user = UsersModel::where('openid', $openid)->first();
        if (empty($user)) {
            return $this->error('用户不存在！');
        }

        $update = [
            'user_id' => $user->id,
            'token' => Helper::makeToken(),
            'expire' => date('Y-m-d H:i:s'),
        ];

        try {
            $user_token = UserTokenModel::updateOrCreate(['user_id' => $user->id], $update);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }

        if (empty($user_token->token)) {
            return $this->error('生成Token失败，请重新尝试！');
        }

        $return['token'] = $user_token->token;
        $return['openid'] = $openid;
        //判断用户是否绑定
        $return['is_bind'] = empty($user->mobile) ? 0 : 1;

        return $this->success($return);
    }

    /**
     * 绑定用户信息
     */
    public function bind(Request $request)
    {
        try {
            $this->validate($request, $this->validate, $this->messages, $this->attributes);
        } catch (\Exception $e) {
            return $this->error($e->validator->errors()->first());
        }
        //检查验证码是否正确
        $code = $request->input('code');
        $mobile = $request->input('mobile');
        if (empty($code)) {
            return $this->error('验证码非法！');
        }

        if (!SmsService::checkCode($code, $mobile)) {
            return $this->error('验证码不正确！');
        }

        $user = $this->auth->user;
        // 判断该手机是否已绑定
        if (!empty($user->mobile)) {
            return $this->error('您已绑定手机号');
        }

        if (!empty(UsersModel::where('mobile', $mobile)->first())) {
            return $this->error('该手机号已被绑定，请换一个再试');
        }

        $data = $request->only(['mobile', 'img', 'nickname']);
        $user->mobile = $data['mobile'];
        $user->nickname = $data['nickname'];
        $user->img = $data['img'];
        if ($user->save()) {
            return $this->success([]);
        } else {
            return $this->error('绑定失败！');
        }
    }

    /**
     * 返回用户信息
     */
    public function info(Request $request)
    {
        $info = [
            'user_id' => $this->auth->user->id,
            'nickname' => $this->auth->user->nickname,
            'mobile' => $this->auth->user->mobile,
            'img' => $this->auth->user->img,
            'is_vip' => $this->auth->user->is_vip,
            'vip_expire' => $this->auth->user->vip_expire,
            'role' => $this->auth->user->role,
            'openid' => $this->auth->user->openid,
        ];
        return $this->success($info);
    }

    /*
     *手机号码解密
     */
    public function decode(Request $request)
    {
        $session_key = $request->input('session_key');
        $encryptedData = $request->input('encryptedData');
        $iv = $request->input('iv');
        $result = DecodeService::decryptData($session_key, $encryptedData, $iv, $data);
        if ($result['code'] !== 0) {
            return $this->success(json_decode($result['msg'], true));
        } else {
            return $this->success($result['msg']);
        }
    }

    /*
    *获取注册机台的地点信息
    */
    public function getRegisterMenchineinfo()
    {
        $info = UsersModel::leftjoin('stores as s','users.store_id','=','s.id')->leftjoin('city as c','s.city_id','=','c.id')->where('users.id',$this->auth->user->id)->select('s.name as store_name','c.name as city_name','s.id as store_id','c.id as city_id')->first();
        return $this->success($info);
    }

}

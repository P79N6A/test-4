<?php
/**
 * User: Arcy
 * Date: 2018/7/18
 * Time: 11:07
 */

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\UserTokenModel;

class UserService
{

    /**
     * 判断用户登录状态
     * @return boolean false->未登录，true->已登录
     */
    public static function isLogin()
    {
        $token = request()->input('token');
        if (empty($token)) {
            return false;
        }
        $info = UserTokenModel::where('token', $token)->first();

        if (empty($info)) {
            return false;
        }
        //token有效期为30分钟
        $expire = 30 * 60;
        $diff = time() - strtotime($info->expire);
        if ($diff > $expire) {
            return false;
        }

        return $info;
    }

    public static function isBind()
    {
        $token = request()->input('token');
        if (empty($token)) {
            return false;
        }
        $info = UserTokenModel::where('token', $token)->first();

        if (empty($info)) {
            return false;
        }
        //检测是否绑定手机号
        if (empty($info->user->mobile)) {
            return false;
        }

        return true;
    }

    /**
     * 判断用户状态
     * @return boolean false->禁用，true->正常
     */
    public static function isActive()
    {
        $token = request()->input('token');
        if (empty($token)) {
            return false;
        }
        $info = UserTokenModel::where('token', $token)->first();

        if (empty($info) || empty($info->user)) {
            return false;
        }

        if ($info->user->status == 0 || $info->user->status == -1) {
            return false;
        }

        return true;
    }
}

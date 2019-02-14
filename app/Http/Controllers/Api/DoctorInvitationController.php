<?php
namespace App\Http\Controllers\Api;

use App\Models\SettingModel;
use App\Models\UsersModel;
use Illuminate\Http\Request;

/**
 * 邀请用户
 */
class DoctorInvitationController extends Controller
{
    /**
     * 获取邀请信息
     */
    public function info(Request $request)
    {
        $info = UsersModel::where('id', $this->auth->user_id)->select('invite_code', 'invite_code_path')->first();
        $img = UsersModel::where('invite_parent_id', '=', $this->auth->user_id)->whereNotNull('img')->select('img')->get();
        $msg = SettingModel::where('disabled', 0)->where('name', '邀请说明')->first();
        $msg = strip_tags($msg['value']);

        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        return $this->success(['invite_code' => $info['invite_code'], 'invite_code_path' => $http_type . $_SERVER['HTTP_HOST'] . $info['invite_code_path'], 'invite_msg' => $msg, 'invite_id' => $this->auth->user_id, 'img' => $img]);
    }
}

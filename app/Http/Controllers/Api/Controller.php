<?php

namespace App\Http\Controllers\Api;

use App\Models\OrderModel;
use App\Models\UsersModel;
use App\Services\UserService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;
    protected $auth = null;
    public function __construct()
    {
        //判断是否登录，若已登录，则更新token过期日期
        if (($user_info = UserService::isLogin())) {
            $user_info->expire = date('Y-m-d H:i:s');
            $user_info->save();
            $this->auth = $user_info;
        }

        //更新超时订单
        $this->updateTimeOutOrder();
        //更新过期VIP
        $this->updateExpireVip();
    }

    //更新超时订单
    protected function updateTimeOutOrder()
    {
        $update_time = time() - (30 * 60);
        $update_time = date('Y-m-d H:i:s', $update_time);

        $where = [
            ['status', 4],
            ['created_at', '<=', $update_time],
        ];

        OrderModel::where($where)->update(['status' => 3]);
    }

    //更新过期VIP
    protected function updateExpireVip()
    {
        $where = [
            ['vip_expire', '<=', date('Y-m-d H:i:s')],
        ];

        UsersModel::where($where)->update(['is_vip' => 0]);
    }

    public function success($data, $code = 0, $msg = '获取成功！')
    {
        return response()->json(['code' => $code, 'msg' => $msg, 'data' => $data]);
    }

    public function error($msg, $code = 1)
    {
        return response()->json(['code' => $code, 'msg' => $msg]);
    }
}

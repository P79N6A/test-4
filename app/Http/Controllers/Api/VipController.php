<?php
namespace App\Http\Controllers\Api;

use App\Helper;
use App\Models\VipModel;
use Illuminate\Http\Request;

class VipController extends Controller
{

    /**
     * 获取VIP信息
     */
    public function info(Request $request)
    {
        $vip = VipModel::find(1);
        $vip->content = Helper::resetImg($vip->content);

        return $this->success($vip);
    }
}
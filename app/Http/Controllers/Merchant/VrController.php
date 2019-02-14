<?php

namespace App\Http\Controllers\Merchant;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class VrController extends Controller
{
    public function index(Request $request)
    {
        $url = config('misc.vr_machine_list_url');
        $data = $request->only('network_status', 'machine_status', 'name', 'store_name', 'page');
        $headers = ['Content-Type' => 'application/x-www-form-urlencoded', 'timeout' => 30];
        $params = ['pagesize' => 20, 'page' => 1];

        if ($data['network_status'] != null && $data['network_status'] != 0) {
            $params['network'] = $data['network_status'];
        }
        if ($data['machine_status'] != null && $data['machine_status'] != 0) {
            $params['status'] = $data['machine_status'];
        }
        if (!empty($data['name'])) {
            $params['name'] = $data['name'];
        }
        if (!empty($data['store_name'])) {
            $params['storeName'] = $data['store_name'];
        }
        if (intval($data['page'])) {
            $params['page'] = $data['page'];
        }

        $response = \Requests::post($url, $headers, $params);

        if ($response->status_code != 200) {
            return view('business.error', ['code' => 500, 'msg' => '接口异常，无法获取数据']);
        }

        $body = json_decode($response->body);

        return view('business.vr-machine-list', [
            'machines' => $body->data,
            'paginated' => $body->paginated,
            'curPage' => $params['page'],
            'url' => $url,
            'params' => $data,
        ]);
    }

    public function storeGameIncome()
    {
        $token = $this->getToken();
        $url = config('misc.vr_game_income_url') . '?token=' . $token;
        return view('business.store-game-income', ['url' => $url]);
    }

    public function gameManagement()
    {
        $token = $this->getToken();
        $url = config('misc.vr_game_management_url') . '?token=' . $token;
        return view('business.game-management', ['url' => $url]);
    }

    public function gameConsumeLog()
    {
        $token = $this->getToken();
        $url = config('misc.vr_game_consume_log_url') . '?token=' . $token;
        return view('business.game-consume-log', ['url' => $url]);
    }

    /**
     * 返回已登录商户用户的商户端 token
     * @return string
     */
    private function getToken()
    {
        $uid = session()->get('id');
        $tokenObj = DB::table('bus_token')->where('userid', $uid)->select('token')->first();
        if ($tokenObj) {
            return $tokenObj->token;
        } else {
            return '';
        }
    }
}

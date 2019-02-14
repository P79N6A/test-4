<?php
namespace App\Libraries;

use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;

class IotDev
{
    static function getToken($appid)
    {
        $app = DB::table('iot_app')->where('appid', $appid)->first();

        if (empty($app)) {
            return false;
        }
        $client = new Client();
        $url = config('misc.zhilianbao_url');
        $response = $client->get($url,
            [
                'query' => [
                    'appid' => $app->appid,
                    'secret' => $app->secret
                ]
            ]
        );
        $data = $response->getBody()->getContents();
        if (empty($data)) {
            return false;
        }

        $r = json_decode($data);
        if ($r->retCode != 0) {
            return false;
        }

        return $r->token;

    }

    static function add($appid, $serialNo = '', $qrcode = '')
    {

        $token = self::getToken($appid);
        if (empty($token)) {
            return false;
        }

        $client = new Client();
        $response = $client->put('https://iot.ctree.com.cn/api/dev/' . $serialNo, [
            'query' => [
                'token' => $token,
                'qrcode' => $qrcode
            ]
        ]);

        $data = $response->getBody()->getContents();
        if (empty($data)) {
            return false;
        }
        $r = json_decode($data);
        if ($r->retCode != 0) {
            return false;
        }
        return true;
    }

}

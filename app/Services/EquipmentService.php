<?php

namespace App\Services;
use Illuminate\Http\Request;
use App\Helper;

class EquipmentService{
    /**
     * 获取机台状态
     *
     * @param string $serialNo 机台编码
     * @return array
     */
    public static function getMachineStatus($serialNo){
        $url = 'https://' . config('iot.iot_api_domain') . '/api/equipment/status';
        $params = [
            'pass' => config('iot.iot_pass'),
            'serial_no' => $serialNo
        ];
        $resp = Helper::curl($url, $params, 1, 1);

        return json_decode($resp, true);
    }

    /**
     * 获取机台参数
     *
     * @param string $serialNo 机台编码
     * @return array
     */
    public static function getMachineParams($serialNo){
        $url = 'https://' . config('iot.iot_api_domain') . '/api/equipment/params';
        $params = [
            'pass' => config('iot.iot_pass'),
            'serial_no' => $serialNo
        ];
        $resp = Helper::curl($url, $params, 1, 1);

        return json_decode($resp, true);
    }

    /**
     * 开启机台
     *
     * @param string $serialNo 机台编码
     * @param string $player 机台玩家位码（10进制）
     * @return array
     */
    public static function startMachine($serialNo, $player){
        $url = 'https://' . config('iot.iot_api_domain') . '/api/equipment/start';
        $params = [
            'pass' => config('iot.iot_pass'),
            'serial_no' => $serialNo,
            'player' => $player
        ];
        $resp = Helper::curl($url, $params, 1, 1);

        return json_decode($resp, true);
    }

    /**
     * 发送userid
     *
     * @param string $serialNo 机台编码
     * @param string $player 机台玩家位码（10进制）
     * @param string $user_id 用户id
     * @return array
     */
    public static function sendUserId($serialNo, $player, $userId){
        $url = 'https://' . config('iot.iot_api_domain') . '/api/equipment/sendUserId';
        $params = [
            'pass' => config('iot.iot_pass'),
            'serial_no' => $serialNo,
            'player' => $player,
            'user_id' => $userId
        ];
        $resp = Helper::curl($url, $params, 1, 1);

        return json_decode($resp, true);
    }
}

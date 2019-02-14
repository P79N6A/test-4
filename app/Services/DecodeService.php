<?php
/**
 * User: GJHaoo
 * Date: 2018/9/10
 * Time: 13:48
 */

namespace App\Services;

class DecodeService
{
    /**
     * 检验数据的真实性，并且获取解密后的明文.
     * @param $encryptedData string 加密的用户数据
     * @param $iv string 与用户数据一同返回的初始向量
     * @param $data string 解密后的原文
     *
     * @return int 成功0，失败返回对应的错误码
     */
    public static function decryptData($session_key, $encryptedData, $iv, &$data)
    {
        if (strlen($session_key) != 24) {
            $return['msg'] = 'NO session_key';
            $return['code'] = 0;
            return $return;
        }
        $aesKey = base64_decode($session_key);

        if (strlen($iv) != 24) {
            $return['msg'] = 'NO iv';
            $return['code'] = 0;
            return $return;
        }
        $aesIV = base64_decode($iv);

        $aesCipher = base64_decode($encryptedData);

        $result = openssl_decrypt($aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);

        $dataObj = json_decode($result);
        if ($dataObj == null) {
            $return['msg'] = 'fail';
            $return['code'] = 0;
            return $return;
        }
        if ($dataObj->watermark->appid != config('wechat.app_id')) {
            $return['msg'] = 'fail';
            $return['code'] = 0;
            return $return;
        }
        $data['msg'] = $result;
        $data['code'] = 1;
        return $data;
    }
}

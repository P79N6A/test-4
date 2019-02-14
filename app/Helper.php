<?php
namespace App;

class Helper
{
    /**
     * 生成随机字符串，已废除
     * @param int $length
     * @param bool $onlyUper
     * @param bool $onlyNumber
     * @return string
     */
    // public static function getRandomString($length = 6, $onlyUper = false, $onlyNumber = false)
    // {
    //     $uperLetters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
    //     $lowerLetters = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];
    //     $numbers = [1, 2, 3, 4, 5, 5, 6, 7, 8, 9, 0];

    //     if ($onlyUper) {
    //         $arr = array_merge($uperLetters, $numbers);
    //     } elseif ($onlyNumber) {
    //         $arr = $numbers;
    //     } else {
    //         $arr = array_merge($uperLetters, $lowerLetters, $numbers);
    //     }
    //     shuffle($arr);

    //     return substr(implode('', $arr), 0, $length);

    // }

    /**
     * 生成随机字符串,比原函数串生成更随机
     * @param int $length
     * @param bool $onlyUper
     * @param bool $onlyNumber
     * @return string
     */
    public static function getRandomString($length = 6, $Uper = true, $Lower = true)
    {
        $uperLetters = 'ABCDEFGHIJKLMNOPQRSTUVWSYZ';
        $lowerLetters= 'abcdefghijklmnopqrstuvwsyz';
        $str = '0123456789';

        if ($Uper) {
            $str .= $uperLetters;
        }
        if ($Lower) {
            $str.= $lowerLetters;
        }
        $str = str_shuffle($str);
        $key = '';
        for ($i=0;$i<=$length-1;$i++) {
            $key .= $str{mt_rand(0, strlen($str) - 1)};
        }
        return $key;
    }

    /**
     * 生成订单号
     */
    public static function makeOrderNum()
    {
        $prefix = date('YmdHis');
        $str = $prefix . static::getRandomString(6, false, false);

        return $str;
    }

    /**
     * curl http 请求
     * @param $url
     * @param string $method
     * @param array $params
     * @return mixed
     */
    public static function http($url, $method = 'get', $params = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        if ($method = 'get') {
            curl_setopt($ch, CURLOPT_POST, 0);
        } elseif ($method == 'post') {
            curl_setopt($ch, CURLOPT_POST, 1);
        }
        $res = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if ($error) {
            return $error;
        }
        return $res;
    }

    /**
     * 检测手机号码格式
     * @param $mobile
     * @return bool
     */
    public static function isMobile($mobile)
    {
        if (empty($mobile)) {
            return false;
        }
        if (!preg_match('/^1[3456789][\d]{9}$/', $mobile) && !preg_match('/^0\d{2,3}\d{7,8}$/', $mobile)) {
            return false;
        }
        return true;
    }

    /**
     * 检测字符是否为ID
     */
    public static function isId($v)
    {
        if (!preg_match("/^[1-9]\d{0,}$/", $v)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 匹配IMG标签，设置为宽度100%，并且附加上地址
     * 主要用于小程序和手机端
     */
    public static function resetImg($v)
    {
        $url = 'http://' . config('domain.api_domain');
        $content = preg_replace_callback(
            "/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i",
            function ($match) use ($url) {
                $src = $match[1];
                if (!strpos('http', $src) || !strpos('https', $src)) {
                    $src = $url . $src;
                }

                return '<img src="'.$src.'" width="100%"/>';
            },
            $v
        );

        return $content;
    }

    /**
     * 生成唯一token
     */
    public static function makeToken()
    {
        $str = self::getRandomString(8);
        $str .= time();
        return md5(sha1($str));
    }

    /**
     * 获取 http 引用（当前链接的上一个页面地址）
     * @return mixed
     */
    public static function getHttpReferrer()
    {
        return $_SERVER['HTTP_REFERER'];
    }

    public static function urlencodeObj($source)
    {
        if (is_array($source)) {
            $source = (object)$source;
        }

        foreach ($source as &$item) {
            if (is_array($item) || is_object($item)) {
                urlencode_array_and_obj($item);
            } else {
                $item = urlencode($item);
            }
        }
        return $source;
    }

    /**
     * 去除字符串中的空格
     * @param $string
     * @return mixed
     */
    public static function trim($string)
    {
        return preg_replace('/\s/', '', $string);
    }

    /**
     * AES加密
     * @param string $data 需要加密/解密的字符串
     * @param boolean $type true为加密，false为解密
     * @param string $key 加密/解密所需的密匙
     * @param string $iv 反向密匙
     * @return string $string 加密/解密后的字符串
     */
    public static function AES($data, $type = true, $key = null, $iv = null)
    {
        empty($key) && $key = "dmtk123456";
        empty($iv) && $iv = base64_encode("654321dmtk");

        if ($type) {
            //加密
            // $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $privateKey, $data, MCRYPT_MODE_CBC, $iv);
            $content = openssl_encrypt($data, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
            return base64_encode($content);
        } else {
            //解密

            $data = base64_decode($data);
            // $decrypted     = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $privateKey, $encryptedData, MCRYPT_MODE_CBC, $iv);
            $content = openssl_decrypt($data, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
            return $content;
        }
    }

    /**
     * 递归生成唯一码
     * @param int $len
     * @return string
     */
    public static function makeUniqueNum($len = 6)
    {
        return substr(base_convert(md5(uniqid(md5(microtime(true)), true)), 16, 10), 0, $len);
    }

    /**
     * @param $url 请求网址
     * @param bool $params 请求参数
     * @param int $ispost 请求方式
     * @param int $https https协议
     * @return bool|mixed
     */
    public static function curl($url, $params = false, $ispost = 0, $https = 0)
    {
        $httpInfo = array();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($https) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 对认证证书来源的检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
        }
        if ($ispost) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_URL, $url);
        } else {
            if ($params) {
                if (is_array($params)) {
                    $params = http_build_query($params);
                }
                curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
            } else {
                curl_setopt($ch, CURLOPT_URL, $url);
            }
        }

        $response = curl_exec($ch);

        if ($response === false) {
            // echo "cURL Error: " . curl_error($ch);exit;
            return false;
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $httpInfo = array_merge($httpInfo, curl_getinfo($ch));
        curl_close($ch);
        return $response;
    }

    /*
    * 计算出两个经纬度之间的距离（单位：米）
    */
    public static function getDistance($lng1, $lat1, $lng2, $lat2)
    {
        // 将角度转为狐度
        $EARTH_RADIUS = 6378137;   //地球半径
        $RAD = pi() / 180.0;

        $radLat1 = $lat1 * $RAD;
        $radLat2 = $lat2 * $RAD;
        $a = $radLat1 - $radLat2;    // 两点纬度差
        $b = ($lng1 - $lng2) * $RAD;  // 两点经度差
        $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2)));
        $s = $s * $EARTH_RADIUS;
        $s = round($s * 10000) / 10000;
        return $s;
    }

    /*
    *根据ip获取用户地址
    */
    public static function getAddressByIp($ip)
    {
        $result = self::curl('https://restapi.amap.com/v3/ip?ip='.$ip.'&output=JSON&key='.config('lbsmap.key'));
        $result = json_decode($result, true);
        if ($result['status'] == 0) {
            return false;
        }
        return $result;
    }

    /*
    *根据地址获取经纬度
    */
    public static function getLocationByAddress($address)
    {
        $result = self::curl('https://restapi.amap.com/v3/geocode/geo?address='.$address.'&output=JSON&key='.config('lbsmap.key'));
        return $result;
    }

    /**
     * 删除数组中的指定值
     */
    public static function delByValue($arr, $value){  
        $keys = array_keys($arr, $value);  
        if(!empty($keys)){  
            foreach ($keys as $key) {  
                unset($arr[$key]);  
            }  
        }  
        return $arr;  
    }
}

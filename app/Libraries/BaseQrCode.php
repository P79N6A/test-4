<?php
/**
 * 统一处理二维码类
 * Created by PhpStorm.
 * User: AIMPER
 * Date: 2016/11/30
 * Time: 10:13
 */

namespace App\Libraries;
use Endroid\QrCode;
use Illuminate\Support\Facades\DB;

class BaseQrCode
{

    /**
     * 生成二维码
     * @param $data
     * @return mixed
     */
    public static function generateQrCode($data)
    {
        $code = random_string(16, true);
        $create_date = time();
        $expires = 0;

        $args_count = count($data);

        $qrcodeType = DB::table('qrcode_type')->where('id', '=', $data[0])->select('app_url', 'params')->first();
        $arr = explode(',', $qrcodeType->params);
        $params_count = count($arr);

        $schema = $qrcodeType->app_url;
        $params = [];

        foreach ($arr as $k => $item) {
            if ($k < ($args_count - 1) && !empty($data[$k + 1])) {
                // 组合 schema
                if ($k == 0) {
                    $schema .= '?' . $item . '=' . $data[$k + 1];
                } elseif ($k < ($args_count - 1)) {
                    $schema .= '&' . $item . '=' . $data[$k + 1];
                }
                // 组合 参数 json 串
                $params[$item] = $data[$k + 1];
            }

        }

        $type = $data[0];
        unset($data[0]);
        if ($type == 14) {
            $machine = DB::table('iot_machine as im')->where('im.id', $data[1])
                ->join('iot_dev as id', 'id.id', '=', 'im.dev_id')->select('serial_no')->first();
            $param = json_encode(["serialNo" => "$machine->serial_no", "tag" => "0"]);
            $_data = $machine->serial_no . ',0';
        } else {
            $param = json_encode($params);
            $_data = implode('-', $data);
        }
        $qrcodeData = [
            'code' => $code,
            'type' => $type,
            'params' => $param,
            'qrschema' => $schema,
            'create_date' => $create_date,
            'expires' => $expires
        ];
        unset($data[0]);
        $qrcodeData['data'] = $_data;

        DB::table('qrcode')->insert($qrcodeData);
        if ($type == 14) {
            $machine = DB::table('iot_machine as im')->where('im.id', $data[1])
                ->join('iot_dev as id', 'id.id', '=', 'im.dev_id')
                ->select('id.serial_no')->first();
            $res = DB::table('qrcode')->where('data', $machine->serial_no . ',0')->select('code')->first();
        } else {
            $res = DB::table('qrcode')->where('type', $type)->where('data', implode('-', $data))->first();
        }
        return $res;

    }


    /**
     * 获取二维码
     * @param $type
     * @param array $params
     * @return bool|string
     */
    /*
    public static function get($type, Array $params){
        if(!intval($type)){
            return false;
        }
        $qrCodeType = DB::table('qrcode_type')->where('id',$type)->first();
        if(!$qrCodeType){
            return false;
        }

        $code = random_string(16, true);
        $preParams = explode(',', $qrCodeType->params);
        $preParamCount = count($preParams);
        $paramCount = count($params);
        $paramArr = [];
        foreach($preParams as $param){
            if($preParamCount <= $paramCount){
                $paramArr[$param] = "$params[$param]";
            }
        }

        // 二维码数据，插入到二维码表
        $data = [
            'code'=>$code,
            'type'=>$type,
            'data'=>implode(',',$params),
            'params'=>json_encode($paramArr),
            'create_date'=>time(),
            'expires'=>0,
        ];
        DB::table('qrcode')->insert($data);

        $qrCodeUrl = config('qrcode.qrcode_url').'/'.$data['code'];
        return $qrCodeUrl;

    }
    */

    /**
     * 生成二维码 - 新
     * @param $type
     * @param $params
     * @return bool
     */
    public static function create($type, $params){
        if(!intval($type)){
            return false;
        }
        if(!empty($params) && !is_array($params)){
            return false;
        }

        $qrCodeType = DB::table('qrcode_type')->where('id',$type)->first();
        if(!$qrCodeType){
            return false;
        }

        $preDefinedParams = explode(',', $qrCodeType->params);  // 预定义参数
        $rebuildParams = [];    // 重构参数
        $data = []; // 重构数据

        if(!empty($params)){
            foreach($params as $k=>$v){
                if(in_array($k,$preDefinedParams)){
                    $rebuildParams[$k] = "$v";
                    $data[] = $v;
                }
            }
        }

        $res = [
            'code' => random_string(16, true),
            'type' => $type,
            'data' => !empty($data) ? implode(',', $data) : '',
            'params' => !empty($rebuildParams) ? json_encode($rebuildParams) : '',
            'create_date' => time(),
        ];

        if(DB::table('qrcode')->insert($res)){
            return true;
        }
        return false;
    }

    /**
     * 获取二维码 - 新
     * @param $type
     * @param null $params
     * @return string
     */
    public static function get($type, $params = null){
        if(!intval($type)){
            return '';
        }
        if(!empty($params) && !is_array($params)){
            return '';
        }

        $qrCodeType = DB::table('qrcode_type')->where('id',$type)->first();
        if(!$qrCodeType){
            return '';
        }

        $preDefinedParams = explode(',', $qrCodeType->params);  // 预定义参数
        $rebuildParams = [];    // 重构参数

        if(!empty($params)){
            foreach($params as $k=>$v){
                if(in_array($k,$preDefinedParams)){
                    $rebuildParams[$k] = "$v";
                }
            }
            $code = DB::table('qrcode')->where('type',$type)->where('params',json_encode($rebuildParams))->first();
            if(!$code){
                self::create($type,$params);
            }
            $code = DB::table('qrcode')->where('type',$type)->where('params',json_encode($rebuildParams))->first();
        }else{
            $code = DB::table('qrcode')->where('type',$type)->first();
            if(!$code){
                self::create($type,$params);
            }
            $code = DB::table('qrcode')->where('type',$type)->first();
        }

        return $code;
    }






}
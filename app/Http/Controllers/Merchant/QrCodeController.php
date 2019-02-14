<?php
/**
 * Created by PhpStorm.
 * User: AIMPER
 * Date: 2016/11/29
 * Time: 16:20
 */

namespace App\Http\Controllers\Merchant;
use App\Http\Controllers\Controller;
use App\Libraries\BaseQrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Endroid\QrCode\QrCode;

class QrCodeController extends Controller
{

    /**
     * 查看二维码
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    /*
    public function show(Request $request){
        $data = $request->all();

        if(!intval($data['type'])){
            return '';
        }
        if(!intval($data['id'])){
            return '';
        }
        $type = $data['type'];
        foreach($data as $k=>$v){
            $values[] = $v;
        }
        unset($data['type']);

        if($type == 14){
            $machine = DB::table('iot_machine as im')->where('im.id',$data['id'])
                ->join('iot_dev as id','id.id','=','im.dev_id')
                ->select('id.serial_no')->first();
            $qrCodeData = DB::table('qrcode')->where('data',$machine->serial_no.',0')->select('code')->first();
        }else{
            $qrCodeData = DB::table('qrcode')->where('type',$type)->where('data',implode('-',$data))->select('code')->first();
        }
        if(!$qrCodeData){
            $qrCodeData = BaseQrCode::generateQrCode($values);
        }

        $qrcodeUrl = config('qrcode.qrcode_url');
        $qrCode = new QrCode();
        $qrCode->setText($qrcodeUrl.'/'.$qrCodeData->code)
            ->setSize(200)
            ->setPadding(10)
            ->setErrorCorrection('high')
            ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
            ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
            ->setLabelFontSize(16)
            ->setImageType(QrCode::IMAGE_TYPE_PNG);

        return response()->make($qrCode->get(),200,array('Content-Type' => $qrCode->getContentType()));
    }
    */

    /**
     * 下载验证码
     * @param Request $request
     * @return \Illuminate\Http\Response|string
     */
    /*
    public function download(Request $request){
        if(!$request->has('type') || !intval($request->get('type'))){
            return '';
        }
        if(!$request->has('id') || !intval($request->get('id'))){
            return '';
        }
        $data = $request->all();
        $type = $request->get('type');
        $id = $request->get('id');
        unset($data['type']);

        if(!$request->has('filename')){
            $filename = str_random(16).'.png';
        }else{
            $filename = $request->get('filename').'.png';
        }

        if($type != 14){
            $qrCodeData = DB::table('qrcode')->where('type',$type)->where('data',implode('-',$data))->select('code')->first();
        }elseif($type == 14){
            $machine = DB::table('iot_machine as im')->where('im.id',$id)->join('iot_dev as id','id.id','=','im.dev_id')
                ->select('id.serial_no')->first();
            $qrCodeData = DB::table('qrcode')->where('data',$machine->serial_no.',0')->select('code')->first();
        }
        if(!$qrCodeData){
            foreach($data as $i){
                $ids[] = $i;
            }
            BaseQrCode::generateQrCode(array_merge([$type],$ids));
            if($type == 14){
                $machine = DB::table('iot_machine as im')->where('im.id',$id)->join('iot_dev as id','id.id','=','im.dev_id')
                    ->select('id.serial_no')->first();
                $qrCodeData = DB::table('qrcode')->where('data',$machine->serial_no.',0')->select('code')->first();
            }else{
                $qrCodeData = DB::table('qrcode')->where('type',$type)->where('data',implode('-',$data))->select('code')->first();
            }
        }

        $qrcodeUrl = config('qrcode.qrcode_url');
        $qrCode = new QrCode();
        $qrCode->setText($qrcodeUrl.'/'.$qrCodeData->code)
            ->setSize(200)
            ->setPadding(10)
            ->setErrorCorrection('high')
            ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
            ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
            ->setLabelFontSize(16)
            ->setImageType(QrCode::IMAGE_TYPE_PNG)
            ->create();

        return response()->make($qrCode->get(),200,[
            'Content-Type'=>'image/'.QrCode::IMAGE_TYPE_PNG,
            'Content-Disposition'=>'attachment;filename='.$filename,
        ]);


    }
    */

    /**
     * 查看二维码 - 新
     * @param Request $request
     * @return \Illuminate\Http\Response|string
     */
    public function show(Request $request){
        $data = $request->all();

        if(!intval($data['type'])){
            return '';
        }

        $qrCodeType = DB::table('qrcode_type')->where('id',$data['type'])->first();
        if(!$qrCodeType){
            return '';
        }
        unset($data['type']);

        $qrCodeData = BaseQrCode::get($qrCodeType->id, $data);

        //$qrcodeUrl = config('qrcode.qrcode_url');
        $qrcodeUrl = DB::table('params')->where('name','qrcode_base_url')->select('value')->first();
        $qrCode = new QrCode();
        $qrCode->setText($qrcodeUrl->value.$qrCodeData->code)
            ->setSize(200)
            ->setPadding(10)
            ->setErrorCorrection('high')
            ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
            ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
            ->setLabelFontSize(16)
            ->setImageType(QrCode::IMAGE_TYPE_PNG);

        if(!$request->has('filename')){
            $filename = str_random(16).'.png';
        }else{
            $filename = $request->get('filename').'.png';
        }

        return response()->make($qrCode->get(),200,array('Content-Type' => $qrCode->getContentType()));
    }

    /**
     * 下载二维码 - 新
     * @param Request $request
     * @return \Illuminate\Http\Response|string
     */
    public function download(Request $request){
        $data = $request->all();

        if(!intval($data['type'])){
            return '';
        }

        $qrCodeType = DB::table('qrcode_type')->where('id',$data['type'])->first();
        if(!$qrCodeType){
            return '';
        }
        unset($data['type']);

        $qrCodeData = BaseQrCode::get($qrCodeType->id, $data);

        $qrcodeUrl = DB::table('params')->where('name','qrcode_base_url')->select('value')->first();
        $qrCode = new QrCode();
        $qrCode->setText($qrcodeUrl->value.'/'.$qrCodeData->code)
            ->setSize(200)
            ->setPadding(10)
            ->setErrorCorrection('high')
            ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
            ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
            ->setLabelFontSize(16)
            ->setImageType(QrCode::IMAGE_TYPE_PNG);

        if(!$request->has('filename')){
            $filename = str_random(16).'.png';
        }else{
            $filename = $request->get('filename').'.png';
        }

        return response()->make($qrCode->get(),200,[
            'Content-Type'=>'image/'.QrCode::IMAGE_TYPE_PNG,
            'Content-Disposition'=>'attachment;filename='.$filename,
        ]);
    }



}
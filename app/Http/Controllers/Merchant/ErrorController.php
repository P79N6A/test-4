<?php
/**
 * Created by PhpStorm.
 * User: AIMPER
 * Date: 2016/10/10
 * Time: 12:04
 */

namespace App\Http\Controllers\Merchant;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ErrorController extends Controller
{
    /**
     * @param int $statusCode 状态吗
     * @param string $msg 错误描述
    */
    public function index(Request $request){
        $code = $request->get('code');
        if(!$code){
            $code = 404;
        }
        $msg = $request->get('msg');
        if(!$msg){
            $msg = 'Not Found';
        }
        return response()->view('merchant.error',['code'=>$code,'msg'=>$msg])
            ->setStatusCode($code);
    }
}
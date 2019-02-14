<?php
/**
 * Created by PhpStorm.
 * User: D.Rui
 * Date: 2016/11/1
 * Time: 10:37
 */

namespace App\Http\Controllers\Merchant;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{


    /**
     * 消息列表
    */
    public function index(Request $request){
        $list = DB::table('bus_user_message as bum')->where('bum.bus_user_id',session('id'))
            ->leftJoin('bus_message as bm','bm.id','=','bum.message_id')
            ->select('bum.id','bm.title','bum.read_status','bum.addtime as receive_time')
            ->paginate(20);

        return view('business.bus-msg-list',['msgs'=>$list]);
    }

    /**
     * 消息详情
    */
    public function detail(Request $request){
        if(!$id = $request->get('id')){
            return view('business.error',['code'=>500,'内部错误']);
        }
        $detail = DB::table('bus_user_message as bum')
            ->where('bum.id',$id)
            ->where('bum.bus_user_id',session('id'))
            ->join('bus_message as bm','bm.id','=','bum.message_id')
            ->select('bm.*')->first();

        if(!$detail){
            return view('business.error',['code'=>404,'该消息不存在']);
        }
        DB::table('bus_user_message')->where('id',$id)->update(['read_status'=>1]);

        return view('business.bus-msg-detail',['detail'=>$detail]);
    }


    /**
     * 对消息进行标记已读
    */
    public function markRead(Request $request){
        if(!$id = intval($request->get('id'))){
            return response()->json(['code'=>403,'msg'=>'内部错误']);
        }

        if(DB::table('bus_user_message')->where('id',$id)->where('bus_user_id',session('id'))->update(['read_status'=>1])){
            return response()->json(['code'=>200,'msg'=>'标记已读成功']);
        }else{
            return response()->json(['code'=>500,'msg'=>'标记已读失败']);
        }
    }

    /**
     * 删除消息
    */
    public function delete(Request $request){
        if(!$id = intval($request->get('id'))){
            return response()->json(['code'=>403,'msg'=>'内部错误']);
        }

        if(DB::table('bus_user_message')->where('id',$id)->where('bus_user_id',session('id'))->delete()){
            return response()->json(['code'=>200,'msg'=>'删除成功']);
        }else{
            return response()->json(['code'=>500,'msg'=>'删除失败']);
        }
    }
}

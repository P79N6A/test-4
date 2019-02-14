<?php
/**
 * Created by PhpStorm.
 * User: D.Rui
 * Date: 2016/11/2
 * Time: 16:06
 */

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommonController extends Controller
{

    /**
     * 获取城市列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCities(Request $request){
        if(!$request->has('pid')){
            return response()->json(['code'=>200,'msg'=>'no pid provided']);
        }

        $cities = DB::table('base.region')->where('parent_id',$request->get('pid'))
            ->select('id','city')->get();
        return response()->json(['code'=>200,'msg'=>'获取成功','data'=>$cities]);
    }

    /**
     * 获取区/县列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBlocks(Request $request){
        if(!$request->has('cid')){
            return response()->json(['code'=>200,'msg'=>'no cid provided']);
        }

        $blocks = DB::table('base.region')->where('parent_id',$request->get('cid'))
            ->select('id','county')->get();
        return response()->json(['code'=>200,'msg'=>'获取成功','data'=>$blocks]);

    }


}
<?php
/**
 * Created by PhpStorm.
 * User: D.Rui
 * Date: 2016/11/16
 * Time: 14:07
 */

namespace App\Http\Controllers\Merchant;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CabinetController extends Controller
{

    /**
     * 机台列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(){
        if(!empty($this->storeIds)){
            $list = DB::table('cabinet as c')->whereIn('c.store_id',$this->storeIds)
                ->where('c.bus_userid','=',$this->parentUserId)
                ->leftJoin('attachment as a','a.id','=','c.image')
                ->select('c.*','a.path')->paginate(20);
        }

        return view('merchant.cabinet-list',['cabinets'=> !empty($list) ? $list : [] ]);
    }

    /**
     * 添加机台
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function add(Request $request)
    {
        if ($request->isMethod('get')) {    // get，显示表单
            return view('merchant.add-cabinet',['stores'=>$this->stores]);

        } elseif ($request->isMethod('post')) { // post，处理数据
            $data = $request->only(['name','iot_id','store_id','image','gallery','price','introduction','guide']);

            if(empty($data['name']) || !preg_replace('/\s/','',$data['name'])){
                return $this->response(403,'名称不能为空');
            }
            if(!intval($data['store_id'])){
                return $this->response(403,'请选择对应门店');
            }
            if(!in_array($data['store_id'],$this->storeIds)){
                return $this->response(403,'您无权选择该门店');
            }
            if(!intval($data['image'])){
                return $this->response(403,'图片不能为空');
            }
            if(empty($data['gallery'])){
                return $this->response(403,'相册不能为空');
            }
            // 把图片ID最后的逗号去掉：1,2,3,4,  ==> 1,2,3,4
            $data['gallery'] = preg_replace('/,$/','',$data['gallery']);

            if(!floatval($data['price'])){
                return $this->response(403,'价格不能为空');
            }
            if(empty($data['introduction']) || !preg_replace('/\s/','',$data['introduction'])){
                return $this->response(403,'介绍不能为空');
            }
            if(empty($data['guide']) || !preg_replace('/\s/','',$data['guide'])){
                return $this->response(403,'玩法攻略不能为空');
            }

            $data['addtime'] = time();

            if($cabinetId = DB::table('cabinet')->insertGetId($data)){
                return $this->response(200,'添加成功',route('merchant.cabinet-list'));
            }else{
                return $this->response(500,'添加失败');
            }
        }

    }

    /**
     * 修改机台
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function edit(Request $request){
        if(!$id = intval($request->get('id'))){
            return view('merchant.error',['code'=>500,'msg'=>'内部错误']);
        }
        // 判断该机台是否是本账号门店所属
        $sids = $this->storeIds ? $this->storeIds : [];
        if($sids){
            $cabinet = DB::table('cabinet as c')->where('c.id',$id)
                ->where('c.bus_userid',$this->parentUserId)
                ->leftJoin('attachment as a','a.id','=','c.image')
                ->select('c.*','a.path')->first();
        }else{
            $cabinet = null;
        }
        if(!$cabinet){
            if($request->isMethod('get')){
                return view('merchant.error',['code'=>404,'msg'=>'该机台不存在']);
            }elseif($request->isMethod('post')){
                return $this->response(404,'该机台不存在');
            }
        }
        // 取出相册图片
        $gallery = DB::table('attachment')->whereIn('id',explode(',',$cabinet->gallery))->lists('path');

        if($request->isMethod('get')){  // GET，显示表单
            return view('merchant.edit-cabinet',['cabinet'=>$cabinet,'stores'=>$this->stores,'gallery'=>$gallery]);

        }elseif($request->isMethod('post')){    // POST，处理数据
            $data = $request->only(['name','iot_id','store_id','image','gallery','price','introduction','guide']);

            if(empty($data['name']) || !preg_replace('/\s/','',$data['name'])){
                return $this->response(403,'名称不能为空');
            }
            if(!intval($data['store_id'])){
                return $this->response(403,'请选择对应门店');
            }
            if(!in_array($data['store_id'],$this->storeIds)){
                return $this->response(403,'您无权选择该门店');
            }
            if(!intval($data['image'])){
                return $this->response(403,'图片不能为空');
            }
            if(empty($data['gallery'])){
                return $this->response(403,'相册不能为空');
            }
            // 把图片ID最后的逗号去掉：1,2,3,4,  ==> 1,2,3,4
            $data['gallery'] = preg_replace('/,$/','',$data['gallery']);

            if(!floatval($data['price'])){
                return $this->response(403,'价格不能为空');
            }
            if(empty($data['introduction']) || !preg_replace('/\s/','',$data['introduction'])){
                return $this->response(403,'介绍不能为空');
            }
            if(empty($data['guide']) || !preg_replace('/\s/','',$data['guide'])){
                return $this->response(403,'玩法攻略不能为空');
            }

            // 如果图片和原图不一样，则更新
            if($data['image'] != $cabinet->image){
                $file = APP_ROOT.'/'.config('upload.root_path').'/'.$cabinet->path;
                @unlink($file);
                DB::table('attachment')->where('id',$cabinet->image)->delete();
            }
            // 如果相册和原图不一样，则更新
            if(array_diff(explode(',',$data['gallery']),explode(',',$cabinet->gallery))){
                $dir = APP_ROOT.'/'.config('upload.root_path').'/';
                foreach($gallery as $item){
                    @unlink($dir.$item);
                }
                DB::table('attachment')->whereIn('id',explode(',',$cabinet->gallery))->delete();
            }

            if(DB::table('cabinet')->where('id',$id)->update($data) !== false){
                return $this->response(200,'修改成功',route('merchant.cabinet-list'));
            }else{
                return $this->response(500,'修改失败');
            }

        }
    }

    public function delete(Request $request){
        if(!$id = $request->get('id')){
            return $this->response(500,'内部错误');
        }

        $cabinet = DB::table('cabinet as c')->where('c.id',$id)
            ->where('c.bus_userid',$this->parentUserId)
            ->whereIn('c.store_id',$this->storeIds)
            ->leftJoin('attachment as a','a.id','=','c.image')
            ->select('c.id','c.image','c.gallery','a.path')->first();
        if(!$cabinet){
            return $this->response(404,'该机台不存在');
        }

        DB::beginTransaction();
        try{
            if(!empty($cabinet->path)){
                $file = APP_ROOT.'/'.config('upload.root_path').'/'.$cabinet->path;
                @unlink($file);
            }

            $image = $cabinet->image ? $cabinet->image : 0;
            $id = $cabinet->id ? $cabinet->id : 0;

            DB::table('attachment')->where('id',$image)->delete();
            DB::table('cabinet')->where('id',$id)->delete();
            DB::commit();

            return $this->response(200,'删除成功',route('merchant.cabinet-list'));
        }catch(Exception $e){
            DB::rollback();
            return $this->response(500,'删除失败');
        }

        
    }







}
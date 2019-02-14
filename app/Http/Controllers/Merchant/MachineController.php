<?php

namespace App\Http\Controllers\Merchant;

use App\Libraries\BaseQrCode;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class MachineController extends Controller
{

    /**
     * 添加/修改机台信息时的允许字段
    */
    protected $fields = ['id','name','product_id','dev_id','usable','remarks'];

    /**
     * 机台列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(){
        if(empty($this->storeIds)){
            return view('business.machine-list');
        }
        $list = DB::table('iot_machine as im')->where('im.del_flag',0)
            ->join('iot_product as ip',function($join){
                $join->on('ip.id','=','im.product_id')->whereIn('ip.store_id',$this->storeIds);
            })
            ->leftJoin('iot_dev as id','id.id','=','im.dev_id')
            ->leftJoin('bus_users as bu','bu.id','=','im.create_by')
            ->leftJoin('iot_online_status as iosa','iosa.id','=','im.id')
            ->select(['im.*','ip.name as product_name','id.name as dev_name','id.serial_no','bu.name as create_user','iosa.status as online_status'])
            ->orderBy('id','desc')->paginate(20);

        return view('business.machine-list',['machines'=>$list]);
    }

    /**
     * 添加机台
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function add(Request $request){
        if($request->isMethod('get')){
            $products = DB::table('iot_product')->whereIn('store_id',$this->storeIds)
                ->where('del_flag',0)->select('id','name')->get();
            // 已经选择过的设备
            $selectedDevs = DB::table('iot_machine')->where('del_flag',0)->lists('dev_id');
            $devices = DB::table('iot_dev')->whereNotIn('id',$selectedDevs)
                ->where('del_flag',0)->where('usable',1)->select('id','name')->get();


            return view('business.add-machine',['products'=>$products,'devices'=>$devices]);
        }elseif($request->isMethod('post')){
            $data = $request->only($this->fields);
            unset($data['id']);

            if(!intval($data['product_id'])){
                return $this->response(403,'请选择产品类型');
            }
            if(!intval($data['dev_id'])){
                return $this->response(403,'请选择设备类型');
            }
            if(!preg_replace('/\s/','',$data['name'])){
                return $this->response(403,'请填写名字');
            }

            $product = DB::table('iot_product')->where('id',$data['product_id'])
                ->where('del_flag',0)->first();
            if(!$product){
                return $this->response(404,'该产品不存在');
            }

            $dev = DB::table('iot_dev as id')->where('id.id',$data['dev_id'])->where('id.del_flag',0)
                ->where('id.usable',1)->join('iot_machine as im',function($join){
                    $join->on('im.dev_id','=','id.id')->where('im.del_flag','=',0);
                })->first();
            if($dev){
                return $this->response(403,'该设备已分配到某个机台，不能重复分配');
            }

            $data['create_by'] = session('id');
            $data['create_date'] = date('Y-m-d H:i:s');

            $machineId = DB::table('iot_machine')->insertGetId($data);
            if($machineId > 0){
                // 生成二维码
                BaseQrCode::generateQrCode([14,$machineId]);
                return $this->response(200,'机台添加成功',route('business.machine-list'));
            }else{
                return $this->response(500,'机台添加失败');
            }
        }
    }

    /**
     * 修改机台
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function edit(Request $request){
        if($request->isMethod('get')){
            if(!$id = intval($request->get('id'))){
                return view('business.error',['code'=>500,'msg'=>'内部错误']);
            }
            $machine = DB::table('iot_machine as im')->where('im.id',$id)
                ->join('iot_product as ip',function($join){
                    $join->on('ip.id','=','im.product_id')->whereIn('ip.store_id',$this->storeIds);
                })->select('im.*')->first();
            if(!$machine){
                return view('business.error',['code'=>404,'msg'=>'该机台不存在']);
            }

            $products = DB::table('iot_product')->whereIn('store_id',$this->storeIds)
                ->where('del_flag',0)->select('id','name')->get();
            $devices = DB::table('iot_dev')->where('usable',1)->select('id','name')->get();

            return view('business.edit-machine',['products'=>$products,'devices'=>$devices,'machine'=>$machine]);

        }elseif($request->isMethod('post')){
            $data = $request->only($this->fields);

            if(!intval($data['id'])){
                return $this->response(500,'内部错误');
            }
            if(!intval($data['product_id'])){
                return $this->response(403,'请选择产品类型');
            }
            if(!intval($data['dev_id'])){
                return $this->response(403,'请选择设备类型');
            }
            if(!preg_replace('/\s/','',$data['name'])){
                return $this->response(403,'请填写名字');
            }

            $id = $data['id'];
            unset($data['id']);
            $data['update_by'] = session('id');
            $data['update_date'] = date('Y-m-d H:i:s');

            if(DB::table('iot_machine')->where('id',$id)->update($data) !== false){
                return $this->response(200,'机台修改成功',route('business.machine-list'));
            }else{
                return $this->response(500,'修改失败');
            }
        }
    }

    /**
     * 删除机台
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request){
        if(!$id = intval($request->get('id'))){
            return $this->response(500,'内部错误');
        }

        $machine = DB::table('iot_machine as im')->where('im.id',$id)
            ->join('iot_product as ip',function($join){
                $join->on('ip.id','=','im.product_id')->whereIn('ip.store_id',$this->storeIds);
            })->select('im.*')->first();
        if(!$machine){
            return $this->response(404,'该机台不存在');
        }
        if(DB::table('iot_machine')->where('id',$id)->update(['del_flag'=>time()])){
            return $this->response(200,'删除成功',route('business.machine-list'));
        }else{
            return $this->response(500,'删除失败');
        }

    }

}

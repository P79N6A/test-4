<?php
namespace App\Http\Controllers\Merchant;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\EquipmentModel;
use App\Services\EquipmentService;
use App\Models\StoreModel;
use App\Models\CityModel;

class EquipmentController extends Controller{

    private $validate = [
        'city_id' => 'required|integer|min:1',
        'store_id' => 'required|integer|min:1',
        'code' => 'required',
        'name' => 'required',
    ];
    private $messages = [

    ];
    private $attributes = [
        'city_id' => '城市',
        'store_id' => '门店',
        'code' => '机台编码',
        'name' => '机台名称',
    ];

    /**
     * 列表
     */
    public function lists(Request $request){
        $storeId = $request->input('store_id');

        $list = EquipmentModel::with(['store', 'city']);

        if($request->has('city')){
            $city = $request->input('city');
            $list->where('city_id',$city);
        }

        if($request->has('store')){
            $store = $request->input('store');
            $list->where('store_id',$store);
        }

        if($request->has('keyword')){
            $keyword = $request->input('keyword');
            $list->where('name','like', '%' . $keyword . '%')->orWhere('code', 'like', '%' . $keyword . '%');
        }

        if($storeId){
            $equipment_list = $list->where('store_id', $storeId)->orderBy('id','desc')->paginate(25);
        } else {
            $equipment_list = $list->whereIn('store_id', $this->storeIds)->orderBy('id','desc')->paginate(25);
        }

        $city_list = CityModel::select('id','name')->orderBy('is_hot')->get();
        $store_list = StoreModel::select('name','id')->orderBy('id','desc')->get();
        
        foreach($list as $key => $value){
            $resp = EquipmentService::getMachineStatus($value['code']);
            $list[$key]['online'] = $resp['code'] ? 0 : 1;
        }

        return view('merchant.equipment.list',[
            'list' => $equipment_list,
            'city_list'=>$city_list,
            'store_list'=>$store_list,
            'keyword' => isset($keyword) ? $keyword : '',
            'store' => isset($store) ? $store : '',
            'city' => isset($city) ? $city : '',
        ]);
    }

    /**
     * 添加
     */
    public function add(Request $request){
        if($request->isMethod('post')){
            // try{
            //     $this->validate($request,$this->validate,$this->messages,$this->attributes);
            // }catch(\Exception $e){
            //     return $this->response(502,$e->validator->errors()->first());
            // }

            //验证通过，保存城市
            $equipment = new EquipmentModel();
            $equipment->city_id = $request->input('city_id');
            $equipment->store_id = $request->input('store_id');
            $equipment->model = $request->input('model', '');
            $equipment->code = $request->input('code');
            $equipment->name = $request->input('name');

            if($equipment->save()){
                return $this->response(200,'添加成功！',route('business.equipment.list'));
            }else{
                return $this->response(422,'处理失败，请重试！');
            }
        }

        return view('merchant.equipment.add',[]);
    }

    /**
     * 修改
     */
    public function modify(Request $request){
        $id = intval($request->input('id'));
        if(empty($id)) return $this->response(500,'ID非法！');
        $equipment = EquipmentModel::find($id);
        if(empty($equipment)) return redirect(route('business.equipment.list'));

        if($request->isMethod('POST')){
            try{
                $this->validate($request,$this->validate,$this->messages,$this->attributes);
            }catch(\Exception $e){
                return $this->response(502,$e->validator->errors()->first());
            }

            //验证通过，保存城市
            $equipment->city_id = $request->input('city_id');
            $equipment->store_id = $request->input('store_id');
            $equipment->model = $request->input('model', '');
            $equipment->code = $request->input('code');
            $equipment->name = $request->input('name');
            $equipment->disabled = $request->input('disabled');

            if($equipment->save()){
                return $this->response(200,'修改成功！',route('business.equipment.list'));
            }else{
                return $this->response(422,'处理失败，请重试！');
            }
        }

        //获取已选城市下的门店列表
        $stores = StoreModel::where('city_id', $equipment->city_id)->get();
       
        // 获取城市列表
        $citys = CityModel::get();

        return view('merchant.equipment.modify',[
            'info' => $equipment,
            'stores' => $stores,
            'citys' => $citys,
        ]);
    }

    /**
     * 不可用设置替换
     */
    // public function setDisabled(Request $request){
    //     $id = intval($request->input('id'));
    //     if(empty($id)) return $this->response(500,'ID非法！');

    //     $info = CourseModel::find($id);
    //     if(empty($info)){
    //         return $this->response(500,'类型不存在！');
    //     }

    //     $info->disabled = intval(!!!($info->disabled));
    //     if($info->save()){
    //         return $this->response(200,'更新成功！');
    //     }else{
    //         return $this->response(422,'更新失败！');
    //     }
    // }

    /**
     * 删除
     */
    // public function delete(Request $request){
    //     $id = intval($request->input('id'));
    //     if(empty($id)) return $this->response(500,'ID非法！');

    //     //判断是否存在课程，如果存在课程则删除失败
    //     $count = \App\Models\CourseModel::where('type_id',$id)->count();
    //     if($count > 0){
    //         return $this->response(422,'存在课程，不能删除！');
    //     }

    //     if(CourseModel::destroy($id)){
    //         return $this->response(200,'删除成功！',route('admin.course.type.list'));
    //     }else{
    //         return $this->response(422,'删除失败');
    //     }
    // }
}

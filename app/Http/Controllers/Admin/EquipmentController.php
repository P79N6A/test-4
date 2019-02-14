<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\EquipmentModel;
use App\Models\EquipmentGamesModel;
use App\Services\EquipmentService;
use App\Models\StoreModel;
use App\Models\CityModel;
use App\Models\GamesLevelModel;

class EquipmentController extends Controller{

    private $validate = [
        'city_id' => 'required|integer|min:1',
        'store_id' => 'required|integer|min:1',
        'code' => 'required',
        'name' => 'required',
        'model' => 'required'
    ];
    private $messages = [

    ];
    private $attributes = [
        'city_id' => '城市',
        'store_id' => '门店',
        'code' => '机台编码',
        'name' => '机台名称',
        'model' => '机台型号',
    ];

    private $validateGames = [
        'city_id' => 'required|integer|min:1',
        'store_id' => 'required|integer|min:1',
        'equipment_id' => 'required|integer|min:1',
        'name' => 'required',
        'model' => 'required'
    ];
    private $messagesGames = [

    ];
    private $attributesGames = [
        'city_id' => '城市',
        'equipment_id' => '机台',
        'store_id' => '门店',
        'name' => '游戏名称',
        'model' => '游戏编号',
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
            $store_list = StoreModel::where('city_id', $request->input('city', ''))->get();
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
            $equipment_list = $list->orderBy('id','desc')->paginate(25);
        }

        $city_list = CityModel::select('id','name')->orderBy('is_hot')->get();
       // $store_list = StoreModel::select('name','id')->orderBy('id','desc')->get();
        
        // foreach($list as $key => $value){
        //     $resp = EquipmentService::getMachineStatus($value['code']);
        //     $list[$key]['online'] = $resp['code'] ? 0 : 1;
        // }

        return view('admin.equipment.list',[
            'list' => $equipment_list,
            'city_list'=>$city_list,
            'store_list'=>isset($store_list) ? $store_list : '',
            'keyword' => isset($keyword) ? $keyword : '',
            'store' => isset($store) ? $store : '',
            'city' => isset($city) ? $city : '',
        ]);
    }

    /**
     *机台列表json格式
     */
    public function json(Request $request){
        $list = EquipmentModel::where('store_id', $request->input('store_id', ''))->get();

        return json_encode($list);
    }

    /**
     * 添加
     */
    public function add(Request $request){
        if($request->isMethod('post')){
            try{
                $this->validate($request,$this->validate,$this->messages,$this->attributes);
            }catch(\Exception $e){
                return $this->response(502,$e->validator->errors()->first());
            }

            //验证通过，保存城市
            $equipment = new EquipmentModel();
            $equipment->city_id = $request->input('city_id');
            $equipment->store_id = $request->input('store_id');
            $equipment->model = $request->input('model', '');
            $equipment->code = $request->input('code');
            $equipment->name = $request->input('name');

            if($equipment->save()){
                return $this->response(200,'添加成功！',route('admin.equipment.list'));
            }else{
                return $this->response(422,'处理失败，请重试！');
            }
        }

        return view('admin.equipment.add',[]);
    }

    /**
     * 修改
     */
    public function modify(Request $request){
        $id = intval($request->input('id'));
        if(empty($id)) return $this->response(500,'ID非法！');
        $equipment = EquipmentModel::find($id);
        if(empty($equipment)) return redirect(route('admin.equipment.list'));

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
                return $this->response(200,'修改成功！',route('admin.equipment.list'));
            }else{
                return $this->response(422,'处理失败，请重试！');
            }
        }

        //获取已选城市下的门店列表
        $stores = StoreModel::where('city_id', $equipment->city_id)->get();
       
        // 获取城市列表
        $citys = CityModel::get();

        return view('admin.equipment.modify',[
            'info' => $equipment,
            'stores' => $stores,
            'citys' => $citys,
        ]);
    }

    /**
     *机台游戏列表
     */
    public function gamesList(Request $request){
        $list = EquipmentGamesModel::with(['store', 'equipment'])->paginate(25);

        return view('admin.equipment.games-list',[
            'list' => $list,
            // 'city_list'=>$city_list,
            // 'store_list'=>isset($store_list) ? $store_list : '',
            // 'keyword' => isset($keyword) ? $keyword : '',
            // 'store' => isset($store) ? $store : '',
            // 'city' => isset($city) ? $city : '',
        ]);

    }

    /**
     *添加机台游戏
     */
    public function gamesAdd(Request $request){
        if($request->isMethod('post')){
            try{
                $this->validate($request,$this->validateGames,$this->messagesGames,$this->attributesGames);
            }catch(\Exception $e){
                return $this->response(502,$e->validator->errors()->first());
            }

            $equipment = new EquipmentGamesModel();
            $equipment->city_id = $request->input('city_id');
            $equipment->equipment_id = $request->input('equipment_id');
            $equipment->store_id = $request->input('store_id');
            $equipment->model = $request->input('model', '');
            $equipment->name = $request->input('name');
            $equipment->disabled = $request->input('disabled');

            if($equipment->save()){
                return $this->response(200,'添加成功！',route('admin.equipment.games.list'));
            }else{
                return $this->response(422,'处理失败，请重试！');
            }
        }
        return view('admin.equipment.games-add',[]);
    }

    /**
     *修改机台游戏
     */
    public function gamesModify(Request $request){
        $equipment = EquipmentGamesModel::find($request->input('id'));
        if($request->isMethod('post')){
            try{
                $this->validate($request,$this->validateGames,$this->messagesGames,$this->attributesGames);
            }catch(\Exception $e){
                return $this->response(502,$e->validator->errors()->first());
            }

            $equipment->equipment_id = $request->input('equipment_id');
            $equipment->store_id = $request->input('store_id');
            $equipment->model = $request->input('model', '');
            $equipment->name = $request->input('name');
            $equipment->disabled = $request->input('disabled');

            if($equipment->save()){
                return $this->response(200,'修改成功！',route('admin.equipment.games.list'));
            }else{
                return $this->response(422,'处理失败，请重试！');
            }
        }

        //获取已选城市下的门店列表
        $stores = StoreModel::where('city_id', $equipment->city_id)->get();
       
        // 获取城市列表
        $citys = CityModel::get();

        // 获取已选门店的机台列表
        $equipments = EquipmentModel::where('store_id', $equipment->store_id)->get();
        // dump($equipment);

        return view('admin.equipment.games-modify',[
            'info' => $equipment,
            'stores' => $stores,
            'citys' => $citys,
            'equipments' => $equipments
        ]);
    }

    /**
     *游戏难易度设置
     */
    public function gameLevel(Request $request){
        $id = $request->input('id');
        if($request->isMethod('post')){
            $data = $request->all();
            $num = 0;
            $save_data = [];
            $type_num = count($data['type']) / count($data['score_begin']);//每个难度判断依据对应的参数数量
            $game_list = EquipmentGamesModel::where('id',$id)->select('model')->first();
            DB::beginTransaction();
            $init_data_data = [];
            for ($i=0;$i<count($data['init_type']);$i++) {
                $init_data_data[$data['init_type'][$i]] = $data['init_value'][$i];
            }

            $init_data['data']       = json_encode($init_data_data);
            $init_data['is_init']    = 1;
            $init_data['games_id']   = $id;
            $init_data['updated_at'] = date('Y-m-d H:i:s');
            $init_list = DB::table('games_level')->where('games_id',$id)->where('is_init',1)->first();
            if(!isset($init_list)){
                $init = DB::table('games_level')->insert($init_data);
            }else{
                $init = DB::table('games_level')->where('games_id',$id)->where('is_init',1)->update($init_data);
            }
            
            if(!$init){
                DB::rollback();
                return $this->response(422,'处理失败，请重试！');
            }

            DB::table('games_level')->where('games_id',$id)->where('is_init',0)->delete();

            for ($i=0;$i<count($data['score_begin']);$i++) {
                for($j=0;$j<$type_num;$j++){
                    if($data['value'][$num] !== ""){
                        $save_data[$data['type'][$num]] = $data['value'][$num];
                        $num ++;
                    }
                }

                $gamesLevel['data']         = json_encode($save_data);
                $gamesLevel['score_end']    = $data['score_end'][$i];
                $gamesLevel['score_begin']  = $data['score_begin'][$i];
                $gamesLevel['games_id']     = $id;
                $gamesLevel['model']       = $game_list->model;
                $gamesLevel['updated_at']   = date('Y-m-d H:i:s');
                $games_result = DB::table('games_level')->insert($gamesLevel);

                if(!$games_result){
                    DB::rollback();
                    return $this->response(422,'处理失败，请重试！');
                }
            }
            DB::commit();

            return $this->response(200,'添加成功');
        }

        $result = GamesLevelModel::where('games_id',$id)->get();
        $result = json_decode(json_encode($result),true);
        foreach ($result as $key => $value) {
            $data =  json_decode(json_encode(json_decode($value['data'])),true); //字符串转数组
            $num=0;
            foreach ($data as $arr => $val) {
                $result[$key]['date'][$num]['type'] = $arr;
                $result[$key]['date'][$num]['value'] = $val;
                $num++;
            }
        }
        $result = json_decode(json_encode($result));
        // dump($result);

        return view('admin.equipment.level',[
            'result' =>  $result,
            'id' => $id
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

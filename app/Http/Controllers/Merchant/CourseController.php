<?php
namespace App\Http\Controllers\Merchant;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\CourseModel;
use App\Models\SuitableAgeModel;
use App\Models\BusinessModel;
use App\Models\CourseTypeModel;
use App\Models\CityModel;
use App\Models\StoreModel;
use App\Models\CourseStoreModel;

class CourseController extends Controller{

    private $validate = [
        'name' => 'required|between:2,16',
        'type_id' => 'required|integer|min:1',
        'price' => 'required|numeric',
        // 'suitable_age' => 'required|integer|min:1',
        'content' => 'required',
        'store_ids' => 'required|array',
        'img' => 'required|integer|min:1',
        'is_hot' => 'required|boolean',
        'is_recommend' => 'required|boolean',
        'disabled' => 'required|boolean',
        'sort' => 'required|integer|min:0'
    ];
    private $messages = [

    ];
    private $attributes = [
        'name' => '课程名称',
        'type_id' => '课程分类',
        'price' => '价格',
        // 'suitable_age' => '合适年龄段',
        'content' => '课程介绍',
        'store_ids' => '适用门店',
        'img' => '首页图',
        'is_hot' => '热门',
        'is_recommend' => '推荐',
        'sort' => '排序'
    ];

    /**
     * 列表
     */
    public function lists(Request $request){
        $storeId = $request->input('store_id');

        $lists = CourseModel::with(['pic', 'suitable', 'type', 'city']);

        if($request->has('city')){
            $city = $request->input('city');
            $lists->where('city_id',$city);
        }

        if ($request->has('keyword')) {
            $keyword = $request->get('keyword');
            $lists->where('name', 'like', '%' . $keyword . '%');
        }

        if($storeId){
            $courseIds = CourseStoreModel::where('store_id', $storeId)->pluck('course_id');

            $list = $lists->whereIn('id', $courseIds)->orderBy('id','desc')->paginate(25);
        } else {
            $courseIds = CourseStoreModel::whereIn('store_id', $this->storeIds)->pluck('course_id');

            $list = $lists->whereIn('id', $courseIds)->orderBy('id','desc')->paginate(25);
        }

        foreach ($list as $key => $value) {
            $storeIds = explode(',', $value['store_ids']);
            $storeNames = StoreModel::whereIn('id', $storeIds)->pluck('name')->toArray();

            $list[$key]['stores'] = implode(' | ', $storeNames);
        }


        $city_list = CityModel::orderBy('is_hot')->get();

        return view('merchant.course.list',[
            'list' => $list,
            'city_list' =>$city_list,
            'city' => isset($city) ? $city : '',
            'keyword' => isset($keyword) ? $keyword : '',
        ]);
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

            $storeIds = $request->input('store_ids');

            // 开启事务
            DB::beginTransaction();

            try {
                $resp1 = DB::table('course')
                    ->insertGetId([
                        'name' => $request->input('name'),
                        'type_id' => $request->input('type_id'),
                        'price' => $request->input('price') * 100, //转换为分做单位
                        // 'suitable_age' => $request->input('suitable_age'), //合适年龄段
                        'content' => $request->input('content'),
                        'img' => $request->input('img'), //首页图
                        'is_hot' => $request->input('is_hot'),
                        'is_recommend' => $request->input('is_recommend'),
                        'disabled' => $request->input('disabled'),
                        'city_id' => $request->input('city_id'),
                        'store_ids' => implode(',', $storeIds), //适用门店
                        'sort' => $request->input('sort'),
                        'created_at' => date('Y-m-d H:i:s')
                    ]);

                $data = [];

                foreach ($storeIds as $key => $storeId) {
                    $temp['course_id'] = $resp1;
                    $temp['store_id'] = $storeId;

                    $data[] = $temp;
                }
                $resp2 = DB::table('course_store')->insert($data);

                if($resp1 && $resp2){
                    DB::commit();
                    return $this->response(200,'添加成功！',route('business.course.list'));
                } else {
                    DB::rollback();
                    return $this->response(422,'处理失败，请重试！');
                }
            } catch(\Exception $e) {
                DB::rollback();
                Log::error($e->getMessage());
                return $this->response(422, $e->getMessage());
            }
        }

        //获取合适年龄段
        $suitable = SuitableAgeModel::get();
        //获取当前商户下的所有门店
        // $bus_user = BusinessModel::find(1);
        // $stores = $bus_user->stores;
        //获取所有课程分类
        $course_type = CourseTypeModel::get();

        return view('merchant.course.add',[
            'suitable' => $suitable,
            // 'stores' => $stores,
            'course_type' => $course_type
        ]);
    }

    /**
     * 修改
     */
    public function modify(Request $request){
        $id = intval($request->input('id'));
        if(empty($id)) return $this->response(500,'ID非法！');
        $course = CourseModel::find($id);
        if(empty($course)) return redirect(route('business.course.list'));

        if($request->isMethod('POST')){
                try{
                    $this->validate($request,$this->validate,$this->messages,$this->attributes);
                }catch(\Exception $e){
                    return $this->response(502,$e->validator->errors()->first());
                }

            $storeIds = $request->input('store_ids');

            // 开启事务
            DB::beginTransaction();

            try {
                // 更新课程信息
                $resp1 = DB::table('course')
                    ->where('id', $id)
                    ->update([
                        'name' => $request->input('name'),
                        'type_id' => $request->input('type_id'),
                        'price' => $request->input('price') * 100, //转换为分做单位
                        // 'suitable_age' => $request->input('suitable_age'), //合适年龄段
                        'content' => $request->input('content'),
                        'img' => $request->input('img'), //首页图
                        'is_hot' => $request->input('is_hot'),
                        'is_recommend' => $request->input('is_recommend'),
                        'disabled' => $request->input('disabled'),
                        'city_id' => $request->input('city_id'),
                        'store_ids' => implode(',', $storeIds), //适用门店
                        'sort' => $request->input('sort'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

                $data = [];

                // 删除旧的课程门店对应关系
                DB::table('course_store')->where('course_id', $id)->delete();

                // 新增课程门店对应关系
                foreach ($storeIds as $key => $storeId) {
                    $temp['course_id'] = $id;
                    $temp['store_id'] = $storeId;

                    $data[] = $temp;
                }
                $resp2 = DB::table('course_store')->insert($data);

                if($resp1 && $resp2){
                    DB::commit();
                    return $this->response(200,'修改成功！',route('business.course.list'));
                } else {
                    DB::rollback();
                    return $this->response(422,'处理失败，请重试！');
                }
            } catch(\Exception $e) {
                DB::rollback();
                Log::error($e->getMessage());
                return $this->response(422, $e->getMessage());
            }
        }

        //获取合适年龄段
        $suitable = SuitableAgeModel::get();
        //获取门店
        // $bus_user = BusinessModel::find(1);
        // $stores = $bus_user->stores;
        $stores = $this->stores;
        //获取所有课程分类
        $course_type = CourseTypeModel::get();

        // 获取城市列表
        $citys = CityModel::get();

        $store_ids = [];
        if(!empty($course->store_ids)) $store_ids = explode(',',$course->store_ids);

        return view('merchant.course.modify',[
            'info' => $course,
            'suitable' => $suitable,
            'stores' => $stores,
            'citys' => $citys,
            'course_type' => $course_type,
            'store_ids' => $store_ids
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

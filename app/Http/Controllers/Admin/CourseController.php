<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CityModel;
use App\Models\CourseModel;
use App\Models\CourseStoreModel;
use App\Models\CourseTypeModel;
use App\Models\OrderModel;
use App\Models\StoreModel;
use App\Models\SuitableAgeModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Log;

class CourseController extends Controller
{

    private $validate = [
        'name' => 'required|between:2,16',
        'type_id' => 'required|integer|min:1',
        'price' => 'required|numeric|min:0.01',
        // 'suitable_age' => 'required|integer|min:1',
        'content' => 'required',
        'store_ids' => 'required|array',
        'img' => 'required|integer|min:1',
        'is_hot' => 'required|boolean',
        'is_recommend' => 'required|boolean',
        'disabled' => 'required|boolean',
        'sort' => 'required|integer|min:0',
    ];
    private $messages = [
        'price.min' => ':attribute 最低0.01元',
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
        'sort' => '排序',
    ];

    /**
     * 列表
     */
    public function lists(Request $request)
    {
        $storeId = $request->input('store_id');

        $lists = CourseModel::with(['pic', 'suitable', 'type', 'city']);

        if($request->has('city')){
            $city = $request->input('city');
            $store_list = StoreModel::where('city_id', $request->input('city', ''))->get();
            $lists->where('city_id',$city);
        }

        if($request->has('store')){
            $store = $request->input('store');
            $lists->where('store_ids',$store);
        }

        if ($request->has('keyword')) {
            $keyword = $request->get('keyword');
            $lists->where('name', 'like', '%' . $keyword . '%');
        }

        if($storeId){
            $courseIds = CourseStoreModel::where('store_id', $storeId)->pluck('course_id');

            $list = $lists->whereNull('deleted_at')->whereIn('id', $courseIds)->orderBy('id','desc')->paginate(25);
        } else {
            $list = $lists->whereNull('deleted_at')->orderBy('id','desc')->paginate(25);
        }

        foreach ($list as $key => $value) {
            $storeIds = explode(',', $value['store_ids']);
            $storeNames = StoreModel::whereIn('id', $storeIds)->pluck('name')->toArray();

            $list[$key]['stores'] = implode(' | ', $storeNames);
        }


        $city_list = CityModel::orderBy('is_hot')->get();

        return view('admin.course.list',[
            'list' => $list,
            'city_list' =>$city_list,
            'city' => isset($city) ? $city : '',
            'store' => isset($store) ? $store : '',
            'store_list' => isset($store_list) ? $store_list : '',
            'keyword' => isset($keyword) ? $keyword : '',
        ]);
    }

    /**
     * 返回json格式列表
     */
    public function json(Request $request){
        $list = CourseModel::where('store_ids', $request->input('store_ids', ''))->get();

        return json_encode($list);
    }

    /**
     * 添加
     */
    public function add(Request $request)
    {
        if ($request->isMethod('post')) {
            try {
                $this->validate($request, $this->validate, $this->messages, $this->attributes);
            } catch (\Exception $e) {
                return $this->response(502, $e->validator->errors()->first());
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
                        'buy_limit' => $request->input('buy_limit', 0),
                        'sort' => $request->input('sort'),
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);

                $data = [];

                foreach ($storeIds as $key => $storeId) {
                    $temp['course_id'] = $resp1;
                    $temp['store_id'] = $storeId;

                    $data[] = $temp;
                }
                $resp2 = DB::table('course_store')->insert($data);

                if ($resp1 && $resp2) {
                    DB::commit();
                    return $this->response(200, '添加成功！', route('admin.course.list'));
                } else {
                    DB::rollback();
                    return $this->response(422, '处理失败，请重试！');
                }
            } catch (\Exception $e) {
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

        return view('admin.course.add', [
            'suitable' => $suitable,
            // 'stores' => $stores,
            'course_type' => $course_type,
        ]);
    }

    /**
     * 修改
     */
    public function modify(Request $request)
    {
        $id = intval($request->input('id'));
        if (empty($id)) {
            return $this->response(500, 'ID非法！');
        }

        $course = CourseModel::find($id);
        if (empty($course)) {
            return redirect(route('admin.course.list'));
        }

        if ($request->isMethod('POST')) {
            try {
                $this->validate($request, $this->validate, $this->messages, $this->attributes);
            } catch (\Exception $e) {
                return $this->response(502, $e->validator->errors()->first());
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
                        'buy_limit' => $request->input('buy_limit', 0),
                        'sort' => $request->input('sort'),
                        'updated_at' => date('Y-m-d H:i:s'),
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

                if ($resp1 && $resp2) {
                    DB::commit();
                    return $this->response(200, '修改成功！', route('admin.course.list'));
                } else {
                    DB::rollback();
                    return $this->response(422, '处理失败，请重试！');
                }
            } catch (\Exception $e) {
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
        $stores = StoreModel::where('city_id', $course->city_id)->get();
        //获取所有课程分类
        $course_type = CourseTypeModel::get();

        // 获取城市列表
        $citys = CityModel::get();

        $store_ids = [];
        if (!empty($course->store_ids)) {
            $store_ids = explode(',', $course->store_ids);
        }

        return view('admin.course.modify', [
            'info' => $course,
            'suitable' => $suitable,
            'stores' => $stores,
            'citys' => $citys,
            'course_type' => $course_type,
            'store_ids' => $store_ids,
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

    public function delete(Request $request)
    {
        $id = intval($request->input('id'));
        if (empty($id)) {
            return $this->response(500, 'ID非法！');
        }

        $course = CourseModel::find($id);
        if (empty($course)) {
            return $this->response(500, '课程不存在！');
        }

        // 课程开启时不能删除
        if (!$course['disabled']) {
            return $this->response(500, '请先停用课程再进行操作！');
        }

        // 用户已购买的课程不能删除
        $ordered = OrderModel::where('course_id', $id)->whereIn('status', [1, 4, 5])->first();

        if (!empty($ordered)) {
            return $this->response(500, '已有购买记录，不能删除该课程');
        }

        DB::beginTransaction();
        try {
            // 删除课程
            // $res1 = DB::table('course')->delete($id);
            $res1 = DB::table('course')->where('id', $id)->update(['disabled' => '1', 'deleted_at' => date('Y-m-d H:i:s')]);
            // 删除课程与门店关系
            // $res2 = DB::table('course_store')->where('course_id', $id)->delete();
            // 删除课时
            // $res3 = DB::table('course_class')->where('course_id', $id)->delete();

            // if ($res1 && $res2 && $res3) {
            if ($res1) {
                DB::commit();

                return $this->response(200, '删除成功！');
            } else {
                DB::rollback();

                return $this->response(500, '删除失败！');
            }
        } catch (\Exception $e) {
            DB::rollback();

            Log::error($e->getMessage());
            return $this->response(500, $e->getMessage());
        }
    }
}

<?php
namespace App\Http\Controllers\Api;

use App\Models\CourseModel;
use App\Models\StoreModel;
use Illuminate\Http\Request;
use App\Helper;

class SearchController extends Controller
{

    /**
     * 搜索课程
     */
    public function course(Request $request)
    {
        $keyword = $request->input('keyword');
        if (empty($keyword)) {
            return $this->error('关键字为空！');
        }

        $course = CourseModel::with('pic')->where('name', 'like', '%' . $keyword . '%')->where('disabled', 0)->get();
        return $this->success($course);
    }

    /**
     * 搜索门店(根据关键字)
     */
    public function store(Request $request)
    {
        $keyword = $request->input('keyword');
        if (empty($keyword)) {
            return $this->error('关键字为空！');
        }
        $longitude = $request->input('longitude');
        $latitude = $request->input('latitude');
        $store = StoreModel::with('pic')->where('name', 'like', '%' . $keyword . '%')->get();
        $store =  json_decode(json_encode($store),true);
        if(empty($store)){
            return $this->error('无相关门店');
        }
        foreach ($store as $arr => $val) {
            $distance = Helper::getdistance($longitude, $latitude, $val['longitude'], $val['latitude']);
            $distance = round($distance/1000,2);
            $store[$arr]['distance'] = $distance;
            $short[$arr] = $distance;
        }
        array_multisort($short, SORT_ASC, SORT_NUMERIC, $store);

        return $this->success($store);
    }

    /**
     * 搜索门店(根据城市ID)
     */
    public function storeByCityid(Request $request)
    {
        $cityid = $request->input('cityid');
        if (empty($cityid)) {
            return $this->error('城市信息为空！');
        }
        $longitude = $request->input('longitude');
        $latitude = $request->input('latitude');
        if($request->has('keyword')){
            $keyword = $request->input('keyword');
            $store = StoreModel::with('pic')->where('city_id',$cityid)->where('name','like','%'.$keyword.'%')->get();
            $store =  json_decode(json_encode($store),true);
            if(empty($store)){
                return $this->error('无相关门店');
            }
            foreach ($store as $arr => $val) {
                $distance = Helper::getdistance($longitude, $latitude, $val['longitude'], $val['latitude']);
                $distance = round($distance/1000,2);
                $store[$arr]['distance'] = $distance;
                $short[$arr] = $distance;
            }
            array_multisort($short, SORT_ASC, SORT_NUMERIC, $store);
            return $this->success($store);
        }
        $store = StoreModel::with('pic')->where('city_id',$cityid)->get();
        $cc = [];
        $store =  json_decode(json_encode($store),true);
            foreach ($store as $arr => $val) {
                $distance = Helper::getdistance($longitude, $latitude, $val['longitude'], $val['latitude']);
                $distance = round($distance/1000,2);
                $store[$arr]['distance'] = $distance;
                $short[$arr] = $distance;
        }
        array_multisort($short, SORT_ASC, SORT_NUMERIC, $store);
        return $this->success($store);
    }

    /*
     *搜索门店（根据城市）
     */
    public function cityStore(Request $request)
    {
        $cityId = $request->input('cityId');
        if (empty($cityId)) {
            return $this->error('请选择城市');
        }

        $store = StoreModel::with('pic')->where('city_id', $cityId)->get();
        $store_arr = json_decode(json_encode($store), true);
        if (count($store_arr) > 0) {
            foreach ($store_arr as $key => $val) {
                $store_name[$key] = $val['name'];
            }
            $result['store_arr'] = $store_name;
            $result['data'] = $store;
            return $this->success($result);
        } else {
            return $this->error('没有相关的门店信息');
        }
    }
}

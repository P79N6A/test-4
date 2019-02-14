<?php
namespace App\Http\Controllers\Api;

use App\Helper;
use App\Models\CityModel;
use App\Models\StoreModel;
use App\Services\UserService;
use Illuminate\Http\Request;

class CityController extends Controller
{

    /**
     * 获取默认城市
     */
    public function getDefault(Request $request)
    {
        //登录了则获取用户选择城市
        if ($user_info = UserService::isLogin()) {
            if (isset($user_info->city)) {
                return $this->success($user_info->city);
            }
        }

        //未登录则选择默认的
        $city = CityModel::where('is_default', 1)->select(['id', 'name'])->first();
        if (empty($city)) {
            return $this->error('获取失败');
        } else {
            return $this->success($city);
        }
    }

    /**
     * 按首字母分类列表
     */
    public function listByLetter(Request $request)
    {
        $city = CityModel::select(['id', 'name', 'first_letter'])->get();

        $temp = [];
        foreach ($city as $item) {
            $temp[$item['first_letter']][] = $item;
        }

        ksort($temp);

        return $this->success($temp);
    }

    /**
     * 获取热门城市
     */
    public function getHot()
    {
        $city = CityModel::select(['id', 'name'])->where('is_hot', 1)->get();

        return $this->success($city);
    }

    /**
     * 设置用户选择的城市
     */
    public function setCity(Request $request)
    {
        $city_id = $request->input('city_id');
        if (!Helper::isId($city_id)) {
            return $this->error('ID非法！');
        }

        $city = CityModel::find($city_id);
        if (empty($city)) {
            return $this->error('城市不存在！');
        }

        $user = $this->auth->user;
        $user->last_city = $city->id;
        if ($user->save()) {
            return $this->success([]);
        } else {
            return $this->error('设置失败！');
        }
    }

    /*
    *查询城市门店数
    */
    public function countStore(Request $request){
        $city_id = $request->input('city_id');
        if(empty($city_id)){
            return $this->error('城市ID为空');
        }
        $count = StoreModel::where('city_id',$city_id)->count();
        return $this->success($count);
    }
}

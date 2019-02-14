<?php
namespace App\Http\Controllers\Api;

use App\Helper;
use App\Models\CityModel;
use App\Models\CourseModel;
use App\Models\CourseStoreModel;
use App\Models\CourseTypeModel;
use App\Models\StoreModel;
use App\Models\UserCourseClassModel;
use App\Models\UserCourseModel;
use Endroid\QrCode\QrCode;
use Illuminate\Http\Request;

class CourseController extends Controller
{

    /**
     * 根据课程类型获取课程列表
     */
    public function lists(Request $request)
    {
        $type_id = $request->input('type_id');
        $suitable = $request->input('suitable');
        $class_order = $request->input('class_order');
        $price_order = $request->input('price_order');
        $limit = (int) $request->input('limit');

        if (!Helper::isId($type_id)) {
            return $this->error('ID获取失败！');
        }

        //获取课程类型
        $course_type = CourseTypeModel::find($type_id);
        if (empty($course_type)) {
            return $this->error('课程类型不存在！');
        }

        $where = [];
        $order = [];
        //条件
        if (Helper::isId($suitable)) {
            $where['suitable_age'] = $suitable;
        }

        //排序1/null-默认，2-升序，3-降序
        if ($class_order == 2) {
            $order['class_num'] = "ASC";
        } elseif ($class_order == 3) {
            $order['class_num'] = "DESC";
        } else {
            $class_order = 1;
        }

        if ($price_order == 2) {
            $order['price'] = "ASC";
        } elseif ($price_order == 3) {
            $order['price'] = "DESC";
        } else {
            $price_order = 1;
        }

        $lists['course_type'] = $course_type->name;
        $lists['class_order'] = $class_order;
        $lists['price_order'] = $price_order;
        $temp = $this->getList('list', $limit, $type_id, $where, $order);
        $lists = array_merge($lists, $temp);
        return $this->success($lists);
    }

    /**
     * 根据门店ID获取课程列表
     */
    public function byStore(Request $request)
    {
        $store_id = $request->input('store_id');
        if (!Helper::isId($store_id)) {
            return $this->error('ID非法！');
        }

        $select = ['id', 'name', 'class_num', 'price', 'img', 'is_hot', 'is_recommend'];
        $course = CourseModel::with('pic','class')->select($select)->whereRaw('FIND_IN_SET(?,store_ids)', [$store_id])->where('disabled',0)->get()->toArray();
        foreach ($course as $key => $value) {
            $times = 0;
            foreach ($value['class'] as $arr => $val) {
                $times += $val['times'];
            }
            $course[$key]['times'] = $times;
            $store_name = StoreModel::where('id',$store_id)->select('name')->first();
            $course[$key]['store'] = $store_name['name'];
        }

        return $this->success($course);
    }

    /**
     * 获取热门课程
     * @param integer $type_id
     * @param integer $limit
     */
    public function getHot(Request $request)
    {
        $type_id = $request->input('type_id');
        $limit = (int) $request->input('limit');
        $suitable = $request->input('suitable');
        $class_order = $request->input('class_order');
        $price_order = $request->input('price_order');

        $where = [];
        $order = [];
        //条件
        if (Helper::isId($suitable)) {
            $where['suitable_age'] = $suitable;
        }

        //排序1/null-默认，2-升序，3-降序
        if ($class_order == 2) {
            $order['class_num'] = "ASC";
        } elseif ($class_order == 3) {
            $order['class_num'] = "DESC";
        } else {
            $class_order = 1;
        }

        if ($price_order == 2) {
            $order['price'] = "ASC";
        } elseif ($price_order == 3) {
            $order['price'] = "DESC";
        } else {
            $price_order = 1;
        }

        $lists['class_order'] = $class_order;
        $lists['price_order'] = $price_order;

        $temp = $this->getList('hot', $limit, $type_id, $where, $order);
        $lists = array_merge($lists, $temp);
        return $this->success($lists);
    }

    // /**
    //  * 获取推荐课程
    //  * @param integer $type_id
    //  * @param integer $limit
    //  */
    // public function getRecommend_old(Request $request)
    // {
    //     $type_id = $request->input('type_id');
    //     $limit = (int) $request->input('limit');
    //     $suitable = $request->input('suitable');
    //     $class_order = $request->input('class_order');
    //     $price_order = $request->input('price_order');

    //     $where = [];
    //     $order = [];
    //     //条件
    //     if (Helper::isId($suitable)) {
    //         $where['suitable_age'] = $suitable;
    //     }

    //     //排序1/null-默认，2-升序，3-降序
    //     if ($class_order == 2) {
    //         $order['class_num'] = "ASC";
    //     } elseif ($class_order == 3) {
    //         $order['class_num'] = "DESC";
    //     } else {
    //         $class_order = 1;
    //     }

    //     if ($price_order == 2) {
    //         $order['price'] = "ASC";
    //     } elseif ($price_order == 3) {
    //         $order['price'] = "DESC";
    //     } else {
    //         $price_order = 1;
    //     }

    //     $lists['class_order'] = $class_order;
    //     $lists['price_order'] = $price_order;

    //     $temp = $this->getList('recommend', $limit, $type_id, $where, $order);
    //     $lists = array_merge($lists, $temp);

    //     return $this->success($lists);
    // }

    /**
     * 获取推荐课程
     * @param integer $type_id
     * @param integer $limit
     */
    public function getRecommend(Request $request)
    {
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $location = $longitude . ',' . $latitude;
        $city_name = $request->input('city_name');

        if (empty($city_name)) {
            //根据经纬度返回地理信息
            $url = "http://restapi.amap.com/v3/geocode/regeo?output=json&location=" . $location . "&key=" . config('lbsmap.key') . "";
            $result = Helper::curl($url);
            $result = json_decode($result, true);

            if ($result['status'] == 0) {
                // 获取地理信息失败
                return $this->error('notInCity');
            }

            //获取城市名称  直辖市的字段位于province   其他位于city
            $city = $result['regeocode']['addressComponent']['city'];
            if($city == []){
                $city = $result['regeocode']['addressComponent']['province'];
            }
            $city_condition = mb_substr($city, 0, -1);
        } else {
            $city_condition = $city_name;
        }
        //根据地址经纬度返回的城市名查询
        $city_condition = CityModel::where('name', 'like', '%' . $city_condition . '%')->first();

        if(empty($city_condition)){
            $error_code = 'notInCity';
            return $this->error($error_code);
        }

        //根据城市ID查询课程
        $all_store = StoreModel::where('city_id', $city_condition->id)->get()->toArray();
        if (count($all_store) > 0) {
            if(!empty($request->input('storeid')) && !empty($request->input('storename'))){
                $min_store_id = $request->input('storeid');
                $min_store_name = $request->input('storename');
            }else{
                $temp = 0;
                foreach ($all_store as $key => $val) {
                    //根据经纬度计算距离
                    $distance = Helper::getdistance($longitude, $latitude, $val['longitude'], $val['latitude']);
                    $store_distance[$key] = $distance;

                    //获取距离最近的门店id、name
                    if ($key > 0) {//当key>0的时候    判断当前门店距离是不是比上一间门店的距离近
                        if ($store_distance[$key] < $temp) {  //如果当前门店距离比上一间门店的距离近那么
                            $min = $store_distance[$key];     //$min 为最短距离
                            $min_store_id = $val['id'];       //最近的门店id
                            $min_store_name = $val['name'];   //最近的门店名称
                        }
                    } else {   // 如果key == 0
                        $min_store_id = $val['id'];   //最近的门店为 key == 0(第【0】间)门店
                        $min = $store_distance[$key]; 
                        $min_store_name = $val['name'];
                    }
                    $temp = $distance;                //当前门店距离用户定位的距离、用于和下一间门店距离做比较
                }
            }
            // return $min_store_id;

            $course_id = CourseStoreModel::where('store_id', $min_store_id)->select('course_id')->get()->toArray();
            foreach ($course_id as $key => $val) {
                $course_id[$key] = $val['course_id'];
            }

            $limit = $request->input('limit');
            $where[] = ['disabled', 0]; //不显示被停用课程
            $where[] = ['class_num', '>', 0]; //不显示课时为0的课程
            $builder = CourseModel::with(['pic','class'])->select(['id', 'name', 'class_num', 'price', 'img'])->where($where)->whereIn('id', $course_id)->whereNull('deleted_at');
            $list = $builder->orderBy('sort', 'desc')->paginate($limit);
            $lists = json_decode(json_encode($list),true);
            $course['total'] = $list->total();
            $course['cur_page'] = $list->currentPage();
            $course['page_size'] = $list->perPage();
            $course['lists'] = $list->items();
            $course['city']['id'] = $city_condition->id;
            $course['city']['name'] = $city_condition->name;
            $course['store_name'] = $min_store_name;
            foreach($course['lists'] as $key=>$value){
                $times = 0;
                foreach ($value['class'] as $arr => $val) {
                    $times += $val['times'];
                }
                $course['lists'][$key]['times'] = $times;
                $course['lists'][$key]['store'] = $min_store_name;
            }
            if ($course['total'] == 0) {
                return $this->error('无相关门店的课程');
            }
            return $this->success($course);
        } else {
            return $this->error('无相关门店');
        }

    }


    function array_to_object($arr) {   
         if (gettype($arr) != 'array') {return;}    
         foreach ($arr as $k => $v) {
            if (gettype($v) == 'array' || getType($v) == 'object') {
                $cc = $this->array_to_object($v);
                $arr[$k] = (object)$cc;        
            }    
        }     
        return (object)$arr;
    }

    /**
     * 课程详细
     */
    public function detail(Request $request)
    {
        $id = $request->input('id');
        if (!Helper::isId($id)) {
            return $this->error('非法ID！');
        }

        $fields = ['name', 'id', 'type_id', 'class_num', 'price', 'suitable_age', 'class_id', 'content', 'store_ids', 'img', 'is_hot', 'is_recommend', 'disabled'];
        $course = CourseModel::with(['suitable', 'type', 'pic','class'])->select($fields)->find($id);
        if (empty($course)) {
            return $this->error('课程不存在！');
        }

        if ($course->class_num <= 0) {
            return $this->error('课程未完善！');
        }

        if ($course->type->disabled) {
            $course->disabled = 1;
        }

        $times = 0 ;
        if($course->class){
            foreach ($course->class as $key => $val) {
                $times += $val['times'];
            }
        }
        // return $course->class;

        $course->content = Helper::resetImg($course->content);

        //获取课程适合门店
        $store_list = [];
        if (!empty($course->store_ids)) {
            $store_list = StoreModel::with('pic')->select(['id', 'name', 'tel', 'img', 'imgs', 'address', 'longitude', 'latitude'])
                ->whereIn('id', \explode(',', $course->store_ids))->get();
        }

        $course->stores = $store_list;
        $course->times = $times;

        return $this->success($course);
    }

    /**
     * 用户课程信息
     */
    public function userCourseInfo(Request $request)
    {
        $user_course_id = $request->input('user_course_id');
        if (!Helper::isId($user_course_id)) {
            return $this->error('ID非法！');
        }

        $where[] = ['user_id', $this->auth->user_id];
        $where[] = ['id', $user_course_id];
        $user_course = UserCourseModel::with('pic')->where($where)->first();
        if (empty($user_course)) {
            return $this->error('用户无此课程！');
        }

        return $this->success($user_course);
    }

    /**
     * 查看课时详情(保留)
     */
    public function courseClass(Request $request)
    {
        $course_id = $request->input('course_id');
    }

    /**
     * 获取课程进度
     */
    public function userCourseProcess(Request $request)
    {
        $user_course_id = $request->input('user_course_id');
        if (!Helper::isId($user_course_id)) {
            return $this->error('ID非法！');
        }

        $where[] = ['user_id', $this->auth->user_id];
        $where[] = ['user_course_id', $user_course_id];
        $course = UserCourseClassModel::with(['class','store'])->where($where)->get();

        return $this->success($course);
    }

    /**
     * 整体获取列表方法
     */
    public function getList($cmd, $limit, $type_id, $other = [], $order = [])
    {
        $where[] = ['disabled', 0]; //不显示被停用课程
        $where[] = ['class_num', '>', 0]; //不显示课时为0的课程
        switch ($cmd) {
            case 'recommend':
                $where[] = ['is_recommend', 1];
                break;
            case 'hot':
                $where[] = ['is_hot', 1];
                break;
        }
        //遍历其他条件
        foreach ($other as $key => $v) {
            $where[] = [$key, $v];
        }
        if (Helper::isId($type_id)) {
            $where[] = ['type_id', $type_id];
        }
        if (empty($limit) || $limit <= 0) {
            $limit = 10;
        }

        $builder = CourseModel::with('pic')->select(['id', 'name', 'class_num', 'price', 'img'])->where($where);

        //循环排序
        foreach ($order as $key => $v) {
            $builder->orderBy($key, $v);
        }

        $list = $builder->orderBy('updated_at')->paginate($limit);
        $temp['total'] = $list->total();
        $temp['cur_page'] = $list->currentPage();
        $temp['page_size'] = $list->perPage();
        $temp['lists'] = $list->items();
        return $temp;
    }

    /**
     * 生成课程核销二维码
     *
     * 二维码格式：订单号|用户课程编号|用户ID
     */
    public function QRCode(Request $request)
    {
        $orderNum = $request->input('order_num');
        $userCourseId = $request->input('user_course_id');
        $userId = $request->input('user_id');

        $qrCode = new QrCode();
        $qrCode
            ->setText(Helper::AES($orderNum . '|' . $userCourseId . '|' . $userId))
            ->setSize(150)
            ->setPadding(10)
            ->setErrorCorrection('high')
            ->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0])
            ->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0])
            ->setLabelFontSize(16)
            ->setImageType(QrCode::IMAGE_TYPE_PNG)
        ;

        return Response($qrCode->get(), 200, ['Content-Type' => $qrCode->getContentType()]);
    }

    public function getRecommendWithoutLocation(Request $request)
    {
        $Location = Helper::getLocationByAddress($request->input('city'));
        $Location = json_decode($Location, true);

        if ($Location['status'] !== 0) {
            $result = explode(",", $Location['geocodes'][0]['location']);
            $res['longitude'] = $result[0];
            $res['latitude'] = $result[1];

            return $this->success($res);
        }
    }
}

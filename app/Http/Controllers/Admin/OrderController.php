<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CityModel;
use App\Models\OrderModel;
use App\Models\StoreModel;
use App\Models\CourseModel;
use Illuminate\Http\Request;
use App\Libraries\ExcelProcessor;
use App\Models\CourseTypeModel;

class OrderController extends Controller
{
    /**
     * 订单列表
     */
    public function lists(Request $request)
    {    
        $list = OrderModel::with(['user', 'course']);

        if($request->has('keyword')){
            $keyword = $request->input('keyword');
            $list->where('order_num','like','%'.$keyword.'%');
        }
        if($request->has('order_city')){
            $order_city = $request->input('order_city');
            $course = CourseModel::where('city_id',$order_city)->select('id')->get();
            $order_store_list = StoreModel::where('city_id', $request->input('order_city', ''))->get();
            $list->whereIn('course_id',$course);
        }
        if($request->has('order_status')){
            $order_status = $request->input('order_status');
            $list->where('status',$order_status);
        }
        if($request->has('order_store')){
            $order_store = $request->input('order_store');
            $course_id = CourseModel::where('store_ids',$order_store)->select('id')->get();
            
            $order_course_list = CourseModel::where('store_ids', $request->input('order_store', ''))->get();
            $list->whereIn('course_id',$course_id);
        }
        if($request->has('order_course')){
            $order_course = $request->input('order_course');
            
            $list->where('course_id',$order_course);
        }
        if($request->has('start_time')){
            $start_time = $request->input('start_time');
            $list->where('created_at','>=',$start_time);
        }
        if($request->has('end_time')){
            $end_time = $request->input('end_time');
            $list->where('created_at','<=',$end_time);
        }

        $lists = $list->orderBy('id', 'DESC')->paginate(25);
        $course_type = CourseTypeModel::select('id','name')->get();
        $courseType = [];
        foreach ($course_type as $key => $value) {
            $courseType[$value['id']] = $value['name'];
        }
        
       // dump(json_decode(json_encode($lists),true) );
        $status = [
            1 => '已支付',
            2 => '已取消',
            3 => '已超时',
            4 => '待支付',
            5 => '已支付',
        ];
        $type = [
            1 => 'VIP订单',
            2 => '单次消费',
            3 => '购买课程',
        ];
        $citys = CityModel::select('id', 'name')->get()->toArray();
        $cityList = [];

        foreach ($citys as $city) {
            $cityList[$city['id']] = $city['name'];
        }

        $stores = StoreModel::select('id', 'name')->get()->toArray();

        $storeList = [];
        foreach ($stores as $store) {
            $storeList[$store['id']] = $store['name'];
        }
        $city_list = CityModel::orderBy('is_hot')->get();
        return view('admin.order.list', [
            'list' => $lists,
            'type' => $type,
            'status' => $status,
            'citys' => $cityList,
            'stores' => $storeList,
            'city_list' =>$city_list,
            'order_city'=> isset($order_city) ? $order_city:'',
            'order_status'=>isset($order_status) ? $order_status:'',
            'order_course_list'=>isset($order_course_list) ? $order_course_list:'',
            'order_course'=>isset($order_course) ? $order_course:'',
            'order_store'=>isset($order_store) ? $order_store:'',
            'order_store_list'=>isset($order_store_list) ? $order_store_list:'',
            'keyword'=> isset($keyword) ? $keyword:'',
            'end_time'=> isset($end_time) ? $end_time:'',
            'start_time'=> isset($start_time) ? $start_time:'',
            'courseType'=>$courseType
        ]);
    }

    //订单列表导出
    public function export(Request $request){
        $list = OrderModel::with(['user', 'course']);

        if($request->has('keyword')){
            $keyword = $request->input('keyword');
            $list->where('order_num','like','%'.$keyword.'%');
        }
        if($request->has('order_city')){
            $order_city = $request->input('order_city');
            $course = CourseModel::where('city_id',$order_city)->select('id')->get();
            $list->whereIn('course_id',$course);
        }
        if($request->has('order_status')){
            $order_status = $request->input('order_status');
            $list->where('status',$order_status);
        }
        if($request->has('order_store')){
            $order_store = $request->input('order_store');
            $course_id = CourseModel::where('store_ids',$order_store)->select('id')->get();
            $list->whereIn('course_id',$course_id);
        }
        if($request->has('order_course')){
            $order_course = $request->input('order_course');
            
            $list->where('course_id',$order_course);
        }
        $lists = $list->orderBy('id', 'DESC')->get()->toArray();

        $course_type = CourseTypeModel::select('id','name')->get();
        $courseType = [];
        foreach ($course_type as $key => $value) {
            $courseType[$value['id']] = $value['name'];
        }

        $citys = CityModel::select('id', 'name')->get()->toArray();
        $cityList = [];
        foreach ($citys as $city) {
            $cityList[$city['id']] = $city['name'];
        }

        $stores = StoreModel::select('id', 'name')->get()->toArray();
        $storeList = [];
        foreach ($stores as $store) {
            $storeList[$store['id']] = $store['name'];
        }

        $status = [
            1 => '已支付',
            2 => '已取消',
            3 => '已超时',
            4 => '待支付',
            5 => '已支付',
        ];

        $arr = [];
        foreach($lists as $k=>$v){
           $arr[$k]['ID'] = $v['id'];
           $arr[$k]['order_num'] = $v['order_num'];
           $arr[$k]['total'] = $v['total']/100;
           $arr[$k]['type'] = $v['type'] == 1 ? 'VIP订单' : $v['type'] == 2 ? '单次消费': '购买课程';
           $arr[$k]['course_type'] = $courseType[$v['course']['type_id']];
           $arr[$k]['city'] = $cityList[$v['course']['city_id']];
           $arr[$k]['store'] = $storeList[$v['course']['store_ids']];
           $arr[$k]['course'] =$v['type'] == 1 ? 'VIP订单' : $v['type'] == 2 ? '单次消费': $v['course']['name'];
           $arr[$k]['user'] = $this->filterNickname($v['user']['nickname']);
           $arr[$k]['status'] = $status[$v['status']];
           $arr[$k]['created_at'] = $v['created_at'];  
        }

        

        ob_end_clean();//清除缓冲区,避免乱码
        $handler = new ExcelProcessor();
        $header = [
             'ID',
             '订单号',
             '订单价',
             '订单类型',
             '课程类型',
             '城市',
             '门店',
             '购买课程',
             '用户',
             '订单状态',
             '下单时间'
        ];
        $filename = date('Y-m-d H：i：s') . ' - 订单导出.xlsx';
        $handler->setHeader($header)->setData($arr)->download($filename);

    }



    //过滤微信表情
    function filterNickname($nickname) {    
        $nickname = preg_replace('/[\x{1F600}-\x{1F64F}]/u', '', $nickname);     
        $nickname = preg_replace('/[\x{1F300}-\x{1F5FF}]/u', '', $nickname);     
        $nickname = preg_replace('/[\x{1F680}-\x{1F6FF}]/u', '', $nickname);     
        $nickname = preg_replace('/[\x{2600}-\x{26FF}]/u', '', $nickname);     
        $nickname = preg_replace('/[\x{2700}-\x{27BF}]/u', '', $nickname);     
        $nickname = str_replace(array('"','\''), '', $nickname);     
        return addslashes(trim($nickname));
    }


}

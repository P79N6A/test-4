<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\PHPTree;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Translation\Dumper\YamlFileDumper;
use App\Models\OrderModel;

class IndexController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->user = session()->get('user');
    }

    /**
     * 后台首页
     */
    public function index()
    {
        $roles = $this->getRoles();
        $permissions = $this->getAdminMenus($roles);
        if (!empty($permissions)) {
            $tree = PHPTree::makeTree($permissions);
        }
        return view('admin.index', ['tree' => !empty($tree) ? $tree : []]);
    }

    /**
     * 后台首页概览
     */
    public function overview()
    {
        $date = time();
        $datearr[0] = date('Y-m-d',$date);
        $datearr[1] = date('Y-m-d',$date- 3600*24);
       // $start_date=$start_date + 3600*24;

        //获取总收入
        $OrderIncome = $this->getOrderIncome();

        //获取用户总游戏次数
        $UserGameNum = $this->getUserGameNum();
        //获取总门店数
        $StoreNum = $this->getStoreNum();

        //获取总机台数
        $MenchineNum = $this->getMenchineNum();

        //获取用户总数
        $UserNum = $this->getUserNum();

        //获取今天的员工开启机台数量
        $TodayStartMenchineNum = $this->getTodayStartMenchineNum($datearr);
        //获取今天的注册人数
        $TodayRegisterNum = $this->getTodayRegisterNum($datearr);

        //获取用户今日的游戏次数
        $TodayUserGameNum = $this->getTodayUserGameNum($datearr);

        return view('admin.overview', [
            'OrderIncome'=> $OrderIncome,
            'UserGameNum'=>$UserGameNum,
            'StoreNum'=>$StoreNum,
            'MenchineNum'=>$MenchineNum,
            'UserNum'=>$UserNum,
            'TodayStartMenchineNum'=>$TodayStartMenchineNum,
            'TodayRegisterNum'=>$TodayRegisterNum,
            'TodayUserGameNum'=>$TodayUserGameNum
        ]);
    }

    /**
    *根据时间返回订单数量、金额
    *@param $start_date $end_date
    *@return $data 
    */
    public function orderNum(Request $request){
        // $start_date = $request->input('start_date');
        // $end_date = $request->input('end_date');
        $date = $request->all();
        // return $date;
        $start_date = strtotime($date['start_date']);
        $end_date = strtotime($date['end_date']);
        while($start_date <= $end_date){
        $datearr[] = date('Y-m-d',$start_date);//得到dataarr的日期数组。
        $start_date=$start_date + 3600*24;
        }
        $datearray = [];
        foreach ($datearr as $key => $value) {
            $datearray[$value]['num'] = 0;
            $datearray[$value]['total'] = 0;
        }
        // return $datearray;

        $orderModel = new orderModel();
        $data = $orderModel->orderTimeGetNum($datearr);
        $data = json_decode(json_encode($data),true);//得到订单数量数组
        // return $data;
        foreach ($data as $arr => $val) {
            $datearray[$val['day']]['num'] = $val['num']; 
            $datearray[$val['day']]['total'] = $val['total']; 
        }
        //return $datearray;

        $json = [];
        $keys = 0;
        foreach ($datearray as $key => $value) {
            $json['num'][$keys] = $value['num'];
            $json['total'][$keys] = $value['total'];
            $keys++;
        }
        $json['date'] = $datearr;
        return $json;
    }

    /**
     * 获取登录管理员拥有的角色
     * @return mixed
     */
    private function getRoles()
    {
        $roles = DB::table('admin_role_user as aru')->where('aru.user_id', $this->user->id)
            ->join('admin_roles as ar', function ($join) {
                $join->on('ar.id', '=', 'aru.role_id')->where('ar.status', '=', 1);
            })
            ->lists('role_id');
        return $roles;
    }

    /**
     * 获取登录管理员所在角色拥有的权限（菜单）
     * @param $roleIds
     * @return mixed
     */
    private function getAdminMenus($roleIds)
    {
        $menus = DB::table('admin_permission_role as apr')
            ->whereIn('apr.role_id', $roleIds)
            ->where('status', 1)
            ->where('disable', 0)
            ->join('admin_permissions as ap', 'ap.id', '=', 'apr.permission_id')
            ->orderBy('display_order', 'asc')->orderBy('id', 'asc')
            ->select('ap.id', 'ap.parent_id', 'ap.name', 'ap.display_name', 'ap.status', 'ap.display_order')
            ->get();
        return $menus;
    }

    /*
    *获取订单总收入
    *@return amount
    */
    public function getOrderIncome(){
        $data = DB::table('order')
        ->where('status',1)
        ->select( DB::raw('FORMAT(sum(total)/100, 2) AS amount') )
        ->first();
        return $data->amount;
    }

    /*
    *获取用户总游戏数
    *@return $total_times 
    */
    public function getUserGameNum(){
        $data = DB::table('user_course_class')
        ->select( DB::raw('SUM(times) AS total_times') )
        ->first();
        return $data->total_times;
    }

    /*
    *获取门店总数
    *return total_num
    */
    public function getStoreNum(){
        $data = DB::table('stores')
        ->select( DB::raw('COUNT(id) as total_num'))
        ->first();
        return $data->total_num;
    }

    /*
    *获取机台总数
    *return total_num
    */
    public function getMenchineNum(){
        $data = DB::table('iot_equipments')
        ->select( DB::raw('COUNT(id) as total_num'))
        ->first();
        return $data->total_num;
    }

    /*
    *获取用户总数
    *return total_num
    */
    public function getUserNum(){
        $data = DB::table('users')
        ->select( DB::raw('COUNT(id) as total_num'))
        ->first();
        return $data->total_num;
    }

    /*
    *获取店员今日开启的机台数
    */
    public function getTodayStartMenchineNum($datearr){

        $data = DB::table('store_staff_record')
        ->whereIn(DB::raw('FROM_UNIXTIME(created_at,"%Y-%m-%d")'),$datearr)
        ->select(DB::raw('COUNT(id) as total_num'), DB::raw( 'FROM_UNIXTIME(created_at,"%Y-%m-%d") as day'))
        ->groupBy('day')
        ->get();
        $data = json_decode(json_encode($data),true);
        foreach ($datearr as $key => $value) {
            $list[$value] = 0;
        }
        
        foreach ($data as $key => $value) {
            $list[$value['day']] = $value['total_num']; 
        }

        $result['total_num'] = $list[date('Y-m-d')];
        $rate = $list[$datearr[0]] ==0 ? 0: ($list[$datearr[1]]  > 0 ?(($list[$datearr[0]]-$list[$datearr[1]])/$list[$datearr[1]]):0);
        $result['rate'] = abs($rate *100);
        if($list[$datearr[0]] > 0){
            if($list[$datearr[1]] > $list[$datearr[0]]){
                $result['trend'] = 'down';
            }else{
                $result['trend'] = 'up';
            }
        }else{
            $result['trend'] = 'none';
        }
        return $result;
    }

    /*
    *获取今日注册人数
    */
    public function getTodayRegisterNum($datearr){
        $data = DB::table('users')
        ->whereIn(DB::raw('DATE_FORMAT(created_at,"%Y-%m-%d")'),$datearr)
        ->wherenotnull('mobile')
        ->select(DB::raw('COUNT(id) as total_num'), DB::raw( 'DATE_FORMAT(created_at,"%Y-%m-%d") as day'))
        ->groupBy('day')
        ->get();
        // dump($data);
        $data = json_decode(json_encode($data),true);
        foreach ($datearr as $key => $value) {
            $list[$value] = 0;
        }
        
        foreach ($data as $key => $value) {
            $list[$value['day']] = $value['total_num']; 
        }

        $result['total_num'] = $list[date('Y-m-d')];
        $rate =  $list[$datearr[0]] ==0 ? 0:($list[$datearr[0]] == 0 ?(($list[$datearr[0]]-$list[$datearr[1]])/$list[$datearr[1]]):0);
        $result['rate'] = abs($rate *100);
        if($list[$datearr[0]] > 0){
            if($list[$datearr[1]] > $list[$datearr[0]]){
                $result['trend'] = 'down';
            }else{
                $result['trend'] = 'up';
            }
        }else{
            $result['trend'] = 'none';
        }
        return $result;
    }

    /*
    *获取今日游戏次数
    */
    public function getTodayUserGameNum($datearr){
        $data = DB::table('user_course_record')
        // ->where(DB::raw('DATE_FORMAT(created_at,"%Y-%m-%d")'),date('Y-m-d'))
        ->whereIn(DB::raw('DATE_FORMAT(created_at,"%Y-%m-%d")'),$datearr)
        ->select( DB::raw('COUNT(id) AS total_times'), DB::raw( 'DATE_FORMAT(created_at,"%Y-%m-%d") as day') )
        ->groupBy('day')
        ->get();
        $data = json_decode(json_encode($data),true);
        foreach ($datearr as $key => $value) {
            $list[$value] = 0;
        }
        
        foreach ($data as $key => $value) {
            $list[$value['day']] = $value['total_times']; 
        }

        $result['total_times'] = $list[date('Y-m-d')];
        $rate = $list[$datearr[0]] ==0 ? 0:($list[$datearr[0]] == 0 ?(($list[$datearr[0]]-$list[$datearr[1]])/$list[$datearr[1]]):0);
        $result['rate'] = abs($rate *100);
        if($list[$datearr[0]] > 0){
            if($list[$datearr[1]] > $list[$datearr[0]]){
                $result['trend'] = 'down';
            }else{
                $result['trend'] = 'up';
            }
        }else{
            $result['trend'] = 'none';
        }
        
        return $result;
    }


}

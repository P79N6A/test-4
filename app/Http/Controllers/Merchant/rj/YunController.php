<?php

namespace App\Http\Controllers\Merchant\rj;
use App\Http\Controllers\Controller;
use EasyWeChat\Core\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\json_decode;
use GuzzleHttp\json_encode;
use App\Http\Models\RjyunModel;
use App\Http\Models\RjmanagementModel;
use App\Http\Models\RjmachineModel;
use Log;

class YunController extends Controller
{

    /*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  * 团队管理 *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  */
    /**
     * 云积分活动管理
     * @param Request $request
     */
    public function rj_yun_list(Request $request){

        $this->system_log('云积分活动列表','merchant');
        $this->update_scduhele_start();	//更新赛程状态

        $data = $request->only('store_name','name','start_time','end_time','activity_type','game_type');

        /** type为machant **/
        $data['bus_user_id'] = session('id');
        /** type为machant **/
        $RjyunModel = new RjyunModel();
        $activity_list = $RjyunModel->lists($data);

        $activity= json_decode(json_encode($activity_list),true);
        foreach($activity['data'] as $key=>$val){

            $bususer_item_id = explode(',',$val['bususer_item_id']);
            $store = DB::table(config('tables.youyibao').'.bus_stores')->whereIn('id',$bususer_item_id)->lists('name');
            $store= json_decode(json_encode($store),true);
            $activity['data'][$key]['store_name'] = implode('、',$store);
            $activity['data'][$key]['store_num'] = count($store);

            $machine_item_id = explode(',',$val['machine_item_id']);
            $machine = DB::table(config('tables.base').'.rj_iot_machine')->whereIn('id',$machine_item_id)->lists('name');
            $machine= json_decode(json_encode($machine),true);
            $activity['data'][$key]['machine_name'] = implode('、',$machine);
            $activity['data'][$key]['machine_num'] = count($machine);
        }
        return view(env('Merchant_view').'.yun.Yun_list',[
            'ads'=>$activity_list,
            'activity'=>$activity,
            'username'=> session('username'),
            'store_name'=>!empty($data['store_name'])?$data['store_name']:'',
            'name'=>!empty($data['name'])?$data['name']:'',
            'start_time'=>!empty($data['start_time'])?$data['start_time']:'',
            'end_time'=>!empty($data['end_time'])?$data['end_time']:'',
            'activity_type'=>!empty($data['activity_type'])?$data['activity_type']:'',
        ]);

    }

    //云积分详情
    public function rj_yun_details(Request $request){
        $id = $request->get('id');
        $frequency = $request->get('frequency');
        if(empty($frequency)){
            //排行
            $checkGame = DB::table(config('tables.base') . '.rj_yun_game_log')
                ->where('yun_activity_id',$id)
                ->orderBy('id','desc')
                ->first();
            $checkGame = json_decode(json_encode($checkGame),true);
            $frequency = $checkGame['frequency'];
        }

        $YunActivityInfo = DB::table(config('tables.base').'.rj_yun_activity')
            ->where('id',$id)
            ->first();
        $YunActivityInfo = json_decode(json_encode($YunActivityInfo), true);
        $YunGameRankList = array();
        if(!empty($YunActivityInfo['prize_item_id'])){
            //活动奖励
            $prize_item_id = explode(',',$YunActivityInfo['prize_item_id']);
            $prize_list = DB::table(config('tables.base') . '.rj_yun_activity_prize')
                ->whereIn('id',$prize_item_id)
                ->orderBy('rank','asc')
                ->get();
            $prize_list = json_decode(json_encode($prize_list),true);
            $YunGameList= DB::table(config('tables.base').'.rj_yun_game_log As yg')
                ->leftJoin(config('tables.base') . '.users AS u', 'u.id', ' =', 'yg.u_id')
                ->where('yg.yun_activity_id',$YunActivityInfo['id'])
                ->where('yg.frequency',$frequency)
                ->orderBy('yg.sum','desc')
                ->select(
                    'u.nickname as username',
                    'u.mobile as mobile',
                    'yg.sum as sum'
                )
                ->get();
            $YunGameList = json_decode(json_encode($YunGameList), true);
            $prizeTypeArray = array(1=>'线下礼品',2=>'奖票',3=>'积分');
            //奖品列表
            $exchange_list = DB::table(config('tables.base') . '.rj_yun_activity_prize_exchange')
                ->where('yun_activity_id',$YunActivityInfo['id'])
                ->where('frequency',$frequency)
                ->get();
            $exchange_list = json_decode(json_encode($exchange_list), true);
            if(!empty($YunGameList)){
                foreach ($YunGameList as $key=>$val){
                    $data['rank'] = $key + 1;
                    $data['username'] = $val['username'];
                    $data['mobile'] = $val['mobile'];
                    $data['sum'] = $val['sum'];
                    $data['prize_type'] = '';
                    $data['prize_item_name'] = '';
                    $data['prize_sum'] = '';
                    $data['frequency'] = $frequency;
                    $data['exchange'] = 0;
                    if(!empty($prize_list[$key]['type'])){
                        $data['prize_type'] = $prizeTypeArray[$prize_list[$key]['type']];
                    }
                    if(!empty($prize_list[$key]['item_name'])){
                        $data['prize_item_name'] = $prize_list[$key]['item_name'];
                    }
                    if(!empty($prize_list[$key]['item_name'])){
                        $data['prize_sum'] = $prize_list[$key]['num'];
                    }
                    foreach ($exchange_list as $ke=>$va){
                        if($va['u_id'] == $val['u_id']){
                            $data['exchange'] = $va['exchange'];
                        }
                    }
                    $YunGameRankList[] = $data;
                }
            }
        }

        return view(env('Admin_view').'.yun.rj_yun_details',[
            'info'=>$YunActivityInfo,
            'YunGameRankList'=>$YunGameRankList,
            'frequency'=>$frequency,
        ]);
    }

    //删除活动
    public function rj_yun_del(Request $request){
        $id = $request->get('id');
        if(!$id)return $this->response(500,'参数ID错误');
        $YunActivityInfo = DB::table(config('tables.base').'.rj_yun_activity')->where('id',$id)->first();
        $YunActivityInfo = json_decode(json_encode($YunActivityInfo), true);
        if($YunActivityInfo['game_type'] == 1){
            $this->YunIntegralModeOrdinary(['yun_activity_id'=>$id]);
        }elseif($YunActivityInfo['game_type'] == 2){
            $this->YunIntegralModeOrdinary(['yun_activity_id'=>$id]);
        }
        $del = DB::table(config('tables.base').'.rj_yun_activity')->where('id',$id)->update(['stauts'=>'2']);
        if(!$del)return $this->response(403,'没有找到该活动');
        $this->system_log('删除活动 ','admin');
        return $this->response(200,'删除成功');
    }
    public function rj_yun_activity_type(Request $request){
        $id = $request->get('id');
        $type = $request->get('type');
        if(!$id)return $this->response(500,'参数ID错误');
        $del = DB::table(config('tables.base').'.rj_yun_activity')->where('id',$id)->update(['activity_type'=>$type=='2'?'1':'2']);
        if(!$del)return $this->response(403,'没有找到该活动');
        $this->system_log('关闭或开启活动 ','merchant');
        //通知云活动机台
        $game_type = DB::table(config('tables.base').'.rj_yun_activity')->where('id',$id)->value('game_type');
        $StartType = $type == '2'?'1':'2';
        if($game_type == 1){
            if($StartType == 1){
                $this->YunIntegralModeFixed(['yun_activity_id'=>$id]);
            }elseif($StartType == 2){
                $this->YunIntegralModeOrdinary(['yun_activity_id'=>$id]);
            }
        }elseif($game_type == 2){
            if($StartType == 1){
                $this->YunIntegralModeAccumulate(['yun_activity_id'=>$id]);
            }elseif($StartType == 2){
                $this->YunIntegralModeOrdinary(['yun_activity_id'=>$id]);
            }
        }
        return $this->response(200,'操作成功');
    }


    //创建云积分活动
    public function rj_yun_add(Request $request){

        if($_POST){
            $data =$_POST;



            unset($data['_token']);

            if(empty($data['name'])){ 		return $this->response(403, '活动名称未填写'); }
            if(empty($data['game_type'])){  return $this->response(403, '请选择积分模式'); }

            $data['total_awards'] = $data['total_awards'][$data['game_type']-1];
            if($data['game_type'] == 2){
                if($data['lottery_type']=='1') $data['start_time'] =  $data['start_time_one'][$data['lottery_type']];
                unset($data['start_time_one']);
            }else{
                if($data['lottery_type']=='1') $data['start_time'] =  $data['start_time_one'][$data['lottery_type']-1];
                unset($data['start_time_one']);
            }

            $data['bususer_item_id'] = implode($data['select_store_id'],','); unset($data['select_store_id']);

            $data['machine_item_id'] = implode($data['select_machine_id'],',');unset($data['select_machine_id']);

            $data['username'] = session('username');
            $data['total_awards'] = $data['total_awards'] == 0 ? 1:$data['total_awards'];
            $data['activity_type'] = '2';
            $data['start_time']=$data['start_time']?strtotime($data['start_time']):0;
            $data['end_time']=$data['end_time']?strtotime($data['end_time']):0;
            $data['create_time']=time();
            $data['orgin']=1;
            if(!empty($data['initial'])){
                $data['accumulate_num'] = $data['initial'];
            }
            if($data['lottery_type'] == 2){
                if($data['start_time'] > $data['end_time']){
                    return $this->response(500,'自定义时间错误');
                }
            }
            if(!empty($data['data']['prize'])){
                $prize = $data['data']['prize'];
                //检测奖励积分是正确
                if($data['game_type'] == 2){
                    $num = 0;
                    foreach($prize as $key=>$val) {
                        $num = $num + $val['num'];
                    }
                    if($num != $data['cap']){
                        return $this->response(500,'积分设置错误,积分要等于封顶值');
                    }
                }
                $checkP = array();
                foreach($prize as $key=>$val) {
                    if(in_array($val['ranking'],$checkP)){
                        return $this->response(500,'积分设置错误,排名名次错误');
                    }
                    $checkP[] = $val['ranking'];
                }
                //入库奖品
                $prise_item_id_arr=[];
                foreach($prize as $key=>$val){

                    $prize_item_arr = array(
                        'type'=>$val['option'],
                        'item_name'=>$val['name'],
                        'itme_img'=>$val['pic'],
                        'num'=>$val['num'],
                        'rank'=>$val['ranking']
                    );
                    $prise_item_id_arr[] = DB::table(config('tables.base').'.rj_yun_activity_prize')->insertGetId($prize_item_arr);
                    unset($prize_item_arr);
                }
                $data['prize_item_id'] = implode($prise_item_id_arr,',');
                unset($data['data']);
            }
            $id = DB::table(config('tables.base').'.rj_yun_activity')->insertGetId($data);
            if(!$id) return $this->response(500,'入库失败');

            $this->system_log('创建云积分活动 ','merchant');
            return $this->response(200,'创建成功',route('business.rj_yun_list'));
        }else{
//            echo '<pre>';
//            print_r($this->rj_yun_storeLists());exit;

           // $store = $this->rj_yun_storeLists();
            return view(env('Merchant_view').'.yun.rj_yun_add',[
                'barnd'=>$this->brandLists()
            ]);
        }

    }
    /**
     * 核销兑换码
     * 1.兑换码是否存在
     * 2.兑换码是否已经兑换
     * 3.兑换成功
     * @param code 兑换码
     */
    public function rj_yun_code(Request $request){
        if($request->has('code')){
            $code = $request->input('code');
            $first = DB::table(config('tables.base').'.rj_yun_activity_prize_exchange')
                ->where('code',$code)
                ->first();
            if(!empty($first)){
                if(($first->exchange) == 1) return $this->response(403,'该码已兑换过了');
                $update_array = ['exchange'=>1];
                $update = DB::table(config('tables.base').'.rj_yun_activity_prize_exchange')->where('exchange',$first->exchange)->update($update_array);
                if($update==false) return $this->response(500,'兑换失败');
                return $this->response(200,'兑换成功');
            }else{
                return $this->response(404,'兑换码不存在');
            }
        }else{
            return $this->response(403,'请传入兑换码');
        }
    }
    //编辑云积分
    public function rj_yun_edit(){
        if($_POST){
            $data = $_POST;

            if(!empty($data['data']['prize'])){
                $prize = $data['data']['prize'];
                unset($data['data']);
                $prize_item_id = DB::table(config('tables.base').'.rj_yun_activity')->where('id',$_POST['id'])->value('prize_item_id');
                if(!empty($prize_item_id)){
                    $prize_item_id = explode(',',$prize_item_id);
                    DB::table(config('tables.base').'.rj_yun_activity_prize')->whereIn('id',$prize_item_id)->delete();
                }
                //检测奖励积分是正确
                if($data['game_type'] == 2){
                    $num = 0;
                    foreach($prize as $key=>$val) {
                        $num = $num + $val['num'];
                    }
                    if($num != $data['cap']){
                        return $this->response(500,'积分设置错误,积分要等于封顶值');
                    }
                }
                $checkP = array();
                foreach($prize as $key=>$val) {
                    if(in_array($val['ranking'],$checkP)){
                        return $this->response(500,'积分设置错误,排名名次错误');
                    }
                    $checkP[] = $val['ranking'];
                }
                //入库奖品
                $prise_item_id_arr=[];
                foreach($prize as $key=>$val){
                    $prize_item_arr = array(
                        'type'=>$val['option'],
                        'item_name'=>$val['name'],
                        'itme_img'=>$val['pic'],
                        'num'=>$val['num'],
                        'rank'=>$val['ranking']
                    );
                    $prise_item_id_arr[] = DB::table(config('tables.base').'.rj_yun_activity_prize')->insertGetId($prize_item_arr);

                    unset($prize_item_arr);
                }
                $data['prize_item_id'] = implode($prise_item_id_arr,',');
            }

            unset($data['_token']);

            if(empty($data['name'])){ 		return $this->response(403, '活动名称未填写'); }
            if(empty($data['game_type'])){  return $this->response(403, '请选择积分模式'); }

            $data['total_awards'] = $data['total_awards'][$data['game_type']-1];

            if(!empty($data['start_time'])){
                $data['start_time'] = $data['start_time']?strtotime($data['start_time']):0;
            }

            if($data['game_type'] == 2){
                $data['start_time'] =  strtotime($data['start_time_one'][1]);
                unset($data['start_time_one']);
            }else{
                if($data['lottery_type']=='1') $data['start_time'] =  strtotime($data['start_time_one'][$data['lottery_type']-1]);
                unset($data['start_time_one']);
            }



            if(!empty($data['select_store_id'])){
                $data['bususer_item_id'] = implode($data['select_store_id'],',');
                unset($data['select_store_id']);
            }else{
                return $this->response(500,'门店不能为空');
            }

            if(!empty($data['select_machine_id'])){
                $data['machine_item_id'] = implode($data['select_machine_id'],',');
                unset($data['select_machine_id']);
            }else{
                return $this->response(500,'机台不能为空');
            }

            $data['total_awards'] = $data['total_awards'];

            if(!empty($data['end_time'])){
                $data['end_time'] = $data['end_time']?strtotime($data['end_time']):0;
            }

            unset($data['id']);
            unset($data['start_time_one']);

            $id = DB::table(config('tables.base').'.rj_yun_activity')->where('id',$_POST['id'])->update($data);

            if($id===false) return $this->response(500,'操作失败');

            $this->system_log( session('username').'编辑云积分活动 ','merchant');
            return $this->response(200,'提交成功',route('business.rj_yun_list'));
        }else{

            $first = DB::table(config('tables.base').'.rj_yun_activity')->where('id',$_GET['id'])->first();
            $first= json_decode(json_encode($first),true);
            if($first['game_type'] == 1){
                if($first['lottery_type'] == 1){
                    if($first['execute_awards'] >= $first['total_awards']){
                        return $this->response(500,'活动已结束',route('business.rj_yun_list'));
                    }
                }elseif($first['lottery_type'] == 2){
                    if(time() >= $first['end_time']){
                        return $this->response(500,'活动已结束',route('business.rj_yun_list'));
                    }
                }
            }elseif($first['game_type'] == 2){
                if($first['execute_awards'] >= $first['total_awards']){
                    return $this->response(500,'活动已结束',route('business.rj_yun_list'));
                }
            }

            if(!empty($first['bususer_item_id'])){
                $bususer_item_id = explode(',',$first['bususer_item_id']);

                $brand = $this->brand_list( $bususer_item_id);
                if(empty($brand)){
                    $first['bususer_item_id'] = [];
                }else{
                    foreach($brand as $key=>$val){
                        $store = DB::table(config('tables.youyibao').'.bus_stores')->whereIn('id',$bususer_item_id)->where('brand_id',$val['id'])->select('id','name')->get();
                        $brand[$key]['store']= json_decode(json_encode($store),true);
                    }
                    $first['bususer_item_id'] = $brand;
                }
            }else{
                $first['bususer_item_id'] = [];
            }

            if(!empty($first['machine_item_id'])){
                $data['array_m_id'] = $first['machine_item_id'];
                $RjmachineModel = new RjmachineModel();
                $machine_item_id = $RjmachineModel->select_machine($data);
                $first['machine_item_id'] = json_decode(json_encode($machine_item_id),true);
            }else{
                $first['machine_item_id'] = [];
            }
            if(!empty($first['prize_item_id'])){
                $p_id = explode(',',$first['prize_item_id']);
                $prize_item_id = DB::table(config('tables.base').'.rj_yun_activity_prize')->whereIn('id',$p_id)->get();
                $first['prize_item_id'] = json_decode(json_encode($prize_item_id),true);
            }else{
                $first['prize_item_id'] = [];
            }

//            echo '<pre>';
//            print_r($first);exit;

            return view(env('Merchant_view').'.yun.rj_yun_edit',[
                'barnd'=>$this->brandLists(),
                'first'=>$first
            ]);
        }
    }

    //获取品牌
    public function brandLists(){
        //商户id(总商户，子商户)

        $find = DB::table(config('tables.youyibao').'.bus_users')->where('id',session('id'))->first();

        if($find->pid==0){
            //总商户下的门店
            $store_id = DB::table(config('tables.youyibao').'.bus_stores')->where('userid',$find->id)->lists('id');
        }else{
            //子商户的门店
            $store_id = DB::table(config('tables.youyibao').'.bus_store_manager')->where('bus_userid',$find->id)->lists('store_id');
        }
        $store_id = json_decode(json_encode($store_id),true);

        //总商户
        $builder = DB::table(config('tables.base').'.brand as b')
                    ->leftJoin(config('tables.youyibao').'.bus_stores as a','b.id','=','a.brand_id')
                    ->whereIn('a.id',$store_id)
                    ->groupby('b.id')
                    ->lists('b.name as brand_name', 'b.id as id');

        $builder= json_decode(json_encode($builder),true);
        return $builder;
    }

    //获取门店
    public function rj_yun_storeLists(){
        //总商户
        $builder = DB::table(config('tables.youyibao').'.bus_stores as a')
            ->leftJoin(config('tables.base').'.brand as b','b.id','=','a.brand_id');
        $builder->whereIn('a.id',$this->merchant_store_id());

        if($_POST){
            if(!empty($_POST['brand_id'])){
                $builder->where('b.id',$_POST['brand_id']);
            }
            if(!empty($_POST['store_name'])){
                $builder->where('a.name','like','%'.$_POST['store_name'].'%');
            }
            if(!empty($_POST['arr_store_id'])){
                $arr_store_id = explode(',',rtrim($_POST['arr_store_id'],','));
                $builder->whereNotIn('a.id',$arr_store_id);
            }
        }

        $builder = $builder->orderBy('a.id')->select('a.name as store_name' , 'a.id as store_id','b.name as brand_name')->get();

        $builder= json_decode(json_encode($builder),true);

        return ['data'=>$builder,'msg'=>'查询成功','code'=>200];
    }

    //获取品牌对应的门店
    public function rj_yun_brand_store(){
        $data = $_POST;

        $arr_store_id = explode(',',rtrim($data['arr_store_id'],','));

        $brand = $this->brand_list($arr_store_id);
        if(empty($brand)){
            return $this->response(200,'未选择门店','',[]);
        }


        foreach($brand as $key=>$val){

            $brand[$key]['store'] = DB::table(config('tables.youyibao').'.bus_stores')->whereIn('id',$arr_store_id)->where('brand_id',$val['id'])->select('id','name')->get();

        }

        return $this->response(200,'添加成功','',$brand);
    }
    //门店下的品牌
    public function brand_list($arr_store_id){
        //总商户
        $builder = DB::table(config('tables.youyibao').'.bus_stores as a')->leftJoin(config('tables.base').'.brand as b','b.id','=','a.brand_id');

        $builder->whereIn('a.id',$this->merchant_store_id());
        if(!empty($arr_store_id)){
            $builder->whereIn('a.id',$arr_store_id);
        }

        $builder = $builder->groupBy('b.id')->select('b.id','b.name')->get();
        $builder= json_decode(json_encode($builder),true);

        return $builder;
    }


    //获取机台
    //未判断机台是否在活动中 (活动中的机台不展示)
    public function rj_yun_machine_list(){
        //data={'arr_store_id':selectID,'arr_machine_id':select_machine_id,'m_name':m_name,'_token':_token};

        if(!$_POST['arr_store_id'])return $this->response(500,'请先选择门店');

        $data['arr_stores_id'] = explode(',',rtrim($_POST['arr_store_id'],','));

        if(!empty($_POST['arr_machine_id'])) $data['array_m_id_notin'] = rtrim($_POST['arr_machine_id'],',');

        if(!empty($_POST['m_name'])) $data['m_name'] = $_POST['m_name'];
        //过滤已存在在其他活动中的机台
        $checkAm = DB::table(config('tables.base').'.rj_yun_activity')->where('stauts',1)->get();
        $checkAm = json_decode(json_encode($checkAm),true);

        $AmIdArray = array();
        foreach ($checkAm as $key=>$val){
            if($val['execute_awards'] < $val['total_awards']){
                if(!empty($val['machine_item_id'])){
                    $MachineItemIdArray = explode(',',$val['machine_item_id']);
                    foreach ($MachineItemIdArray as $machineId){
                        if(!in_array($machineId,$AmIdArray)){
                            $AmIdArray[] = $machineId;
                        }
                    }
                }
            }
        }
        $data['NotAmIdArray'] = $AmIdArray;

        $RjmachineModel = new RjmachineModel();
        $device = $RjmachineModel->select_machine($data);

        // $this->_sql();
        // echo '<pre>';print_r($device);exit;

        if(empty($device)){
            return $this->response(403,'该商户下没有可用机台');
        }
        return $this->response(200,'获取成功','',$device);



    }


    public function merchant_store_id(){

        $find = DB::table(config('tables.youyibao').'.bus_users')->where('id',session('id'))->first();
        if($find->pid==0){
            //总商户下的门店
            $store_id = DB::table(config('tables.youyibao').'.bus_stores')->where('userid',$find->id)->lists('id');
        }else{
            //子商户的门店
            $store_id = DB::table(config('tables.youyibao').'.bus_store_manager')->where('bus_userid',$find->id)->lists('store_id');
        }
        $store_id = json_decode(json_encode($store_id),true);

        return $store_id;
    }




}

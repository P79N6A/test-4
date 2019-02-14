<?php

namespace App\Http\Controllers\Merchant\rj;
use App\Http\Controllers\Controller;
use EasyWeChat\Core\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Models\RjmanagementModel;
use App\Http\Models\RjmachineModel;
use GuzzleHttp\json_decode;
use Log;

class ManagementController extends Controller
{

    /*  *  *  *  *  *  *  *  *  *  *  *  *  *  *  * 团队管理 *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  */
    /**
     * 活动管理
     * @param Request $request
     */
    public function Activity_list(Request $request){
    	$this->system_log('查看活动','merchant');
    	$this->update_scduhele_start();	//更新赛程状态
    	
//     	echo '<pre>';
//     	print_r(session('pid'));exit;
    	
    	$data = $request->only('name','start_time','end_time','activity_type','game_type');
    	
   /** type为machant **/
        $data['bus_user_id'] = session('id');
   /** type为machant **/

    	$RjmanagementModel = new RjmanagementModel();
    	$activity_list = $RjmanagementModel->lists($data);

    	return view(env('Merchant_view').'.management.Activity_list',[
    			  		'ads'=>$activity_list,
	    				'name'=>!empty($data['name'])?$data['name']:'',
			      		'start_time'=>!empty($data['start_time'])?$data['start_time']:'',
			      		'end_time'=>!empty($data['end_time'])?$data['end_time']:'',
			      		'activity_type'=>!empty($data['activity_type'])?$data['activity_type']:'',
    	]);
    	
    }
    
    
    
    //创建活动
    public function add_activity(Request $request){
    	
    	if($_POST){
    		$this->system_log('创建活动','merchant');
    		
    		$data = $request->only('name','start_time','end_time','rule','title','game_name','game_rule','game_type','merchint_id');
    		
    		if(empty($data['name'])){ 		return $this->response(403, '活动名称未填写'); }
    		if(empty($data['start_time'])){ return $this->response(403, '请选择活动开始时间'); }
    		if(empty($data['end_time'])){   return $this->response(403, '请选择活动结束时间'); }
    		if(empty($data['rule'])){ 		return $this->response(403, '活动规则未填写'); }
    		if(empty($data['title'])){ 		return $this->response(403, '活动标题未填写'); }
    		if(empty($data['game_name'])){  return $this->response(403, '游戏名称未填写'); }
    		if(empty($data['game_rule'])){  return $this->response(403, '游戏规则未填写'); }
            if(empty($data['game_type'])){  return $this->response(403, '未选择游戏模式'); }
    		if(session('pid')=='0'){
    			$data['merchint_id'] = session('id');
    		}else{
    			$data['merchint_id'] = session('pid');
    		}
    		
    		$data['activity_type']='1';
    		$data['start_time']=$data['start_time']?strtotime($data['start_time']):0;
    		$data['end_time']=$data['end_time']?strtotime($data['end_time']):0;
    		$data['create_time']=time();
            $data['orgin']=2;
    		$id = DB::table(config('tables.base').'.rj_activity')->insertGetId($data);
    		if(!$id)return $this->response(500,'入库失败');
    		return $this->response(200,'创建成功',route('business.rj_activity_list'));
    	}else{
    		
    		$RjmanagementModel = new RjmanagementModel();
    		$merchant_list = $RjmanagementModel->merchant_list();		//获取商户列表
    		return view(env('Merchant_view').'.management.add_activity',[
    				'ads'=>$merchant_list,
    		]);
    	}
    	
    }
    
    //编辑活动

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View|\think\response\View
     */
    public function update_activity(Request $request){
    	$id=$request->get('id');
    	
    	if($_POST){
    		$this->system_log('编辑活动','merchant');
    		
    		if(!$id)return $this->response(500,'保存失败,id不存在');
    		$data = $request->only('name','start_time','end_time','rule','title','game_name','game_rule','game_type');
    		
    		if(empty($data['name'])){
    			return $this->response(403, '活动名称未填写');
    		}
    		if(empty($data['start_time'])){
    			return $this->response(403, '请选择活动开始时间');
    		}
    		if(empty($data['end_time'])){
    			return $this->response(403, '请选择活动结束时间');
    		}
    		if(empty($data['rule'])){
    			return $this->response(403, '活动规则未填写');
    		}
    		if(empty($data['title'])){
    			return $this->response(403, '活动标题未填写');
    		}
    		if(empty($data['game_name'])){
    			return $this->response(403, '游戏名称未填写');
    		}
    		if(empty($data['game_rule'])){
    			return $this->response(403, '游戏规则未填写');
    		}
    	
    	
    		
    		$data['start_time']=$data['start_time']?strtotime($data['start_time']):0;
    		$data['end_time']=$data['end_time']?strtotime($data['end_time']):0;
    		
    		$getid = DB::table(config('tables.base').'.rj_activity')->where('id',$id)->update($data);
    		if(!$getid)return $this->response(500,'保存失败');
    		return $this->response(200,'保存成功',route('business.rj_activity_list'));
    	}else{
	    	if(empty($id)){
	    		return view('admin.error', ['code' => 404, 'msg' => 'id不存在']);
	    	}
    		
    		$RjmanagementModel = new RjmanagementModel();
    		$merchant_list = $RjmanagementModel->merchant_list();	//获取商户列表
    		
    	
    		$first_activity = DB::select('select a.*, 
    				(select u.name as merchint_name from '.config('tables.youyibao').'.bus_users as u where a.merchint_id=u.id) as merchint_name 
    				from 
    				'.config('tables.base').'.rj_activity as a where id='.$id);
    	
//     		echo '<pre>';
//     		print_r($first_activity[0]);exit;
    		return view(env('Merchant_view').'.management.update_activity',[
    				'ads'=>$merchant_list,
    				'first'=>$first_activity[0]
    		]);
    	}
    	 
    }

    //删除活动
    public function activity_del(Request $request){
        $id = $request->get('id');
        if(!$id)return $this->response(500,'参数ID错误');
        $del = DB::table(config('tables.base').'.rj_activity')->where('id',$id)->update(['stauts'=>'2']);
        if(!$del)return $this->response(403,'没有找到该活动');
        $this->system_log('删除活动 ','admin');
        return $this->response(200,'删除成功');
    }
    
    /**
     * @name 模糊查询商户列表
     * @param pid
     * @param merchint_name   商户昵称
     * @return Ambigous <multitype:, mixed>
     */
    public function merchint_list(Request $request){
    	$data = $request->only('pid','merchint_name');
    	$where=array(
    			'pid'=>$data['pid'],
    			'name'=>$data['merchint_name'],
    	);
    	$RjmanagementModel = new RjmanagementModel();
    	$merchant_list = $RjmanagementModel->merchant_list($where);
    	
//     	echo '<pre>';
//     	print_r($merchant_list);exit;
    	
    	return $merchant_list;
    }
    
    
    

    /************************************ 活动详情 兑换码 兑换 *******************************************/
    /**
     * 获取已结束的整个活动赛程详情
     * @param activity_id  活动id
     */
    public function activity_game_info(Request $request){

        $id = $request->get('id');
        if(!empty($id)){
            //活动
            $activity_info = DB::table(config('tables.base').'.rj_activity')->where('id',$id)->first();
            $activity_info = json_decode(json_encode($activity_info),true);

            //赛程游戏列表
            $game_type = $request->get('game_type');
            if($game_type==1){
                $news_list = $this->user_game_list($id,1);
                $activity_info['scduhele'] = $news_list;
            }else{
                $arr_id =  DB::table(config('tables.base').'.rj_activity_scduhele')->where('a_id',$id)->lists('id','s_name');
                if(!empty($arr_id)){
                    foreach($arr_id as $key=>$val){
                        $activity_info['scduhele'][$val]['ScduheleName'] = $key;
                        $activity_info['scduhele'][$val]['TeamRanking'] =  $this->TeamRanking($val);
                    }
                }else{
                    $activity_info['scduhele'] = [];
                }


//                echo '<pre>';
//                print_r($activity_info);exit;

            }

            return view(env('Merchant_view').'.management.activity_game_info',[
                'activity_info'=>$activity_info,
                'game_type'=>$game_type
            ]);
        }else{
            return view('admin.error', ['code' => 403, 'msg' => '请传入活动id不存在']);
        }
    }

    /**
     * @name 获取用户 活动的赛程下 游戏记录
     * @param unknown $id
     * @param unknown $PC 判断是否为获取数据   0-生产兑换码 1-获取数据
     * @return mixed
     */
    public function user_game_list($id,$PC=0){
    	$user_game_list = DB::table(config('tables.base').'.rj_game_log as gl')
    	->leftJoin(config('tables.base').'.users as u','u.id','=','gl.u_id')
    	->leftJoin(config('tables.base').'.rj_activity_scduhele as s','s.id','=','gl.s_id')
    	->where('s.a_id',$id)
    	->where('gl.network_status','2')
    	->orderby('gl.s_id','asc')
    	->orderby('gl.num','desc')
    	->select(
    			's.a_id',
    			'gl.id as id',
    			'gl.u_id as u_id',
    			'u.nickname as nickname',
    			'u.username as username',
    			'gl.num as num',
    			'gl.create_time as game_create_time',
    			'gl.s_id as s_id',
    			's.s_name',
    			's.p_id as prize_id'
    	)
    	->get();
    	//	  	$this->_sql();
    	$user_game_list = json_decode(json_encode($user_game_list),true);
    	if(!empty($user_game_list)){
    		$news_list = [];
    		$kk=0;
    		foreach($user_game_list as $key=>$val){
    			$news_list[$val['s_id']]['name'] = $val['s_name'];
    			//	$news_list[$val['s_id']]['prize_id'] = $val['prize_id'];
    			$news_list[$val['s_id']]['list'][$kk] = $val;
    			//     		echo '<pre>';
    			//     		print_r($val);exit;
    			if($PC==1){
    				$UPI = DB::table(config('tables.base').'.rj_game_user_prize as g')
    				->where('g.u_id',$val['u_id'])
    				->where('g.s_id',$val['s_id'])
    				->leftJoin(config('tables.base').'.rj_activity_prize_item as pi','pi.id','=','g.p_item_id')
    				->select('pi.*','g.exchange_type as exchange_type')->first();
    				//	$this->_sql();
    					
    				if(!empty($UPI)){
    					$UPI = json_decode(json_encode($UPI),true);
    					 
    					if($UPI['type']==1){
    						$news_list[$val['s_id']]['list'][$kk]['pirze_item_name'] = $UPI['item_name'];	//线下礼品
    					}
    					//     						if(($UPI->type)==2){
    					//     							$news_list[$u_key]['list'][$kk]['pirze_num'] = $UPI->num;			//奖票
    					//     						}
    					$news_list[$val['s_id']]['list'][$kk]['exchange_type'] = $UPI['exchange_type'];	//是否已经兑换
    				}else{
    					$news_list[$val['s_id']]['list'][$kk]['pirze_item_name'] = '--';
    					$news_list[$val['s_id']]['list'][$kk]['exchange_type'] = 0;
    				}
    			}
    			$kk++;
    		}
    
    		//	$news_list = $this->prize_lists($news_list);
    
    	}else{
    		$news_list=[];
    	}
    	 
    	return $news_list;
    }
     
    
    
    /**
     * 推送兑换码1
     */
    public function push_redeem_code(Request $request){
    	$id = $request->input('id');
    	$news_list = $this->user_game_list($id);
    	if(!empty($news_list)){
    		$news_list = $this->prize_lists($news_list);
    	}
    	 
    	return $this->response(200,'兑换码生成成功','');
    }
    
    /**
     * 开始生成兑换码2
     * @param unknown $news_list
     */
    public function prize_lists($news_list){
    	 
    	// 	echo '<pre>';
    	//	print_r($news_list);
    	 
    	foreach($news_list as $key=>$val){
    		foreach($val['list'] as $k=>$v){
    			$k++;
    			//		print_r($v);
    			 
    			//获取赛程下面的奖品
    			$json_prize_item_id = DB::table(config('tables.base').'.rj_activity_prize')->where('id',$v['prize_id'])->value('prize_item_id');
    			$prize_item_id = json_decode($json_prize_item_id,true);		//获取到的奖品数组
    			 
    			if(!empty($prize_item_id)){
    
    				$obj_item_list = DB::table(config('tables.base').'.rj_activity_prize_item')->whereIn('id',$prize_item_id)->get();
    				$arr_item_list = json_decode(json_encode($obj_item_list),true);
    				foreach($arr_item_list as $key_item=>$val_item){
    					if($val_item['type']==1){  //线下礼品才生成兑换码
    						if($k==$val_item['rank']){	//按排名发布奖品
    							$find = DB::table(config('tables.base').'.rj_game_user_prize')
    							->where('u_id',$v['u_id'])
    							->where('s_id',$v['s_id'])
    							->where('p_item_id',$val_item['id'])
    							->first();
    							if(empty($find)){
    								$code = $this->exchange_code($v['game_create_time'],$v['u_id'],$v['a_id'],$v['s_id']);  //生成兑换码
    								$add_data = array(
    										'u_id'=>$v['u_id'],
    										's_id'=>$v['s_id'],
    										'p_item_id'=>$val_item['id'],
    										'exchange_code'=>$code,
    										'exchange_type'=>1
    								);
    								DB::table(config('tables.base').'.rj_game_user_prize')->insert($add_data);
    								 
    							}
    						}
    					}
    				}
    
    			}
    		}
    	}
    	 
    	return true;
    }
    
    
    /**
     * 制造兑换码 T170612U123A2S322
     * 1时间(年月日)--T
     * 2用户id-------U
     * 3活动id-------A
     * 4赛程id-------S
     */
    public function exchange_code($date,$uid,$aid,$sid){
    	$tiem = date('ymd',strtotime($date));
    	return 'T'.$tiem.'U'.$uid.'A'.$aid.'S'.$sid;
    }
    
    
    /**
     * 核销兑换码
     * 1.兑换码是否存在
     * 2.兑换码是否已经兑换
     * 3.兑换成功
     * @param code 兑换码
     */
    public function Write_off_code(Request $request){
    	if($request->has('code')){
    		$code = $request->input('code');
    		$first = DB::table(config('tables.base').'.rj_game_user_prize')->where('exchange_code',$code)->first();
    		//print_r($first);exit;
    		if(!empty($first)){
    			if(($first->exchange_type)==2) return $this->response(403,'该码已兑换过了');
    			$update_array = ['exchange_type'=>2];
    			$update = DB::table(config('tables.base').'.rj_game_user_prize')->where('exchange_code',$code)->update($update_array);
    			if($update==false) return $this->response(500,'兑换失败');
    			return $this->response(200,'兑换成功');
    		}else{
    			return $this->response(404,'兑换码不存在');
    		}
    	}else{
    		return $this->response(403,'请传入兑换码');
    	}
    }
    
    
    
    
	/***************************************************** 赛程管理  *****************************************************************/


    /*
     * 机台列表（某个商户下面的机台列表）
     */
    public function machine_user(Request $request){
    	$data = $request->only('bus_user_id','array_m_id','array_m_id_notin','stores_name','m_name','hardware_sn','firmware_sn','model');
    	if(!$data['bus_user_id'])return $this->response(500,'商户id不存在');
    	 
    	$RjmachineModel = new RjmachineModel();
    	$device = $RjmachineModel->select_machine($data);
    	//	$this->_sql();
    	//echo '<pre>';print_r($device);exit;
    	if(empty($device)){
    		return $this->response(403,'该商户下没有机台');
    	}
    	return $this->response(200,'获取成功','',$device);
    	 
    }

    /**
     * @name 获取赛程
     * @param 根据活动id获取赛程
     */
    public function add_schedule(Request $request){
        $this->system_log('查看赛程列表 ','admin');
        $id=$request->get('id');
        if(empty($id)){ return view('admin.error', ['code' => 404, 'msg' => 'id不存在']); }

        $game_type=$request->get('game_type');


        $activity = DB::table(config('tables.base').'.rj_activity')->where('id',$id)->first();

        //通过商户id 判断活动是否完善
        if(!empty($activity->merchint_id)){
            $RjmanagementModel = new RjmanagementModel();

            //获取子账号
            $where=array( 'pid'=>$activity->merchint_id );
            $merchant_list_sub = $RjmanagementModel->merchant_list($where);

            //获取总账号
            $where=array( 'id'=>$activity->merchint_id );
            $merchant_list_zong =$RjmanagementModel->merchant_list($where);

            //合并数组
            $merchant_list = array_merge($merchant_list_zong,$merchant_list_sub);
        }else{
            $url = route('admin.update_activity',['id'=>$id]);
            echo  '<script>
    						alert("请先完善活动信息");
    						window.location.href="'.$url.'"
    				   </script>';
        }

        //获取赛程
        $scduhele_list = DB::table(config('tables.base').'.rj_activity_scduhele')->where('a_id',$activity->id)->orderBy('id','asc')->get();

//	        $this->_sql();
//     		echo '<pre>';
//     		print_r($scduhele_list);exit;

        if(empty($scduhele_list)){
            $scduhele_list=[];
        }else{
            $scduhele_list= json_decode(json_encode($scduhele_list),true);
            foreach($scduhele_list as $key=>$val){
                //子账号
                $scduhele_list[$key]['b_id']=DB::table(config('tables.base').'.rj_activity_scduhele_bususer')
                    ->where('s_id',$val['id'])
                    ->lists('b_id');
                if($game_type==1){

                    //单机
                    //机台列表
                    $arr_m_id = DB::table(config('tables.base').'.rj_activity_scduhele_machine')->where('s_id',$val['id'])->lists('m_id');

                    if($arr_m_id){
                        $select_machine_data['array_m_id'] = implode(',', $arr_m_id);
                        $RjmachineModel = new RjmachineModel();
                        $device = $RjmachineModel->select_machine($select_machine_data);
                        //	$this->_sql();
                        $scduhele_list[$key]['m_id'] = json_decode(json_encode($device),true);
                    }

                }else{

                    //团队
                    //机台列表
                    $arr_m_id = DB::table(config('tables.base').'.rj_activity_scduhele_machine')->where('s_id',$val['id'])->get();
                    $arr_m_id = json_decode(json_encode($arr_m_id),true);
//     echo '<pre>';
//    print_r($arr_m_id);
                    $m = [];
                    foreach($arr_m_id as $m_key=>$m_val){
                        $m[$m_val['team_name']]['team_name'] = $m_val['team_name'];
                        $m[$m_val['team_name']]['m_id'][$m_key]=$m_val;
                    }
//    print_r($m);
                    $i=0;
                    $m_return=[];

                    foreach($m as $r_key=>$r_val){
                        $m_return[$i]['team_name'] = $r_val['team_name'];
                        @$m_return[$i]['m_id'] = [];
//echo $r_key;
//print_r($r_val['m_id']);
                        $m_id_arr=[];
                        foreach($r_val['m_id'] as $kk=>$vv){
                            $m_id_arr[$kk] = $vv['m_id'];
                        }

                        if($m_id_arr){
                            $select_machine_data['array_m_id'] = implode(',', $m_id_arr);
                            $RjmachineModel = new RjmachineModel();
                            $device = $RjmachineModel->select_machine($select_machine_data);
                            //	$this->_sql();
                            @$m_return[$i]['m_id'] = json_decode(json_encode($device),true);
                        }

                        $i++;
                        $scduhele_list[$key]['team'] = $m_return;
                    }

//    print_r($m_return);exit;

                }



                //获取奖品列表
                $prize = json_decode(json_encode(DB::table(config('tables.base').'.rj_activity_prize')->where('id',$val['p_id'])->first()),true);	//获取奖项id
// 	    					echo '<pre>';
// 	    					print_r($prize);
                $prize_item_id = json_decode($prize['prize_item_id'],true);
//							print_r($prize_item_id);
                $prize_item = DB::table(config('tables.base').'.rj_activity_prize_item')->whereIn('id',$prize_item_id)->get();
//  						print_r($prize_item);
//			$this->_sql();
                $scduhele_list[$key]['p_id'] = json_decode(json_encode($prize_item),true);

            }
        }

//     		echo '<pre>';
//     		print_r($scduhele_list);exit;

        $game_type = $request->get('game_type');
        if($game_type==1){
            return view(env('Merchant_view').'.management.add_schedule',[
                'a_id'=>$id,
                'activity'=>$activity,
                'merchant'=>$merchant_list,
                'scduhele_list'=>$scduhele_list
            ]);
        }else{
            return view(env('Merchant_view').'.management.add_schedule_two',[
                'a_id'=>$id,
                'activity'=>$activity,
                'merchant'=>$merchant_list,
                'scduhele_list'=>$scduhele_list
            ]);
        }
    }


    /**
     * 编辑赛程
     */
    public function insert_schedule(Request $request){
        $this->system_log('编辑赛程 ','admin');
        $data = $request->only('data');
        $id = $request->get('id');
        $game_type = $request->get('game_type');

//     	echo $id;
//     	echo $game_type;
//     	echo '<pre>';
//     	print_r($data['data']);exit;

        $schedule_id = DB::table(config('tables.base').'.rj_activity_scduhele')->where('a_id',$id)->lists('id');	//该活动下的所有赛程
        $data_id = array_column($data['data'], 'id');	//未删除赛程
        $arr_del_id = array_diff($schedule_id,$data_id);

        // 	   	print_r($schedule_id);
        // 	   	print_r($data_id);
        // 	   	print_r($arr_del_id);exit;
        //删除赛程
        if(!empty($arr_del_id)){
            $this->schedule_delete($arr_del_id);
        }
        //   	echo 2;exit;


        //添加和编辑赛程
        if(!empty($data['data'])){

            foreach($data['data'] as $k=>$v){

                if(empty($v['s_name'])){
                    return $this->response(403, '第'.($k+1).'个赛程名称未填写');
                }
                if(empty($v['merchant_id'])){
                    return $this->response(403, '第'.($k+1).'个赛程未选择子商户');
                }
                if($game_type==1){
                    //单机
                    if(empty($v['machine_id'])){
                        return $this->response(403, '第'.($k+1).'个赛程未添加机台');
                    }
                }else{
                    //团队
                    if(empty($v['team'])){
                        return $this->response(403, '第'.($k+1).'个赛程未添加团队');
                    }
                }
                if(empty($v['prize'])){
                    return $this->response(403, '第'.($k+1).'个赛程未设置奖品');
                }

                if(empty($v['id'])){
                    //添加赛程
                    $prize_id = 0;
                    if(!empty($v['prize'])){
                        //入库奖品
                        $prise_item_id_arr=[];
                        foreach($v['prize'] as $key=>$val){

                            $prize_item_arr = array(
                                'type'=>$val['option'],
                                'item_name'=>$val['name'],
                                'itme_img'=>$val['pic'],
                                'num'=>$val['num'],
                                'rank'=>$val['ranking']
                            );
                            $prise_item_id_arr[] = Db::table(config('tables.base').'.rj_activity_prize_item')->insertGetId($prize_item_arr);
                            unset($prize_item_arr);
                        }
                        $prize_id = DB::table(config('tables.base').'.rj_activity_prize')->insertGetId(['prize_item_id'=>json_encode($prise_item_id_arr)]);
                    }

                    //赛程入库
                    $insert_data = array(
                        's_name'=>$v['s_name'],
                        'price'=>$v['price'],
                        'a_id'=>$id,
                        'p_id'=>$prize_id
                    );
                    $return = DB::table(config('tables.base').'.rj_activity_scduhele')->insertGetId($insert_data);

                    //入库有权控制的子商户
                    $a_s_bus_user = [];
                    foreach($v['merchant_id'] as $m_key=>$m_val){
                        $a_s_bus_user[]=array(
                            'b_id'=>$m_val,
                            's_id'=>$return,
                        );
                    }
                    DB::table(config('tables.base').'.rj_activity_scduhele_bususer')->insert($a_s_bus_user);

                    //入库机台
                    $a_s_m=[];
                    if($game_type==1){
                        //单机
                        foreach($v['machine_id'] as $machine_key=>$machine_val){
                            $a_s_m[]=array(
                                'm_id'=>$machine_val,
                                's_id'=>$return,
                                'm_state'=>0,
                                's_state'=>1,
                            );
                        }
                    }else{
                        //团队
                        foreach($v['team'] as $t_key=>$t_val){
                            foreach($t_val['machine_id'] as $machine_key=>$machine_val){
                                $a_s_m[]=array(
                                    'team_name'=>$t_val['game_name'],
                                    'm_id'=>$machine_val,
                                    's_id'=>$return,
                                    'm_state'=>0,
                                    's_state'=>1,
                                );
                            }
                        }

                    }

                    DB::table(config('tables.base').'.rj_activity_scduhele_machine')->insert($a_s_m);

                }else{
                    //编辑赛程
                    $first_schedule = DB::table(config('tables.base').'.rj_activity_scduhele')->where('id',$v['id'])->first();	//赛程信息

                    $update_data = array(
                        's_name'=>$v['s_name'],
                        'price'=>$v['price'],
                        'a_id'=>$id,
                    );
                    $return = DB::table(config('tables.base').'.rj_activity_scduhele')->where('id',$v['id'])->update($update_data);

                    //编辑有权控制的子商户
                    DB::table(config('tables.base').'.rj_activity_scduhele_bususer')->where('s_id',$v['id'])->delete();
                    $a_s_bus_user=[];
                    foreach($v['merchant_id'] as $m_key=>$m_val){
                        $a_s_bus_user[]=array(
                            'b_id'=>$m_val,
                            's_id'=>$v['id'],
                        );
                    }
                    DB::table(config('tables.base').'.rj_activity_scduhele_bususer')->insert($a_s_bus_user);

                    //编辑机台
                    DB::table(config('tables.base').'.rj_activity_scduhele_machine')->where('s_id',$v['id'])->delete();
                    $a_s_m=[];
                    if($game_type==1){
                        //单机
                        foreach($v['machine_id'] as $machine_key=>$machine_val){
                            $a_s_m[]=array(
                                'm_id'=>$machine_val,
                                's_id'=>$v['id'],
                                'm_state'=>0,
                                's_state'=>1,
                            );
                        }
                    }else{
                        //团队
                        foreach($v['team'] as $t_key=>$t_val){
                            foreach($t_val['machine_id'] as $machine_key=>$machine_val){
                                $a_s_m[]=array(
                                    'team_name'=>$t_val['game_name'],
                                    'm_id'=>$machine_val,
                                    's_id'=>$v['id'],
                                    'm_state'=>0,
                                    's_state'=>1,
                                );
                            }
                        }
                    }

                    DB::table(config('tables.base').'.rj_activity_scduhele_machine')->insert($a_s_m);

                    //编辑奖品
                    $prize_id = 0;
                    if(!empty($v['prize'])){

                        $prise_item_id_arr=[];
                        foreach($v['prize'] as $key=>$val){
                            if(empty($val['id'])){
                                $prize_item_arr = array(
                                    'type'=>$val['option'],
                                    'item_name'=>$val['name'],
                                    'itme_img'=>$val['pic'],
                                    'num'=>$val['num'],
                                    'rank'=>$val['ranking']
                                );
                                $prise_item_id_arr[] = Db::table(config('tables.base').'.rj_activity_prize_item')->insertGetId($prize_item_arr);
                                unset($prize_item_arr);
                            }else{
                                $prize_item = array(
                                    'type'=>$val['option'],
                                    'item_name'=>$val['name'],
                                    'itme_img'=>$val['pic'],
                                    'num'=>$val['num'],
                                    'rank'=>$val['ranking']
                                );
                                $res = Db::table(config('tables.base').'.rj_activity_prize_item')->where('id',$val['id'])->update($prize_item);
//	Log::debug('奖品编辑'.json_encode($prize_item));
                                $prise_item_id_arr[] = $val['id'];

                                unset($prize_item_arr);
                            }
                        }

                        //		print_r($prise_item_id_arr);exit;

                        //删除多余奖品
                        $prize_item_id = DB::table(config('tables.base').'.rj_activity_prize')->where('id',$first_schedule->p_id)->value('prize_item_id');
                        if(!empty($prize_item_id)){
                            $del_prize_item_id = array_diff(json_decode($prize_item_id,true),$prise_item_id_arr);
                            DB::table(config('tables.base').'.rj_activity_prize_item')->whereIn('id',$del_prize_item_id)->delete();
                        }
                        $prize_id = DB::table(config('tables.base').'.rj_activity_prize')->where('id',$first_schedule->p_id)->update(array('prize_item_id'=>json_encode($prise_item_id_arr)));

                    }

                }

                unset($insert_data);
                unset($prize_id);
            }
/**
            $retrun  = $this->EquipmentQR_code($id); 	//更新机台机位
            $this->write_log('更新机台机位'.$retrun);
**/


            return $this->response(200, '提交成功');

        }
        else
            return $this->response(200, '未添加赛程');

    }



    /**
     * 删除赛程
     * @param unknown $arr_del_id   通过对比数据库的活动赛程ID 找到需要删除的赛程
     * @return boolean
     */
    public function schedule_delete($arr_del_id){
    	$this->system_log('删除赛程 ','merchant');
    	 	 
    	// 根据赛程ID 找到奖品池id  再根据奖品池ID 删除奖品
   		 $arr_p_id = DB::table(config('tables.base').'.rj_activity_scduhele')->whereIn('id',$arr_del_id)->lists('p_id');
    	if(!empty($arr_p_id)){
    		$array_prize_item_id = DB::table(config('tables.base').'.rj_activity_prize')->whereIn('id',$arr_p_id)->lists('prize_item_id');
  // echo '<pre>'; print_r($array_prize_item_id);exit;	
    		foreach ($array_prize_item_id as $p_key => $p_val) {
    			DB::table(config('tables.base').'.rj_activity_prize_item')->whereIn('id',json_decode($p_val,true))->delete();	//根据奖品池ID 找到奖品ID 直接删除
    		}
    	}
    	// 根据赛程ID 删除赛程
    	DB::table(config('tables.base').'.rj_activity_scduhele')->whereIn('id',$arr_del_id)->delete();
    	
    	//删除赛程商户
    	DB::table(config('tables.base').'.rj_activity_scduhele_bususer')->whereIn('s_id',$arr_del_id)->delete();
    	
    	return true;
    }

   
 
    
  
}

<?php

namespace App\Http\Controllers\Merchant\rj;
use App\Http\Controllers\Controller;
use EasyWeChat\Core\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Models\RjmachineModel;
use GuzzleHttp\json_decode;
use GuzzleHttp\json_encode;
use App\Http\Controllers\RuanJieApi\EquipmentController;

class OtaController extends Controller
{

	private $u_key='!@#$%^&*1234567890qwerturrwad$%^$^&*fadzXzbdshrna349697373@!@#$';
	public $error='';
	public $success='';
	
  	/**
  	 *@name  OTA 升级历史
  	 *
     */
    public function rj_ota_list(Request $request){
    	$this->system_log('查看OTA升级 ','merchant');
    	
    	$data = $request->only('brand_name','model','id','stores_name','m_name','firmware_sn','code','hardware_sn');
    	 
    	$RjmachineModel = new RjmachineModel();
    	$device = $RjmachineModel->ota($data);		
    	//     echo '<pre>';print_r($device);exit;
    
  		//     echo '<pre>';	  print_r($this->getQueryLog());	exit;
    
    	return view(env('Merchant_view').'.ota.ota_list',[
    			'ads'=>$device,
    			'id'=>!empty($data['id'])?$data['id']:'',
    			'stores_name'=>!empty($data['stores_name'])?$data['stores_name']:'',
    			'm_name'=>!empty($data['m_name'])?$data['m_name']:'',
    			'firmware_sn'=>!empty($data['firmware_sn'])?$data['firmware_sn']:'',
    			'code'=>!empty($data['code'])?$data['code']:'',
    			'hardware_sn'=>!empty($data['hardware_sn'])?$data['hardware_sn']:''
    	]);
    }
    
    //移除升级机台
    public function rj_ota_list_del(Request $request){
    	$this->system_log('移除升级机台 ','merchant');
    	if($_POST){
	    	$res = DB::table(config('tables.base').'.rj_upgrade_machine')->where('id',$_POST['id'])->delete();
	    	if($res=='') return $this->response(403, '移除失败');
	    	return $this->response(200, '移除成功');
	    }else{
	    	return $this->response(500, '请求内部错误');
	    }
    }
    
    
    /**
     * @name 固件版本管理
     * 
     */
    public function rj_ota_firmware(Request $request){
		if($_POST){
			
			$data = $request->only([ 'firmware_sn', 'hardware_sn', 'firmware_id', 'ramrk', 'start' ]);
			
			if (!preg_replace('/\s/', '', $data['firmware_sn'])) {
				return $this->response(403, '请输入固件版本号');
			}
			if (!preg_replace('/\s/', '', $data['hardware_sn'])) {
				return $this->response(403, '请输入硬件版本号');
			}
			if (!preg_replace('/\s/', '', $data['firmware_id'])) {
				return $this->response(403, '请上传新固件版本');
			}
				
//			//判断固件版本号和硬件版本号是否存在
//			$firmware_sn = DB::table(config('tables.base').'.rj_ota_upgrade')->where('firmware_sn',$data['firmware_sn'])->value('firmware_sn');
//			//var_dump($firmware_sn);exit;
//			if (!empty($firmware_sn)) {
//				return $this->response(403, '固件版本号存在');
//			}
//			$hardware_sn = DB::table(config('tables.base').'.rj_ota_upgrade')->where('hardware_sn',$data['hardware_sn'])->value('hardware_sn');
//			if (!empty($hardware_sn)) {
//				return $this->response(403, '硬件版本号存在');
//			}

			$data['add_date'] = date('Y-m-d H:i:s',time());
			$id = DB::table(config('tables.base').'.rj_ota_upgrade')->insertGetId($data);	
			if ($id) {
				$this->system_log('上传固件版本 ','merchant');
				return $this->response(200, '保存成功');
			}
			return $this->response(500, '内部错误');
		}else{
			//已上传固件
			$upgrade = DB::table(config('tables.base').'.rj_ota_upgrade')->orderBy('add_date','desc')->paginate(20);
			//	echo '<pre>';	print_r($upgrade);exit;
			return view(env('Merchant_view').'.ota.ota_firmware',[
					'ads'=>$upgrade
			]);
		}
    }
    
    /**
     * 版本状态修改
     */
    public function ota_firmware_update(){
    	if($_POST){
    		$res = DB::table(config('tables.base').'.rj_ota_upgrade')->where('id',$_POST['id'])->update(['start'=>$_POST['start']]);
    		if($res=='') return $this->response(403, '操作失败');
    		$this->system_log('固件版本状态修改 ','merchant');
    		return $this->response(200, '操作成功');
    	}else{
    		return $this->response(500, '请求内部错误');
    	}
    }
    
    
    /**
     * 版本详情
     * @return Ambigous <\Illuminate\View\View, \Illuminate\Contracts\View\Factory>
     */
    public function ota_firmware_info(){
    	
    	$this->system_log('查看固件版本详情','merchant');
   		//版本详情
    	$upgrade = DB::table(config('tables.base').'.rj_ota_upgrade as o')->where('o.id',$_GET['id'])
					    	->leftJoin(config('tables.youyibao').'.attachment as a','a.id','=','o.firmware_id')
					    	->select('o.*','a.path as firmware_url')
					    	->first();
    	$upgrade= json_decode(json_encode($upgrade),true);		//  echo '<pre>';print_r($upgrade);exit;  
    	
    	//升级列表
    	$machine_list = DB::table(config('tables.base').'.rj_upgrade_machine as u')->where('u.upgrade_id',$_GET['id'])
						    	->leftJoin(config('tables.base').'.rj_iot_machine AS m','m.id','=','u.machine_id')
						    	->leftJoin(config('tables.base').'.rj_iot_product AS p', 'p.id',' =','m.product_id')
						    	->leftJoin(config('tables.base').'.rj_iot_dev AS d','d.id',' = ','m.dev_id')
						    	->leftJoin(config('tables.youyibao').'.bus_stores AS s', 's.id','=','p.store_id')
						    	->select(
						    			'u.id as u_id',
						    			'u.type as upgrade_type',
						    			'u.s_date as s_date',
						    			'u.e_date as e_date',
						    			'u.is_upgrade as is_upgrade',
						    			's.brand_id as brand_id',
						    			's.name as stores_name',
						    			'm.name as m_name',
						    			'p.model as model',
						    			'd.firmware_sn as firmware_sn',
						    			'd.hardware_sn as hardware_sn'
						    	)
						    	->get();
//     	echo '<pre>';
//     	$this->_sql();
//     	print_r($machine_list);exit;


    	return view(env('Merchant_view').'.ota.ota_firmware_info',[
    			'info'=>$upgrade,
    			'ads'=>$machine_list
    	]);
    }
    
    
    //选择机台
    public function ota_select_machine(Request $request){
    	$id = $request->get('id');
    	if($_POST){
    		$session = $request->session()->get($this->u_key.$id);
    		$return = $this->ota_select_machine_update($_POST,$id,$session);
    		
    		if($return=='200'){return $this->response($return, $this->success);}else{return $this->response($return,$this->error);}
    		
    	}else{
    		$data = $request->only('brand_name','stores_name','m_name','firmware_sn','model','hardware_sn');
    		 
    		$RjmachineModel = new RjmachineModel();
    		$device = $RjmachineModel->select_machine($data);
    		
    		
    		//该版本已经选中的机台
    		$machine_id= array_column(json_decode(json_encode($device),true), 'm_id');		//查询出来的机台
    		$arr_machine_id = DB::table(config('tables.base').'.rj_upgrade_machine')		//该版本已选中的机台
							    		->where('upgrade_id',$id)
							    		->whereIn('machine_id',$machine_id)
							    		->lists('machine_id');
    		//加入缓存
    		$request->session()->put($this->u_key.$id,json_encode(['arr_machine_id'=>$arr_machine_id,'machine_id'=>$machine_id]));
    		
    		
    		return view(env('Merchant_view').'.ota.ota_select_machine',[
    				'ads'=>$device,
    				'arr_machine_id'=>$arr_machine_id,	//根据查询出来已选中的数据
    				'id'=>$id,
    				'brand_name'=>!empty($data['brand_name'])?$data['brand_name']:'',
    				'stores_name'=>!empty($data['stores_name'])?$data['stores_name']:'',
    				'm_name'=>!empty($data['m_name'])?$data['m_name']:'',
    				'firmware_sn'=>!empty($data['firmware_sn'])?$data['firmware_sn']:'',
    				'model'=>!empty($data['model'])?$data['model']:'',
    				'hardware_sn'=>!empty($data['hardware_sn'])?$data['hardware_sn']:'',
    		]);
    	}
    }
    
    
    public function ota_select_machine_update($data,$id,$session_json){
    	$this->system_log('添加升级固件机台','merchant');
    	if($data['type']=='2'){
    		if(empty($data['start'])){ $this->error = '请选择定时时间'; return 403; }
    		if(empty($data['end'])){	$this->error = '请选择定时时间'; return 403; }
    	}
    	
    	$session_array = json_decode($session_json,true);
	    	$arr_machine_id =$session_array['arr_machine_id']; //该版本已选中的机台
	    	$machine_id =$session_array['machine_id']; //查询出来的机台

        //先更新机台升级列表
        $save['time']=date('Y-m-d H:i:s',time());
        DB::table(config('tables.base').'.rj_upgrade_machine')
            ->where('upgrade_id',$id)
            ->whereIn('machine_id',$machine_id)
            ->update($save);

// 	    	echo '<pre>';
// 	    	print_r($data);exit;   
	    	
	    	//页面传过来的id
	    	if(!empty($data['sel'])){ $sel = $data['sel']; 	}else{ $sel = array(); 	}
	    	
	    	
	    	$del_uid = array_diff($arr_machine_id, $sel);	//找出删除的元素  
	    	if(!empty($del_uid)){
	    		$table = DB::table(config('tables.base').'.rj_upgrade_machine')->where('upgrade_id',$id)
	    					->whereIn('machine_id',$del_uid)->delete();
	    	}
	    	
	    	$add_uid = array_diff($sel, $arr_machine_id);		///找出新增的元素 
	    	if(!empty($add_uid)){
		    	foreach($add_uid as $k=>$v){
		    		$add[$k]['upgrade_id']=$id;
		    		$add[$k]['machine_id']=$v;
		    		$add[$k]['type']=$data['type'];
		    		$add[$k]['s_date']=!empty($data['start'])?$data['start']:'';
		    		$add[$k]['e_date']=!empty($data['end'])?$data['end']:'';
		    		$add[$k]['is_upgrade']='0';
		    	}
		    	$table = DB::table(config('tables.base').'.rj_upgrade_machine')->where('upgrade_id',$id)->insert($add);
	    	}

        //立即升级通知机台
        if($data['type']=='0'){

            $list_serialNo = DB::table(config('tables.base').'.rj_iot_dev as d')
                ->leftJoin(config('tables.base').'.rj_iot_machine AS m','m.dev_id','=','d.id')
                ->whereIn('m.id',$sel)
                ->lists('d.serial_no');
            if(!$list_serialNo){ $list_serialNo=[];}
            $Equipment =  new EquipmentController();

            $param['serial_no'] = json_encode($list_serialNo);


            $res = $Equipment->NoticeMachineUpgrade($param);
            if($res===false){
                $this->error = $Equipment->error; return 403;
            }
        }
	    	
	    	$this->success = '修改成功';
	    	return 200;

	    	
	    	
    }
    
    
  
}

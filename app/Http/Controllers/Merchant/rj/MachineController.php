<?php

namespace App\Http\Controllers\Merchant\rj;
use App\Http\Controllers\Controller;
use EasyWeChat\Core\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\json_decode;
use GuzzleHttp\json_encode;
use App\Http\Models\RjmachineModel;
use App\Http\Controllers\RuanJieApi\EquipmentController;


class MachineController extends Controller
{

    /**
     * 机台列表
     * @return 
     */
    public function index(Request $request){
    	$this->system_log('查看机台列表','merchant');
    	//$this->EquipmentStatusUpdate();		//去阿里获取数据并更新数据库数据
    	
    	$data = $request->only('brand_name','stores_name','is_disable','m_name','firmware_sn','is_open','model','hardware_sn','code');

    	/** type为machant **/
        $data['bus_user_id'] = session('id');
    	/** type为machant **/
    	
		$RjmachineModel = new RjmachineModel();
    	$device = $RjmachineModel->lists($data);
    	
//  	$this->_sql();
//      echo '<pre>';print_r($device);exit;


      return view(env('Merchant_view').'.machine.Machine-list',[
      		'ads'=>$device,
      		'brand_name'=>!empty($data['brand_name'])?$data['brand_name']:'',
      		'stores_name'=>!empty($data['stores_name'])?$data['stores_name']:'',
      		'is_disable'=>!empty($data['is_disable'])?$data['is_disable']:0,
      		'm_name'=>!empty($data['m_name'])?$data['m_name']:'',
      		'firmware_sn'=>!empty($data['firmware_sn'])?$data['firmware_sn']:'',
      		'is_open'=>$data['is_open']>(-1)?$data['is_open']:'',
      		'model'=>!empty($data['model'])?$data['model']:'',
      		'hardware_sn'=>!empty($data['hardware_sn'])?$data['hardware_sn']:'',
      		'is_activate'=>!empty($data['is_activate'])?$data['is_activate']:0,
            'code'=>!empty($data['code'])?$data['code']:'',
      ]);
    }
    

    /**
     * 机台详情
     * @return
     */
    public function info(Request $request){
    	$this->system_log('查看机台详情','merchant');
    	
    	$id = $_GET['id'];
    	if(empty($id)){
    		   return view('admin.error', ['code' => 404, 'msg' => 'id不存在  内部错误']);
    	}
    	
    	$RjmachineModel = new RjmachineModel();
    	$res = $RjmachineModel->info($id);

        $Equipment = new EquipmentController();
        $GameNum = $Equipment->MachineGameNum(array('serial_no'=>$res['builder'][0]['code']));

//     echo '<pre>';
//     print_r($res);exit;	
    	return view(
	    			env('Merchant_view').'.machine.Machine-info',
	    			[
	    			    'info'=>$res['builder'][0],
                        'brand_name'=>$res['brand_name'],
                        'package'=>$res['package'],
                        'GameNum'=>$GameNum['GameNum'],
                        'GameNumMsg'=>$Equipment->error?$Equipment->error:$Equipment->success
                    ]
    			);
    
    }
     

    
    /**
     * 编辑
     * @return
     */
    public function save(Request $request){
    
			if($_POST){
				
	    		  //机台套餐管理
	            if(!empty($_POST['package_type'])){
		            if(!empty($_POST['package_id'])){
		            	if(isset($_POST['is_delete'])){
		            		//删除
		            		$operation =  DB::table(config('tables.base').'.rj_machine_package')->where('id',$_POST['package_id'])->Delete();
		            		if($operation===false){
		            			return $this->response(500,'套餐更新失败');
		            		}else{
		            			$this->system_log('删除机台套餐','merchant');
		            			return $this->response(200,'套餐更新成功');
		            		}
		            	}else{
		            		//更新
		            		$operation = DB::table(config('tables.base').'.rj_machine_package')->where('id',$_POST['package_id'])->update(['is_open'=>$_POST['is_open']]);
		            		if($operation===false){
		            			return $this->response(500,'套餐更新失败');
		            		}else{
		            			$this->system_log('更新机台套餐','merchant');
		            			return $this->response(200,'套餐更新成功');
		            		}
		            	}
		            }else{
			            	$package['machine_id'] = $_POST['m_id'];
			            	$package['package_type'] = $_POST['package_type'];
			            	$package['coin_price'] = $_POST['coin_price'];
			            	$package['coin_qty'] = isset($_POST['coin_qty'])?$_POST['coin_qty']:0;
			            	$package['num'] = isset($_POST['num'])?$_POST['num']:0;
			            	$package['is_open'] = 1;
			            $id = DB::table(config('tables.base').'.rj_machine_package')->insertGetId($package);
			            	if($id){
			            		$this->system_log('添加机台套餐','merchant');
			            		return $this->response(200,'套餐添加成功','',$id);
			            	}else{
			            		return $this->response(500,'套餐添加失败');
			            	}
		            }
	            }
	            
	            //机台信息修改
	            if(!empty($_POST['machine'])){
	            	$data[$_POST['data']['0']]=$_POST['data']['1'];
	            	if($_POST['data']['0']=='coin_qty'){
	            		$operation = DB::table(config('tables.base').'.rj_iot_product');
	            	}elseif($_POST['data']['0']=='name'){
	            		$operation = DB::table(config('tables.base').'.rj_iot_machine');
	            	}elseif($_POST['data']['0']=='is_disable'){
	            		$operation = DB::table(config('tables.base').'.rj_iot_dev');
	            	}elseif($_POST['data']['0']=='pay_type'){
	            		$operation = DB::table(config('tables.base').'.rj_iot_product');
	            		$data[$_POST['data']['0']]=$_POST['data']['1'];
	            	}else{
	            		return $this->response(500,'参数错误');
	            	}
	            	
	            	$res = $operation->where('id',$_POST['m_id'])->update($data);
	            	if($res===false){
	            		return $this->response(500,'更新失败');
	            	}else{
	            		$this->system_log('更新机台信息','merchant');
	            		return $this->response(200,'更新成功');
	            	}
	            }
	            
	            //添加或删除机台相册
	            if($_POST['img_id']){
	            	$get_gallery = DB::table(config('tables.base').'.rj_iot_product')->where('id',$_POST['p_id'])->value('gallery');
	            	if(empty($get_gallery)){
	            		$get_gallery = $_POST['img_id'];
	            	}else{
	            		if($_POST['start']=='add'){
	            			
	            			//多图处理
	            			$get_gallery = $get_gallery.','.$_POST['img_id'];
	            			
	            			//暂时单图处理，上面有处理多图，如需要多图，注释下面这段代码
	            			$get_gallery = $_POST['img_id'];
	            			
	            		}elseif($_POST['start']=='del'){
	            		
	            			$get_gallery_array = explode( ',',$get_gallery);
	            			$get_gallery='';
	            		
	            			foreach($get_gallery_array as $key=>$val){
	            				if($val==$_POST['img_id']){unset($get_gallery_array[$key]);}
	            			}
	            			$get_gallery = implode(',',$get_gallery_array);
	            		}else{
	            			return $this->response(500,'操作失败');
	            		}
	            	}
	            	
	            	
	            	$res =  DB::table(config('tables.base').'.rj_iot_product')->where('id',$_POST['p_id'])->update(['gallery'=>$get_gallery]);
	            	if($res===false){
	            		return $this->response(500,'操作失败');
	            	}else{
	            		$this->system_log('更新机台图片','merchant');
	            		return $this->response(200,'操作成功');
	            	}
	            }
	            
	        }
    	
    }


    /**
     * 解绑机台(解绑机台与门店的关系，并将机台设置为为激活状态)
     */
    public function rj_machine_un_bundling(Request $request){

        $id = $request->input('id');
        $machine_info = DB::table(config('tables.base').'.rj_iot_machine')->where('id',$id)->first();

        if($machine_info){
//开启事务
            DB::beginTransaction();
            $machine_info = json_decode(json_encode($machine_info),true);

            //清空门店，和机台型号
            $up_product = DB::table(config('tables.base').'.rj_iot_product')
                ->where('id',$machine_info['product_id'])
                ->update([
                    'store_id'=>'',
                    'model'=>''
                ]);

            //设置为未激活状态
            $up_dev = DB::table(config('tables.base').'.rj_iot_dev')
                ->where('id',$machine_info['dev_id'])
                ->update(['is_activate'=>1]);

            if($up_product===false){
                DB::rollBack();	//事务回滚
                return $this->response(500,'删除错误');
            }
            if($up_dev===false){
                DB::rollBack();	//事务回滚
                return $this->response(500,'删除错误');
            }
//提交事务
            DB::commit();
            return $this->response(200,'删除成功');
        }else{
            return $this->response(404,'未找到几台信息');
        }
    }

    /**
     * 删除机台(清空机台下的绑定记录)
     */
    public function machine_del(Request $request){
        $id = $request->input('id');
        $machine_info = DB::table(config('tables.base').'.rj_iot_machine')->where('id',$id)->first();

        if($machine_info){
            //开启事务
            DB::beginTransaction();
            $machine_info = json_decode(json_encode($machine_info),true);
            $del_product = DB::table(config('tables.base').'.rj_iot_product')
                ->where('id',$machine_info['product_id'])
                ->delete();

            $del_dev = DB::table(config('tables.base').'.rj_iot_dev')
                ->where('id',$machine_info['dev_id'])
                ->delete();

            $del_machine = DB::table(config('tables.base').'.rj_iot_machine')
                ->where('id',$machine_info['id'])
                ->delete();

            if($del_product===false){
                DB::rollBack();	//事务回滚
                return $this->response(500,'删除错误');
            }
            if($del_dev===false){
                DB::rollBack();	//事务回滚
                return $this->response(500,'删除错误');
            }
            if($del_machine===false){
                DB::rollBack();	//事务回滚
                return $this->response(500,'删除错误');
            }
            //提交事务
            DB::commit();
            return $this->response(200,'删除成功');
        }else{
            return $this->response(404,'未找到几台信息');
        }
    }



    /**
     * 机台监控
     */
    public function monitor(Request $request){
    //	echo '机台监控';exit;
    	if($_POST){

    	}else{
    		$this->system_log('查看机台监控','merchant');
    		
    		$data = $request->only('brand_name','stores_name','is_disable','m_name','firmware_sn','is_open','model','hardware_sn','is_activate','code');
    		/** type为machant **/
            $data['bus_user_id'] = session('id');
    		/** type为machant **/
    		   
    		$RjmachineModel = new RjmachineModel();
    		$device = $RjmachineModel->lists($data);
    		//      echo '<pre>';print_r($device);exit;
    		
    		return view(env('Merchant_view').'.machine.Machine-monitor',[
    				'ads'=>$device,
    				'brand_name'=>!empty($data['brand_name'])?$data['brand_name']:'',
    				'stores_name'=>!empty($data['stores_name'])?$data['stores_name']:'',
    				'is_disable'=>!empty($data['is_disable'])?$data['is_disable']:0,
    				'm_name'=>!empty($data['m_name'])?$data['m_name']:'',
    				'firmware_sn'=>!empty($data['firmware_sn'])?$data['firmware_sn']:'',
    				'is_open'=>$data['is_open']>(-1)?$data['is_open']:'',
    				'model'=>!empty($data['model'])?$data['model']:'',
    				'hardware_sn'=>!empty($data['hardware_sn'])?$data['hardware_sn']:'',
    				'is_activate'=>!empty($data['is_activate'])?$data['is_activate']:0,
                    'code'=>!empty($data['code'])?$data['code']:'',
    		]);
    	}
    }
    
    /**
     * 异常详情
     */
    public function monitor_info(){
    	$this->system_log('查看机台异常详情','merchant');
    	
    	$id = $_GET['id'];
    	if(empty($id)){
    		return view('admin.error', ['code' => 404, 'msg' => 'id不存在  内部错误']);
    	}

        $serial_no = DB::table(config('tables.base').'.rj_iot_machine as m')
                    ->leftjoin(config('tables.base').'.rj_iot_dev as d','d.id','=','m.dev_id')
                    ->where('m.id',$id)
                    ->value('serial_no');
//        $Equipment = new EquipmentController();
//        $Equipment->EliminateFaultCode(['serial_no'=>$serial_no]);


        $RjmachineModel = new RjmachineModel();
    	$res = $RjmachineModel->info($id);
    	//     echo '<pre>';
    	//     print_r($res);exit;
    	return view(
    			env('Merchant_view').'.machine.Machine-monitor-info',
    			['info'=>$res['builder'][0],'brand_name'=>$res['brand_name'],'log'=>$res['log']]
    	);
    }

    /**
     * 机台二维码
     * @return
     */
    public function machine_qrcode(Request $request){
        $DevInfo = DB::table(config('tables.base') . '.rj_iot_dev As dev')
            ->leftJoin(config('tables.base') . '.rj_iot_machine AS m', 'm.dev_id', ' =', 'dev.id')
            ->where('dev.serial_no', $_GET['code'])
            ->select(
                'dev.id as id',
                'dev.serial_no as serial_no',
                'm.id as m_id',
                'dev.game_player_num as game_player_num',
                'm.type as type'
            )
            ->first();
        $DevInfo = json_decode(json_encode($DevInfo), true);
        $zip = new \ZipArchive();
        $FileNameZip = '';
        if($DevInfo['type']==1){
            $game_player_num = 4;
            for ($i = 1; $i <= $game_player_num; $i++) {
                $G_param['serial_no'] = $_GET['code'];
                $G_param['game_player_n'] = 'N' . $i;
                $outFileName = $G_param['serial_no'].'('.$G_param['game_player_n'].').png';
                \QRcode::png($this->shortUrl($G_param),$outFileName);
                $FileNameZip = $G_param['serial_no'].'|QrCode.zip';
                $zip->open($FileNameZip,\ZipArchive::CREATE);   //打开压缩包
                $zip->addFile($outFileName,basename($outFileName));   //向压缩包中添加文件
                $zip->close();  //关闭压缩包
            }
            $fileinfo = pathinfo($FileNameZip);
            header('Content-type: application/x-'.$fileinfo['extension']);
            header('Content-Disposition: attachment; filename='.$fileinfo['basename']);
            header('Content-Length: '.filesize($FileNameZip));
            readfile($FileNameZip);
        }else{
            $game_player_num = $DevInfo['game_player_num'];
            for ($i = 1; $i <= $game_player_num; $i++) {
                $G_param['serial_no'] = $_GET['code'];
                $G_param['game_player_n'] = 'N' . $i;
                $outFileName = $G_param['serial_no'].'('.$G_param['game_player_n'].').png';
                \QRcode::png($this->shortUrl($G_param),$outFileName);
                $FileNameZip = $G_param['serial_no'].'|QrCode.zip';
                $zip->open($FileNameZip,\ZipArchive::CREATE);   //打开压缩包
                $zip->addFile($outFileName,basename($outFileName));   //向压缩包中添加文件
                $zip->close();  //关闭压缩包
            }
            $fileinfo = pathinfo($FileNameZip);
            header('Content-type: application/x-'.$fileinfo['extension']);
            header('Content-Disposition: attachment; filename='.$fileinfo['basename']);
            header('Content-Length: '.filesize($FileNameZip));
            readfile($FileNameZip);
        }

        exit;
    }


}

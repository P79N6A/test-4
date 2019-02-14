<?php

namespace App\Http\Controllers\Merchant\rj;
use App\Http\Controllers\Controller;
use EasyWeChat\Core\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Models\RjmachineModel;
use GuzzleHttp\json_decode;
use GuzzleHttp\json_encode;

class SystemController extends Controller
{

	public $error='';
	public $success='';
	
  	/**
  	 *@name 日志列表
     */
    public function rj_system_list(Request $request){
    
    	$list_sql = DB::table(config('tables.base').'.rj_system_log');
    	
    	if($request->has('action')){
    		$list_sql->where('action',$request->input('action'));
    	}
    	if($request->has('execution_name')){
    		$list_sql->where('execution_name','like','%'.$request->input('execution_name').'%');
    	}
    	if($request->has('execution_time')){
    		$list_sql->where('execution_time','like','%'.$request->input('execution_time').'%');
    	}
    	
    	//商户只允许获取商户下面的日志，
    	if(session('pid')>0){
    		//子商户
    		$bus_list = DB::table(config('tables.youyibao').'.bus_users')->where('pid',session('pid'))->lists('id');
    		$bus_list[] = session('pid');
    	}else{
    		//总商户
    		$bus_list = DB::table(config('tables.youyibao').'.bus_users')->where('pid',session('id'))->lists('id');
    		$bus_list[] = session('id');
    	}
    	$list_sql->whereIn('user_id',$bus_list);

    	$list = $list_sql->where('ac_type','merchant')->orderby('id','desc')->paginate(20);
    	
    //	$this->_sql();
    	$action = $request->input('action');
    	$execution_name = $request->input('execution_name');
    	$execution_time = $request->input('execution_time');
    	return view(env('Merchant_view').'.log.system_list',[
    			'ads'=>$list,
    			'action'=>!empty($action)?$action:'',
    			'execution_name'=>!empty($execution_name)?$execution_name:'',
    			'execution_time'=>!empty($execution_time)?$execution_time:'',
    	]);
    }
    
    //删除日志
    public function rj_system_del(Request $request){
    	
  //  	$this->system_log('删除日志','admin');
    	if($_POST){
	    	$res = DB::table(config('tables.base').'.rj_system_log')->where('id',$_POST['id'])->delete();
	    	
	    	if($res=='') return $this->response(403, '删除失败');
	    	return $this->response(200, '删除成功');
	    }else{
	    	return $this->response(500, '请求内部错误');
	    }
    }
    
 
    
    
  
}

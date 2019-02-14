<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\BusOperationLog;
use Operation;

class LogController extends Controller{

	private $action = [ 1 => '登入成功' , 2=> '登录失败' , 3=>'登出', 4=>'添加',5=>'更新',6=>'删除'];

	public function show(Request $request){
		// Operation::insert('test','这是一条添加测试1','after');
		// echo "string";
		// Operation::delete('test','这是一条删除测试2','before');
		// echo json_encode(null);
		// $list = BusOperationLog::get();
		// dd($list);
		
		/* 获取提交数据 */
		$data = $request->only([
            'start_date', 'end_date', 'range', 'store', 'mobile', 'order_no', 'pay_no',
            'package_name', 'payment_type', 'price_start', 'price_end', 'status', 'convert_type'
        ]);

        /* 单页条数 */
        $limit = 25;

        $builder = BusOperationLog::orderBy('log_id','DESC');

        //判断是否父帐号，父帐号拥有查看全部
        // if(session('pid') == 0){
        // 	$child_ids = DB::table('bus_users')->where('pid',$this->parentUserId)->lists('id');
        // }

        // $child_ids[] = session('id');

        // $builder->whereIn('userid',$child_ids);

        /* 获取帐号下可以查看的操作日志的用户名 */
        // if(session('pid') == 0){
       		$allow_users = DB::table('bus_users')->select(['id','name'])->orderBy('id','DESC')->get();
        // }

        /* 筛选用户 */
        $filter_user_id = $request->get('uid');
        if($request->has('uid') && intval($filter_user_id) ){
        	$builder->where('userid',$filter_user_id);
        }

        /* 时间段筛选 */
        if ($request->has('start_date') && !$request->has('end_date')) {      // 只有开始时间
            $start_date = strtotime($request->get('start_date'));

            $builder->where('create_at' ,'>=' ,$request->get('start_date'));

        } elseif (!$request->has('start_date') && $request->has('end_date')) { // 只有结束时间
            $end_date = strtotime($request->get('end_date'));

            $builder->where('create_at' ,'<=' ,$request->get('end_date'));

        } elseif ($request->has('start_date') && $request->has('end_date')) {  // 有开始时间和结束时间
            $start_date = strtotime($request->get('start_date'));
            $end_date = strtotime($request->get('end_date'));

            $builder->whereBetween('create_at',[$request->get('start_date'),$request->get('end_date')]);
        }

        if (!empty($start_date) && !empty($end_date)) {
            if ($start_date == strtotime(date('Y-m-d')) && $end_date == (strtotime(date('Y-m-d')) + 86399)) {
                $range = 1;
            } elseif ($start_date == strtotime(date('Y-m-d', strtotime('-1 day'))) && $end_date == (strtotime(date('Y-m-d', strtotime('-1 day'))) + 86399)) {
                $range = 2;
            } elseif ($start_date == strtotime(date('Y-m-d', strtotime('-7 day'))) && $end_date == strtotime(date('Y-m-d 23:59:59'))) {
                $range = 3;
            } elseif ($start_date == strtotime(date('Y-m-d', strtotime('-30 day'))) && $end_date == strtotime(date('Y-m-d 23:59:59'))) {
                $range = 4;
            }
        }

        $logs = $builder->paginate($limit);

        return view('admin.log-show', [
        	'logs' => $logs,
            'start_date' => !empty($start_date) ? $request->get('start_date') : '',
            'end_date' => !empty($end_date) ? $request->get('end_date') : '',
            'range' => isset($range) ? $range : 0,
            'allow_users' => $allow_users,
            'filter_user_id' => $filter_user_id,
        ]);
	}

	public function detail(Request $request){
		$log_id = $request->get('id');

		if(!intval($log_id)){
			return $this->response(500, '内部错误');
		}

		$log = BusOperationLog::find($log_id);

		if(!$log){
			return $this->response(500, '内部错误');
		}

		if(!empty($log->before)){
			$log->before = json_decode($log->before,true);
		}

		if(!empty($log->after)){
			$log->after = json_decode($log->after,true);
		}

		if(in_array($log->action, [1,2,3])){

			return view('admin.log-detail-1',[
				'info' => $log,
				'action'=>$this->action
			]);
		}elseif (in_array($log->action, [4,5,6])) {
			
			return view('admin.log-detail-1',[
				'info' => $log,
				'action'=>$this->action
			]);
		}

	}
}
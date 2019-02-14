<?php

namespace App\Http\Controllers\Merchant\rj;
use App\Http\Controllers\Controller;
use EasyWeChat\Core\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\json_decode;
use GuzzleHttp\json_encode;
use App\Http\Models\RjorderModel;

class ReportController extends Controller
{

   /**
    * 门店营收列表
    */
   public function store_report(Request $request){
       $data = $request->only('stores_name','stores_address','starttime','endtime');
       $data['arr_status'] = array(1,2);
       /** type为machant **/
       $data['bus_user_id'] = session('id');
       /** type为machant **/

       $RjorderModel = new RjorderModel();
       $order_list=$RjorderModel->lists($data,'store_id');

//       $order_list = $order_list->toArray();
//  //     $list = json_decode(json_encode($order_list['data']),true);
//       echo '<pre>';
//       print_r($order_list);exit;


	   	return view(env('Merchant_view').'.report.store_report',[  'ads'=>$order_list,
            'stores_name'=>empty($data['stores_name'])?'':$data['stores_name'],
            'stores_address'=>empty($data['stores_address'])?'':$data['stores_address'],
            'starttime'=>empty($data['starttime'])?'':$data['starttime'],
            'endtime'=>empty($data['endtime'])?'':$data['endtime']
            ]);
   }
   
   /**
    * 门店营收详情
    */
   public function store_report_detail(Request $request){
       $data = $request->only('store_id','starttime','endtime');

       $RjorderModel = new RjorderModel();
       $lists = $RjorderModel->storeslists($data);

   	return view(env('Merchant_view').'.report.store_report_detail',[
   	    'ads'=>$lists,
        'starttime'=>empty($data['starttime'])?'':$data['starttime'],
        'endtime'=>empty($data['endtime'])?'':$data['endtime'],
        'store_id'=>empty($data['store_id'])?0:$data['store_id']
        ]);
   }
   
   
   
   /**
    * 机台营收列表
    */
   public function machint_report(Request $request){
       $data = $request->only('machine_id','m_name','serial_no','m_type','bus_name','stores_name','m_starttime','m_endtime');
       $data['arr_status'] = array(1,2);
       /** type为machant **/
       $data['bus_user_id'] = session('id');
       /** type为machant **/

       $RjorderModel = new RjorderModel();
       $order_list=$RjorderModel->lists($data,'machine_id');
 //     echo '<pre>';
//       print_r($order_list);

       $list = $order_list->toArray();
       $lists = json_decode(json_encode($list['data']),true);

 //      print_r($lists);

       $machine_lists = array_column($lists,'machine_id');
//       print_r($machine_lists);
    //       $ticket = DB::table(config('tables.base').'.rj_iot_machine_ticket')
    //                   ->whereIn('machine_id',$machine_lists)
    //                   ->groupBy('machine_id')
    //                   ->select(
    //                       'rj_iot_machine_ticket.*',
    //                       DB::raw( 'SUM(ticket) as ticket')
    //                   )
    //                   ->get();
    //       $ticket = json_decode(json_encode($ticket),true);
//       print_r($ticket);
//       exit;
       if(!empty($data['m_starttime'])){
           $m_starttime = $data['m_starttime'];
           $m_endtime = empty($data['m_endtime'])?strtotime('Y-m-d H:i:s',time()):$data['m_endtime'];
       }
       foreach($machine_lists as $key=>$val){
           if(!empty($data['m_starttime'])){
               $ticket = DB::table(config('tables.base').'.rj_iot_machine_ticket')
                   ->where('machine_id',$val['machine_id'])
                   ->whereBetween('create_date',array($m_starttime,$m_endtime))
                   ->SUM('ticket');
           }else{
               $ticket = DB::table(config('tables.base').'.rj_iot_machine_ticket')
                   ->where('machine_id',$val['machine_id'])
                   ->SUM('ticket');
           }
           $lists[$key]['ticket'] = $ticket;
       }
       $lists = json_decode(json_encode($lists));

//       echo '<pre>';
//       print_r($lists);
//       exit;

   		return view(env('Merchant_view').'.report.machint_report',[
   		    'ads'=>$order_list,
            'order_lists'=>$lists,
            'machine_id'=>empty($data['machine_id'])?'':$data['machine_id'],
            'm_name'=>empty($data['m_name'])?'':$data['m_name'],
            'serial_no'=>empty($data['serial_no'])?'':$data['serial_no'],
            'm_type'=>empty($data['m_type'])?'':$data['m_type'],
            'bus_name'=>empty($data['bus_name'])?'':$data['bus_name'],
            'stores_name'=>empty($data['stores_name'])?'':$data['stores_name'],
            'm_starttime'=>empty($data['m_starttime'])?'':$data['m_starttime'],
            'm_endtime'=>empty($data['m_endtime'])?'':$data['m_endtime']
        ]);
   }
   
   /**
    * 机台营收详情
    */
   public function machint_report_detail(Request $request){

       $data = $request->only('machine_id','starttime','endtime','payment_type','user_name','m_starttime','m_endtime');

       $RjorderModel = new RjorderModel();
       $lists = $RjorderModel->machinelists($data);

//            echo '<pre>';
//            print_r($lists);
//            $this->_sql();
//            exit;

        return view(env('Merchant_view').'.report.machint_report_detail',[
            'ads'=>$lists,
            'starttime'=>empty($data['starttime'])?'':$data['starttime'],
            'endtime'=>empty($data['endtime'])?'':$data['endtime'],
            'payment_type'=>empty($data['payment_type'])?'':$data['payment_type'],
            'user_name'=>empty($data['user_name'])?'':$data['user_name'],
            'machine_id'=>empty($data['machine_id'])?'':$data['machine_id'],
            'm_starttime'=>empty($data['m_starttime'])?'':$data['m_starttime'],
            'm_endtime'=>empty($data['m_endtime'])?'':$data['m_endtime']
        ]);
   }
    
   
  
}

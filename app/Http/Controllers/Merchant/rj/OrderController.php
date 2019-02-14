<?php

namespace App\Http\Controllers\Merchant\rj;
use App\Http\Controllers\Controller;
use EasyWeChat\Core\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Models\RjorderModel;
use GuzzleHttp\json_decode;
use GuzzleHttp\json_encode;
use App\Libraries\ExcelProcessor;


class OrderController extends Controller
{

	public function index(Request $request){

        //更新订单状态
        $time = time()-60*20;
        DB::table(config('tables.base').'.rj_iot_order')
            ->where('addtime','<',$time)
            ->where('payment_type','=','2')
            ->where('status',1)
            ->update(['status'=>3,'pay_coins'=>0,'pay_price'=>0]);

        $data = $request->only('refno','m_name','serial_no','stores_name','store_id','status','payment_type','pay_price','machine_status');

        $data['bus_user_id'] = session('id');
        $data['request_model'] = 'Order';

        //echo session('id');exit;
        $RjorderModel = new RjorderModel();
        $order_list=$RjorderModel->lists($data);

        //$this->_sql();
        return view(
            env('Merchant_view').'.order.index',[
                'ads'=>$order_list,
                'refno'=>!empty($data['refno'])?$data['refno']:'',
                'm_name'=>!empty($data['m_name'])?$data['m_name']:'',
                'serial_no'=>!empty($data['serial_no'])?$data['serial_no']:'',
                'stores_name'=>!empty($data['stores_name'])?$data['stores_name']:'',
                'store_id'=>!empty($data['store_id'])?$data['store_id']:'',
                'pay_price'=>!empty($data['pay_price'])?$data['pay_price']:'',
                'status'=>!empty($data['status'])?$data['status']:-1,
                'payment_type'=>$data['payment_type']>(-1)?$data['payment_type']:0,
                'machine_status'=>!empty($data['machine_status'])?$data['machine_status']:0
                ]
        );
		
	}

	//详情
    public function detail(Request $request){
        $this->system_log('查看订单详情 ','admin');

        $id = $_GET['id'];
        if(empty($id)){
            return view('admin.error', ['code' => 404, 'msg' => 'id不存在  内部错误']);
        }

        $RjorderModel = new RjorderModel();
        $info=$RjorderModel->info($id);
//        echo '<pre>';
//print_r($info);exit;
        return view(
            env('Merchant_view').'.order.order-info',
            ['info'=>$info]
        );

    }

	//删除
    public function del(Request $request){

        $id = $request->input('id');
        $machine_info = DB::table(config('tables.base').'.rj_iot_order')->where('id',$id)->first();

        if($machine_info){

            DB::beginTransaction();
            $order = DB::table(config('tables.base').'.rj_iot_order')->where('id',$id)->delete();
            if($order===false){
                DB::rollBack();	//事务回滚
                return $this->response(500,'删除错误');
            }
            DB::commit();

            return $this->response(200,'删除成功');
        }else{
            return $this->response(404,'未找到订单信息');
        }
    }
    //导出
    public function export(Request $request){

        $data = $request->only('refno','m_name','serial_no','stores_name','store_id','status','payment_type','pay_price','machine_status');
        $RjorderModel = new RjorderModel();
        $list=$RjorderModel->lists($data);
        $lists = $list->toArray();

        $objPHPExcel = new \PHPExcel();

        //设置表头
        $objPHPExcel->getActiveSheet(0)
            ->setCellValue('A1', '编号')
            ->setCellValue('B1', '订单号')
            ->setCellValue('C1', '用户编号')
            ->setCellValue('D1', '门店ID')
            ->setCellValue('E1', '门店名称')
            ->setCellValue('F1', '机台ID')
            ->setCellValue('G1', '机台名称')
            ->setCellValue('H1', '硬件编号')
            ->setCellValue('I1', '支付类型')
            ->setCellValue('J1', '订单状态')
            ->setCellValue('K1', '是否异常')
            ->setCellValue('L1', '下单时间')
            ->setCellValue('M1', '支付币数')
            ->setCellValue('N1', '支付时间')
            ->setCellValue('O1', '备注');
        //循环遍历数据到表格
        $data =  json_decode(json_encode($lists['data']),true);
        //    print_r($data);exit;
        foreach($data as $k=>$v){
            if($v['status']===0){ $status = '待付款';
            }elseif($v['status']==1){  $status = '游戏中';
            }elseif($v['status']==2){   $status = '已使用';
            }elseif($v['status']==3){   $status = '已过期';
            }else{  $status='';  }
            if( $v['payment_type'] == 1){$payment_type = '游币支付';}else{$payment_type = '线下投币';}
            if( $v['machine_status'] == 1){$machine_status = '正常';}elseif( $v['machine_status'] == 2){$machine_status = '异常';}else{$machine_status='';}

            $objPHPExcel->getActiveSheet(0)
                ->setCellValue('A'.($k + 2), $v['id'])
                ->setCellValue('B'.($k + 2), $v['refno'])
                ->setCellValue('C'.($k + 2), $v['userid'])
                ->setCellValue('D'.($k + 2), $v['store_id'])
                ->setCellValue('E'.($k + 2), $v['stores_name'])
                ->setCellValue('F'.($k + 2), $v['machine_id'])
                ->setCellValue('G'.($k + 2), $v['m_name'])
                ->setCellValue('H'.($k + 2), $v['serial_no'])
                ->setCellValue('I'.($k + 2), $payment_type)
                ->setCellValue('J'.($k + 2), $status)
                ->setCellValue('K'.($k + 2), $machine_status)
                ->setCellValue('L'.($k + 2),$v['addtime'])
                ->setCellValue('M'.($k + 2), $v['pay_price'])
                ->setCellValue('N'.($k + 2), date('Y-m-d H:i:s',$v['pay_date']))
                ->setCellValue('O'.($k + 2), $v['remark']);
        }

        ob_end_clean();//清除缓冲区,避免乱码

        // 设置工作薄名称
        $objPHPExcel->getActiveSheet(0)->setTitle('订单列表');
        $objPHPExcel->getActiveSheet()->getStyle('A1:O1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A1:O1')->getFont()->setSize(12);

        $fileName = date('YmdHi');//or $xlsTitle 文件名称可根据自己情况设定
        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$fileName.'.xls"');
        header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

    public function  exportTow(Request $request){
        $data = $request->only('refno','m_name','serial_no','stores_name','store_id','status','payment_type','pay_price','machine_status','start_time','end_time');
        $data['request_model'] = 'exportTow';
        $RjorderModel = new RjorderModel();
        $list = $RjorderModel->lists($data,'order.id');

        //print_r($list);exit;
        //$lists = $list->toArray();
        //$data =  json_decode(json_encode($lists['data']),true);
        $lists = $list;
        $data =  json_decode(json_encode($lists),true);

        $arr = [];
        foreach($data as $k=>$v){
            $arr[$k]['refno'] = $v['refno'];
            if(!empty($v['stores_name'])){
                $arr[$k]['stores_name'] = $v['stores_name'];
            }else{
                $arr[$k]['stores_name'] = "";
            }
            if(!empty($v['m_name'])){
                $arr[$k]['m_name'] = $v['m_name'];
            }else{
                $arr[$k]['m_name'] = "";
            }
            $arr[$k]['serial_no'] = $v['serial_no'];

            $status = '';
            if($v['status'] === 0){ $status = '待付款';
            }elseif($v['status']==1){  $status = '游戏中';
            }elseif($v['status']==2){   $status = '已使用';
            }elseif($v['status']==3){   $status = '已过期';
            }
            $arr[$k]['status']  = $status;

            if( $v['payment_type'] == 1){
                $arr[$k]['payment_type'] = '游币支付';
            }else{
                $arr[$k]['payment_type'] = '线下投币';
            }

            if(!empty($v['mobile'])){
                $arr[$k]['mobile'] = $v['mobile'];
            }else{
                $arr[$k]['mobile'] = '';
            }

            $arr[$k]['pay_coins'] = $v['pay_coins'];
            $arr[$k]['addtime'] = $v['addtime'];
            if( $v['orgin'] == 2){
                $arr[$k]['orgin'] = '微信';
            }elseif($v['orgin'] == 1){
                $arr[$k]['orgin'] = 'APP';
            }else{
                $arr[$k]['orgin'] = '未知';
            }

            if( $v['machine_status'] == 1){
                $arr[$k]['machine_status'] = '正常';
            }elseif( $v['machine_status'] == 2){
                $arr[$k]['machine_status'] = '异常';
            }else{
                $arr[$k]['machine_status']='';
            }
        }
        ob_end_clean();//清除缓冲区,避免乱码
        $handler = new ExcelProcessor();
        $header = [
            '订单号',
            '门店名称',
            '机台名称',
            '硬件编号',
            '交易状态',
            '支付方式',
            '用户手机号',
            '支付币数',
            '下单时间',
            '订单来源',
            '是否异常'
        ];
        $filename = date('Y-m-d H：i：s') . ' - 订单导出.xlsx';
        $handler->setHeader($header)->setData($arr)->download($filename);
    }


}

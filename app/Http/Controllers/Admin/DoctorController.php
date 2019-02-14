<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Admin\MemberModel;
use App\Models\DoctorMoneyRecordModel;
use App\Services\DoctorService;

/**
 * 医生管理
 */
class DoctorController extends Controller
{
    /**
     * 医生列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        if ($request->has('keyword')) {
            $keyword = $request->get('keyword');
            $members = MemberModel::where('status', 1)
                        ->where('role', 2)
                        ->where(function($query) use ($keyword){
                            $query->where('nickname', 'like', '%' . $keyword . '%')
                            ->orWhere('mobile', 'like', '%' . $keyword . '%');
                        })
                        ->orderBy('id','DESC')
                        ->paginate(20);
            $member = $members->toArray();
            $member = json_decode(json_encode($member['data']),true);
            foreach ($member as $key => $val) {
                $total = DoctorMoneyRecordModel::totalDoctor($val['id']);
                $total = json_decode(json_encode($total),true);
                $members[$key]['order_amount'] = $total['order_amount'];
                $members[$key]['record_amount'] = $total['record_amount'];
                $members[$key]['money'] = $total['money'];
               //    $members[$key]['order_amount'] = $total['order_amount'];
            }
        } else {
            $members = MemberModel::where('status', 1)->where('role', 2)->orderBy('id','DESC')->paginate(20);
            $member = $members->toArray();
            $member = json_decode(json_encode($member['data']),true);
            foreach ($member as $key => $val) {
                $total = DoctorMoneyRecordModel::totalDoctor($val['id']);
                $total = json_decode(json_encode($total),true);
                $members[$key]['order_amount'] = $total['order_amount'];
                $members[$key]['record_amount'] = $total['record_amount'];
                $members[$key]['money'] = $total['money'];
               //    $members[$key]['order_amount'] = $total['order_amount'];
            }
        }

        foreach($members as $key => $member){
            $members[$key]['inviter'] = MemberModel::where('id', $member['invite_parent_id'])->value('nickname');
            $members[$key]['invite_num'] = MemberModel::where('invite_parent_id', $member['id'])->count('id');
        }

        return view('admin.doctor.list', [
            'members' => $members,
            'keyword' => isset($keyword) ? $keyword : '',
        ]);
    }

    /**
     * 修改医生信息
     */
    public function edit(Request $request)
    {
         $id = intval($request->input('id'));
         if(empty($id)) return $this->response(500,'ID非法！');
         $member = MemberModel::find($id);
        if($request->isMethod('POST')){
            $num=MemberModel::where('id','=',$id)->update(['realname'=>$request->input('realname'),'nickname'=>$request->input('nickname')]);

            if($num>0){
                return $this->response(200,'修改成功！',route('admin.doctor-list'));
            }else{
                return $this->response(422,'处理失败，请重试！');
            }
        }

        return view('admin.doctor.edit',[
            'info'=>$member
        ]);
    }

    /**
     * 软删除医生信息
     */
    public function delete(Request $request)
    {
        $memberId = $request->get('id');
        if (!intval($memberId)) {
            return $this->response('500', '内部错误');
        }

        $member = MemberModel::where('role', 2)->where('id', $memberId)->first();
        if (!$member) {
            return $this->response(404, '该医生不存在');
        }

        if (MemberModel::where('id', $memberId)->update(['status' => '-1', 'delete_at' => date('Y-m-d H:i:s')])) {
            return $this->response(200, '用户删除成功', route('admin.doctor-list'));
        } else {
            return $this->response(500, '用户删除失败');
        }
    }

    /**
     * 设置医生推广课程佣金
     */
    public function setCommissionRate(Request $request)
    {
        $memberId = $request->get('id');
        if (!intval($memberId)) {
            return $this->response('500', '内部错误');
        }

        $member = MemberModel::where('role', 2)->where('id', $memberId)->first();
        if (!$member) {
            return $this->response(404, '该医生不存在');
        }

        $rate = $request->get('rate');

        if(!is_numeric($rate) || $rate > 100 || $rate < 0){
            return $this->response('500', '请输入正确范围内的佣金比例');
        }

        if (MemberModel::where('id', $memberId)->update(['commission_rate' => bcdiv($rate, 100, 4)])) {
            return $this->response(200, '设置佣金比例成功', route('admin.doctor-list'));
        } else {
            return $this->response(500, '设置佣金比例失败');
        }
    }

    /*
    *奖金详细列表
    */
    public function recordList(Request $request){
        $id = $request->get('id');
        if(empty($id)){
            $result = DoctorMoneyRecordModel::leftjoin('users as u','u.id','=','doc_money_record.user_id')
                 ->where('doc_money_record.type',1)->where('doc_money_record.status',1)
                 ->select('u.id','u.openid','u.nickname','u.img','u.mobile','doc_money_record.created_at','doc_money_record.updated_at',
                    DB::raw('convert(doc_money_record.money/100 ,decimal(10,2)) as money'),
                    DB::raw('convert(doc_money_record.order_amount/100 ,decimal(10,2)) as order_amount'),
                    DB::raw( 'convert(u.commission_rate*100 ,decimal(10,2))as commission_rate')
                )
                 ->paginate(10);
        return view('admin.doctor.record-list', [
            'list' => $result,
        ]);
        }
        $result = DoctorMoneyRecordModel::leftjoin('users as u','u.id','=','doc_money_record.user_id')
                 ->where('doc_money_record.user_id',$id)->where('doc_money_record.type',1)->where('doc_money_record.status',1)
                 ->select('u.id','u.openid','u.nickname','u.img','u.mobile','doc_money_record.created_at','doc_money_record.updated_at',
                    DB::raw('convert(doc_money_record.money/100 ,decimal(10,2)) as money'),
                    DB::raw('convert(doc_money_record.order_amount/100 ,decimal(10,2)) as order_amount'),
                    DB::raw( 'convert(u.commission_rate*100 ,decimal(10,2))as commission_rate')
                )
                 ->paginate(10);
        return view('admin.doctor.record-list', [
            'list' => $result,
        ]);
        
    }

}
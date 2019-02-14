<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Admin\MemberModel;
use App\Http\Models\Admin\DoctorMoneyRecordModel as MoneyModel;

/**
 * 医生资金管理
 */
class DoctorMoneyRecordController extends Controller
{
    /**
     * 提现列表
     *
     * @param status 审核状态， 1 审核通过 -1 审核不通过 0 待审核
     *
     * @return json
     */
    public function index(Request $request)
    {
        $status = $request->input('status', '');
        $usersId = $request->get('usersId');
        $moneys = MoneyModel::where('type', 3)->where('status', $request->input('status', ''));
        if (!empty($usersId)) {
            $moneys->where('user_id', $usersId);
        }
        $moneys = $moneys->paginate(20);
        foreach ($moneys as $key => $money) {
            $memberInfo = MemberModel::where('id', $money['user_id'])->select('nickname', 'mobile')->first();
            $moneys[$key]['nickname'] = $memberInfo['nickname'];
            $moneys[$key]['mobile'] = $memberInfo['mobile'];
        }
        return view('admin.doctor.money-record', [
            'moneys' => $moneys,
            'status' => $status,
            'usersId'=>$usersId
        ]);
    }

    /**
     * 审核操作
     *
     * @param id int 提现记录id
     * @param desc string 审核备注
     * @param status 审核状态， 1 审核通过 -1 审核不通过
     *
     * @return json
     */
    public function operate(Request $request)
    {
        $usersId = $request->get('usersId');

        $id = $request->get('id');
        if (!intval($id)) {
            return $this->response('500', '内部错误');
        }

        $record = MoneyModel::where('type', 3)->where('id', $id)->where('status', 0)->first();
        if (!$record) {
            return $this->response(404, '该提现信息不存在');
        }

        $desc = $request->get('desc', '');
        $status = $request->get('status', '');

        if (empty($status) || !in_array($status, [-1,1])) {
            return $this->response(500, '参数错误');
        }

        // status = 1 时需判断该用户是否有足够的余额提现，剩余可提现 = 佣金增加 - 佣金扣减 - 已提现
        if ($status == 1) {
            $moneyAdd = MoneyModel::where('type', 1)->where('status', 1)->sum('money') ?? 0;
            // $moneyDeduct = MoneyModel::where('type', 2)->where('status', 1)->sum('money') ?? 0;
            $moneyWithdraw = MoneyModel::where('type', 3)->whereIn('status', [0, 1])->sum('money') ?? 0;

            // if($record['money'] > ($moneyAdd - $moneyDeduct - $moneyWithdraw)){
            if ($record['money'] > ($moneyAdd - $moneyWithdraw)) {
                return $this->response(500, '该医生没有足够可提现余额');
            }
        }

        $data = [
            'status' => $status,
            'desc' => $desc,
            'operated_at' => time(),
            'operator' => session('username')
        ];

        if (MoneyModel::where('id', $id)->update($data)) {
            if (!empty($usersId)) {
                return $this->response(200, '操作成功', route('admin.doctor-money-list', ['usersId'=>$usersId]));
            }
            return $this->response(200, '操作成功', route('admin.doctor-money-list'));
        } else {
            return $this->response(500, '操作失败');
        }
    }
}

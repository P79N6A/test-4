<?php
namespace App\Http\Controllers\Api;

use App\Helper;
use App\Models\BankCardModel;
use App\Models\DoctorMoneyRecordModel as MoneyModel;
use Illuminate\Http\Request;
use Zhuzhichao\BankCardInfo\BankCard;

/**
 * 资金管理
 */
class DoctorMoneyRecordController extends Controller
{
    /**
     * 提交提现审核
     */
    public function withdraw(Request $request)
    {
        $withdrawMoney = $request->input('money'); // 单位：分
        $bankCardNum = $request->input('bankCardNum');

        if (empty($withdrawMoney) || $withdrawMoney <= 0 || !is_numeric($withdrawMoney)) {
            return $this->error('请输入正确的提现金额');
        }

        if (empty($bankCardNum)) {
            return $this->error('请输入银行卡号');
        }

        // 检查银行卡号是否正确
        $bankCardInfo = BankCard::info($bankCardNum);
        if (!$bankCardInfo['validated']) {
            return $this->error('银行卡信息有误，请再次检查');
        }

        if (empty(BankCardModel::where('card_num', $bankCardNum)->where('user_id', $this->auth->user_id)->where('status', 1)->first())) {
            return $this->error('该银行卡还没有绑定');
        }

        // 判断是否有足够的余额提现
        if ($withdrawMoney > $this->get_remain_withdraw_money($this->auth->user_id)) {
            return $this->error('可提取余额不足');
        }

        $withdraw = new MoneyModel();

        $withdraw->record_id = Helper::makeOrderNum();
        $withdraw->user_id = $this->auth->user_id;
        $withdraw->type = 3;
        $withdraw->money = $withdrawMoney;
        $withdraw->card_num = $bankCardNum;

        if ($withdraw->save()) {
            return $this->success([], 0, '提交成功，请耐心等待审核');
        } else {
            return $this->error('提交失败，请稍候再试');
        }
    }

    /**
     * 剩余可提现金额
     */
    public function remainWithdraw()
    {
        return $this->success([
            'remain' => $this->get_remain_withdraw_money($this->auth->user_id),
            'default_bank_card_info' => BankCardModel::where('user_id', $this->auth->user_id)->where('status', 1)->first(),
        ]);
    }

    /**
     * 获取剩余可提现金额
     *
     * @param int $userId
     * @return int
     */
    private function get_remain_withdraw_money($userId)
    {
        // 累积增加的佣金总额
        $moneyAdd = MoneyModel::where('type', 1)->where('status', 1)->where('user_id', $userId)->sum('money') ?? 0;
        // 累积扣减的佣金总额
        // $moneyDeduct = MoneyModel::where('type', 2)->where('status', 1)->where('user_id', $userId)->sum('money') ?? 0;
        // 累积待审核提现和已审核提现的总额
        $moneyWithdraw = MoneyModel::where('type', 3)->whereIn('status', [0, 1])->where('user_id', $userId)->sum('money') ?? 0;

        // return ($moneyAdd - $moneyDeduct - $moneyWithdraw) ?? 0;
        return ($moneyAdd - $moneyWithdraw) ?? 0;
    }
}
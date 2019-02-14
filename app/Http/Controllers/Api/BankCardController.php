<?php
namespace App\Http\Controllers\Api;

use App\Models\BankCardModel;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Zhuzhichao\BankCardInfo\BankCard;

/**
 * 银行卡绑定
 */
class BankCardController extends Controller
{
    /**
     * 根据卡号获取银行卡信息
     */
    public function getBankCardInfo(Request $request)
    {
        $bankCardNum = $request->input('bankCardNum');

        if (empty($bankCardNum)) {
            return $this->error('请输入银行卡号');
        }

        $bankCardInfo = BankCard::info($bankCardNum);

        if (($response = $this->validateBankCardInfo($bankCardInfo, $bankCardNum)) !== true) {
            return $response;
        }

        return $this->success($bankCardInfo);
    }

    /**
     * 绑定银行卡
     *
     * @param string $card_username 持卡人名称
     * @param string $card_mobile 预留手机号
     * @param string $card_num 银行卡号
     * @param string $code 短信验证码
     *
     * @return json
     */
    public function bind(Request $request)
    {
        try {
            $this->validate(
                $request,
                [
                    'card_username' => 'required',
                    'card_mobile' => 'required|mobile',
                    'card_num' => 'required',
                    'code' => 'required',
                ],
                [],
                [
                    'card_username' => '持卡人姓名',
                    'card_mobile' => '银行预留手机号',
                    'card_num' => '银行卡号',
                    'code' => '短信验证码',
                ]
            );
        } catch (\Exception $e) {
            return $this->error($e->validator->errors()->first());
        }

        // 检查验证码是否正确
        $code = $request->input('code');
        $mobile = $request->input('card_mobile');

        if (empty($code)) {
            return $this->error('验证码非法！');
        }

        if (!SmsService::checkCode($code, $mobile)) {
            return $this->error('验证码不正确！');
        }

        // 再次检查银行卡号是否正确
        $bankCardInfo = BankCard::info($request->input('card_num'));

        if (($response = $this->validateBankCardInfo($bankCardInfo, $request->input('card_num'))) !== true) {
            return $response;
        }

        // 保存信息
        $cardData = new BankCardModel();
        $cardData->card_username = $request->input('card_username');
        $cardData->card_mobile = $mobile;
        $cardData->card_num = $request->input('card_num');

        $cardData->user_id = $this->auth->user_id;
        // $cardData->user_id = 1;

        $cardData->card_type = $bankCardInfo['cardType'];
        $cardData->card_type_name = $bankCardInfo['cardTypeName'];
        $cardData->bank_code = $bankCardInfo['bank'];
        $cardData->bank_name = $bankCardInfo['bankName'];
        $cardData->bank_img = $bankCardInfo['bankImg'];
        $cardData->status = 1; // 默认启用

        if ($cardData->save()) {
            return $this->success([], 0, '绑定成功');
        } else {
            return $this->error('绑定失败！');
        }
    }

    /**
     * 我的银行卡列表
     */
    public function myBankCardList()
    {
        $result = BankCardModel::where('user_id', $this->auth->user_id)->get()->toArray();
        foreach ($result as $key => $val) {
            $before = substr($val['card_num'], 0, 4);
            $after = substr($val['card_num'], -4);
            $result[$key]['card_num'] = $before . ' **** **** *** ' . $after;
        }

        return $this->success($result);
    }

    private function validateBankCardInfo($bankCardInfo, $bankCardNum)
    {
        if (!$bankCardInfo['validated']) {
            return $this->error('银行卡信息有误，请再次检查');
        }

        // 判断银行卡范围
        if (!in_array($bankCardInfo['bank'], ['BOC', 'CCB', 'ICBC', 'ABC'])) {
            return $this->error('目前只支持农业银行，中国银行，建设银行以及工商银行');
        }

        if ($bankCardInfo['cardType'] == 'CC') {
            return $this->error('不支持信用卡，请使用储蓄卡');
        }

        // 判断银行卡号是否重复绑定
        if (!empty(BankCardModel::where('card_num', $bankCardNum)->first())) {
            return $this->error('银行卡已绑定，请换新卡再试');
        }

        return true;
    }
}
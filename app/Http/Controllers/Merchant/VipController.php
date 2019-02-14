<?php
namespace App\Http\Controllers\Merchant;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\VipModel;

class VipController extends Controller{

    private $validate = [
        'price' => 'required',
        'content' => 'required'
    ];
    private $messages = [
    ];
    private $attributes = [
        'price' => '价格',
        'content' => '内容'
    ];

    /**
     * 修改
     */
    public function modify(Request $request){

        $vip = VipModel::find(1);
        if(empty($vip)) return redirect(route('business.vip.modify'));

        if($request->isMethod('POST')){
            try{
                $this->validate($request,$this->validate,$this->messages);
            }catch(\Exception $e){
                return $this->response(502,$e->validator->errors()->first());
            }

            //验证通过，保存城市
            $vip->price = $request->input('price') * 100;
            $vip->content = $request->input('content');

            if($vip->save()){
                return $this->response(200,'修改成功！',route('business.vip.modify'));
            }else{
                return $this->response(422,'处理失败，请重试！');
            }
        }

        return view('Merchant.vip.modify',[
            'info' => $vip
        ]);
    }
}

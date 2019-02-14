<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\BusinessModel;

class BusinessController extends Controller{

    private $validate = [
        'name' => 'required|alpha_dash|between:2,200',
        'nickname' => 'required|between:2,200',
        'description' => 'required',
        'mobile' => 'mobile',
        'email' => 'email',
        'password' => 'required',
        'disabled' => 'required|boolean',
    ];
    private $messages = [

    ];
    private $attributes = [
        'name' => '商户名',
        'nickname' => '昵称',
        'description' => '描述',
        'mobile' => '手机号',
        'email' => '邮箱',
        'password' => '密码',
        'disabled' => '禁止'
    ];

    /**
     * 列表
     */
    public function lists(Request $request){
        $list = BusinessModel::with('city')->paginate(25);

        return view('admin.business.list',[
            'list' => $list
        ]);
    }

    /**
     * 添加(保留拓展)
     */
    public function add(Request $request){
        if($request->isMethod('post')){
            try{
                $this->validate($request,$this->validate,$this->messages,$this->attributes);
            }catch(\Exception $e){
                return $this->response(502,$e->validator->errors()->first());
            }

            //验证通过，保存城市
            $business = new BusinessModel();
            $business->title = $request->input('title');
            $business->img = $request->input('img');
            $business->url = $request->input('url');
            $business->sort = $request->input('sort');
            $business->type = 0; //预留字段
            $business->pos_id = 1; //预留字段

            if($business->save()){
                return $this->response(200,'添加成功！',route('admin.business.list'));
            }else{
                return $this->response(422,'处理失败，请重试！');
            }
        }

        return view('admin.business.add');
    }

    /**
     * 修改（保留拓展）
     */
    public function modify(Request $request){
        $id = intval($request->input('id'));
        if(empty($id)) return $this->response(500,'ID非法！');
        $business = BusinessModel::find($id);
        if(empty($business)) return redirect(route('admin.business.list'));

        if($request->isMethod('POST')){
            try{
                $this->validate($request,$this->validate,$this->messages,$this->attributes);
            }catch(\Exception $e){
                return $this->response(502,$e->validator->errors()->first());
            }

            //验证通过，保存城市
            $business->title = $request->input('title');
            $business->img = $request->input('img');
            $business->url = $request->input('url');
            $business->sort = $request->input('sort');

            if($business->save()){
                return $this->response(200,'修改成功！',route('admin.ads.list'));
            }else{
                return $this->response(422,'处理失败，请重试！');
            }
        }

        return view('admin.ads.modify',[
            'info' => $ads
        ]);
    }

    /**
     * 删除（保留拓展）
     */
    public function delete(Request $request){
        $id = intval($request->input('id'));
        if(empty($id)) return $this->response(500,'ID非法！');

        if(BusinessModel::destroy($id)){
            return $this->response(200,'删除成功！',route('admin.ads.list'));
        }else{
            return $this->response(422,'删除失败');
        }
    }
}

<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\BrandModel;

class BrandController extends Controller{

    private $validate = [

    ];
    private $messages = [

    ];
    private $attributes = [

    ];

    /**
     * 列表
     */
    public function lists(Request $request){
        $list = BrandModel::with('belongs')->paginate(25);

        return view('admin.brand.list',[
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

            //验证通过
            $business = new BrandModel();
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
        $business = BrandModel::find($id);
        if(empty($business)) return redirect(route('admin.business.list'));

        if($request->isMethod('POST')){
            try{
                $this->validate($request,$this->validate,$this->messages,$this->attributes);
            }catch(\Exception $e){
                return $this->response(502,$e->validator->errors()->first());
            }

            //验证通过
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
     * 品牌列表（json）
     */
    public function json(Request $request){
        $bus_user_id = 1;
        $list = BrandModel::where('bus_user_id',$bus_user_id)->get();

        return json_encode($list);
    }

    /**
     * 删除（保留拓展）
     */
    public function delete(Request $request){
        $id = intval($request->input('id'));
        if(empty($id)) return $this->response(500,'ID非法！');

        if(BrandModel::destroy($id)){
            return $this->response(200,'删除成功！',route('admin.ads.list'));
        }else{
            return $this->response(422,'删除失败');
        }
    }
}

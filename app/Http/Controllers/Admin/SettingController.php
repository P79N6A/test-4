<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Models\Admin\SettingModel;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{

    /**
     * 基本设置列表
     */
    public function index(Request $request)
    {
        $result = SettingModel::where('disabled', 0)->paginate(20);
        return view('admin.setting.list', [
           'list'=>$result
        ]);
    }

    /*
    *新增基础设置
    */
    public function add(Request $request)
    {
        if($request->isMethod('post')){
            $setting = new SettingModel();
            $setting->name = $request->input('name');
            $setting->value = $request->input('value');
            if($setting->save()){
                return $this->response(200,'添加成功！',route('admin.setting.list'));
            }else{
                return $this->response(422,'处理失败，请重试！');
            }
        }
        return view('admin.setting.add', [
          
        ]);
    }

    /**
     * 修改
     */
    public function modify(Request $request){
        $id = intval($request->input('id'));
        if(empty($id)) return $this->response(500,'ID非法！');
        $setting = SettingModel::find($id);
        if(empty($setting)) return redirect(route('admin.setting.list'));

        if($request->isMethod('POST')){

            //验证通过，保存城市
            $setting->name = $request->input('name');
            $setting->value = $request->input('value');

            if($setting->save()){
                return $this->response(200,'修改成功！',route('admin.setting.list'));
            }else{
                return $this->response(422,'处理失败，请重试！');
            }
        }

        return view('admin.setting.modify',[
            'info' => $setting
        ]);
    }

    /**
     * 删除
     */
    public function delete(Request $request){
        $id = intval($request->input('id'));
        if(empty($id)) return $this->response(500,'ID非法！');

        if(SettingModel::where('id',$id)->update(['disabled'=>1])){
            return $this->response(200,'删除成功！',route('admin.setting.list'));
        }else{
            return $this->response(422,'删除失败');
        }
    }
}

<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\CityModel;

class CityController extends Controller{

    private $validate = [
        'name' => 'required|between:2,16|unique:city,name',
        'first_letter' => 'required|alpha|size:1',
        'is_hot' => 'required|boolean'
    ];
    private $messages = [
        'name.required' => '城市名称不能为空！',
        'name.between' => '城市名称介乎:min-:max之间！',
        'name.unique' => '已存在同名城市！',
        'first_letter.required' => '首字母不能为空！',
        'first_letter.alpha' => '首字母必须是A-Z！',
        'first_letter.size' => '首字母不符合！',
        'is_hot.required' => '热门城市选项必须！',
        'is_hot.bollean' => '热门城市选项格式不正确！'
    ];

    /**
     * 城市列表
     */
    public function lists(Request $request){
        $list = CityModel::paginate(25);

        return view('admin.city.list',[
            'list' => $list
        ]);
    }

    /**
     * 添加城市
     */
    public function add(Request $request){
        if($request->isMethod('post')){
            try{
                $this->validate($request,$this->validate,$this->messages);
            }catch(\Exception $e){
                return $this->response(502,$e->validator->errors()->first());
            }

            //验证通过，保存城市
            $city = new CityModel();
            $city->name = $request->input('name');
            $city->first_letter = $request->input('first_letter');
            $city->is_hot = $request->input('is_hot');

            if($city->save()){
                return $this->response(200,'添加成功！',route('admin.city.list'));
            }else{
                return $this->response(422,'处理失败，请重试！');
            }
        }

        return view('admin.city.add');
    }

    /**
     * 修改城市
     */
    public function modify(Request $request){
        $id = intval($request->input('id'));
        if(empty($id)) return $this->response(500,'ID非法！');
        $city = CityModel::find($id);
        if(empty($city)) return redirect(route('admin.city.list'));

        if($request->isMethod('POST')){
            try{
                //忽略当前城市查重
                $this->validate['name'] = $this->validate['name'] . ',' . $id;
                $this->validate($request,$this->validate,$this->messages);
            }catch(\Exception $e){
                return $this->response(502,$e->validator->errors()->first());
            }

            //验证通过，保存城市
            $city->name = $request->input('name');
            $city->first_letter = $request->input('first_letter');
            $city->is_hot = $request->input('is_hot');

            if($city->save()){
                return $this->response(200,'修改成功！',route('admin.city.list'));
            }else{
                return $this->response(422,'处理失败，请重试！');
            }
        }

        return view('admin.city.modify',[
            'info' => $city
        ]);
    }

    /**
     * 返回json格式列表
     */
    public function json(Request $request){
        $list = CityModel::get();

        return json_encode($list);
    }

    /**
     * 删除城市
     */
    public function delete(Request $request){

    }
}

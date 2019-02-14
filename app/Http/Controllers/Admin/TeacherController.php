<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\TeacherModel;

class TeacherController extends Controller{

    private $validate = [
        'name' => 'required|between:2,16',
        'job' => 'required|between:2,20',
        'img' => 'required|integer|min:1'
    ];
    private $messages = [

    ];
    private $attributes = [
        'name' => '名字',
        'job' => '任职',
        'img' => '图片'
    ];

    /**
     * 列表
     */
    public function lists(Request $request){
        $list = TeacherModel::with(['pic'])->paginate(25);

        return view('admin.teacher.list',[
            'list' => $list
        ]);
    }

    /**
     * 添加
     */
    public function add(Request $request){
        if($request->isMethod('post')){
            try{
                $this->validate($request,$this->validate,$this->messages,$this->attributes);
            }catch(\Exception $e){
                return $this->response(502,$e->validator->errors()->first());
            }

            //验证通过
            $teacher = new TeacherModel();
            $teacher->name = $request->input('name');
            $teacher->img = $request->input('img');
            $teacher->job = $request->input('job');

            if($teacher->save()){
                return $this->response(200,'添加成功！',route('admin.teacher.list'));
            }else{
                return $this->response(422,'处理失败，请重试！');
            }
        }

        return view('admin.teacher.add');
    }

    /**
     * 修改（保留拓展）
     */
    public function modify(Request $request){
        $id = intval($request->input('id'));
        if(empty($id)) return $this->response(500,'ID非法！');
        $teacher = TeacherModel::find($id);
        if(empty($teacher)) return redirect(route('admin.teacher.list'));

        if($request->isMethod('POST')){
            try{
                $this->validate($request,$this->validate,$this->messages,$this->attributes);
            }catch(\Exception $e){
                return $this->response(502,$e->validator->errors()->first());
            }

            //验证通过
            $teacher->name = $request->input('name');
            $teacher->img = $request->input('img');
            $teacher->job = $request->input('job');

            if($teacher->save()){
                return $this->response(200,'修改成功！',route('admin.teacher.list'));
            }else{
                return $this->response(422,'处理失败，请重试！');
            }
        }

        return view('admin.teacher.modify',[
            'info' => $teacher
        ]);
    }

    /**
     * 以json形式返回教室列表
     */
    public function json(Request $request){
        $list = TeacherModel::get();

        return json_encode($list);
    }

    /**
     * 删除
     */
    public function delete(Request $request){
        $id = intval($request->input('id'));
        if(empty($id)) return $this->response(500,'ID非法！');

        if(TeacherModel::destroy($id)){
            return $this->response(200,'删除成功！',route('admin.teacher.list'));
        }else{
            return $this->response(422,'删除失败');
        }
    }
}

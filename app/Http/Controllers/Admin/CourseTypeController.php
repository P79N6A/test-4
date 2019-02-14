<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\CourseTypeModel;

class CourseTypeController extends Controller{

    private $validate = [
        'name' => 'required|between:2,16',
        'icon' => 'required|integer|min:0',
        'sort' => 'integer',
        'disabled' => 'required|boolean',
        // 'pid' => 'required|integer|min:0',
        // 'type' => 'required|boolean',
        // 'pos_id' => 'required|integer|min:0'
    ];
    private $messages = [
        'name.required' => '广告名称不能为空！',
        'name.between' => '广告名称介乎:min-:max之间！',
        'icon.required' => '图标不能为空！',
        'icon.integer' => '图标格式不符！',
        'icon.min' => '图标格式不符！',
        'sort.integer' => '排序必须为数字！',
        // 'pid.required' => '地址不能为空！',
    ];

    /**
     * 列表
     */
    public function lists(Request $request){
        $list = CourseTypeModel::with('img')->paginate(25);

        return view('admin.courseType.list',[
            'list' => $list
        ]);
    }

    /**
     * 添加
     */
    public function add(Request $request){
        if($request->isMethod('post')){
            try{
                $this->validate($request,$this->validate,$this->messages);
            }catch(\Exception $e){
                return $this->response(502,$e->validator->errors()->first());
            }

            //验证通过，保存城市
            $course_type = new CourseTypeModel();
            $course_type->name = $request->input('name');
            $course_type->icon = $request->input('icon');
            $course_type->sort = $request->input('sort');
            $course_type->disabled = $request->input('disabled');
            $course_type->pid = 0; //预留字段

            if($course_type->save()){
                return $this->response(200,'添加成功！',route('admin.course.type.list'));
            }else{
                return $this->response(422,'处理失败，请重试！');
            }
        }

        return view('admin.courseType.add');
    }

    /**
     * 修改
     */
    public function modify(Request $request){
        $id = intval($request->input('id'));
        if(empty($id)) return $this->response(500,'ID非法！');
        $course_type = CourseTypeModel::find($id);
        if(empty($course_type)) return redirect(route('admin.courseType.list'));

        if($request->isMethod('POST')){
            try{
                $this->validate($request,$this->validate,$this->messages);
            }catch(\Exception $e){
                return $this->response(502,$e->validator->errors()->first());
            }

            //验证通过，保存城市
            $course_type->name = $request->input('name');
            $course_type->icon = $request->input('icon');
            $course_type->sort = $request->input('sort');
            $course_type->disabled = $request->input('disabled');

            if($course_type->save()){
                return $this->response(200,'修改成功！',route('admin.course.type.list'));
            }else{
                return $this->response(422,'处理失败，请重试！');
            }
        }

        return view('admin.courseType.modify',[
            'info' => $course_type
        ]);
    }

    /**
     * 不可用设置替换
     */
    // public function setDisabled(Request $request){
    //     $id = intval($request->input('id'));
    //     if(empty($id)) return $this->response(500,'ID非法！');

    //     $info = CourseTypeModel::find($id);
    //     if(empty($info)){
    //         return $this->response(500,'类型不存在！');
    //     }

    //     $info->disabled = intval(!!!($info->disabled));
    //     if($info->save()){
    //         return $this->response(200,'更新成功！');
    //     }else{
    //         return $this->response(422,'更新失败！');
    //     }
    // }

    /**
     * 删除
     */
    // public function delete(Request $request){
    //     $id = intval($request->input('id'));
    //     if(empty($id)) return $this->response(500,'ID非法！');

    //     //判断是否存在课程，如果存在课程则删除失败
    //     $count = \App\Models\CourseModel::where('type_id',$id)->count();
    //     if($count > 0){
    //         return $this->response(422,'存在课程，不能删除！');
    //     }

    //     if(CourseTypeModel::destroy($id)){
    //         return $this->response(200,'删除成功！',route('admin.course.type.list'));
    //     }else{
    //         return $this->response(422,'删除失败');
    //     }
    // }
}

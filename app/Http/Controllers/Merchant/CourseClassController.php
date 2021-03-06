<?php
namespace App\Http\Controllers\Merchant;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\CourseClassModel;
use App\Models\CourseModel;

class CourseClassController extends Controller{

    private $validate = [
        'name' => 'required|between:2,30',
        'course_id' => 'required|integer|min:1',
        'type' => 'integer|in:1,2',
        'times' => 'required|integer|min:1',
    ];
    private $messages = [
        'required' => ':attribute 不能为空',
        'between'  => ':attribute 长度必须在 :min 和 :max 之间',
        'integer'  => ':attribute 必须是整数',
        'min'      => ':attribute 最小值为 :min',
    ];
    private $attributes = [
        'name' => '课时名称',
        'course_id' => '课程',
        'type' => '类型',
        'times' => '课时可开启游戏最大次数'
    ];

    /**
     * 列表
     */
    public function lists(Request $request){
        $course_id = intval($request->input('course_id'));
        if(empty($course_id)) return $this->response(500,'ID非法！');
        $list = CourseClassModel::with('course')->where('course_id',$course_id)->paginate(25);

        return view('merchant.courseClass.list',[
            'list' => $list,
            'course_id' => $course_id
        ]);
    }

    /**
     * 添加
     */
    public function add(Request $request){
        $course_id = intval($request->input('course_id'));
        if(empty($course_id)) return $this->response(500,'ID非法！');
        $course = CourseModel::find($course_id);
        if(empty($course)) return $this->response(500,'课程不存在！');
        if($request->isMethod('post')){
            try{
                $this->validate($request,$this->validate,$this->messages,$this->attributes);
            }catch(\Exception $e){
                return $this->response(502,$e->validator->errors()->first());
            }

            //获取目前课时数
            $course_class_list = CourseClassModel::where('course_id',$course_id)->orderBy('sort','ASC')->get();
            $count = 1;
            foreach($course_class_list as $item){
                CourseClassModel::where('id',$item->id)->update(['sort'=>$count]);
                $count++;
            }

            //验证通过，保存城市
            $course_class = new CourseClassModel();
            $course_class->name = $request->input('name');
            $course_class->type = $request->input('type');
            $course_class->course_id = $request->input('course_id');
            $course_class->times = $request->input('times');
            $course_class->sort = $count;

            if($course_class->save()){
                //课程的课时加一
                $course = CourseModel::find($course_class->course_id);
                $course->class_num += 1;
                $course->class_id = empty($course->class_id) ? $course_class->id : $course->class_id . ',' . $course_class->id;
                $course->save();
                return $this->response(200,'添加成功！',route('business.course.class.list',['course_id'=>$course_id]));
            }else{
                return $this->response(422,'处理失败，请重试！');
            }
        }

        return view('merchant.courseClass.add',[
            'course' => $course
        ]);
    }

    /**
     * 修改
     */
    public function modify(Request $request){
        $id = intval($request->input('id'));
        if(empty($id)) return $this->response(500,'ID非法！');
        $course_class = CourseClassModel::find($id);
        if(empty($course_class)) return $this->response(500,'课时不存在！');

        if($request->isMethod('POST')){
            try{
                //去除course_id
                unset($this->validate['course_id']);
                $this->validate($request,$this->validate,$this->messages,$this->attributes);
            }catch(\Exception $e){
                return $this->response(502,$e->validator->errors()->first());
            }

            //验证通过，保存城市
            $course_class->name = $request->input('name');
            $course_class->type = $request->input('type');
            $course_class->times = $request->input('times');

            if($course_class->save()){
                return $this->response(200,'修改成功！',route('business.course.class.list',['course_id'=>$course_class->course->id]));
            }else{
                return $this->response(422,'处理失败，请重试！');
            }
        }

        return view('merchant.courseClass.modify',[
            'info' => $course_class
        ]);
    }

    // /**
    //  * 不可用设置替换(保留)
    //  */
    // public function setDisabled(Request $request){
    //     $id = intval($request->input('id'));
    //     if(empty($id)) return $this->response(500,'ID非法！');

    //     $info = CourseClassModel::find($id);
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

    // /**
    //  * 删除（保留）
    //  */
    // public function delete(Request $request){
    //     $id = intval($request->input('id'));
    //     if(empty($id)) return $this->response(500,'ID非法！');

    //     //判断是否存在课程，如果存在课程则删除失败
    //     $count = \App\Models\CourseModel::where('type_id',$id)->count();
    //     if($count > 0){
    //         return $this->response(422,'存在课程，不能删除！');
    //     }

    //     if(CourseClassModel::destroy($id)){
    //         return $this->response(200,'删除成功！',route('business.course.type.list'));
    //     }else{
    //         return $this->response(422,'删除失败');
    //     }
    // }
}

<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\NoticeModel;

class NoticeController extends Controller{

    private $validate = [
        'title' => 'required|between:2,200',
        'content' => 'required',
    ];
    private $messages = [
    ];
    private $attributes = [
        'title' => '标题',
        'content' => '内容'
    ];

    /**
     * 列表
     */
    public function lists(Request $request){
        $list = NoticeModel::where('disabled',0)->orderBy('id','DESC')->paginate(25);

        return view('admin.notice.list',[
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
            $notice = new NoticeModel();
            $notice->title = $request->input('title');
            $notice->content = $request->input('content');

            if($notice->save()){
                return $this->response(200,'添加成功！',route('admin.notice.list'));
            }else{
                return $this->response(422,'处理失败，请重试！');
            }
        }

        return view('admin.notice.add');
    }

    /**
     * 修改
     */
    public function modify(Request $request){
        $id = intval($request->input('id'));
        if(empty($id)) return $this->response(500,'ID非法！');
        $notice = NoticeModel::find($id);
        if(empty($notice)) return redirect(route('admin.notice.list'));

        if($request->isMethod('POST')){
            try{
                $this->validate($request,$this->validate,$this->messages);
            }catch(\Exception $e){
                return $this->response(502,$e->validator->errors()->first());
            }

            //验证通过，保存城市
            $notice->title = $request->input('title');
            $notice->content = $request->input('content');

            if($notice->save()){
                return $this->response(200,'修改成功！',route('admin.notice.list'));
            }else{
                return $this->response(422,'处理失败，请重试！');
            }
        }

        return view('admin.notice.modify',[
            'info' => $notice
        ]);
    }

    /**
     * 删除
     */
    public function delete(Request $request){
        $id = intval($request->input('id'));
        if(empty($id)) return $this->response(500,'ID非法！');

        if(NoticeModel::where('id',$id)->update(['disabled'=>1])){
            return $this->response(200,'删除成功！',route('admin.notice.list'));
        }else{
            return $this->response(422,'删除失败');
        }
    }
}

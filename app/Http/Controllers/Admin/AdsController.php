<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\AdsModel;

class AdsController extends Controller
{
    private $validate = [
        'title' => 'required|between:2,200',
        'img' => 'required|integer|min:1',
        'url' => 'required',
        'sort' => 'integer'
        // 'type' => 'required|boolean',
        // 'pos_id' => 'required|integer|min:0'
    ];
    private $messages = [
        'name.required' => '广告名称不能为空！',
        'name.between' => '广告名称介乎:min-:max之间！',
        'img.required' => '图片不能为空！',
        'url.required' => '地址不能为空！',
        'sort.integer' => '排序必须为数字！'
    ];

    /**
     * 列表
     */
    public function lists(Request $request)
    {
        $list = AdsModel::with(['pos','pic'])->paginate(25);

        return view('admin.ads.list', [
            'list' => $list
        ]);
    }

    /**
     * 添加
     */
    public function add(Request $request)
    {
        if ($request->isMethod('post')) {
            try {
                $this->validate($request, $this->validate, $this->messages);
            } catch (\Exception $e) {
                return $this->response(502, $e->validator->errors()->first());
            }

            //验证通过，保存城市
            $ads = new AdsModel();
            $ads->title = $request->input('title');
            $ads->img = $request->input('img');
            $ads->url = $request->input('url');
            $ads->sort = $request->input('sort');
            $ads->type = 0; //预留字段
            $ads->pos_id = 1; //预留字段

            if ($ads->save()) {
                return $this->response(200, '添加成功！', route('admin.ads.list'));
            } else {
                return $this->response(422, '处理失败，请重试！');
            }
        }

        return view('admin.ads.add');
    }

    /**
     * 修改
     */
    public function modify(Request $request)
    {
        $id = intval($request->input('id'));
        if (empty($id)) {
            return $this->response(500, 'ID非法！');
        }
        $ads = AdsModel::find($id);
        if (empty($ads)) {
            return redirect(route('admin.ads.list'));
        }

        if ($request->isMethod('POST')) {
            try {
                $this->validate($request, $this->validate, $this->messages);
            } catch (\Exception $e) {
                return $this->response(502, $e->validator->errors()->first());
            }

            //验证通过，保存城市
            $ads->title = $request->input('title');
            $ads->img = $request->input('img');
            $ads->url = $request->input('url');
            $ads->sort = $request->input('sort');

            if ($ads->save()) {
                return $this->response(200, '修改成功！', route('admin.ads.list'));
            } else {
                return $this->response(422, '处理失败，请重试！');
            }
        }

        return view('admin.ads.modify', [
            'info' => $ads
        ]);
    }

    /**
     * 删除
     */
    public function delete(Request $request)
    {
        $id = intval($request->input('id'));
        if (empty($id)) {
            return $this->response(500, 'ID非法！');
        }

        if (AdsModel::destroy($id)) {
            return $this->response(200, '删除成功！', route('admin.ads.list'));
        } else {
            return $this->response(422, '删除失败');
        }
    }
}

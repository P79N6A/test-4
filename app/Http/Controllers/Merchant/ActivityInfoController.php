<?php
/**
 * Created by PhpStorm.
 * User: D.Rui
 * Date: 2016/11/1
 * Time: 14:35
 */

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Jobs\PushActivityInfoMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivityInfoController extends Controller
{

    /**
     * 资讯列表
     */
    public function index(Request $request)
    {
        $data = $request->only('sid', 'keyword');
        $limit = 20;
        $fields = [
            'ai.id', 'ai.title', 'ai.description', 'ai.push_flag', 'ai.flag',
            'ai.post_flag', 'ai.is_push', 'ai.addtime', 'bs.name as store_name'
        ];

        $builder = DB::table('activity_info as ai')->where('ai.is_delete', 0)
            ->leftJoin('bus_stores as bs', 'bs.id', '=', 'ai.store_id');

        if (!empty($data['sid']) && $data['sid'] > 0) {
            $builder->where('ai.store_id', $data['sid']);
        }

        if (!empty($data['keyword']) && preg_replace('/\s/', '', $data['keyword'])) {
            $builder->where('ai.title', 'like', '%' . $data['keyword'] . '%');
        } else {
            $builder->whereIn('ai.store_id', $this->storeIds);
        }

        $list = $builder->select($fields)->orderBy('id', 'desc')->paginate($limit);

        return view('business.activity-info-list', [
            'infos' => $list,
            'stores' => $this->stores,
            'sid' => $data['sid'] ? $data['sid'] : 0,
            'keyword' => $data['keyword'] ? $data['keyword'] : ''
        ]);

    }

    /**
     * 创建资讯
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function add(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('business.add-activity-info', ['stores' => $this->stores]);

        } elseif ($request->isMethod('post')) {
            $data = $request->only('title', 'store_id', 'content', 'push_flag');

            if (!preg_replace('/[\s]+/', '', $data['title'])) {
                return $this->response(403, '标题不能为空');
            }
            if (!intval($data['store_id'])) {
                return $this->response(403, '请选择门店');
            }
            if (!preg_replace('/[\s]+/', '', $data['content'])) {
                return $this->response(403, '内容不能为空');
            }
            if (!in_array($data['store_id'], $this->storeIds)) {
                return $this->response(403, '您没有被授权选择该门店');
            }
            $data['addtime'] = time();

            if (DB::table('activity_info')->insert($data)) {

                \Operation::insert('activity_info','添加资讯['.$data['title'].']！',$data);

                return $this->response(200, '资讯创建成功', route('business.activity-info-list'));
            } else {
                return $this->response(403, '资讯创建失败');
            }
        }
    }

    /**
     * 修改资讯
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function edit(Request $request)
    {
        if ($request->isMethod('get')) {
            if (!$request->has('id')) {
                return view('business.error', ['code' => 500, 'msg' => '内部错误']);
            }
            $info = DB::table('activity_info as ai')->whereIn('ai.store_id', $this->storeIds)
                ->where('ai.id', $request->get('id'))->select('ai.*')->first();
            if (!$info) {
                return view('business.error', ['code' => 404, 'msg' => '找不到改资讯']);
            }
            return view('business.edit-activity-info', ['stores' => $this->stores, 'info' => $info]);

        } elseif ($request->isMethod('post')) {
            $data = $request->only('id', 'store_id', 'title', 'content', 'push_flag');

            if (!intval($data['id'])) {
                return $this->response(500, '内部错误');
            }

            $info = DB::table('activity_info as ai')->whereIn('ai.store_id', $this->storeIds)
                ->where('ai.id', $request->get('id'))->select('ai.*')->first();

            if (!$info) {
                return $this->response(404, '找不到该资讯');
            }

            $before_data = $info;

            $id = $data['id'];
            unset($data['id']);

            if (!preg_replace('/[\s]+/', '', $data['title'])) {
                return $this->response(403, '标题不能为空');
            }
            if (!intval($data['store_id'])) {
                return $this->response(403, '请选择门店');
            }
            if (!preg_replace('/[\s]+/', '', $data['content'])) {
                return $this->response(403, '内容不能为空');
            }
            if (!in_array($data['store_id'], $this->storeIds)) {
                return $this->response(403, '您没有被授权选择该门店');
            }

            if (DB::table('activity_info')->where('id', $id)->update($data) !== false) {

                \Operation::update('activity_info','修改资讯['.$before_data->title.']！', $before_data ,$data);

                return $this->response(200, '资讯修改成功', route('business.activity-info-list'));
            } else {
                return $this->response(403, '资讯修改失败');
            }
        }
    }

    /**
     * 删除资讯
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        if (!$request->has('id')) {
            return $this->response(500, '内部错误');
        }

        $info = DB::table('activity_info as ai')->whereIn('ai.store_id', $this->storeIds)
            ->where('ai.id', $request->get('id'))->select('ai.*')->first();

        if (!$info) {
            return $this->response(404, '找不到该资讯');
        }

        if (DB::table('activity_info')->where('id', $info->id)->update(['is_delete' => 1]) !== false) {

            \Operation::delete('activity_info','删除资讯['.$info->title.']！', $info);

            return $this->response(200, '删除成功', route('business.activity-info-list'));
        } else {
            return $this->response(500, '删除失败');
        }

    }

    /**
     * 发布资讯，使得在APP端显示
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function switchPostInfo(Request $request)
    {
        if (!intval($request->get('id')) || !intval($request->get('s'))) {
            return $this->response(500, '内部错误');
        }
        $id = $request->get('id');
        $flag = $request->get('s') == 1 ? $request->get('s') : 0;

        $info = DB::table('activity_info as ai')->where('ai.id', $id)
            ->whereIn('ai.store_id', $this->storeIds)->first();
        if (!$info) {
            return $this->response(404, '该资讯不存在');
        }

        if (DB::table('activity_info')->where('id', $id)->update(['post_flag' => $flag, 'post_time' => time()]) !== false) {

            \Operation::update('activity_info','推送资讯['.$info->title.']！', [], []);

            return $this->response(200, '操作成功', route('business.activity-info-list'));
        } else {
            return $this->response(500, '操作失败');
        }

    }

    /**
     * 推荐资讯到轮播框
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function recommend(Request $request)
    {
        if (!intval($request->get('id'))) {
            return $this->response(500, '内部错误');
        }
        $info = DB::table('activity_info')->where('id',$request->get('id'))
            ->whereIn('store_id', $this->storeIds)->where('is_delete', 0)
            ->where('post_flag', 1)->where('post_time', '>', 0)->first();
        if (!$info) {
            return $this->response(403, '该资讯不存在或者还未发布');
        }
        if ($info->flag == 1) {
            $flag = 0;
        } else {
            $flag = 1;
        }
        if (DB::table('activity_info')->where('id', $info->id)->update(['flag' => $flag]) !== false) {
            return $this->response(200, '操作成功', route('business.activity-info-list'));
        } else {
            return $this->response(500, '操作成功');
        }
    }

    /**
     * 推送活动资讯，推送状态栏通知
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function push(Request $request)
    {
        if ($request->isMethod('get')) {
            if (!intval($request->get('id'))) {
                return view('business.error', ['code' => 500, 'msg' => '内部错误']);
            }

            $info = DB::table('activity_info')->where('id', $request->get('id'))
                ->whereIn('store_id', $this->storeIds)->first();

            if (!$info) {
                return view('business.error', ['code' => 404, 'msg' => '该资讯不存在']);
            }
            if ($info->push_flag == 0) {
                return view('business.error', ['code' => 403, 'msg' => '该资讯未设置允许推送，操作不允许']);
            }
            if ($info->is_push == 1) {
                return view('business.error', ['code' => 403, 'msg' => '该资讯已推送，操作被取消']);
            }

            return view('business.push-activity-info', ['id' => $request->get('id'), 'stores' => $this->stores]);

        } elseif ($request->isMethod('post')) {
            $data = $request->only('id', 'store_id', 'target');

            if (!intval($request->get('id'))) {
                return $this->response(500, '内部错误');
            }
            if (!$data['store_id'] || !intval($data['store_id'])) {
                return $this->response(403, '请选择门店');
            }
            if (!$data['target'] || !intval($data['target'])) {
                return $this->response(403, '请选择目标人群');
            }

            $info = DB::table('activity_info')->where('id', $data['id'])
                ->whereIn('store_id', $this->storeIds)->first();
            if (!$info) {
                return $this->response(404, '该资讯不存在');
            }
            if ($info->push_flag == 0) {
                return $this->response(403, '该资讯未设置允许推送，操作不允许');
            }
            if ($info->is_push == 1) {
                return $this->response(403, '该资讯已推送，操作取消');
            }

            DB::table('activity_info')->where('id', $data['id'])->update(['is_push' => 1]);

            if ($data['target'] == 1) {  // 取消费过的用户
                $count = DB::table('order')->whereIn('store_id', $this->storeIds)
                    ->where('store_id', $data['store_id'])->distinct()->count('userid');
                $builder = DB::table('order')->whereIn('store_id', $this->storeIds)
                    ->where('store_id', $data['store_id'])->distinct();
            } elseif ($data['target'] == 2) {  // 取门店访客
                $count = DB::table('store_visit_log')->whereIn('store_id', $this->storeIds)
                    ->where('store_id', $data['store_id'])->distinct()->count('userid');
                $builder = DB::table('store_visit_log')->whereIn('store_id', $this->storeIds)
                    ->where('store_id', $data['store_id'])->distinct();
            } else {
                return $this->response(500, '内部错误');
            }

            if (empty($count)) {
                return $this->response(403, '该门店下指定目标人群数量为0，不能推送');
            }

            $schema = DB::table('qrcode_type')->where('id', 13)->first();
            $link = config('activity_info.base_url') . '?id=' . $info->id . '&t=' . time();
            $builder->select('userid')->chunk(100, function ($users) use ($schema, $link, $info) {
                foreach ($users as $user) {
                    $uids[] = $user->userid;
                }
                $job = new PushActivityInfoMessage([
                    'uids' => $uids,
                    'title' => '门店活动资讯提醒',
                    'content' => '您收到一条门店活动资讯，请查收！',
                    'scheme' => $schema->app_url . '?url=' . $link . '&name=' . $info->title
                ]);
                // 把任务推送到队列
                $this->dispatch($job);
            });

            \Operation::update('activity_info','向手机端状态栏推送资讯['.$info->title.']！', [], []);

            return $this->response(200, '推送任务已下达，推送操作将在后台完成', route('business.activity-info-list'));
        }
    }

}
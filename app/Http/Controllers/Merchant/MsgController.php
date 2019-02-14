<?php

namespace App\Http\Controllers\Merchant;

use App\Jobs\BusPushMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class MsgController extends Controller
{

    /**
     * 消息列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $msgs = DB::table('message')
            ->where('from', 2)
            ->where('account_id', $this->parentUserId)
            ->select('id', 'title', 'content', 'addtime')
            ->paginate(20);
        return view('business.message-list', ['msgs' => $msgs]);
    }

    /**
     * 推送消息给用户
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function push(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('business.push-message');

        } elseif ($request->isMethod('post')) {
            $data = $request->only('title', 'content', 'target', 'uids');
            if (!$data['title']) {
                return $this->response(403, '请输入消息标题');
            }
            if (!$data['content']) {
                return $this->response(403, '请输入消息内容');
            }
            if (!intval($data['target'])) {
                return $this->response(403, '请选择推送目标人群');
            }

            if ($data['target'] == 1) {
                if (empty($data['uids'])) {
                    return $this->response(403, '请筛选一部分用户作为目标用户');
                }
            }

            $msg = [
                'account_id' => $this->parentUserId,
                'operator' => session()->get('id'),
                'title' => $data['title'],
                'content' => $data['content'],
                'from' => 2,
                'addtime' => time()
            ];
            $job = new BusPushMessage($msg, $data['target'], $data['uids'], $this->storeIds);
            $this->dispatch($job);
            return $this->response(200, '任务已下达，作业将在后台完成，并可能会花费一定时间，请耐心等待，推送任务完成后该消息将出现在消息中心', route('business.message-center'));

        }

    }

    /**
     * 消息详情
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function detail(Request $request)
    {
        if (!intval($request->get('id'))) {
            return view('admin.error', ['code' => 500, 'msg' => '内部错误']);
        }
        $msg = DB::table('message')->where('id', $request->get('id'))->where('account_id', $this->parentUserId)->first();
        if (!$msg) {
            return view('admin.error', ['code' => 404, 'msg' => '该消息不存在']);
        }
        return view('business.message-detail', ['msg' => $msg]);
    }

    /**
     * 按用户名、昵称、手机号码 搜索用户
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchUser(Request $request)
    {
        if ($request->isMethod('get')) {
            $keyword = $request->get('keyword');
            if (preg_replace('/\s/', '', $keyword)) {
                $visitors = DB::table('store_visit_log')->whereIn('store_id', $this->storeIds)->distinct()->lists('userid');
                $consumers = DB::table('order')->whereIn('store_id', $this->storeIds)->lists('userid');
                $uids = array_unique(array_merge($visitors, $consumers));
                $matches = DB::table(config('tables.base') . '.users')
                    // ->whereIn('id', $uids) //$uids过多，导致in效率低效，应使用join代替
                    ->where(function ($query) use ($keyword) {
                        $query->where('username', 'like', '%' . $keyword . '%')
                            ->orWhere('nickname', 'like', '%' . $keyword . '%')
                            ->orWhere('mobile', 'like', '%' . $keyword . '%');
                    })
                    // ->distinct()
                    ->select('id', 'username')->get();
                return response()->json($matches);
            }
        }
    }

}

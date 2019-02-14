<?php
namespace App\Http\Controllers\Api;

use App\Helper;
use App\Models\NoticeModel;
use App\Models\NoticeReadModel;
use Illuminate\Http\Request;

class NoticeController extends Controller
{

    /**
     * 获取通知列表
     */
    public function lists(Request $request)
    {
        $notice = NoticeModel::where('disabled', 0)->paginate(10);
        //获取已读信息
        $read_id = NoticeReadModel::where('user_id', $this->auth->user_id)->pluck('notice_id');
        if (empty($read_id)) {
            $read_id = [];
        } else {
            $read_id = $read_id->toArray();
        }
        foreach ($notice as &$item) {
            $item->content = Helper::resetImg($item->content);
            if (in_array($item->id, $read_id)) {
                $item->read = 1;
            } else {
                $item->read = 0;
            }
        }

        return $this->success($notice->items());
    }

    /**
     * 获取通知详细
     */
    public function info(Request $request)
    {
        $id = $request->input('id');
        if (!Helper::isId($id)) {
            return $this->error('ID非法！');
        }

        $notice = NoticeModel::find($id);

        if (empty($notice)) {
            return $this->error('通知不存在！');
        }

        //设置为已读
        $where = [
            ['user_id', $this->auth->user_id],
            ['notice_id', $id],
        ];
        if (NoticeReadModel::where($where)->count() == 0) {
            NoticeReadModel::create(['user_id' => $this->auth->user_id, 'notice_id' => $id]);
        }

        $notice->content = Helper::resetImg($notice->content);

        return $this->success($notice);
    }

    /**
     * 获取未读信息
     */
    public function unRead(Request $request)
    {
        //获取已读信息
        $read_id = NoticeReadModel::where('user_id', $this->auth->user_id)->pluck('notice_id');
        if (!empty($read_id)) {
            $read_id = $read_id->toArray();
            $count = NoticeModel::whereNotIn('id', $read_id)->where('disabled', 0)->count();
        } else {
            $count = NoticeModel::where('disabled', 0)->count();
        }

        $data['count'] = $count;

        return $this->success($data);
    }
}

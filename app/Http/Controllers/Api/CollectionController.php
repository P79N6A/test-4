<?php
namespace App\Http\Controllers\Api;

use App\Helper;
use App\Models\CollectionModel;
use App\Models\CourseModel;
use Illuminate\Http\Request;
use App\Models\StoreModel;

class CollectionController extends Controller
{

    /**
     * 收藏课程
     * 必须用户登录
     */
    public function add(Request $request)
    {
        $course_id = $request->input('course_id');
        if (!Helper::isId($course_id)) {
            return $this->error('课程ID非法！');
        }

        //验证课程是否存在
        $info = CourseModel::find($course_id);
        if (empty($info)) {
            return $this->error('课程不存在！');
        }

        //判断是否收藏
        if ($this->isCollection($course_id)) {
            return $this->error('已在收藏夹中！');
        }

        $fav = new CollectionModel();
        $fav->user_id = $this->auth->user_id;
        $fav->course_id = $course_id;
        if ($fav->save()) {
            return $this->success('收藏成功！');
        } else {
            return $this->error('收藏失败！');
        }
    }

    /**
     * 获取用户收藏列表
     * 必须用户登录
     */
    public function lists(Request $request)
    {
        //获取用户收藏
        $collects = CollectionModel::where('user_id', $this->auth->user_id)->orderBy('updated_at', 'DESC')->paginate(10);
        if (empty($collects)) {
            return $this->success([]);
        }

        $course_ids = [];
        foreach ($collects as $item) {
            $course_ids[] = $item->course_id;
        }

        //获取收藏的课程
        $list = CourseModel::with(['pic','class'])->whereIn('id', $course_ids)
            ->where('disabled', 0)
            ->select(['id', 'name', 'content', 'price', 'class_num', 'is_hot', 'is_recommend', 'img','store_ids'])
            ->get();
        $list = json_decode(json_encode($list),true); 
        foreach ($list as $arr => $val) {
            $times = 0;
            foreach ($val['class'] as $key => $value) {
                $times += $value['times'];
            }
            $list[$arr]['times'] = $times;
            $store = StoreModel::where("id",$val['store_ids'])->select('name')->first();
            $list[$arr]['store'] = $store['name'];
        }
        if (empty($list)) {
            $list = [];
        }

        return $this->success($list);
    }

    public function delete(Request $request)
    {
        $course_id = $request->input('course_id');
        if (!Helper::isId($course_id)) {
            return $this->error('课程ID非法！');
        }

        //删除响应项目
        $where[] = ['user_id', $this->auth->user_id];
        $where[] = ['course_id', $course_id];
        if (CollectionModel::where($where)->delete()) {
            return $this->success([]);
        } else {
            return $this->error('删除失败！');
        }
    }

    /**
     * 判断是否收藏
     */
    public function is(Request $request)
    {
        $course_id = $request->input('course_id');
        if (!Helper::isId($course_id)) {
            return $this->error('课程ID非法！');
        }

        if ($this->isCollection($course_id)) {
            $return['is_collect'] = 1;
        } else {
            $return['is_collect'] = 0;
        }

        return $this->success($return);
    }

    /**
     * 判断是否已收藏
     * @return boolean false-未收藏，true-已收藏
     */
    private function isCollection($course_id)
    {
        if (!isset($this->auth)) { //未登录
            return false;
        }

        $where[] = ['user_id', $this->auth->user_id];
        $where[] = ['course_id', $course_id];
        $fav_info = CollectionModel::where($where)->first();
        if (empty($fav_info)) {
            return false;
        } else {
            return true;
        }
    }
}

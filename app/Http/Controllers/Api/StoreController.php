<?php
namespace App\Http\Controllers\Api;

use App\Helper;
use App\Models\AttachmentModel;
use App\Models\StoreModel;
use App\Models\TeacherModel;
use Illuminate\Http\Request;

class StoreController extends Controller
{

    /**
     * 获取门店详细
     */
    public function detail(Request $request)
    {
        $id = $request->input('id');
        if (!Helper::isId($id)) {
            return $this->error('ID非法！');
        }

        $store = StoreModel::with(['brand'])->find($id);
        if (empty($store)) {
            return $this->error('门店不存在！');
        }

        //获取门店图片
        $pic = [];
        if (!empty($store->imgs)) {
            $pic = AttachmentModel::whereIn('id', explode(',', $store->imgs))->get();
        }
        $store->pic = $pic;

        //获取教师信息
        $teachers = [];
        if (!empty($store->teachers)) {
            $teachers = TeacherModel::with('pic')->whereIn('id', explode(',', $store->teachers))->get();
        }
        $store->teachers = $teachers;

        return $this->success($store);
    }
}

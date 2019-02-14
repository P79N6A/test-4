<?php
namespace App\Http\Controllers\Api;

use App\Models\CourseTypeModel;
use Illuminate\Http\Request;

class CourseTypeController extends Controller
{
    /**
     * 获取课程类型
     */
    public function lists(Request $request)
    {
        $type = CourseTypeModel::with('img')->where('disabled', 0)->select(['name', 'icon', 'id'])->orderBy('sort', 'ASC')->get();

        return $this->success($type);
    }
}

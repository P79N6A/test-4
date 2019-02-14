<?php
namespace App\Http\Controllers\Api;

use App\Models\SuitableAgeModel;
use Illuminate\Http\Request;

class SuitableAgeController extends Controller
{

    /**
     * 获取适合年龄段列表
     */
    public function lists(Request $request)
    {
        $age = SuitableAgeModel::orderBy('sort', 'ASC')->get();

        return $this->success($age);
    }
}

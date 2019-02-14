<?php
namespace App\Http\Controllers\Api;

use App\Models\AdsModel;
use Illuminate\Http\Request;

class AdsController extends Controller
{
    /**
     * 根据广告位获取对应的广告
     */
    public function lists(Request $request)
    {
        $pos_id = $request->get('pos_id');
        $pos_id = intval($pos_id);
        if (!$pos_id) {
            $pos_id = 0;
        }

        $ads = AdsModel::with('pic')->where('pos_id', $pos_id)->select(['title', 'url', 'img'])->orderBy('sort', 'ASC')->get();

        return $this->success($ads);
    }
}

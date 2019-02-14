<?php

namespace App\Http\Controllers\Merchant;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class RedPackageController extends Controller
{

    /**
     * 红包活动列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $list = DB::table('red_pacakge_activity as ra')->where('is_delete', 0)
            ->leftJoin('red_package_get_log as rpg','rpg.red_package_id','=','ra.id')
            ->where('ra.bus_userid', $this->parentUserId)
//            ->where('ra.end_date', '>', time())
            ->select('ra.*',DB::raw('COUNT(DISTINCT rpg.userid) AS get_count'))
            ->orderBy('ra.id', 'desc')
            ->groupBy('ra.id')
            ->paginate(8);

        return view('business.red-packages', ['activities' => $list]);
    }

    /**
     * 新建红包活动
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function add(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('business.add-red-package-activity');

        } elseif ($request->isMethod('post')) {
            $data = $request->only('title', 'description', 'start_date', 'end_date');

            if (!preg_replace('/\s/', '', $data['title'])) {
                return $this->response(403, '请输入活动标题');
            }
            if (!preg_replace('/\s/', '', $data['description'])) {
                return $this->response(403, '请输入活动描述');
            }
            if (!strtotime($data['start_date']) || !strtotime($data['end_date'])) {
                return $this->response(403, '请选择起始时间');
            }
            if ($data['start_date'] >= $data['end_date']) {
                return $this->response(403, '结束时间必须大于开始时间');
            }
            $data['bus_userid'] = $this->parentUserId;
            $data['start_date'] = strtotime($data['start_date']);
            $data['end_date'] = strtotime($data['end_date']);

            // 检测活动周期是否与其他活动冲突
            $repeat = DB::table('red_pacakge_activity')
                ->where('bus_userid', $this->parentUserId)
                ->where('is_delete', 0)
                ->where(function ($query) use ($data) {
                    $query->where(function ($query) use ($data) {
                        $query->where(function ($query) use ($data) {
                            $query->where('start_date', '<=', $data['start_date'])->where('end_date', '>=', $data['start_date']);
                        });
                    })->orWhere(function ($query) use ($data) {
                        $query->orWhere('start_date', '<=', $data['end_date'])->where('end_date', '>=', $data['end_date']);
                    });
                })->first();
            if ($repeat) {
                return $this->response(403, '同一时间段内只能有一个活动，请重新选择时间段');
            }

            if (DB::table('red_pacakge_activity')->insert($data)) {
                return $this->response(200, '活动新建成功', route('business.red-package-activities'));
            } else {
                return $this->response(500, '活动新建失败');
            }

        }
    }

    /**
     * 修改活动
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function edit(Request $request)
    {
        if ($request->isMethod('get')) {
            if (!intval($request->get('id'))) {
                return view('business.error', ['code' => 500, 'msg' => '内部错误']);
            }
            $detail = DB::table('red_pacakge_activity')->where('id', $request->get('id'))
                ->where('bus_userid', $this->parentUserId)->where('is_delete', 0)->first();
            if (!$detail) {
                return view('business.error', ['code' => 404, 'msg' => '该活动不存在']);
            }
            return view('business.edit-red-package-activity', ['detail' => $detail]);

        } elseif ($request->isMethod('post')) {
            $data = $request->only('id', 'title', 'description', 'start_date', 'end_date');

            if (!intval($data['id'])) {
                return $this->response(500, '内部错误');
            }
            if (!preg_replace('/\s/', '', $data['title'])) {
                return $this->response(403, '请输入活动标题');
            }
            if (!preg_replace('/\s/', '', $data['description'])) {
                return $this->response(403, '请输入活动描述');
            }
            if (!strtotime($data['start_date']) || !strtotime($data['end_date'])) {
                return $this->response(403, '请选择起始时间');
            }
            if ($data['start_date'] >= $data['end_date']) {
                return $this->response(403, '结束时间必须大于开始时间');
            }
            $id = $data['id'];
            unset($data['id']);

            $detail = DB::table('red_pacakge_activity')->where('id', $id)
                ->where('bus_userid', $this->parentUserId)->where('is_delete', 0)->first();
            if (!$detail) {
                return $this->response(404, '该活动不存在');
            }

            $data['bus_userid'] = $this->parentUserId;
            $data['start_date'] = strtotime($data['start_date']);
            $data['end_date'] = strtotime($data['end_date']);

            // 检测活动周期是否与其他活动冲突
            $repeat = DB::table('red_pacakge_activity')->where('bus_userid', $this->parentUserId)
                ->where('is_delete', 0)->where('id', '!=', $detail->id)
                ->where(function ($query) use ($data) {
                    $query->where(function ($query) use ($data) {
                        $query->where('start_date', '<=', $data['start_date'])->where('end_date', '>=', $data['start_date']);
                    })->orWhere(function ($query) use ($data) {
                        $query->orWhere('start_date', '<=', $data['end_date'])->where('end_date', '>=', $data['end_date']);
                    });
                })->first();
            if ($repeat) {
                return $this->response(403, '同一时间段内只能有一个活动，请重新选择时间段');
            }

            if (DB::table('red_pacakge_activity')->where('id', $id)->update($data) !== false) {
                return $this->response(200, '活动修改成功', route('business.red-package-activities'));
            } else {
                return $this->response(500, '活动修改失败');
            }

        }
    }

    /**
     * 删除红包活动
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        if (!intval($request->get('id'))) {
            return $this->response(500, '内部错误');
        }
        $act = DB::table('red_pacakge_activity')->where('id', $request->get('id'))
            ->where('is_delete', 0)
            ->where('bus_userid', $this->parentUserId)->where('is_delete', 0)->first();
        if (!$act) {
            return $this->response(404, '该活动不存在');
        }
        if (DB::table('red_pacakge_activity')->where('id', $act->id)->update(['is_delete' => 1]) !== false) {
            return $this->response(200, '删除成功', route('business.red-package-activities'));
        } else {
            return $this->response(500, '删除失败');
        }
    }

    /**
     * 添加奖品到红包池
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function putGift(Request $request)
    {
        if ($request->isMethod('get')) {
            if (!$request->get('id')) {
                return view('admin.error', ['code' => 500, 'msg' => '内部错误']);
            }
            $act = DB::table('red_pacakge_activity')->where('id', $request->get('id'))
                ->where('is_delete', 0)->where('bus_userid', $this->parentUserId)
                ->first();
            if (!$act) {
                return view('admin.error', ['code' => 404, 'msg' => '该活动不存在']);
            }
            if ($act->end_date < time()) {
                return view('admin.error', ['code' => 403, 'msg' => '该活动已过期']);
            }
            // 已领取总人数
            $gotCount = DB::table('red_package_get_log')->where('red_package_id', $act->id)
                ->distinct()->count('userid');
            // 已投放的红包
            $publishedRegPags = DB::table('red_package_pool as rpp')->where('rpp.activity_id', $act->id)->where('rpp.is_delete', 0)
                ->leftJoin('ticket as t', function ($join) {
                    $join->on('t.id', '=', 'rpp.item_id')->where('rpp.type', '=', 3);
                })
                ->leftJoin('ticket_extend as te','te.ticket_id','=','t.id')
                ->leftJoin('bus_stores as bs', 'bs.id', '=', 'te.store_id')
                ->select('rpp.*', 'bs.name as store_name', 't.name as ticket_name')->get();
            // 可用卡券
//            $tickets = DB::table('ticket')
//                ->where('userid', $this->parentUserId)
//                ->where('is_delete', 0)->where('admin', 0)
//                ->where('expire_date', '>', time())
//                ->select('id', 'type', 'name', 'circulation')
//                ->get();

            return view('business.put-gift-to-red-pool', [
                'activity' => $act,
                'gotCount' => $gotCount,
                'publishedRedPags' => $publishedRegPags,
//                'tickets' => $tickets,
                'stores' => $this->stores,
            ]);

        } elseif ($request->isMethod('post')) {
            $data = $request->only('id', 'store_ids', 'stock', 'ticket_ids','range');

            if (!intval($data['id'])) {
                return $this->response(500, '内部错误');
            }
            $act = DB::table('red_pacakge_activity')->where('id', $data['id'])->where('bus_userid', $this->parentUserId)
                ->where('is_delete', 0)->first();
            if (!$act) {
                return $this->response(404, '该活动不存在');
            }
            if ($act->end_date < time()) {
                return $this->response(403, '该活动已过期');
            }
            if (empty($data['store_ids']) || !is_array($data['store_ids'])) {
                return $this->response(403, '请选择门店');
            }
            if (empty($data['ticket_ids']) || !is_array($data['ticket_ids'])) {
                return $this->response(403, '请选择卡券');
            }
            $sids = array_intersect($this->storeIds, $data['store_ids']);

            foreach ($data['ticket_ids'] as $k => $v) {

                $ticket = DB::table('ticket as t')->where('t.id', intval($v))
                    ->join('ticket_extend as te', function ($join) use ($sids) {
                        $join->on('te.ticket_id', '=', 't.id')->whereIn('te.store_id', $sids);
                    })->where('t.admin', 0)->where('t.is_delete', 0)->where('expire_date', '>', time())
                    ->select('t.*')->first();

                if (!$ticket) {
                    return $this->response(404, '其中一张卡券不存在或已过期');
                }

                if (!intval($data['stock'][$k])) {
                    return $this->response(403, '请填写卡券【' . $ticket->name . '】的投放库存');
                }
                if ($data['stock'][$k] > $ticket->circulation) {
                    return $this->response(403, '卡券【' . $ticket->name . '】的投放库存大于该卡券的总库存');

                }
                // 红包池记录
                $poolRecord[] = [
                    'item_id' => $ticket->id,
                    'activity_id' => $act->id,
                    'type' => 3,
                    'range' => $data['range'],
                    'stock' => $data['stock'][$k],
                    'addtime' => time()
                ];
            }
            if($poolRecord){
                foreach ($poolRecord as $record) {
                    $redPoolId = DB::table('red_package_pool')->insertGetId($record);
                    // 减原库存
                    DB::table('ticket')->where('id', $ticket->id)->decrement('circulation', $record['stock']);
                    // 卡券可用门店
                    $avaiSids = DB::table('ticket_extend')->where('ticket_id', $record['item_id'])->lists('store_id');
                    // 红包池关联表
                    foreach ($avaiSids as $sid) {
                        DB::table('red_package_prize_store_relation')->insert(['red_pool_id' => $redPoolId, 'store_id' => $sid]);
                    }
                }
            }else{
                return $this->response(500, '没选择卡券或卡券无效');
            }
            return $this->response(200, '操作成功', route('business.red-package-activities'));
        }

    }

}

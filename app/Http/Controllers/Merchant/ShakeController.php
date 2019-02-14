<?php
/**
 * Created by PhpStorm.
 * User: D.Rui
 * Date: 2016/11/10
 * Time: 17:27
 */

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShakeController extends Controller
{
    /**
     * 摇一摇活动列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        if ($this->storeIds) {
            $shakes = DB::table('shake_activity as sa')
                ->where('sa.del_flag', 0)
                ->where('sa.end_date', '>', time())
                ->whereIn('sa.store_id', $this->storeIds)
                ->leftJoin('bus_stores as bs', 'bs.id', '=', 'sa.store_id')
                ->leftJoin('shake_record as sr', function ($join) {
                    $join->on('sr.activity_id', '=', 'sa.id')->where('sr.is_win', '=', 1);
                })
                ->select([
                    'sa.*', 'bs.name as store_name',
                    DB::raw('COUNT(DISTINCT sr.userid) AS win_count'),
                ])
                ->groupBy('sa.id')
                ->orderBy('sa.addtime', 'desc')->paginate(10);
        } else {
            $shakes = null;
        }

        return view('business.shake-activity-list', ['activities' => $shakes]);
    }

    /**
     * 历史已过期摇一摇活动
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function history()
    {
        if (!$this->storeIds) {
            $list = null;
        } else {
            $list = DB::table('shake_activity as sa')
                ->where('del_flag', 0)
                ->where('sa.end_date', '<', time())
                ->whereIn('sa.store_id', $this->storeIds)
                ->leftJoin('bus_stores as bs', 'bs.id', '=', 'sa.store_id')
                ->select('sa.*', 'bs.name as store_name')
                ->orderBy('sa.addtime', 'desc')->paginate(10);
        }
        return view('business.history-shake-activity', ['activities' => $list]);
    }

    /**
     * 创建摇一摇活动
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function add(Request $request)
    {
        if ($request->isMethod('get')) {
            if (!$this->storeIds) {
                return view('business.error', ['code' => 403, 'msg' => '您没有已创建的门店或者无授权的门店，无法创建活动']);
            }

            return view('business.add-shake-activity', ['stores' => $this->stores]);

        } elseif ($request->isMethod('post')) {

            if (!$this->storeIds) {
                return $this->response(403, '您没有已创建的门店或者无授权的门店，无法创建活动');
            }

            $data = $request->only([
                'title', 'description', 'start_date', 'end_date', 'win_limit', 'store_ids'
            ]);

            if (!preg_replace('/\s/', '', $data['title'])) {
                return $this->response(403, '请填写活动标题');
            }

            if (!preg_replace('/\s/', '', $data['description'])) {
                return $this->response(403, '请填写活动描述');
            }

            if (!strtotime($data['start_date'])) {
                return $this->response(403, '请选择开始时间');
            }

            if (!strtotime($data['end_date'])) {
                return $this->response(403, '请选择结束时间');
            }

            /*
            if(strtotime($data['start_date']) <= time()){
                return $this->response(403, '开始时间必须大于等于当前时间');
            }
            */

            if ($data['end_date'] < $data['start_date']) {
                return $this->response(403, '结束时间必须大于开始时间');
            }

            if (!intval($data['win_limit'])) {
                return $this->response(403, '请填写中奖限制次数');
            }

            if (!$data['store_ids'] || !is_array($data['store_ids'])) {
                return $this->response(403, '请勾选至少一个门店');
            }

            // 检测活动的时间段冲突
            $conflict = DB::table('shake_activity')
                ->where('del_flag', 0)
                ->whereIn('store_id', $data['store_ids'])
                ->where(function ($query) use ($data) {
                    $query->where(function ($query) use ($data) {
                        $query->where('start_date', '<=', strtotime($data['start_date']))
                            ->where('end_date', '>=', strtotime($data['start_date']));
                    })->orWhere(function ($query) use ($data) {
                        $query->where('start_date', '<=', strtotime($data['end_date']))
                            ->where('end_date', '>=', strtotime($data['end_date']));
                    });
                })->first();

            if ($conflict) {
                return $this->response(403, '同一门店同一时间段内只能有一个活动');
            }

            $sids = array_intersect($data['store_ids'], $this->storeIds);
            $data['start_date'] = strtotime($data['start_date']);
            $data['end_date'] = strtotime($data['end_date']);
            $data['addtime'] = time();
            unset($data['store_ids']);

            DB::beginTransaction();
            try {
                foreach ($sids as $sid) {
                    if ($sid > 0) {
                        $data['store_id'] = $sid;
                        DB::table('shake_activity')->insert($data);
                    }
                }
                DB::commit();
                return $this->response(200, '创建成功', route('business.shake-activity-list'));
            } catch (Exception $e) {
                DB::rollBack();
                return $this->response(500, '创建失败');
            }

        }
    }

    /**
     * 修改摇一摇活动
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function edit(Request $request)
    {
        if ($request->isMethod('get')) {

            if (!intval($request->get('id'))) {
                return view('business.error', ['code' => 500, 'msg' => '内部错误']);
            }

            if (!$this->storeIds) {
                return view('business.error', ['code' => 403, 'msg' => '您没有被授权任何门店，不可修改该活动']);
            }

            $act = DB::table('shake_activity')
                ->where('del_flag', 0)
                ->whereIn('store_id', $this->storeIds)
                ->where('id', $request->get('id'))
                ->first();

            if (!$act) {
                return view('business.error', ['code' => 404, 'msg' => '该活动不存在']);
            }

            if ($act->end_date < time()) {
                return view('business.error', ['code' => 403, 'msg' => '该活动已过期，不能修改']);
            }

            if (!in_array($act->store_id, $this->storeIds)) {
                return view('business.error', ['code' => 403, 'msg' => '您没获得活动对应门店的授权，不可修改该活动']);
            }

            return view('business.edit-shake-activity', [
                'activity' => $act,
                'stores' => $this->stores
            ]);

        } elseif ($request->isMethod('post')) {
            $data = $request->only([
                'id', 'title', 'description', 'start_date', 'end_date', 'win_limit'
            ]);

            if (!intval($data['id'])) {
                return $this->response(403, '内部错误');
            }

            if (!preg_replace('/\s/', '', $data['title'])) {
                return $this->response(403, '请填写活动标题');
            }

            if (!preg_replace('/\s/', '', $data['description'])) {
                return $this->response(403, '请填写活动描述');
            }


            if (!strtotime($data['end_date'])) {
                return $this->response(403, '请选择结束时间');
            }

            if (!intval($data['win_limit'])) {
                return $this->response(403, '请填写中奖限制次数');
            }

            if (!$this->storeIds) {
                return $this->response(403, '您没有被授权任何门店，不可修改该活动');

            }

            $act = DB::table('shake_activity')
                ->where('del_flag', 0)
                ->whereIn('store_id', $this->storeIds)
                ->where('id', $data['id'])
                ->first();

            if (!$act) {
                return $this->response(404, '该活动不存在');
            }

            if ($act->start_date > time()) {
                if (!strtotime($data['start_date'])) {
                    return $this->response(403, '请选择开始时间');
                }
            }

            if (strtotime($data['end_date']) <= $act->start_date || (!empty($data['start_date']) && strtotime($data['end_date']) <= strtotime($data['start_date']))) {
                return $this->response(403, '结束时间必须大于开始时间');
            }

            if ($act->end_date < time()) {
                return $this->response(404, '该活动已过期，不能修改');
            }

            $startDate = !empty($data['start_date']) ? strtotime($data['start_date']) : $act->start_date;
            $data['start_date'] = $startDate;

            // 检测活动的时间段冲突
            $conflict = DB::table('shake_activity')
                ->where('del_flag', 0)
                ->where('store_id', $act->store_id)
                ->where('id', '!=', $act->id)
                ->where(function ($query) use ($data, $startDate) {
                    $query->where(function ($query) use ($data, $startDate) {
                        $query->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $startDate);
                    })->orWhere(function ($query) use ($data) {
                        $query->where('start_date', '<=', strtotime($data['end_date']))
                            ->where('end_date', '>=', strtotime($data['end_date']));
                    });
                })->first();

            if ($conflict) {
                return $this->response(403, '同一门店同一时间段内只能有一个活动');
            }

            // 如果活动已开始，开始时间不能改
            if ($act->start_date < time()) {
                unset($data['start_date']);
            }

            $data['end_date'] = strtotime($data['end_date']);
            unset($data['id']);

            if (DB::table('shake_activity')->where('id', $act->id)->update($data) !== false) {
                return $this->response(200, '修改成功', route('business.shake-activity-list'));
            } else {
                return $this->response(200, '修改失败');
            }

        }
    }

    /**
     * 删除摇一摇活动
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        if (!intval($request->get('id'))) {
            return $this->response(500, '内部错误');
        }

        if (!$this->storeIds) {
            return $this->response(403, '您未被授权任何门店，不能删除该活动');
        }

        $act = DB::table('shake_activity')
            ->where('id', $request->get('id'))
            ->where('del_flag', 0)
            ->first();

        if (!$act) {
            return $this->response(404, '该活动不存在');
        }

        if (!in_array($act->store_id, $this->storeIds)) {
            return $this->response(403, '您未被授权活动对应门店，不能删除该活动');
        }

        if (DB::table('shake_activity')->where('id', $act->id)->update(['del_flag' => 1]) !== false) {
            return $this->response(200, '删除成功', route('business.shake-activity-list'));
        } else {
            return $this->response(200, '删除失败');
        }

    }

    /**
     * 投放奖品到摇一摇活动中
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function publishGift(Request $request)
    {
        if ($request->isMethod('get')) {
            if (!$id = intval($request->get('id'))) {
                return $this->response(403, '内部错误');
            }

            if (!$this->storeIds) {
                return view('business.error', ['code' => 403, 'msg' => '您未被授权任何门店，不能向该活动投放奖品']);
            }

            //检测活动有效期
            $activity = DB::table('shake_activity as sa')
                ->whereIn('sa.store_id', $this->storeIds)
                ->where('sa.del_flag', 0)
                ->where('sa.id', $id)
                ->leftJoin('shake_record as sr', 'sr.activity_id', '=', 'sa.id')
                ->select('sa.*', DB::raw('COUNT(DISTINCT sr.userid) AS prized_count'))
                ->first();
            if (time() > $activity->end_date) {
                return view('business.error', ['code' => 403, 'msg' => '该活动已结束，不能投放奖品']);
            }

            // 已投放的奖品
            $addedPrizes = DB::table('prize_pool as pp')
                ->where('pp.is_delete', 0)
                ->where('pp.activity_id', $activity->id)
                ->leftJoin('ticket as t', function ($join) {
                    $join->on('t.id', '=', 'pp.prize_id')->where('pp.type', '=', 1);
                })
                ->leftJoin('packages as p', function ($join) {
                    $join->on('p.id', '=', 'pp.prize_id')->where('pp.type', '=', 2);
                })
                ->select(['pp.*', 't.name as ticket_name', 'p.name as package_name'])
                ->get();

            // 可投放的卡券和套餐
            $tickets = DB::table('ticket as t')
                ->where('is_delete', 0)
                ->where('circulation', '>', 0)
                ->where('t.expire_date', '>=', time())
                ->where('t.admin', 0)
                ->where('t.offline', 0)
                ->join('ticket_extend as te', function ($join) use ($activity) {
                    $join->on('te.ticket_id', '=', 't.id')->where('te.store_id', '=', $activity->store_id);
                })
                ->distinct('t.id')
                ->select('t.id', 't.name', 't.type', 't.circulation as stock')->get();

            $packages = DB::table('packages as p')
                ->where('is_delete', 0)
                //->where('flag', 0)
                ->where('p.expire_date', '>=', time())
                ->where('stock', '>', 0)
                ->join('package_store_relation as psr', function ($join) use ($activity) {
                    $join->on('psr.package_id', '=', 'p.id')->where('psr.store_id', '=', $activity->store_id);
                })->select('p.id', 'p.name', 'p.price', 'p.stock')
                ->get();

            if (empty($packages) && empty($tickets)) {
                return view('merchant.error', ['code' => 403, 'msg' => '该门店无可用的卡券和套餐，创建后在操作']);
            }

            return view('business.publish-shake-gift', [
                'addedPrizes' => $addedPrizes,
                'activity' => $activity,
                'packages' => $packages,
                'tickets' => $tickets,
            ]);

        } elseif ($request->isMethod('post')) {
            $data = $request->all();

            if (empty($data['activity_id'])) {
                return $this->response(500, '内部错误');
            }

            //检测活动有效期
            $activity = DB::table('shake_activity as sa')
                ->where('sa.id', $data['activity_id'])
                ->where('sa.del_flag', 0)
                ->whereIn('sa.store_id', $this->storeIds)
                ->select('sa.*')->first();

            if (!$activity) {
                return $this->response(404, '该活动不存在');
            }

            if (time() > $activity->end_date) {
                return $this->response(403, '该活动已结束，不能投放奖品');
            }

            if (empty($data['type']) || !intval($data['type'])) {
                return $this->response(403, '请选择奖品类型');
            }

            if ($data['type'] == 1) {
                if (empty($data['ticket_id'])) {
                    return $this->response(403, '请选择卡券');
                }
                unset($data['package_ids']);
            } elseif ($data['type'] == 2) {
                if (empty($data['package_id'])) {
                    return $this->response(403, '请选择商品/套餐');
                }
                unset($data['ticket_ids']);
            } else {
                return $this->response(500, '内部错误');
            }

            if (!intval($data['stock'])) {
                return $this->response(403, '请填写库存');
            }

            if (!floatval($data['probability'])) {
                return $this->response(403, '请填写中奖概率');
            }

            if (floatval($data['probability']) >= 1) {
                return $this->response(403, '中奖概率必须小于1');
            }

            // 判断库存是否充足
            $stock = 0;
            $prize = null;
            if ($data['type'] == 1) {
                $prize = DB::table('ticket as t')
                    ->where('is_delete', 0)
                    ->where('admin', 0)
                    ->where('t.id', $data['ticket_id'])
                    ->join('ticket_extend as te', function ($join) use ($activity) {
                        $join->on('te.ticket_id', '=', 't.id')
                            ->where('te.store_id', '=', $activity->store_id);
                    })
                    ->select('t.*')
                    ->first();
                if (!$prize) {
                    return $this->response(404, '该卡券不存在');
                }
                if ($prize->offline != 0) {
                    return $this->response(403, '卡券【' . $prize->name . '】已下架，不能选择');
                }
                $stock = $prize->circulation;
            } elseif ($data['type'] == 2) {
                $prize = DB::table('packages as p')
                    ->where('p.id', $data['package_id'])
                    ->where('is_delete', 0)
                    ->join('package_store_relation as psr', function ($join) use ($activity) {
                        $join->on('psr.package_id', '=', 'p.id')
                            ->where('psr.store_id', '=', $activity->store_id);
                    })
                    ->select('p.*')
                    ->first();
                if (!$prize) {
                    return $this->response(404, '该商品不存在');
                }
                $stock = $prize->stock;
            }
            if ($data['stock'] > $stock) {
                return $this->response(403, '奖品库存量不能大于总库存量：' . $stock);
            }

            $data['original_stock'] = $data['stock'];
            $data['addtime'] = time();
            $data['prize_id'] = !empty($data['ticket_id']) ? $data['ticket_id'] : $data['package_id'];
            unset($data['ticket_id'], $data['package_id']);

            // 给奖品池增加库存，同时给总库存减少对应奖品池库存量
            DB::beginTransaction();
            try {
                // 往奖品池插数据
                DB::table('prize_pool')->insert($data);

                $table = $data['type'] == 1 ? 'ticket' : 'packages';
                $field = $data['type'] == 1 ? 'circulation' : 'stock';

                DB::table($table)->where('id', $prize->id)->decrement($field, $data['stock']); // 总库存减少对应数量
                DB::commit();
                return $this->response(200, '奖品投放成功', route('business.shake-activity-list'));
            } catch (Exception $e) {
                DB::rollBack();
                return $this->response(500, '奖品投放失败');
            }

        }
    }

    /**
     * 修改摇一摇奖品库存
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeGiftStock(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->only('activity_id', 'prize_id', 'stock');

            if (!$data['activity_id'] || !$data['prize_id']) {
                return $this->response(500, '内部错误');
            }

            if (!$this->storeIds) {
                return $this->response(403, '您未被授权任何门店，不能修改该活动奖品的库存');
            }

            $prize = DB::table('prize_pool as pp')
                ->where('pp.is_delete', 0)
                ->where('pp.activity_id', $data['activity_id'])
                ->where('pp.id', $data['prize_id'])
                ->join('shake_activity as sa', function ($join) {
                    $join->on('sa.id', '=', 'pp.activity_id')
                        ->where('sa.store_id', '>', 0)
                        ->whereIn('sa.store_id', $this->storeIds);
                })
                ->select('pp.*')->first();

            if (!$prize) {
                return $this->response(403, '该奖品不存在或者您未被授权该活动对应的门店，不能执行此操作');
            }

            $modify = ['stock' => $data['stock']];
            if ($prize->stock > $data['stock']) {
                $modify['original_stock'] = $prize->original_stock - (abs($prize->stock - $data['stock']));
            } elseif ($prize->stock < $data['stock']) {
                $modify['original_stock'] = $prize->original_stock + (abs($prize->stock - $data['stock']));
            }
            $state = DB::table('prize_pool')->where('id', $prize->id)->update($modify);

            if ($state !== false) {
                return $this->response(200, '库存修改成功', route('business.publish-shake-gift', ['id' => $data['activity_id']]));
            } else {
                return $this->response(500, '库存修改失败');
            }

        }
    }

    /**
     * 修改奖品中奖概率
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeGiftRate(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->only('activity_id', 'prize_id', 'rate');
            if (!$data['activity_id'] || !$data['prize_id'] || !$data['rate']) {
                return $this->response(500, '内部错误');
            }

            if ($data['rate'] < 0 || $data['rate'] > 100) {
                return $this->response(403, '中奖概率必须大于0%小于100%');
            }

            if (!$this->storeIds) {
                return $this->response(403, '您未被授权任何门店，不能修改该活动奖品的库存');
            }

            $prize = DB::table('prize_pool as pp')
                ->where('pp.is_delete', 0)
                ->where('pp.activity_id', $data['activity_id'])
                ->where('pp.id', $data['prize_id'])
                ->join('shake_activity as sa', function ($join) {
                    $join->on('sa.id', '=', 'pp.activity_id')
                        ->where('sa.store_id', '>', 0)
                        ->whereIn('sa.store_id', $this->storeIds);
                })
                ->select('pp.*')->first();

            if (!$prize) {
                return $this->response(403, '该奖品不存在或者您未被授权该活动对应的门店，不能执行此操作');
            }

            $state = DB::table('prize_pool')->where('id', $prize->id)->update(['probability' => round($data['rate'] / 100, 3)]);

            if ($state !== false) {
                return $this->response(200, '奖品中奖概率修改成功', route('business.publish-shake-gift', ['id' => $data['activity_id']]));
            } else {
                return $this->response(500, '奖品中奖概率修改失败');
            }

        }
    }

    /**
     * 删除摇一摇奖品
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteGift(Request $request)
    {
        if ($request->isMethod('get')) {
            $data = $request->only('activity_id', 'prize_id');

            if (!$data['activity_id'] || !$data['prize_id']) {
                return $this->response(500, '内部 错误');
            }

            if (!$this->storeIds) {
                return $this->response(403, '您未被授权任何门店，不能修改该活动奖品的库存');
            }

            $prize = DB::table('prize_pool as pp')
                ->where('pp.is_delete', 0)
                ->where('pp.activity_id', $data['activity_id'])
                ->where('pp.id', $data['prize_id'])
                ->join('shake_activity as sa', function ($join) {
                    $join->on('sa.id', '=', 'pp.activity_id')
                        ->where('sa.store_id', '>', 0)
                        ->whereIn('sa.store_id', $this->storeIds);
                })
                ->select('pp.*')->first();

            if (!$prize) {
                return $this->response(403, '该奖品不存在或者您未被授权该活动对应的门店，不能执行此操作');
            }

            $state = DB::table('prize_pool')->where('id', $prize->id)->update(['is_delete' => 1]);

            if ($state !== false) {
                return $this->response(200, '奖品删除成功', route('business.publish-shake-gift', ['id' => $data['activity_id']]));
            } else {
                return $this->response(500, '奖品删除失败');
            }

        }
    }

}
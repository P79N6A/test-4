<?php

namespace App\Http\Controllers\Merchant;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class SekillController extends Controller
{

    /**
     * 秒杀活动列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $list = DB::table('sekill_activity as sa')
            ->leftJoin('bus_stores as bs', 'bs.id', '=', 'sa.store_id')
            ->whereIn('sa.store_id', $this->storeIds)
            ->where('sa.del_flag', 0)
            ->select('sa.*', 'bs.name as store_name')
            ->orderBy('sa.id', 'desc')
            ->paginate(20);
        return view('business.sekill-activities', ['activities' => $list]);
    }

    /**
     * 创建秒杀活动
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function add(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('business.add-sekill-activity', ['stores' => $this->stores]);

        } elseif ($request->isMethod('post')) {
            $data = $request->only('title', 'description', 'start_date', 'end_date', 'status', 'store_ids');
            if (!preg_replace('/\s/', '', $data['title'])) {
                return $this->response(403, '活动标题不能为空');
            }
            if (!preg_replace('/\s/', '', $data['description'])) {
                return $this->response(403, '活动描述不能为空');
            }
            if (!$data['start_date'] || !strtotime($data['start_date'])) {
                return $this->response(403, '活动开始时间不能为空');
            }
            if (!$data['end_date'] || !strtotime($data['end_date'])) {
                return $this->response(403, '活动结束时间不能为空');
            }
            if($data['start_date'] > $data['end_date']){
                return $this->response(403,'开始时间不能大于结束时间');
            }

            if (!$data['store_ids'] || !is_array($data['store_ids'])) {
                return $this->response(403, '请选择举行门店');
            }

            $storeIds = $data['store_ids'];
            // 检测同时间段活动冲突，同一时间段统一门店
            foreach ($storeIds as $storeId) {
                $repeat = DB::table('sekill_activity')->where('del_flag', 0)
                    ->where('store_id', '=', $storeId)
                    ->where(function ($join) use ($data) {
                        $join->where(function ($query) use ($data) {
                            $query->where('start_date', '<=', $data['start_date'])
                                ->where('end_date', '>=', $data['start_date']);
                        })->orWhere(function ($query) use ($data) {
                            $query->where('start_date', '<=', $data['end_date'])
                                ->where('end_date', '>=', $data['end_date']);
                        });
                    })->first();

                if ($repeat) {
                    return $this->response(403, '其中一个或多个门店同一时间段内只能有一个活动');
                }
            }

            unset($data['store_ids']);
            $acts = [];
            foreach ($storeIds as $storeId) {
                $act = $data;
                $act['create_date'] = date('Y-m-d H:i:s');
                $act['store_id'] = intval($storeId);
                $acts[] = $act;
            }

            if (DB::table('sekill_activity')->insert($acts)) {
                return $this->response(200, '创建成功', route('business.sekill-activities'));
            } else {
                return $this->response(500, '创建失败');
            }

        }
    }

    /**
     * 修改秒杀活动
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function edit(Request $request)
    {
        if ($request->isMethod('get')) {
            if (!intval($request->get('id'))) {
                return view('business.error', ['code' => 500, 'msg' => '内部错误']);
            }

            $act = DB::table('sekill_activity as sa')
                ->leftJoin('bus_stores as bs', 'bs.id', '=', 'sa.store_id')
                ->where('sa.id', $request->get('id'))
                ->whereIn('sa.store_id', $this->storeIds)
                ->select('sa.*', 'bs.name as store_name')
                ->first();

            if (!$act) {
                return view('business.error', ['code' => 404, 'msg' => '该活动不存在']);
            }
            if ($act->end_date < date('Y-m-d H:i:s')) {
                return view('business.error', ['code' => 403, 'msg' => '该活动一结束，不能修改']);
            }

            return view('business.edit-sekill-activity', ['activity' => $act]);

        } elseif ($request->isMethod('post')) {
            $data = $request->only('id', 'title', 'description', 'start_date', 'end_date', 'status');
            if (!preg_replace('/\s/', '', $data['title'])) {
                return $this->response(403, '活动标题不能为空');
            }
            if (!preg_replace('/\s/', '', $data['description'])) {
                return $this->response(403, '活动描述不能为空');
            }
            if (!$data['start_date'] || !strtotime($data['start_date'])) {
                return $this->response(403, '活动开始时间不能为空');
            }
            if (!$data['end_date'] || !strtotime($data['end_date'])) {
                return $this->response(403, '活动结束时间不能为空');
            }
            if($data['start_date'] > $data['end_date']){
                return $this->response(403,'开始时间不能大于结束时间');
            }

            $act = DB::table('sekill_activity')->where('id', $data['id'])->first();
            unset($data['id']);

            if (!$act) {
                return $this->response(404, '该活动不存在');
            }
            if (strtotime($act->end_date) < time()) {
                return $this->response(403, '该活动已结束，不能修改');
            }

            // 检测同时间段活动冲突
            $repeat = DB::table('sekill_activity')
                ->where('del_flag', 0)
                ->where('id', '!=', $act->id)
                ->where('store_id', $act->store_id)
                ->where(function ($query) use ($data) {
                    $query->where(function ($query) use ($data) {
                        $query->where('start_date', '<=', $data['start_date'])
                            ->where('end_date', '>=', $data['start_date']);
                    })->orWhere(function ($query) use ($data) {
                        $query->where('start_date', '<=', $data['end_date'])
                            ->where('end_date', '>=', $data['end_date']);
                    });
                })->first();

            if ($repeat) {
                return $this->response(403, '同一时间段内只能有一个活动');
            }

            $data['status'] = intval($data['status']);

            if (DB::table('sekill_activity')->where('id', $act->id)->update($data) !== false) {
                return $this->response(200, '修改成功', route('business.sekill-activities'));
            } else {
                return $this->response(500, '修改失败');
            }

        }
    }

    /**
     * 删除秒杀活动
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        if (!intval($request->get('id'))) {
            return $this->response(500, '内部错误');
        }

        $act = DB::table('sekill_activity')
            ->where('id', $request->get('id'))
            ->whereIn('store_id', $this->storeIds)
            ->first();
        if (!$act) {
            return $this->response(404, '该活动不存在或您没操作权限');
        }
        if (DB::table('sekill_activity')->where('id', $act->id)->update(['del_flag' => 1]) !== false) {
            return $this->response(200, '删除成功', route('business.sekill-activities'));
        } else {
            return $this->response(500, '删除失败');
        }
    }

    /**
     * 投放套餐到秒杀活动
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function putPackage(Request $request)
    {
        if ($request->isMethod('get')) {
            if (!intval($request->get('id'))) {
                return view('business.error', ['code' => 500, '内部错误']);
            }
            $act = DB::table('sekill_activity')
                ->where('id', $request->get('id'))
                ->whereIn('store_id', $this->storeIds)
                ->where('end_date', '>=', date('Y-m-d H:i:s'))
                ->where('status', 1)
                ->where('del_flag', 0)
                ->first();

            if (!$act) {
                return view('business.error', ['code' => 404, 'msg' => '该活动不存在或者或者已暂停或者已过期']);
            }

            // 已投放的套餐
            $putPackages = DB::table('sekill_activity_package as sap')
                ->leftJoin('packages as p', 'p.id', '=', 'sap.package_id')
                ->where('sap.activity_id', $act->id)
                ->select('p.name', 'sap.price', 'sap.buy_limit', 'sap.stock')->get();
            $putPackageIds = DB::table('sekill_activity_package as sap')
                ->leftJoin('packages as p', 'p.id', '=', 'sap.package_id')
                ->where('sap.activity_id', $act->id)
                ->lists('p.id');

            // 可用套餐
            $packages = DB::table('packages as p')
                ->join('package_store_relation as psr', function ($query) use ($act) {
                    $query->on('psr.package_id', '=', 'p.id')->where('psr.store_id', '=', $act->store_id);
                })
                ->whereNotIn('p.id', $putPackageIds)
                ->where('p.userid', $this->parentUserId)
                ->where('p.is_delete', 0)
                ->where('p.expire_date', '>', time())
                ->select('p.id', 'p.name', 'p.price', 'p.stock')
                ->orderBy('p.id', 'desc')
                ->get();

            return view('business.put-sekill-package', [
                'activity' => $act,
                'packages' => $packages,
                'putPackages' => $putPackages
            ]);

        } elseif ($request->isMethod('post')) {
            $data = $request->only('activity_id', 'packages', 'prices', 'buy_limits', 'stocks');

            if (!intval($data['activity_id'])) {
                return $this->response(500, '内部错误');
            }

            $act = DB::table('sekill_activity')->whereIn('store_id', $this->storeIds)
                ->where('id', $data['activity_id'])
                ->where('end_date', '>=', date('Y-m-d H:i:s'))
                ->where('status', 1)
                ->where('del_flag', 0)
                ->first();
            if (!$act) {
                return $this->response(500, '该活动不存在或者已暂停活动已过期');
            }

            if (empty($data['packages']) || !is_array($data['packages'])) {
                return $this->response(403, '请选择至少一个套餐并填写秒杀价格、秒杀限购数和秒杀库存');
            }
            $new = [];
            $update = [];
            foreach ($data['packages'] as $k => $item) {
                $package = DB::table('packages')->where('id', $item)->where('userid', $this->parentUserId)
                    ->where('is_delete', 0)->where('expire_date', '>', time())->first();

                if (!$package) {
                    return $this->response(403, '其中一个套餐不存在，请重新选择');
                }
                // 检测套餐是否重复投放，同一个秒杀活动不能放同一个套餐，因为有秒杀价的冲突
                $repeat = DB::table('sekill_activity_package')
                    ->where('activity_id', $act->id)
                    ->where('package_id', $package->id)
                    ->first();
                if ($repeat) {
                    return $this->response(403, '同一个活动不能投放同一个套餐【' . $package->name . '】进去');
                }

                if (!is_numeric($data['prices'][$k])) {
                    return $this->response(403, '请填写秒杀价格');
                }
                if ($data['prices'][$k] > $package->price) {
                    return $this->response(403, '其中一个套餐的秒杀价格大于原价');
                }
                if (!is_numeric($data['buy_limits'][$k]) || floatval($data['buy_limits'][$k]) <= 0) {
                    return $this->response(403, '请填写限购数');
                }
                if ($data['buy_limits'][$k] > $data['stocks'][$k]) {
                    return $this->response(403, '秒杀限购数不能大于秒杀总库存数');
                }
                if (!intval($data['stocks'][$k])) {
                    return $this->response(403, '请填写其中一个套餐的库存');
                }
                if ($package->stock < $data['stocks'][$k]) {
                    return $this->response(403, '其中一个套餐的秒杀库存大于其总库存，请重新填写秒杀库存或者增加总库存后再操作');
                }

                $new[] = [
                    'activity_id' => $act->id,
                    'package_id' => $item,
                    'price' => $data['prices'][$k],
                    'buy_limit' => $data['buy_limits'][$k],
                    'stock' => $data['stocks'][$k],
                    'create_date' => date('Y-m-d H:i:s')
                ];
                $update[] = [
                    'id' => $package->id,
                    'stock' => ($package->stock - $data['stocks'][$k]),
                    'is_sekill' => 1,
                ];

            }

            DB::beginTransaction();
            try {
                DB::table('sekill_activity_package')->insert($new);
                foreach ($update as $up) {
                    DB::table('packages')->where('id', $up['id'])->update(['stock' => $up['stock'], 'is_sekill' => $up['is_sekill']]);
                }
                DB::commit();
                return $this->response(200, '投放成功', route('business.sekill-activities'));

            } catch (\Exception $e) {
                DB::rollBack();
                return $this->response(500, '投放失败');
            }
        }

    }

}

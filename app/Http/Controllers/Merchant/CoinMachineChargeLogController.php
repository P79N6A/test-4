<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CoinMachineChargeLogController extends Controller
{

    /**
     * 提币机充值日志列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $data = $request->only('store', 'serial', 'operator', 'sd', 'ed', 'order');
        $limit = 20;

        if ($data['order'] == 1) {
            $orderBy = 'asc';
        } else {
            $orderBy = 'desc';
        }

        $fields = [
            'cmc.id', 'bs.name as store_name', 'cm.serial_no as serial',
            'cm.name as machine_name', 'cmc.coin', 'bu.name as operator', 'cmc.create_date'
        ];

        $builder = DB::table('coin_machine as cm')
            ->where('cm.del_flag', 0)
            ->whereIn('cm.store_id', $this->storeIds)
            ->join('coin_machine_charge_log as cmc', function ($join) use ($data) {
                $join->on('cmc.machine_id', '=', 'cm.id');
                if (strtotime($data['sd']) && !strtotime($data['ed'])) {
                    $join->where('cmc.create_date', '>=', $data['sd']);
                } elseif (!strtotime($data['sd']) && strtotime($data['ed'])) {
                    $join->where('cmc.create_date', '<=', $data['ed']);
                } elseif (strtotime($data['sd']) && strtotime($data['ed'])) {
                    $join->where('cmc.create_date', '>=', $data['sd'])->where('cmc.create_date', '<=', $data['ed']);
                }
            });

        if ($data['store']) {
            $builder->join('bus_stores as bs', function ($join) use ($data) {
                $join->on('bs.id', '=', 'cm.store_id')
                    ->whereIn('bs.id', $this->storeIds)
                    ->where('bs.name', 'like', '%' . $data['store'] . '%');
            });
        } else {
            $builder->join('bus_stores as bs', 'bs.id', '=', 'cm.store_id');
        }

        if ($data['serial']) {
            $builder->where('cm.serial_no', 'like', '%' . $data['serial'] . '%');
        }

        if ($data['operator']) {
            $builder->join('bus_users as bu', function ($join) use ($data) {
                $join->on('bu.id', '=', 'cmc.operator')->where('bu.name', 'like', '%' . $data['operator'] . '%')->where(function ($query) {
                    $query->where('bu.pid', '=', $this->parentUserId)->orWhere('bu.id', '=', $this->parentUserId);
                });
            });
        } else {
            $builder->leftJoin('bus_users as bu', 'bu.id', '=', 'cmc.operator');
        }

        $list = $builder->select($fields)->orderBy('create_date', $orderBy)->paginate($limit);

        return view('business.coin-machine-charge-log', ['logs' => $list, 'params' => $data]);
    }

    /**
     * 增加添币记录
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function add(Request $request)
    {
        if ($request->isMethod('get')) {

            $machines = DB::table('coin_machine')->whereIn('store_id', $this->storeIds)->where('del_flag', 0)
                ->select('id', 'name')->get();

            if ($request->ajax() && $request->has('action') && $request->get('action') == 'getMachines') {
                $storeId = $request->get('storeId');
                $store = DB::table('bus_stores')->where('userid', $this->parentUserId)->where('is_delete', 0)
                    ->where('id', $storeId)->where('status', 1)->first();

                if ($store) {
                    $machines = DB::table('coin_machine')->where('store_id', $store->id)
                        ->where('del_flag', 0)
                        ->select('id', 'name')->get();
                    return $this->response(200, '机台数据获取成功', '', $machines);
                } else {
                    return $this->response(200, '机台数据获取成功', '', []);
                }
            }

            return view('business.add-coin-charge-log', ['stores' => $this->stores, 'machines' => $machines]);

        } elseif ($request->isMethod('post')) {

            $data = $request->only('store_id', 'machine_id', 'coin');

            if (!intval($data['store_id'])) {
                return $this->response(403, '请选择门店');
            }

            if (!in_array($data['store_id'], $this->storeIds)) {
                return $this->response(403, '所选门店不合法');
            }

            $store = DB::table('bus_stores')->where('id', $data['store_id'])->where('is_delete', 0)->first();

            unset($data['store_id']);

            if (!$store) {
                return $this->response(404, '该门店不存在');
            }

            if ($store->status !== 1) {
                return $this->response(404, '该门店状态异常');
            }

            if (!intval($data['machine_id'])) {
                return $this->response(403, '请选择机台');
            }

            $machine = DB::table('coin_machine')->where('id', $data['machine_id'])
                ->whereIn('store_id', $this->storeIds)->where('del_flag', 0)->first();

            if (!$machine) {
                return $this->response(404, '该机台不存在');
            }

            if ($machine->usable == 1) {
                return $this->response(403, '该机台已禁用');
            }

            if (!intval($data['coin'])) {
                return $this->response(403, '请填写大于0的币数');
            }

            $data['operator'] = session()->get('id');
            $data['create_date'] = date('Y-m-d H:i:s');

            if (DB::table('coin_machine_charge_log')->insert($data)) {
                return $this->response(200, '记录添加成功', route('business.coin-machine-charge-log'));
            } else {
                return $this->response(500, '记录添加失败');
            }

        }
    }

    /**
     * 删除添币记录
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        if ($request->isMethod('get')) {
            $id = intval($request->get('id'));

            if (!$id) {
                return $this->response(403, '请求出错');
            }

            $log = DB::table('coin_machine_charge_log as cmc')
                ->where('cmc.id', $id)
                ->join('coin_machine as cm', 'cm.id', '=', 'cmc.machine_id')
                ->join('bus_stores as bs', function ($join) {
                    $join->on('bs.id', '=', 'cm.store_id')->whereIn('bs.id', $this->storeIds);
                })->select('cmc.*')->first();

            if (!$log) {
                return $this->response(404, '该添币记录不存在');
            }

            if (DB::table('coin_machine_charge_log')->delete($log->id)) {
                return $this->response(200, '添币记录删除成功', route('business.coin-machine-charge-log'));
            } else {
                return $this->response(500, '添币记录删除失败');
            }

        }
    }

}

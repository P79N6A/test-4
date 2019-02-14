<?php

namespace App\Http\Controllers\Merchant;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class CoinMachineController extends Controller
{

    /**
     * 序列号长度
     * @var int
     */
    private $serialLength = 64;
    /**
     * 备注长度
     * @var int
     */
    private $remarkLength = 150;

    /**
     * 机台列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $data = $request->only('store', 'keyword', 'start_date', 'end_date');
        $limit = 20;

        $fields = ['cm.id', 'bs.name as store_name', 'cm.serial_no as serial', 'cm.name', 'cm.usable', 'cm.create_date'];

        $builder = DB::table('coin_machine as cm')->where('del_flag', 0);

        if ($data['store']) {
            $builder->join('bus_stores as bs', function ($join) use ($data) {
                $join->on('bs.id', '=', 'cm.store_id')->where('bs.name', 'like', '%' . $data['store'] . '%');
            });
        } else {
            $builder->join('bus_stores as bs', function ($join) {
                $join->on('bs.id', '=', 'cm.store_id')->whereIn('bs.id', $this->storeIds);
            });
        }

        if ($data['keyword']) {
            $builder->where(function ($query) use ($data) {
                $query->where('cm.serial_no', 'like', '%' . $data['keyword'])->orWhere(function ($query) use ($data) {
                    $query->where('cm.name', 'like', '%' . $data['keyword'] . '%');
                });
            });
        }

        if (strtotime($data['start_date']) && !strtotime($data['end_date'])) {
            $builder->where('cm.create_date', '>=', $data['start_date']);
        } elseif (!strtotime($data['start_date']) && strtotime($data['end_date'])) {
            $builder->where('cm.create_date', '<=', $data['end_date']);
        } elseif (strtotime($data['start_date']) && strtotime($data['end_date'])) {
            $builder->whereBetween('cm.create_date', [$data['start_date'], $data['end_date']]);
        }

        $list = $builder->select($fields)->orderBy('cm.id', 'desc')->paginate($limit);

        return view('business.coin-machine-list', ['machines' => $list, 'params' => $data]);
    }

    /**
     * 添加机台
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function add(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('business.add-coin-machine', ['stores' => $this->stores]);

        } elseif ($request->isMethod('post')) {
            $data = $request->only('name', 'serial', 'store', 'remarks');

            if (!trim_blanks($data['name'])) {
                return $this->response(403, '请输入机台名称');
            }

            if (!trim_blanks($data['serial'])) {
                return $this->response(403, '请输入机台序列号');
            }

            if (strlen(trim_blanks($data['serial'])) > $this->serialLength) {
                return $this->response(403, '序列号长度不能大于' . $this->serialLength . '位');
            }

            $serialExist = DB::table('coin_machine')->where('serial_no', $data['serial'])->first();

            if ($serialExist) {
                return $this->response(403, '该序列号已被使用');
            }

            if (!intval($data['store'])) {
                return $this->response(403, '请选择所属门店');
            }

            if (!in_array($data['store'], $this->storeIds)) {
                return $this->response(403, '所选门店不合法');
            }

            $store = DB::table('bus_stores')->whereIn('id', $this->storeIds)
                ->where('is_delete', 0)->where('id', $data['store'])->first();

            if (!$store) {
                return $this->response(404, '该门店不存在');
            }

            if ($store->status != 1) {
                return $this->response(403, '该门店状态异常');
            }

            if ($data['remarks'] && strlen($data['remarks']) > $this->remarkLength) {
                return $this->response(403, '备注长度不能超过' . ($this->remarkLength / 3) . '个汉字');
            }

            $secret = md5(Uuid::uuid4()->toString());

            $save = [
                'name' => $data['name'],
                'serial_no' => $data['serial'],
                'store_id' => $store->id,
                'secret_key' => $secret,
                'create_date' => date('Y-m-d H:i:s'),
                'create_by' => session()->get('id'),
                'remarks' => $data['remarks']
            ];

            if (DB::table('coin_machine')->insert($save)) {
                return $this->response(200, '机台添加成功', route('business.coin-machine-list'));
            } else {
                return $this->response(500, '机台添加失败');
            }

        }
    }

    /**
     * 修改机台
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function edit(Request $request)
    {
        if ($request->isMethod('get')) {
            $id = intval($request->get('id'));

            if (!$id) {
                return view('business.error', ['code' => 403, 'msg' => '请求出错']);
            }

            $machine = DB::table('coin_machine')
                ->whereIn('store_id', $this->storeIds)
                ->where('id', $id)
                ->where('del_flag', 0)->first();

            if (!$machine) {
                return view('business.error', ['code' => 404, 'msg' => '该机台不存在']);
            }

            return view('business.edit-coin-machine', ['machine' => $machine, 'stores' => $this->stores]);

        } elseif ($request->isMethod('post')) {
            $data = $request->only('id', 'name', 'store', 'remarks');

            if (!intval($data['id'])) {
                return $this->response(403, '请求出错');
            }

            if (!trim_blanks($data['name'])) {
                return $this->response(403, '请输入机台名称');
            }

            if (!intval($data['store'])) {
                return $this->response(403, '请选择门店');
            }

            if (!in_array($data['store'], $this->storeIds)) {
                return $this->response(403, '所选门店不合法');
            }

            $store = DB::table('bus_stores')->whereIn('id', $this->storeIds)
                ->where('is_delete', 0)->where('id', $data['store'])->first();

            if (!$store) {
                return $this->response(403, '该门店不存在');
            }

            $machine = DB::table('coin_machine')
                ->whereIn('store_id', $this->storeIds)
                ->where('id', $data['id'])
                ->where('del_flag', 0)->first();

            if (!$machine) {
                return $this->response(403, '该机台不存在');
            }

            $save = [
                'name' => $data['name'],
                'store_id' => $store->id,
                'remarks' => $data['remarks']
            ];

            if (DB::table('coin_machine')->where('id', $machine->id)->update($save) !== false) {
                return $this->response(200, '机台修改成功', route('business.coin-machine-list'));
            } else {
                return $this->response(500, '机台修改失败');
            }

        }
    }

    /**
     * 切换机台启用/禁用状态
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function switchStatus(Request $request)
    {
        if ($request->isMethod('get')) {
            $id = $request->get('id');

            if (!intval($id)) {
                return $this->response(403, '请求出错');
            }

            $machine = DB::table('coin_machine')->whereIn('store_id', $this->storeIds)
                ->where('id', $id)->where('del_flag', 0)->first();

            if (!$machine) {
                return $this->response(404, '该机台不存在');
            }

            $usable = $machine->usable == 0 ? 1 : 0;

            if (DB::table('coin_machine')->where('id', $machine->id)->update(['usable' => $usable]) !== false) {
                return $this->response(200, '操作成功', route('business.coin-machine-list'));
            } else {
                return $this->response(500, '操作失败');
            }

        }
    }

    /**
     * 删除机台
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

            $machine = DB::table('coin_machine')->whereIn('store_id', $this->storeIds)
                ->where('id', $id)->where('del_flag', 0)->first();

            if (!$machine) {
                return $this->response(404, '该机台不存在');
            }

            if (DB::table('coin_machine')->where('id', $machine->id)->update(['del_flag' => time()])) {
                return $this->response(200, "机台删除成功", route('business.coin-machine-list'));
            } else {
                return $this->reponse(500, '机台删除失败');
            }

        }
    }

}

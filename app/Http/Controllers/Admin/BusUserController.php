<?php
/**
 * Created by PhpStorm.
 * User: AIMPER
 * Date: 2016/12/28
 * Time: 18:30
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PHPTree;
use App\Http\Models\Admin\UserModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helper;

class BusUserController extends Controller
{

    /**
     * 商户列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $builder = DB::table('bus_users as bu')
            ->leftJoin('bus_user_extend as bue', 'bue.bus_userid', '=', 'bu.id')
            ->leftJoin('bus_stores as bs', 'bs.userid', '=', 'bu.id')
            ->leftJoin(config('tables.base') . '.brand as b', 'b.id', '=', 'bue.brand_id')
            ->where('bu.pid', 0)
            ->orderBy('bu.regtime', 'desc');
        $limit = 20;

        if (intval($request->get('status'))) {
            $status = $request->get('status');
            if ($status == 1) {
                $builder->where('bu.status', 1);
            } elseif ($status == 2) {
                $builder->where('bu.status', 0);
            }
        }

        if (preg_replace('/\s/', '', $request->get('keyword'))) {
            $keyword = $request->get('keyword');
            $builder->where('bu.name', 'like', '%' . $keyword . '%');
        }

        $users = $builder->select([
            'bu.id', 'bu.name', 'bu.mobile', 'bu.status', 'bu.regtime', 'b.name as brand_name',
            DB::raw('COUNT(bs.id) as store_count')
        ])->groupBy('bu.id')->paginate($limit);

        return view('admin.bus-user-list', [
            'users' => $users,
            'status' => !empty($status) ? $status : 0,
            'keyword' => !empty($keyword) ? $keyword : ''
        ]);

    }

    /**
     * 恢复商家正常状态
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeStatus(Request $request)
    {
        if (!intval($request->get('id'))) {
            return $this->response(500, '内部错误');
        }
        $user = DB::table('bus_users')->where('id', $request->get('id'))->first();
        if (!$user) {
            return $this->response(403, '该商家账号不存在');
        }
        if ($user->status == 1) {
            return $this->response(403, '该商家账号已经是正常状态，无需进行该操作');
        }
        if (DB::table('bus_users')->where('id', $user->id)->update(['status' => 1])) {
            return $this->response(200, '账号状态设置成功', route('admin.bus-user-list'));
        } else {
            return $this->response(500, '账号状态设置失败');
        }
    }

    /**
     * 新建商家账号
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function createBusUser(Request $request)
    {
        if ($request->isMethod('get')) {
            $brands = DB::table(config('tables.base') . '.brand')->where('status', 1)->select('id', 'name')->get();
            return view('admin.create-bus-user', ['brands' => $brands]);

        } elseif ($request->isMethod('post')) {
            $data = $request->only('name', 'brand_id', 'mobile', 'password');

            if (!preg_replace('/\s/', '', $data['name'])) {
                return $this->response(403, '名字不能为空');
            }
            if (!intval($data['brand_id'])) {
                return $this->response(403, '请选择品牌');
            }
            if (strlen(preg_replace('/\s/', '', $data['name'])) < 5) {
                return $this->response(403, '名字至少5个字符');
            }
            if (preg_match('/#/', $data['name'])) {
                return $this->response(403, '商户账号名字不能有【#】特殊字符');
            }
            if (!preg_replace('/\s/', '', $data['mobile'])) {
                return $this->response(403, '手机号码不能为空');
            }
            if (!preg_match('/^1[34578]\d{9}$/', $data['mobile'])) {
                return $this->response(403, '手机号码格式不对');
            }
            if (!preg_replace('/\s/', '', $data['password'])) {
                return $this->response(403, '密码不能为空');
            }
            if (strlen(preg_replace('/\s/', '', $data['password'])) < 6) {
                return $this->response(403, '密码至少6个字符');
            }

            $name = preg_replace('/\s/', '', $data['name']);
            $repeat = DB::table('bus_users')->where('name', $name)->where('pid', 0)->count();
            if ($repeat) {
                return $this->response(403, '账号名字重复，请重新填写');
            }

            $mobile = $data['mobile'];
            $salt = random_string(6);
            $password = md5(md5(preg_replace('/\s/', '', $data['password'])) . $salt);
            $newUser = [
                'name' => $name,
                'mobile' => $mobile,
                'salt' => $salt,
                'password' => $password,
                'regtime' => time()
            ];
            DB::beginTransaction();
            try {
                $uid = DB::table('bus_users')->insertGetId($newUser);
                $extend = [
                    'bus_userid' => $uid,
                    'brand_id' => intval($data['brand_id']),
                ];
                DB::table('bus_user_extend')->insert($extend);
                DB::commit();
                return $this->response(200, '新账号创建成功', route('admin.bus-user-list'));
            } catch (Exception $e) {
                DB::rollBack();
                return $this->response(500, '新账号创建失败');
            }

        }
    }

    /**
     * 设置商户服务费率 -- 需求已改，本方法已作废
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function setServiceRate(Request $request)
    {
        if ($request->isMethod('get')) {
            if (!intval($request->get('id'))) {
                return $this->response(500, '内部错误');
            }
            $user = DB::table('bus_users as bu')->where('bu.id', $request->get('id'))->where('pid', 0)
                ->leftJoin('bus_user_extend as bue', 'bue.bus_userid', '=', 'bu.id')
                ->select('bu.id', 'bue.service_charge')->first();
            if (!$user) {
                return $this->response(403, '该商户不存在');
            }
            return view('admin.set-service-rate', ['user' => $user]);

        } elseif ($request->isMethod('post')) {
            $data = $request->only('id', 'rate');
            if (!intval($data['id'])) {
                return $this->response(500, '内部错误');
            }
            if (!is_numeric($data['rate'])) {
                return $this->response(403, '请输入合法数字');
            }
            $user = DB::table('bus_users')->where('id', $data['id'])->where('pid', 0)->first();
            if (!$user) {
                return $this->response(404, '该商户不存在');
            }
            // 检测是否已设置服务费率
            $exist = DB::table('bus_user_extend')->where('bus_userid', $user->id)->first();
            if ($exist) {
                if (DB::table('bus_user_extend')->where('bus_userid', $data['id'])->update(['service_charge' => $data['rate']]) !== false) {
                    return $this->response(200, '设置成功', route('admin.bus-user-list'));
                } else {
                    return $this->response(500, '设置失败');
                }
            } else {
                if (DB::table('bus_user_extend')->insert(['bus_userid' => $data['id'], 'service_charge' => $data['rate']])) {
                    return $this->response(200, '设置成功', route('admin.bus-user-list'));
                } else {
                    return $this->response(500, '设置失败');
                }
            }
        }
    }

    /**
     * 关联商家和旧门店数据
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function associateStore(Request $request)
    {
        if ($request->isMethod('get')) {
            if (!intval($request->get('id'))) {
                return view('admin.error', ['code' => 500, 'msg' => '内部错误']);
            }
            $user = DB::table('bus_users')->where('id', $request->get('id'))->first();
            if (!$user) {
                return view('admin.error', ['code' => 404, 'msg' => '该商家账号不存在']);
            }
            return view('admin.associate-store', ['user' => $user]);

        } elseif ($request->isMethod('post')) {
            if ($request->has('keyword') && $request->has('search')) { // 执行门店关键字检索处理
                $keyword = preg_replace('/\s/', '', $request->get('keyword'));
                $stores = DB::table('bus_stores')->where('name', 'like', '%' . $keyword . '%')
                    ->where('associated', 0)->select('id', 'name')->get();
                return response()->json($stores);

            } else {  // 执行关联操作
                $data = $request->only('id', 'store_ids');
                if (!intval($data['id'])) {
                    return $this->response(500, '内部错误');
                }

                if (empty($data['store_ids']) || !is_array($data['store_ids'])) {
                    return $this->response(403, '请选择要关联的门店');
                }

                $user = DB::table('bus_users as bu')
                    ->where('bu.id', $data['id'])
                    ->leftJoin('bus_user_extend as bue', 'bue.bus_userid', '=', 'bu.id')
                    ->select('bu.*', 'bue.brand_id')->first();
                if (!$user) {
                    return view('admin.error', ['code' => 404, 'msg' => '该商家账号不存在']);
                }
                // 根据用户ID获取其商家主账号ID
                $pid = $user->pid > 0 ? $user->pid : $user->id;

                foreach ($data['store_ids'] as $item) {
                    DB::table('bus_stores')->where('id', intval($item))->update(['userid' => $pid, 'brand_id' => $user->brand_id, 'associated' => 1]);
                }

                return $this->response(200, '关联成功', route('admin.bus-user-list'));

            }

        }
    }

    /**
     * 重置商户账号密码
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function resetPassword(Request $request)
    {
        if ($request->isMethod('get')) {
            if (!$request->get('id')) {
                return view('admin.error', ['code' => 500, 'msg' => '内部错误']);
            }
            $user = DB::table('bus_users')->where('id', $request->get('id'))->first();
            if (!$user) {
                return view('admin.error', ['code' => 404, 'msg' => '该商户账号不存在']);
            }
            return view('admin.reset-bus-password', ['user' => $user]);

        } elseif ($request->isMethod('post')) {
            $data = $request->only('id', 'password', 'confirm');
            if (!$data['id']) {
                return $this->response(500, '内部错误');
            }
            if (!$data['password'] && !$data['confirm']) {
                return $this->response(403, '请填写新密码和确认密码');
            }
            if ($data['password'] != $data['confirm']) {
                return $this->response(403, '两次密码不一致');
            }
            $user = DB::table('bus_users')->where('id', $data['id'])->first();
            if (!$user) {
                return $this->response(404, '该商户账号不存在');
            }
            $salt = random_string(6);
            $update = [
                'password' => $password = md5(md5(preg_replace('/\s/', '', $data['password'])) . $salt),
                'salt' => $salt
            ];
            if (DB::table('bus_users')->where('id', $user->id)->update($update) !== false) {
                return $this->response(200, '密码重置成功', route('admin.bus-user-list'));
            } else {
                return $this->response(500, '密码重置失败');
            }
        }
    }

    /**
     * 商户关联门店列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function associatedStores(Request $request)
    {
        if (!$request->get('id')) {
            return view('admin.error', ['code' => 500, 'msg' => '内部错误']);
        }
        $user = DB::table('bus_users')
            ->where('id', $request->get('id'))
            ->where('pid', 0)
            ->first();
        if (!$user) {
            return $this->response(404, '该商户不存在');
        }
        $stores = DB::table('bus_stores as bs')
            ->leftJoin(config('tables.base') . '.brand as b', 'b.id', '=', 'bs.brand_id')
            ->leftJoin(config('tables.base') . '.region as r', 'r.id', '=', 'bs.region_id')
            ->where('bs.userid', $user->id)
            ->select([
                'b.name as brand_name', 'bs.id', 'bs.name', 'bs.addtime', 'bs.status',
                'bs.mobile', 'bs.address', 'r.full_name as region'
            ])
            ->paginate(20);
        return view('admin.associated-stores', ['user' => $user, 'stores' => $stores]);
    }

    /**
     * 分配菜单角色，用于控制其菜单访问
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function allocateMenuRole(Request $request)
    {
        if ($request->isMethod('get')) {
            $id = $request->get('id');

            if (!intval($id)) {
                return view('admin.error', ['code' => 403, 'msg' => '请求出错']);
            }

            $user = DB::table('bus_users')
                ->where('pid', 0)
                ->where('id', $id)
                ->select('id', 'name', 'status')
                ->first();

            if (!$user) {
                return view('admin.error', ['code' => 404, 'msg' => '该商户账号不存在']);
            }

            if ($user->status == 2) {
                return view('admin.error', ['code' => 403, 'msg' => '该商户账号状态异常，不能执行此操作']);
            }

            $myRoles = DB::table('bus_menu_role_user')->where('userid', $user->id)->lists('role_id');

            $roles = DB::table('bus_menu_role')->where('status', 1)->select('id', 'name')->get();

            return view('admin.allocate-menu-role', ['user' => $user, 'roles' => $roles, 'myRoles' => $myRoles]);

        } elseif ($request->isMethod('post')) {
            $id = $request->get('id');
            $roleIds = $request->get('role_ids');

            if (!intval($id)) {
                return $this->response(403, '请求出错');
            }

            $user = DB::table('bus_users')->where('pid', 0)->where('id', $id)->first();
            if (!$user) {
                return $this->response(404, '该商家账号不存在');
            }
            if ($user->status != 1) {
                return $this->response(403, '该商家账号状态异常，不能执行此操作');
            }

            $records = [];
            if (!empty($roleIds) && is_array($roleIds)) {
                foreach ($roleIds as $roleId) {
                    $records[] = ['userid' => $user->id, 'role_id' => $roleId];
                }
            }

            DB::beginTransaction();
            try {
                DB::table('bus_menu_role_user')->where('userid', $user->id)->delete();
                DB::table('bus_menu_role_user')->insert($records);
                DB::commit();
                return $this->response(200, '角色分配成功', route('admin.bus-user-list'));
            } catch (Exception $e) {
                DB::rollBack();
                return $this->response(500, '角色分配失败');
            }

        }
    }

    /**
     * 修改商户账号绑定手机
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function resetMobile(Request $request)
    {
        if ($request->isMethod('get')) {
            $id = $request->get('id');

            if (!intval($id)) {
                return view('admin.error', ['code' => 500, 'msg' => '内部错误']);
            }

            $user = DB::table('bus_users')->where('id', $id)->select(['id', 'name', 'mobile'])->first();

            if (!$user) {
                return view('admin.error', ['code' => 404, 'msg' => '该商户账号不存在']);
            }

            return view('admin.reset-bus-mobile', ['user' => $user]);

        } elseif ($request->isMethod('post')) {
            $data = $request->only('id', 'mobile');

            if (!$data['id']) {
                return $this->response(500, '内部错误');
            }

            if (!$data['mobile']) {
                return $this->response(403, '请输入手机号码');
            }

            if (!Helper::isMobile($data['mobile'])) {
                return $this->response(403, '手机号码格式不正确');
            }

            $user = DB::table('bus_users')->find($data['id']);

            if (!$user) {
                return $this->response(404, '该商户账号不存在');
            }

            if (DB::table('bus_users')->where('id', $user->id)->update(['mobile' => $data['mobile']]) !== false) {
                return $this->response(200, '手机号修改成功', route('admin.bus-user-list'));
            } else {
                return $this->response(200, '手机号修改失败');
            }

        }
    }

}
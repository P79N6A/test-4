<?php
/**
 * Created by PhpStorm.
 * User: D.Rui
 * Date: 2016/11/2
 * Time: 18:07
 * Description: 商家 RBAC 角色类
 */

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\PHPTree;

class BusRoleController extends Controller
{

    /**
     * 角色列表
     */
    public function index()
    {
        $list = DB::table('bus_roles as br')
            ->leftJoin('bus_role_store_access_control as brs', function ($join) {
                $join->on('brs.role_id', '=', 'br.id');
            })->where('br.account_id', $this->parentUserId)
            ->select('br.*', DB::raw('COUNT(brs.store_id) as store_count'))
            ->groupBy('br.id')
            ->get();

        return view('business.role-list', ['roles' => $list]);
    }

    /**
     * 添加角色
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function add(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('business.add-role');
        } elseif ($request->isMethod('post')) {
            $data = $request->only('name', 'description');
            if (!preg_replace('/\s/', '', $data['name'])) {
                return $this->response(403, '角色名称不能为空');
            }
            $data['account_id'] = $this->parentUserId;
            $data['addtime'] = time();

            if (DB::table('bus_roles')->insert($data)) {

                \Operation::insert('bus_roles','添加角色['.$data['name'].']！',$data);

                return $this->response(200, '角色添加成功', route('business.role-list'));
            } else {
                return $this->response(500, '角色添加失败');
            }
        }
    }

    /**
     * 修改角色
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function edit(Request $request)
    {
        if ($request->isMethod('get')) {
            if (!$request->has('id')) {
                return view('business.error', ['code' => 403, 'msg' => '拒绝访问']);
            }

            $role = DB::table('bus_roles as br')->join('bus_users as bu', function ($join) {
                $join->on('bu.id', '=', 'br.account_id')->where('bu.id', '=', $this->parentUserId);
            })->select('br.*')->where('br.id', $request->get('id'))->first();

            if (!$role) {
                return view('business.error', ['code' => 404, 'msg' => '该角色不存在']);
            }
            return view('business.edit-role', ['role' => $role]);
        } elseif ($request->isMethod('post')) {
            $data = $request->only('id', 'name', 'description', 'status');

            if (!intval($data['id'])) {
                return $this->response(403, '拒绝访问');
            }
            if (!$data['name']) {
                return $this->response(403, '角色名称不能为空');
            }
            $id = $data['id'];
            unset($data['id']);

            $role = DB::table('bus_roles as br')->join('bus_users as bu', function ($join) {
                $join->on('bu.id', '=', 'br.account_id')->where('bu.id', '=', $this->parentUserId);
            })->select('br.*')->where('br.id', $request->get('id'))->first();

            if (!$role) {
                return $this->response(404, '该角色不存在');
            }

            $before_data = $role;

            if (DB::table('bus_roles')->where('id', $id)->update($data) !== false) {

                \Operation::update('bus_roles','修改角色['.$before_data->name.']！',$before_data,$data);

                return $this->response(200, '角色修改成功', route('business.role-list'));
            } else {
                return $this->response(500, '角色修改失败');
            }
        }
    }

    /**
     * 删除角色
     */
    public function delete(Request $request)
    {
        if (!intval($request->has('id'))) {
            return $this->response(403, '拒绝访问');
        }
        $id = $request->get('id');

        $role = DB::table('bus_roles as br')->join('bus_users as bu', function ($join) {
            $join->on('bu.id', '=', 'br.account_id')->where('bu.id', '=', $this->parentUserId);
        })->select('br.*')->where('br.id', $id)->first();

        if (!$role) {
            return $this->response(404, '该角色不存在');
        }

        // 删除操作:
        // 解除相应角色用户关联
        // 解除角色权限关联
        // 删除角色
        DB::transaction(function () use ($id) {
            DB::table('bus_role_user')->where('role_id', $id)->delete();
            DB::table('bus_role_permission')->where('role_id', $id)->delete();
            DB::table('bus_roles')->where('id', $id)->delete();
        });


        \Operation::delete('bus_roles','删除角色['.$role->name.']！',$role);

        return $this->response(200, '角色删除成功', route('business.role-list'));
    }

    /**
     * 分配权限
     */
    public function allocatePermission(Request $request)
    {
        if ($request->isMethod('get')) {
            if (!intval($request->get('id'))) {
                return view('business.error', ['code' => 403, 'msg' => '拒绝访问']);
            }

            $id = $request->get('id');

            // 检查该角色是否是本商家所有
            $role = DB::table('bus_roles')->where('account_id', $this->parentUserId)
                ->where('id', $request->get('id'))->first();
            if (!$role) {
                return view('business.error', ['code' => 404, 'msg' => '找不到该角色']);
            }

            // 已分配权限
            $myPerms = DB::table('bus_role_permission')->where('role_id', $id)->lists('permission_name');

            // 全部权限
//            $perms = DB::table('bus_menus')->where('status',1)->where('assignable',1)->get();
            $perms = DB::table('bus_menu_role_relation as bmrr')
                ->join('bus_menus as bm', function ($join) {
                    $join->on('bm.id', '=', 'bmrr.menu_id')->where('bm.status', '=', 1)->where('bm.assignable', '=', 1);
                })
                ->whereIn('bmrr.role_id', $this->roleIds)
                ->select('bm.id', 'bm.parent_id', 'bm.name', 'bm.action')
                ->orderBy('bm.display_order')->orderBy('bm.id', 'desc')
                ->distinct()
                ->get();
            $appPerms = DB::table('bus_app_permission')->get();
            $allocatedAppPerms = DB::table('bus_app_role_permission')->where('role_id', $id)->lists('permission_id');
            $perms = PHPTree::makeTree($perms);
            return view('business.allocate-permission-new', [
                'perms' => $perms,
                'role' => $role,
                'myPerms' => $myPerms,
                'appPerms' => $appPerms,
                'allocatedAppPerms' => $allocatedAppPerms,
            ]);

        } elseif ($request->isMethod('post')) {
            $data = $request->only('role_id', 'menus', 'app-perms');

            if (!intval($data['role_id'])) {
                return $this->response(403, '拒绝访问');
            }
            $role_id = $request->get('role_id');

            // 传递过来的网站权限id
            if ($request->has('menus')) {
                $menus = array_unique($data['menus']);
                // 过滤掉不可分配的权限，主要针对人员管理那块
                $menus = DB::table('bus_menus')->whereIn('action', $menus)->where('assignable', 1)->lists('action');
            } else {
                $menus = [];
            }
            // 已经分配的网站权限id
            $oldIds = DB::table('bus_role_permission')->where('role_id', $role_id)
                ->lists('permission_name');
            // 新增的网站权限id
            $addMenus = array_diff($menus, $oldIds);
            // 删除的网站权限id
            $delMenus = array_diff($oldIds, $menus);

            $appMenus = $request->has('app-perms') ? $request->get('app-perms') : [];
            $allocatedAppPerms = DB::table('bus_app_role_permission')->where('role_id', $role_id)->lists('permission_id');
            $addAppMenus = array_diff($appMenus, $allocatedAppPerms);
            $delAppMenus = array_diff($allocatedAppPerms, $appMenus);

            $comment = '权限分配！';

            // 先删除，后新增
            if ($delMenus) {
                foreach ($delMenus as $delMenu) {
                    DB::table('bus_role_permission')->where('role_id', $role_id)
                        ->where('permission_name', $delMenu)->delete();
                }

                $delMenus_str = implode(',', $delMenus);
                $comment .= '删除权限：' . $delMenus_str ."！";
            }
            if ($addMenus) {
                foreach ($addMenus as $addMenu) {
                    DB::table('bus_role_permission')->insert(['role_id' => $role_id, 'permission_name' => $addMenu]);
                }

                $addMenus_str = implode(',', $addMenus);
                $comment .= '增加权限：' . $addMenus_str ."！";
            }
            if ($delAppMenus) {
                DB::table('bus_app_role_permission')->where('role_id', $role_id)->whereIn('permission_id', $delAppMenus)->delete();
            }
            if ($addAppMenus) {
                foreach ($addAppMenus as $addAppMenu) {
                    DB::table('bus_app_role_permission')->insert(['role_id' => $role_id, 'permission_id' => $addAppMenu]);
                }
            }

            \Operation::delete('bus_role_permission',$comment);

            return $this->response(200, '权限分配成功', route('business.role-list'));
        }
    }

    /**
     * 对角色分配数据访问权限
     */
    public function allocateDataAccessPermission(Request $request)
    {
        if ($request->isMethod('get')) {
            if (!$roleId = intval($request->get('id'))) {
                if ($request->isMethod('get')) {
                    return view('business.error', ['code' => 500, 'msg' => '内部错误']);
                } elseif ($request->isMethod('post')) {
                    return $this->response(500, '内部错误');
                }
            }

            // 检查角色从属
            $role = DB::table('bus_roles')->where('id', $roleId)
                ->where('account_id', $this->parentUserId)->first();
            if (!$role) {
                return view('business.error', ['code' => 404, 'msg' => '该角色不存在']);
            }
            $stores = $this->stores;
            $allocatedStores = DB::table('bus_role_store_access_control')->where('role_id', $roleId)->lists('store_id');

            return view('business.allocate-data-access-permission', [
                'role' => $role,
                'stores' => $stores,
                'alloStores' => $allocatedStores
            ]);

        } elseif ($request->isMethod('post')) {
            if (!$id = intval($request->get('id'))) {
                return $this->response(500, '内部错误');
            }

            if (!$storeIds = $request->has('store_ids') || !is_array($request->get('store_ids'))) {
                // return $this->response(403,'请选择门店');
            }
            // 删除旧的角色
            DB::table('bus_role_store_access_control')->where('role_id', $id)->delete();

            $data = [];
            if ($request->has('store_ids')) {
                foreach ($request->get('store_ids') as $store) {
                    $data[] = ['role_id' => $id, 'store_id' => $store];
                }
            }
            if ($data) {
                DB::table('bus_role_store_access_control')->insert($data);
            }
            return $this->response(200, '权限分配成功', route('business.role-list'));

        }


    }


}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\PHPTree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class BusMenuRoleController extends Controller
{
    /**
     * 角色列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $list = DB::table('bus_menu_role')->orderBy('id', 'asc')->get();
        return view('admin.bus-menu-role-list', ['roles' => $list]);
    }

    /**
     * 创建角色
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function add(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('admin.add-bus-menu-role');

        } elseif ($request->isMethod('post')) {
            $data = $request->only('name', 'description');

            if (!trim_blanks($data['name'])) {
                return $this->response(403, '角色名称不能为空');
            }

            $data['create_date'] = date('Y-m-d H:i:s');

            if (DB::table('bus_menu_role')->insert($data)) {
                return $this->response(200, '菜单角色创建成功', route('admin.bus-menu-role-list'));
            } else {
                return $this->response(500, '菜单角色创建失败');
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
            $id = $request->get('id');

            if (!intval($id)) {
                return view('admin.error', ['code' => 403, 'msg' => '请求出错']);
            }

            $role = DB::table('bus_menu_role')->find($id);

            if (!$role) {
                return view('admin.error', ['code' => 404, 'msg' => '该角色不存在']);
            }

            return view('admin.edit-bus-menu-role', ['role' => $role]);

        } elseif ($request->isMethod('post')) {
            $data = $request->only('id', 'name', 'description', 'status');

            if (!intval($data['id'])) {
                return $this->response(403, '请求出错');
            }

            if (!trim_blanks($data['name'])) {
                return $this->response(403, '角色名称不能为空');
            }

            $role = DB::table('bus_menu_role')->find($data['id']);

            if (!$role) {
                return $this->response(404, '该角色不存在');
            }

            unset($data['id']);

            if (DB::table('bus_menu_role')->where('id', $role->id)->update($data)) {
                return $this->response(200, '菜单角色修改成功', route('admin.bus-menu-role-list'));
            } else {
                return $this->response(500, '菜单角色修改失败');
            }
        }
    }

    /**
     * 删除角色
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        if ($request->isMethod('get')) {
            $id = $request->get('id');

            if (!intval($id)) {
                return $this->response(403, '请求出错');
            }

            $role = DB::table('bus_menu_role')->find($id);

            if (!$role) {
                return $this->response(404, '该角色不存在');
            }

            if (DB::table('bus_menu_role')->delete($role->id)) {
                return $this->response(200, '角色删除成功', route('admin.bus-menu-role-list'));
            } else {
                return $this->response(500, '角色删除失败');
            }
        }
    }

    /**
     * 分配菜单权限
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function allocateMenu(Request $request)
    {
        if ($request->isMethod('get')) {
            $id = $request->get('id');

            if (!intval($id)) {
                return view('admin.error', ['code' => 403, 'msg' => '请求出错']);
            }

            $role = DB::table('bus_menu_role')->find($id);

            if (!$role) {
                return view('admin.error', ['code' => 404, 'msg' => '该角色不存在']);
            }

            if ($role->status == 2) {
                return view('admin.error', ['code' => 403, 'msg' => '该角色为禁用状态，不能分配菜单']);
            }

            $menus = DB::table('bus_menus')
                ->where('status', 1)
                ->select('parent_id', 'id', 'name', 'description')
                ->orderBy('display_order')
                ->orderBy('id', 'desc')->get();

            $menus = PHPTree::makeTree($menus);

            $myPerms = DB::table('bus_menu_role_relation')->where('role_id', $role->id)->lists('menu_id');

            return view('admin.allocate-menu-for-role', ['role' => $role, 'perms' => $menus, 'myPerms' => $myPerms]);

        } elseif ($request->isMethod('post')) {
            $data = $request->only('id', 'menus');

            if (!intval($data['id'])) {
                return $this->response(403, '请求出错');
            }

            $role = DB::table('bus_menu_role')->find($data['id']);

            if (!$role) {
                return view('admin.error', ['code' => 404, 'msg' => '该角色不存在']);
            }

            if ($role->status == 2) {
                return view('admin.error', ['code' => 403, 'msg' => '该角色为禁用状态，不能分配菜单']);
            }

            $records = [];
            if (!empty($data['menus']) && is_array($data['menus'])) {
                foreach ($data['menus'] as $datum) {
                    $records[] = ['role_id' => $role->id, 'menu_id' => $datum];
                }
            }

            $allocated = DB::table('bus_menu_role_relation')->where('role_id', $role->id)->lists('menu_id');
            $deleted = array_diff($allocated, $data['menus']);

            DB::beginTransaction();
            try {
                DB::table('bus_menu_role_relation')->where('role_id', $role->id)->delete();
                if ($records) {
                    DB::table('bus_menu_role_relation')->insert($records);
                }
                $this->changeChildPerms($role->id, $deleted);
                DB::commit();
                return $this->response(200, '菜单权限分配成功', route('admin.bus-menu-role-list'));
            } catch (Exception $e) {
                DB::rollBack();
                return $this->response(200, '菜单权限分配失败');
            }

        }
    }

    /**
     * 在分配主账号权限的同时，把子账号的权限值也同步，主要是针对主账号权限减少的情况
     * @param $roleId int 商户主账号角色ID
     * @param $perms array 菜单权限ID数组
     * @return void
     */
    private function changeChildPerms($roleId, $perms)
    {
        // actions
        $actions = DB::table('bus_menus')->whereIn('id', $perms)->lists('action');
        // 商户主账号ID
        $users = DB::table('bus_menu_role_user')->where('role_id', $roleId)->lists('userid');
        // 主账号创建的子账号角色ID
        $roles = DB::table('bus_roles')->whereIn('account_id', $users)->lists('id');
        // 删除权限
        DB::table('bus_role_permission')->whereIn('role_id', $roles)->whereIn('permission_name', $actions)->delete();
    }

}

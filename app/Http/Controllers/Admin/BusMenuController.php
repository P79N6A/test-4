<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\PHPTree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class BusMenuController extends Controller
{
    /**
     * 菜单列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $list = DB::table('bus_menus')->orderBy('display_order')->orderBy('id', 'desc')->get();
        $list = PHPTree::makeTree($list);
        return view('admin.bus-menu-list', ['menus' => $list]);
    }

    public function add(Request $request)
    {
        if ($request->isMethod('get')) {
            $menus = DB::table('bus_menus')
//                ->where('status', 1)
                ->orderBy('display_order')
                ->orderBy('id', 'desc')
                ->select('parent_id', 'id', 'name')
                ->get();

            $menus = PHPTree::makeTree($menus);

            return view('admin.add-bus-menu', ['menus' => $menus]);

        } elseif ($request->isMethod('post')) {
            $data = $request->only([
                'parent_id', 'action', 'name', 'description', 'display', 'display_order', 'assignable'
            ]);

            if (!trim($data['action'])) {
                return $this->response(403, '请输入菜单操作');
            }
            if (!trim($data['name'])) {
                return $this->response(403, '请输入菜单名称');
            }

            $repeat = DB::table('bus_menus')->where('action', trim($data['action']))->first();

            if ($repeat) {
                return $this->response(403, '菜单操作重复');
            }

            $data['display_order'] = $data['display_order'] ? 1 : 0;
            $data['addtime'] = time();

            if (DB::table('bus_menus')->insert($data)) {
                return $this->response(200, '菜单创建成功', route('admin.bus-menu-list'));
            } else {
                return view('admin.add-bus-menu');
                return $this->response(200, '菜单创建失败');
            }

        }
    }

    public function edit(Request $request)
    {
        if ($request->isMethod('get')) {

            $id = $request->get('id');

            if (!intval($id)) {
                return view('admin.error', ['code' => 403, 'msg' => '请求出错']);
            }

            $menu = DB::table('bus_menus')->find($id);

            if (!$menu) {
                return view('admin.error', ['code' => 404, 'msg' => '该菜单不存在']);
            }

            $menus = DB::table('bus_menus')
//                ->where('status', 1)
                ->orderBy('display_order')
                ->orderBy('id', 'desc')
                ->select('parent_id', 'id', 'name')
                ->get();

            $menus = PHPTree::makeTree($menus);

            return view('admin.edit-bus-menu', ['detail' => $menu, 'menus' => $menus]);

        } elseif ($request->isMethod('post')) {
            $data = $request->only([
                'id', 'parent_id', 'action', 'name', 'description', 'display', 'display_order', 'status', 'assignable'
            ]);

            if (!intval($data['id'])) {
                return $this->response(403, '请求出错');
            }

            if (!trim($data['action'])) {
                return $this->response(403, '请输入菜单操作');
            }
            if (!trim($data['name'])) {
                return $this->response(403, '请输入菜单名称');
            }

            $menu = DB::table('bus_menus')->find($data['id']);

            if (!$menu) {
                return $this->response(404, '该菜单不存在');
            }

            $repeat = DB::table('bus_menus')
                ->where('id', '!=', $menu->id)
                ->where('action', trim($data['action']))
                ->first();

            if ($repeat) {
                return $this->response(403, '菜单操作重复');
            }

            unset($data['id']);

            if (DB::table('bus_menus')->where('id', $menu->id)->update($data) !== false) {
                $this->recursiveChangeStatus($menu->id, $data['status']);
                return $this->response(200, '菜单修改成功', route('admin.bus-menu-list'));
            } else {
                return $this->response(500, '菜单修改失败');
            }

        }
    }

    public function delete(Request $request)
    {
        if ($request->isMethod('get')) {
            $id = $request->get('id');

            if (!intval($id)) {
                return $this->response(403, '请求出错');
            }

            $menu = DB::table('bus_menus')->where('id', $id)->first();

            if (!$menu) {
                return $this->response(404, '该菜单不存在');
            }

            if (DB::table('bus_menus')->delete($menu->id)) {
                return $this->response(200, '菜单删除成功', route('admin.bus-menu-list'));
            } else {
                return $this->response(500, '菜单删除失败');
            }
        }

    }

    /**
     * 递归修改菜单的状态
     * @param $menuId
     * @param $status
     * @return bool
     */
    private function recursiveChangeStatus($menuId, $status)
    {
        DB::table('bus_menus')->where('id', $menuId)->orWhere('parent_id', $menuId)->update(['status' => $status]);
        $children = DB::table('bus_menus')->where('parent_id', $menuId)->lists('id');
        foreach ($children as $child) {
            $this->recursiveChangeStatus($child, $status);
        }
        return true;
    }

}

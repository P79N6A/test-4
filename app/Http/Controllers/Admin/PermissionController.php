<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\PHPTree;
use app\Http\Models\Admin\RoleModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\PermissionModel as Permission;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{

    /**
     * 权限列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $res = DB::table('admin_permissions')->orderBy('display_order','asc')->orderBy('id','ASC')->get();
        $tree = PHPTree::makeTree($res);
        return view('admin.permission-list',['permissions'=>$tree]);
    }

    /**
     * 添加权限
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function add(Request $request){
        if($request->isMethod('get')){

            $tree = $this->getPermissionTree();
            return view('admin.add-permission',['tree'=>$tree]);
        }elseif($request->isMethod('post')){
            $data = $request->only('parent_id','name','display_name','description','status','disable','display_order');

            if(!$name = preg_replace('/\s/','',$data['name'])){
                return $this->response(403,'权限标识符不能为空');
            }
            if(!$display_name = preg_replace('/\s/','',$data['display_name'])){
                return $this->response(403,'权限名称不能为空');
            }

            $repeat = Permission::where('name',$data['name'])->count();
            if($repeat){
                return $this->response(403,'权限标识符重复');
            }

            $permission = new Permission();
            $permission->parent_id = intval($data['parent_id']);
            $permission->name = $name;
            $permission->display_name = $display_name;
            $permission->description = $data['description'];
            $permission->status = intval($data['status']);
            $permission->display_order = intval($data['display_order']);
            $permission->disable = intval($data['disable']);

            if($permission->save()){
                // 给超级管理员角色添加权限
                $superRole = DB::table('admin_roles')->where('root',1)->first();
                DB::table('admin_permission_role')->insert(['permission_id'=>$permission->id,'role_id'=>$superRole->id]);
                return $this->response(200,'权限添加成功',route('admin.permission-list'));
            }else{
                return $this->response(500,'权限添加失败');
            }
        }
   }

    /**
     * 修改权限
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function edit(Request $request){
       if($request->isMethod('get')){
           if(!intval($request->get('id'))){
                return view('admin.error',['code'=>500,'msg'=>'内部错误']);
            }
           $perm = Permission::find($request->get('id'));
           if(!$perm){
               return view('admin.error',['code'=>404,'msg'=>'该权限不存在']);
           }

           $tree = $this->getPermissionTree();
           return view('admin.edit-permission',['permission'=>$perm,'tree'=>$tree]);

       }elseif($request->isMethod('post')){
           $data = $request->only('id','parent_id','name','display_name','description','status','disable','display_order');

           if(!intval($data['id'])){
               return $this->response(500,'内部错误');
           }
           if(!$name = preg_replace('/\s/','',$data['name'])){
               return $this->response(403,'权限标识符不能为空');
           }
           if(!$display_name = preg_replace('/\s/','',$data['display_name'])){
               return $this->response(403,'权限名称不能为空');
           }

           $permission = Permission::find($data['id']);
           if(!$permission){
               return $this->response(404,'该权限不存在');
           }
           $permission->parent_id = intval($data['parent_id']);
           $permission->name = $name;
           $permission->display_name = $display_name;
           $permission->description = $data['description'];
           $permission->status = intval($data['status']);
           $permission->display_order = intval($data['display_order']);
           $permission->disable = intval($data['disable']);
           if($permission->save()){
               $this->recursiveModifyPermission($permission->id, 'disable', $permission->disable);
               return $this->response(200,'权限修改成功',route('admin.permission-list'));
           }else{
               return $this->response(500,'权限修改失败');
           }
       }
    }

    /**
     * 删除权限
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request){
        if(!intval($request->get('id'))){
            return $this->response(500,'内部错误');
        }
        $perm = Permission::find($request->get('id'));
        if(!$perm){
            return $this->response(403,'该权限不存在');
        }
        if(DB::table('admin_permissions')->where('id',$perm->id)->delete()){
            // 删除权限角色关联
            DB::table('admin_permission_role')->where('permission_id',$perm->id)->delete();
            return $this->response(200,'权限删除成功',route('admin.permission-list'));
        }else{
            return $this->response(500,'权限删除失败');
        }

    }

    /**
     * 获取树形权限树
     * @return mixed
     */
    private function getPermissionTree(){
        $res = DB::table('admin_permissions')->orderBy('display_order','asc')->orderBy('id','asc')->get();
        $tree = PHPTree::makeTree($res);
        return $tree;
    }

    /**
     * 递归修改权限属性
     * 目前权限的分级最多支持三级
     * 例如：禁用某菜单则同时禁用其子菜单
     * @param int $permissionId
     * @param string $key 属性名
     * @param int $value 属性值
     * @return bool
     */
    private function recursiveModifyPermission($permissionId, $key, $value){
        // $permissionId 为一级权限
        // 二级权限
        $children = DB::table('admin_permissions')
            ->where('parent_id',$permissionId)
            ->lists('id');
        $grandchildren = DB::table('admin_permissions')
            ->whereIn('parent_id',$children)
            ->lists('id');

        $ids = array_merge([$permissionId],$children,$grandchildren);

        if(DB::table('admin_permissions')->whereIn('id',$ids)->update([$key=>$value]) !== false){
            return true;
        }
        return false;

    }

}

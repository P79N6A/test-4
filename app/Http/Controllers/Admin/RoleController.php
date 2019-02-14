<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PHPTree;
use App\Http\Models\Admin\PermissionModel;
use Illuminate\Http\Request;
use App\Http\Models\Admin\RoleModel;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    /**
     * 角色列表
    */
    public function index()
    {
        $res = RoleModel::all();
        return view('admin.role-list-new',['roles'=>$res]);
    }

    /**
     * 创建角色
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function add(Request $request){
        if($request->isMethod('get')){
            return view('admin.add-role');
        }elseif($request->isMethod('post')){
            $data = $request->only('name','display_name','status','description');

            if(!$data['name']){
                return $this->response(403,'请输入角色唯一标识符');
            }
            if(!$data['display_name']){
                return $this->response(403,'请输入角色名称');
            }

            $role = new RoleModel();
            $role->name = $data['name'];
            $role->display_name = $data['display_name'];
            $role->description = $data['description'];
            $role->status = $data['status'];
            if($role->save()){
                return $this->response(200,'角色创建成功',route('admin.role-list'));
            }else{
                return $this->response(500,'角色创建失败');
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
        if($request->isMethod('get')){
            if(!$request->has('id') && !intval($request->get('id'))){
                return view('admin.error',['code'=>500,'msg'=>'内部错误']);
            }
            $role = RoleModel::find($request->get('id'));
            if(!$role){
                return view('admin.error',['code'=>404,'msg'=>'该角色不存在']);
            }
            if($role->root == 1){
                return view('admin.error',['code'=>403,'msg'=>'超级管理员不能被修改']);
            }
            return view('admin.edit-role',['role'=>$role]);

        }elseif($request->isMethod('post')){
            $data = $request->only('id','name','display_name','description','status');

            if(!intval($data['id'])){
                return $this->response(500,'内部错误');
            }
            if(!preg_replace('/\s/','',$data['name'])){
                return $this->response(403,'角色唯一标识符不能为空');
            }
            if(!preg_replace('/\s/','',$data['display_name'])){
                return $this->response(403,'角色名称不能为空');
            }

            $role = RoleModel::find($data['id']);

            if(!$role){
                return $this->response(404,'该角色不存在');
            }
            if($role->root == 1){
                return $this->response(403,'超级管理员不能被修改');
            }
            $nameRepeat = RoleModel::where('name',$data['name'])->where('id','!=',$data['id'])->count();
            if($nameRepeat){
                return $this->response(403,'标识符已重复，请重新填写');
            }

            $role->name = $data['name'];
            $role->display_name = $data['display_name'];
            $role->description = $data['description'];
            $role->status = $data['status'];

            if($role->save()){
                return $this->response(200,'修改成功',route('admin.role-list'));
            }else{
                return $this->response(500,'修改失败');
            }

        }
    }

    /**
     * 删除角色
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request){
        if(!intval($request->get('id'))){
            return $this->response(500,'内部错误');
        }

        $role = RoleModel::find($request->get('id'));
        if(!$role){
            return $this->response(404,'该角色不存在');
        }
        if($role->root == 1){
            return $this->response(403,'超级管理员角色不能被删除');
        }

        if($role->delete()){
            // 删除对应的 角色权限关联 和 角色用户关联
            DB::table('admin_permission_role')->where('role_id',$role->id)->delete();
            DB::table('admin_role_user')->where('role_id',$role->id)->delete();
            return $this->response(200,'删除成功',route('admin.role-list'));
        }else{
            return $this->response(500,'删除失败');
        }

    }

    /**
     * 分配权限
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function allocatePermission(Request $request){
        if($request->isMethod('get')){
            if(!intval($request->get('id'))){
                return view('admin.error',['code'=>500,'msg'=>'内部错误']);
            }
            $role = RoleModel::find($request->get('id'));
            if(!$role){
                return view('admin.error',['code'=>404,'msg'=>'该角色不存在']);
            }
            if($role->root == 1){
                return view('admin.error',['code'=>403,'msg'=>'超级管理员角色的权限不能被修改']);
            }

            // 全部权限
            // $perms = PermissionModel::all();
            $perms = DB::table('admin_permissions')->orderBy('display_order','asc')->orderBy('id','asc')->get();
            $tree = PHPTree::makeTree($perms);
            // 已分配权限
            $allocatedPerms = DB::table('admin_permission_role')->where('role_id',$role->id)->lists('permission_id');
            return view('admin.allocate-permission-new',['role'=>$role,'perms'=>$tree,'allocatedPerms'=>$allocatedPerms]);

        }elseif($request->isMethod('post')){
            $data = $request->only('id','perms');
            if(!intval($data['id']) || (!empty($data['perms']) && !is_array($data['perms'])) ){
                return $this->response(500,'内部错误');
            }

            $role = RoleModel::find($data['id']);
            if(!$role){
                return $this->response(404,'该角色不存在');
            }
            if($role->root == 1){
                return $this->response(403,'超级管理员角色的权限不能被修改');
            }

            // 删除旧权限关联
            DB::table('admin_permission_role')->where('role_id',$data['id'])->delete();
            if(!empty($data['perms'])){
                foreach($data['perms'] as $perm){
                    $records[] = ['role_id'=>$data['id'],'permission_id'=>$perm];
                }
                if(!empty($records)){
                    DB::table('admin_permission_role')->insert($records);
                }
            }
            return $this->response(200,'权限分配成功',route('admin.role-list'));
        }
    }


}

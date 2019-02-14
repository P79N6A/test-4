<?php

namespace App\Http\Controllers\Merchant;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Http\Controllers\BaseController;
use App\Http\Models\Merchant\RoleModel;
use Illuminate\Support\Facades\DB;

class RoleController extends BaseController
{
    /**
     * 角色列表
    */
    public function index()
    {
        $res = RoleModel::all();
        return view('merchant.rolelist',['roles'=>$res]);
    }

    /**
     * 创建角色表单
     */
    public function create()
    {
        return view('merchant.addrole');
    }

    /**
     * 创建角色
     * $array 包含三个元素
     * name：角色唯一标识符，必须
     * display_name：人类可读角色名，可选
     * description：角色描述，可选
     */
    public function store(Request $request){
        $data = $request->all();
        if(!$data['name']){
            return response()->json(['code'=>403,'msg'=>'缺少角色名称']);
        }
        // 检查角色标识符是否重复
        $count = Role::where('name',$data['name'])->count();
        if($count){
            return response()->json(['code'=>'403','msg'=>'角色标识符重复']);
        }

        $role = new Role();
        $role->name = $data['name'];
        $role->display_name = $data['display_name'];
        $role->description = $data['description'];
        if($role->save()){
            return response()->json(['code'=>200,'msg'=>'角色添加成功','url'=>'/rolelist']);
        }else{
            return response()->json(['code'=>500,'msg'=>'角色添加失败']);
        }
    }

    /**
     * 为修改角色信息显示表单
     */
    public function edit(Request $request)
    {
        $id = intval($request->get('id'));
        if(!$id){
            return view('merchant.500');
        }
        $role = Role::where('id',$id)->first();
        if(!$role){
            return response()
                ->view('merchant.error',['code'=>500,'msg'=>'内部错误'])
                ->setStatusCode(500);
        }
        return view('merchant.editrole',['role'=>$role]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $data = $request->all();
        $id = intval($data['id']);
        if(!$data['name']){
            return response()->json(['code'=>403,'msg'=>'标识符不能为空']);
        };
        if(!intval($id)){
            return response()->json(['code'=>500,'msg'=>'内部错误']);
        }
        if(!$role = Role::find($id)){
            return response()->json(['code'=>500,'msg'=>'内部错误']);
        }
        $duplicate = Role::where('name',$data['name'])->where('id','!=',$id)->first();
        if($duplicate){
            return response()->json(['code'=>403,'msg'=>'角色名称不能重复']);
        }
        if($data['name'] != $role->name){
            $role->name = $data['name'];
        }
        if($data['display_name']){
            $role->display_name = $data['display_name'];
        }
        if($data['description']){
            $role->description = $data['description'];
        }
        if($role->save()){
            return response()->json(['code'=>200,'msg'=>'角色修改成功','url'=>route('merchant.rolelist')]);
        }else{
            return response()->json(['code'=>500,'msg'=>'角色修改失败']);
        }
    }

    /**
     * 删除角色
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $id = intval($request->get('id'));
        if(!$id){
            return response()->json(['code'=>404,'msg'=>'没有该角色']);
        }
        if($id == 1){
            return response()->json(['code'=>401,'msg'=>'超级管理员角色不能删除']);
        }

        DB::transaction(function() use($id) {
            // 解除对应 角色-用户 关联
            DB::table('role_user')->where('role_id',$id)->delete();
            // 解除对应 角色-权限 关联
            DB::table('permission_role')->where('role_id',$id)->delete();
            // 删除角色
            RoleModel::find($id)->delete();
        });

        return response()->json(['code'=>200,'msg'=>'角色删除成功','url'=>route('merchant.rolelist')]);
    }
}

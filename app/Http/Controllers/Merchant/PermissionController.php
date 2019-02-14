<?php

namespace App\Http\Controllers\Merchant;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Models\Merchant\PermissionModel as Permission;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    /**
     * 权限列表
     *
     */
    public function index()
    {
        $res = Permission::paginate(20);
        return view('merchant.permlist',['permissions'=>$res]);
    }

    /**
     * 显示新增权限表单
     */
    public function create()
    {
        return view('merchant.addperm');
    }

    /**
     * 新增权限
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        if(!$data['name']){
            return response()->json(['code'=>403,'msg'=>'缺少权限名称']);
        }
        $permission = new Permission();
        $permission->name = $data['name'];
        $permission->display_name = $data['display_name'];
        $permission->description = $data['description'];
        if($permission->save()){
            return response()->json(['code'=>200,'msg'=>'权限新增成功','url'=>route('merchant.permlist')]);
        }else{
            return response()->json(['code'=>500,'msg'=>'新增权限失败']);
        }
    }

    /**
     * 修改权限页面
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $id = intval($request->get('id'));
        if(!$id){
            return response()
                ->view('merchant.error',['code'=>500,'msg'=>'内部错误'])
                ->setStatusCode(500);
        }
        $perm = Permission::find($id);
        return view('merchant.editperm',['permission'=>$perm]);
    }

    /**
     * 更新权限信息
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $data = $request->all();
        if(!intval($request->get('id')) || !$request->get('name')){
            return response()->json(['code'=>500,'msg'=>'内部错误']);
        }
        $perm = Permission::find(intval($request->get('id')));
        $perm->name = $data['name'];
        if($request->has('display_name')){
            $perm->display_name = $data['display_name'];
        }
        if($request->has('description')){
            $perm->description = $data['description'];
        }

        if($perm->save()){
            return response()->json(['code'=>200,'msg'=>'权限修改成功','url'=>route('merchant.permlist')]);
        }else{
            return response()->json(['code'=>500,'msg'=>'权限修改失败']);
        }
    }

    /**
     * 删除权限
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $id = intval($request->get('id'));
        if(!$id){
            return response()->json(['code'=>500,'msg'=>'内部错误']);
        }
        DB::transaction(function() use ($id){
            // 删除对应 权限-角色 关联
            DB::table('permission_role')->where('permission_id',$id)->delete();
            // 删除权限
            Permission::where('id',$id)->delete();
        });
        return response()->json(['code'=>200,'msg'=>'删除成功','url'=>route('merchant.permlist')]);
    }






}

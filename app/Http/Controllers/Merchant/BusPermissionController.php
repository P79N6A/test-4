<?php
/**
 * Created by PhpStorm.
 * User: D.Rui
 * Date: 2016/11/2
 * Time: 18:09
 * Description: 商家 RBAC 权限类
 */

namespace App\Http\Controllers\Merchant;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BusPermissionController extends Controller
{
    protected $table = 'bus_permissions';

    /**
     * 权限列表
    */
    public function index(){
        $perms = DB::table($this->table)->where('account_id',$this->parentUserId)->get();
        return view('merchant.permission-list',['permissions'=>$perms]);
    }

    /**
     * 创建权限表单
    */
    public function create(){
        return view('merchant.add-permission');
    }

    /**
     * 存储新建的权限
    */
    public function store(Request $request){
        $data = $request->all();
        if(!$data['name']){
            return $this->response(403,'标识符不能为空');
        }
        if(!$data['display_name']){
            return $this->response(403,'显示名称不能为空');
        }

        // 检查权限标识符是否重复
        $count = DB::table($this->table)->where('name',$data['name'])
            ->where('account_id',$this->parentUserId)->count();
        if($count){
            return $this->response(403,['权限标识符已被使用']);
        }

        $data['account_id'] = $this->parentUserId;
        $data['addtime'] = time();
        if(DB::table($this->table)->insert($data)){
            return $this->response(200,'权限添加成功',route('merchant.permission-list'));
        }else{
            return $this->response(500,'权限添加失败');
        }
    }

    /**
     * 修改权限表单
    */
    public function edit(Request $request){
        if(!intval($request->has('id'))){
            return $this->response(403,'拒绝访问');
        }
        $perm = DB::table($this->table)->where('id',$request->get('id'))
            ->where('account_id',$this->parentUserId)->first();

        if(!$perm){
            return $this->response(404,'没有该权限');
        }

        return view('merchant.edit-permission',['permission'=>$perm]);
    }

    /**
     * 修改权限操作
    */
    public function update(Request $request){
        $data = $request->all();
        if(!$data['id']){
            return $this->response(403,'拒绝访问');
        }
        if(!$data['name']){
            return $this->response(403,'权限标识符不能为空');
        }

        $id = $data['id'];
        unset($data['id']);

        // 检查权限标识符是否重复
        $count = DB::table($this->table)->where('name',$data['name'])
            ->where('account_id',$this->parentUserId)
            ->where('id','!=',$id)->count();
        if($count){
            return $this->response(403,['权限标识符已被使用']);
        }

        if(DB::table($this->table)->where('id',$id)->update($data) !== false){
            return $this->response(200,'修改成功',route('merchant.permission-list'));
        }else{
            return $this->response(500,'修改失败');
        }
    }

    /**
     * 删除权限
    */
    public function delete(Request $request){
        if(!intval($request->has('id'))){
            return $this->response(403,'拒绝访问');
        }
        $id = $request->get('id');
        $count = DB::table($this->table)->where('id',$id)
            ->where('account_id',$this->parentUserId)->count();
        if(!$count){
            return $this->response(404,'没有该权限值');
        }

        // 解除角色权限关联
        DB::transaction(function() use($id){
            DB::table('bus_role_permission')->where('permission_id',$id)->delete();
            DB::table($this->table)->where('account_id',$this->parentUserId)
                ->where('id',$id)->delete();
        });

        return $this->response(200,'删除成功',route('merchant.permission-list'));
    }





}
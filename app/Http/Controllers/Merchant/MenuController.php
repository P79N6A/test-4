<?php
/**
 * Created by PhpStorm.
 * User: D.Rui
 * Date: 2016/11/3
 * Time: 14:14
 */

namespace App\Http\Controllers\Merchant;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Tree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\PHPTree;

class MenuController extends Controller
{
    protected $table = 'bus_menus';

    /**
     * 菜单列表
    */
    public function index(){
        $menus = DB::table($this->table)->orderBy('display_order','asc')->orderBy('addtime','desc')->get();
        $menus = PHPTree::makeTree($menus);
        return view('merchant.menu-list',['menus'=>$menus]);
    }

    /**
     * 添加菜单表单
    */
    public function create(){
        $menus = DB::table($this->table)->select('id','parent_id','name')->get();
        $menuTree = PHPTree::makeTree($menus);
        return view('merchant.add-menu',['menus'=>$menuTree]);
    }

    /**
     * 存储新添加的菜单
    */
    public function store(Request $request){
        $data = $request->only('parent_id','action','name','description','icon','display','display_order');
        if(!$data['name']){
            return $this->response(403,'菜单名称不能为空');
        }
        $data['addtime'] = time();

        if($data['parent_id'] == 0){
            if(!$data['icon']){
                return $this->response(403,'请选择图标');
            }
        }

        // 检查菜单路由是否重复
        if($data['action']){
            $count = DB::table($this->table)->where('action',$data['action'])->count();
            if($count){
                return $this->response(403,'菜单操作（路由）重复');
            }
        }

        if(DB::table($this->table)->insert($data)){
            return $this->response(200,'菜单添加成功',route('merchant.menu-list'));
        }else{
            return $this->response(500,'菜单添加失败');
        }
    }

    /**
     * 修改菜单表单
    */
    public function edit(Request $request){
        $menu = DB::table($this->table)->where('id',$request->get('id'))->first();
        if(!$menu){
            return view('merchant.error',['code'=>404,'msg'=>'该菜单不存在']);
        }
        $menus = DB::table($this->table)->select('id','parent_id','name')->get();
        $menuTree = PHPTree::makeTree($menus);
        return view('merchant.edit-menu',['menuTree'=>$menuTree,'detail'=>$menu]);
    }

    /**
     * 修改菜单
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request){
        $data = $request->only('id','parent_id','action','name','description','icon','display','display_order');

        if(!intval($request->get('id'))){
            return $this->response(403,'拒绝访问');
        }
        $id = $request->get('id');
        unset($data['id']);

        if(!$data['name']){
            return $this->response(403,'菜单名称不能为空');
        }

        if($data['parent_id'] == 0){
            if(!$data['icon']){
                return $this->response(403,'请选择图标');
            }
        }

        // 检查操作名是否重复
        if($data['action']){
            $count = DB::table($this->table)->where('id','!=',$id)->where('action',$data['action'])->count();
            if($count){
                return $this->response(500,'操作名重复');
            }
        }

        if(DB::table($this->table)->where('id',$id)->update($data) !== false){
            return $this->response(200,'修改成功',route('merchant.menu-list'));
        }else{
            return $this->response(500,'修改失败');
        }
    }

    /**
     * 删除菜单
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request){
        if(!$id = intval($request->get('id'))){
            return $this->response(500,'内部错误');
        }
        $count = DB::table($this->table)
            ->where('id',$id)->count();
        if(!$count){
            return $this->response(404,'该菜单不存在');
        }
        if(DB::table($this->table)->where('id',$id)->delete()){
            return $this->response(200,'删除成功');
        }else{
            return $this->response(500,'删除失败');
        }
    }

    public function orderMenu(Request $request){
        $data = $request->all();
        if(!empty($data['display_order']) && is_array($data['display_order'])){
            foreach($data['display_order'] as $k=>$v){
                DB::table('bus_menus')->where('id',$k)->update(['display_order'=>$v]);
            }
        }else{
            return $this->response(403,'内部错误');
        }
        return $this->response(200,'菜单排序成功',route('merchant.menu-list'));
    }



}
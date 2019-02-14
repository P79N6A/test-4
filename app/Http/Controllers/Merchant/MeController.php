<?php
/**
 * Created by PhpStorm.
 * User: AIMPER
 * Date: 2016/10/21
 * Time: 10:20
 */

namespace App\Http\Controllers\Merchant;
use App\Http\Models\Merchant\UserModel;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class MeController extends Controller
{

    /**
     * 个人配置信息
    */
    public function profile(){
        $profile = DB::table('bus_users as bu')->leftJoin('base.region as r','r.id','=','bu.area_id')
            ->select('bu.name','bu.mobile','bu.email','r.country','r.city','r.county')
            ->where('bu.id',session('id'))->first();

        return view('merchant.profile',['profile'=>$profile]);
    }

    /**
     * 修改密码
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function changePassword(Request $request){
        if($request->isMethod('get')){
            return view('business.change-password');

        }elseif($request->isMethod('post')){
            $data = $request->all();
            if(!preg_replace('/\s+/','',$data['old_password'])){
                return $this->response(403,'旧密码不能为空');
            }
            if(!$new_password = preg_replace('/\s+/','',$data['new_password'])){
                return $this->response(403,'新密码不能为空');
            }
            if(strlen($new_password) < 6){
                return $this->response(403,'密码至少为6个字符');
            }
            $user = UserModel::find(session('id'));

            if($user->password != md5(md5($data['old_password']).$user->salt)){
                return $this->response(403,'旧密码不正确');
            }

            $salt = str_random(6);
            $user->salt = $salt;
            $user->password = md5(md5($new_password).$salt);

            if($user->save()){

                \Operation::update('bus_users','修改密码，['.$user->name.']！', [] ,[]);

                return $this->response(200,'密码修改成功',route('business.overview'));
            }else{
                return $this->response(500,'密码修改失败');
            }
        }
    }







}
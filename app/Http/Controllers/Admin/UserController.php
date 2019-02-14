<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

//use App\Models\User;
//use App\Models\Role;
//use App\Models\Permission;
use App\Http\Models\Admin\UserModel;
use App\Http\Models\Admin\RoleModel;
use App\Http\Models\Admin\PermissionModel;
use Illuminate\Support\Facades\DB;
use App\Helper;

class UserController extends Controller
{

    /**
     * 管理员列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        if ($request->has('keyword')) {
            $keyword = $request->get('keyword');
            $users = UserModel::where('name', 'like', '%' . $keyword . '%')->paginate(20);
        } else {
            $users = UserModel::paginate(20);
        }
        return view('admin.user-list', [
            'users' => $users,
            'keyword' => isset($keyword) ? $keyword : '',
        ]);
    }

    /**
     * 登录
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function login(Request $request)
    {
        if ($request->isMethod('get')) {
            if (session()->get('uid') && session()->get('username')) {
                return response()->redirectTo(route('admin.index'));
            }

            return view('admin.login');

        } elseif ($request->isMethod('post')) {
            $data = $request->only('name', 'password');

            if (!preg_replace('/\s/', '', $data['name'])) {
                return $this->response(403, '请输入名字');
            }
            if (!preg_replace('/\s/', '', $data['password'])) {
                return $this->response(403, '请输入密码');
            }
            if (strlen(preg_replace('/\s/', '', $data['password'])) < 6) {
                return $this->response(403, '密码至少6位字符');
            }

            $user = UserModel::where('name', $data['name'])
                ->where('password', md5($data['password']))
                ->select()->first();
            if (!$user) {
                return $this->response(403, '用户名或密码错误');
            }

            session([
                'uid' => $user->id,
                'username' => $user->name,
                'email' => $user->email,
                'user' => $user
            ]);

            return $this->response(200, '登录成功', route('admin.index'));

        }
    }

    /**
     * 退出登录
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        session()->forget('uid');
        session()->forget('username');
        session()->forget('email');
        session()->forget('user');
        session()->forget('idVerified');
        return $this->response(200, '退出成功', route('admin.login'));
    }

    /**
     * 创建管理员
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function add(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('admin.add-user');

        } elseif ($request->isMethod('post')) {
            $data = $request->only('name', 'mobile', 'password');
            if (!preg_replace('/\s/', '', $data['name'])) {
                return $this->response(403, '用户名不能为空');
            }
            if (strlen(preg_replace('/\s/', '', $data['name'])) < 5) {
                return $this->response(403, '用户名不能少于5个字符');
            }
            if (!$data['mobile']) {
                return $this->response(403, '请输入管理员手机号码');
            }
            if (!Helper::isMobile($data['mobile'])) {
                return $this->response(403, '手机号码格式不正确');
            }
            if (!preg_replace('/\s/', '', $data['password'])) {
                return $this->response(403, '密码不能为空');
            }
            if (strlen(preg_replace('/\s/', '', $data['password'])) < 6) {
                return $this->response(403, '密码不能少于6个字符');
            }

            $repeat = UserModel::where('name', $data['name'])->count();
            if ($repeat) {
                return $this->response(403, '用户名重复');
            }

            $user = new UserModel();
            $user->name = preg_replace('/\s/', '', $data['name']);
            $user->mobile = $data['mobile'];
            $user->password = md5(preg_replace('/\s/', '', $data['password']));
            if ($user->save()) {
                return $this->response(200, '用户创建成功', route('admin.user-list'));
            } else {
                return $this->response(500, '用户创建失败');
            }
        }
    }

    /**
     * 修改管理员
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function edit(Request $request)
    {
        if ($request->isMethod('get')) {
            if (!intval($request->get('id'))) {
                return view('admin.error', ['code' => 500, 'msg' => '内部错误']);
            }
            $user = UserModel::find($request->get('id'));
            if (!$user) {
                return view('admin.error', ['code' => 404, 'msg' => '该用户不存在']);
            }
            return view('admin.edit-user', ['user' => $user]);

        } elseif ($request->isMethod('post')) {
            $data = $request->only('id', 'name', 'mobile', 'password');

            if (!intval($data['id'])) {
                return $this->response(500, '内部错误');
            }
            if (!$name = preg_replace('/\s/', '', $data['name'])) {
                return $this->response(403, '名字不能为空');
            }
            if (strlen($name) < 5) {
                return $this->response(403, '名字不能少于5个字符');
            }
            if (!$data['mobile']) {
                return $this->response(403, '请输入手机号码');
            }
            if (!Helper::isMobile($data['mobile'])) {
                return $this->response(403, '手机号码格式不正确');
            }
            if (preg_replace('/\s/', '', $data['password']) && (strlen(preg_replace('/\s/', '', $data['password'])) < 6)) {
                return $this->response(403, '密码不能少于6个字符');
            }

            $user = UserModel::find($data['id']);
            if (!$user) {
                return $this->response(403, '该用户不存在');
            }

            $repeat = UserModel::where('name', $data['name'])->where('id', '!=', $user->id)->count();
            if ($repeat) {
                return $this->response(403, '用户名重复');
            }

            $user->name = $name;
            $user->mobile = $data['mobile'];
            $user->password = md5(preg_replace('/\s/', '', $data['password']));
            if ($user->save()) {
                return $this->response(200, '用户修改成功', route('admin.user-list'));
            } else {
                return $this->response(500, '用户修改失败');
            }

        }
    }

    /**
     * 分配角色
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function allocateRole(Request $request)
    {
        if ($request->isMethod('get')) {
            if (!intval($request->get('id'))) {
                return view('admin.error', ['code' => 500, 'msg' => '内部错误']);
            }
            $user = UserModel::find($request->get('id'));
            if (!$user) {
                return view('admin.error', ['code' => 404, 'msg' => '该用户不存在']);
            }
            $roles = RoleModel::where('status', 1)->select('id', 'display_name')->get();
            // 已分配的角色
            $allocatedRoles = DB::table('admin_role_user as aru')->where('aru.user_id', $user->id)
                ->join('admin_roles as ar', 'ar.id', '=', 'aru.role_id')
                ->lists('ar.id');

            return view('admin.allocate-role', ['user' => $user, 'roles' => $roles, 'allocatedRoles' => $allocatedRoles]);

        } elseif ($request->isMethod('post')) {
            $data = $request->only('userid', 'role_ids');
            if (!intval($data['userid'])) {
                return $this->response(500, '内部错误');
            }

            $user = UserModel::find($data['userid']);
            if (!$user) {
                return $this->response(404, '该用户不存在，不能分配角色');
            }

            // 删除原有角色分配
            DB::table('admin_role_user')->where('user_id', $user->id)->delete();

            if ($request->has('role_ids') && is_array($request->get('role_ids'))) {
                $roleIds = $request->get('role_ids');
                foreach ($roleIds as $roleId) {
                    $user->roles()->attach($roleId);
                }
            }

            return $this->response(200, '角色分配成功', route('admin.user-list'));
        }

    }

    /**
     * 删除管理员
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        if (!intval($request->get('id'))) {
            return $this->response('500', '内部错误');
        }

        $user = UserModel::find($request->get('id'));
        if (!$user) {
            return $this->response(404, '该用户不存在');
        }
        // 不能删除自己
        /*
        if($user->id == session('uid')){
            return $this->response(403,'您不能删除自己的账号');
        }
        */
        // 如果该用户拥有超级管理员角色，并且拥有该角色的用户只有一个，则不能删除该用户
        $root = DB::table('admin_role_user as aru')->where('aru.user_id', $user->id)
            ->join('admin_roles as ar', function ($join) {
                $join->on('ar.id', '=', 'aru.role_id')->where('ar.root', '=', 1);
            })->join('admin_role_user as aru1', function ($join) {
                $join->on('aru1.role_id', '=', 'ar.id')->where('ar.root', '=', 1);
            })->select([
                'aru.user_id', 'aru.role_id', 'ar.root',
                DB::raw('COUNT(aru1.user_id) as root_user_count')
            ])->first();
        if ($root->user_id && $root->root_user_count == 1) {
            return $this->response(403, '该管理员是唯一一个超级管理员，不能删除');
        }

        if ($user->delete()) {
            // 删除用户角色关联
            DB::table('admin_role_user')->where('user_id', $user->id)->delete();
            return $this->response(200, '用户删除成功', route('admin.user-list'));
        } else {
            return $this->response(500, '用户删除失败');
        }


    }

    /**
     * 已登录管理员修改自己登录密码
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function changePwd(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('admin.change-password');

        } elseif ($request->isMethod('post')) {
            $data = $request->only('old', 'new');

            if (!preg_replace('/\s/', '', $data['old'])) {
                return $this->response(403, '请输入旧密码');
            }
            if (!preg_replace('/\s/', '', $data['new'])) {
                return $this->response(403, '请输入新密码');
            }
            if (strlen(preg_replace('/\s/', '', $data['new'])) < 6) {
                return $this->response(403, '新密码最少6个字符');
            }
            $old = preg_replace('/\s/', '', $data['old']);
            $new = preg_replace('/\s/', '', $data['new']);

            $user = UserModel::where('name', session()->get('username'))->where('password', md5($old))->first();
            if (!$user) {
                return $this->response(403, '旧密码不正确');
            }
            $user->password = md5($new);
            if ($user->save()) {
                return $this->response(200, '密码修改成功', route('admin.overview'));
            } else {
                return $this->response(500, '密码修改失败');
            }
        }
    }

    /**
     * 设置手机号码
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function setMobile(Request $request)
    {
        if ($request->isMethod('get')) {
            $referrer = $request->get('referrer');
            $user = DB::table('admin_users')->where('id', session()->get('uid'))->first();
            return view('admin.set-mobile', ['mobile' => $user->mobile, 'referrer' => $referrer]);

        } elseif ($request->isMethod('post')) {
            $mobile = $request->get('mobile');
            $referrer = $request->get('referrer');

            if (!$mobile) {
                return $this->response(403, '请输入手机号码');
            }

            if (!Helper::isMobile($mobile)) {
                return $this->response(403, '手机号码格式不正确');
            }

            $state = DB::table('admin_users')->where('id', session()->get('uid'))->update(['mobile' => $mobile]);

            if ($state) {
                return $this->response(200, '手机号码设置成功', $referrer);
            } else {
                return $this->response(500, '手机号码设置失败');
            }

        }
    }

    /**
     * 设置转账密码
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function setTransferPassword(Request $request)
    {
        $user = DB::table('admin_users')->where('id', session()->get('uid'))->first();
        if (!$user->mobile) {
            $referrer = $request->fullUrl();
            return redirect(route('admin.set-mobile', ['referrer' => $referrer]));
        }

        if ($request->isMethod('get')) {
            $user = DB::table('admin_users')->where('id', session()->get('uid'))->first();

            if (!$user->mobile) {
                return redirect(route('admin.set-mobile', ['referrer' => $request->fullUrl()]));
            }

            return view('admin.set-transfer-password', ['user' => $user]);

        } elseif ($request->isMethod('post')) {
            $user = DB::table('admin_users')->where('id', session()->get('uid'))->first();
            $client = new Client();
            $data = $request->only('verify_code', 'password', 'confirm_password');

            if (!$data['verify_code']) {
                return $this->response(403, '请输入验证码');
            }
            if (!$data['password']) {
                return $this->response(403, '请输入转账密码');
            }
            if (strlen($data['password']) < 6) {
                return $this->response(403, '密码长度至少为6位');
            }
            if (!$data['confirm_password']) {
                return $this->response(403, '请输入确认转账密码');
            }
            if ($data['password'] != $data['confirm_password']) {
                return $this->response(403, '两次密码不一致');
            }

            $verifyUrl = config('misc.code_verify_url') . $user->mobile . '/' . intval($data['verify_code']);
            $res = $client->post($verifyUrl);

            if ($res->getStatusCode() !== 200) {
                return $this->response(500, '校验验证码出错，请稍后重试');
            }

            $content = json_decode($res->getBody()->getContents());
            if ($content->retCode !== 0) {
                return $this->response(500, '校验验证码出错，请稍后重试');
            }
            if (!$content->valid) {
                return $this->response(500, '验证码不正确，请重新输入');
            }

            $save['transfer_encrypt'] = str_random(6);
            $save['transfer_password'] = md5($data['password'] . $save['transfer_encrypt']);

            if (DB::table('admin_users')->where('id', $user->id)->update($save) !== false) {
                return $this->response(200, '转账密码设置成功', route('admin.bank-card-management'));
            } else {
                return $this->response(500, '转账密码设置失败');
            }
        }

    }

}

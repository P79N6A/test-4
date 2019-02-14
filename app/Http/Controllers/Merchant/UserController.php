<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Models\Merchant\UserModel as User;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{

    /**
     * 后台登录页面展示
     */
    public function login()
    {
        $uid = session('id');
        $name = session('username');
        if ($uid && $name) {
            return redirect(route('business.index'));
        }
        return view('business.login');
    }

    /**
     * 登录操作
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function processlogin(Request $request)
    {
        if (empty($username = $request->get('username')) || !preg_replace('/[\s]+/', '', $username)) {
            return $this->response(403, '用户名不能为空');
        }
        if (empty($password = $request->get('password')) || !preg_replace('/[\s]+/', '', $password)) {
            return $this->response(403, '密码不能为空');
        }

        if (preg_match('/#/', $username)) {
            $arr = explode('#', $username);
            $parentUserExist = DB::table('bus_users')->where('name', $arr[0])->where('pid', 0)->first();
            if (!$parentUserExist) {

                \Operation::loginFail('使用不存在商家账号('.$username.')登陆！');

                return $this->response(403, '该商家账号不存在');
            }
            $userExist = DB::table('bus_users')
                ->where('pid', $parentUserExist->id)
                ->where('name', $arr[1])
                ->select('name', 'salt')->first();
        } else {
            $userExist = DB::table('bus_users')
                ->where('name', $username)->where('pid', 0)
                ->select('name', 'salt')->first();
        }

        if (!$userExist) {

            \Operation::loginFail('账号('.$username.')登陆不存在！');

            return $this->response(403, '用户不存在');
        }

        if (isset($parentUserExist)) {
            $user = DB::table('bus_users as bs')->where('bs.pid', $parentUserExist->id)
                ->leftJoin('bus_role_user as bru', 'bru.user_id', '=', 'bs.id')
                ->select('bs.*')
                ->where('bs.name', $userExist->name)
                ->where('bs.password', md5(md5($password) . $userExist->salt))
                ->first();
        } else {
            $user = DB::table('bus_users as bs')
                ->leftJoin('bus_role_user as bru', 'bru.user_id', '=', 'bs.id')
                ->select('bs.*')
                ->where('bs.name', $userExist->name)
                ->where('bs.password', md5(md5($password) . $userExist->salt))
                ->first();
        }

        if (!$user) {

            \Operation::loginFail('登陆用户名或密码错误！');

            return $this->response(401, '用户名或密码错误');
        }
        $this->user = $user;

        session([
            'id' => $user->id,
            'pid' => $user->pid,
            'username' => $user->name,
            'email' => $user->email,
        ]);

        \Operation::loginSuccess('用户('.$username.')于'.date('Y-m-d H:i:s'). '成功登陆商户后台！');

        return $this->response(200, '登录成功', route('business.index'));
    }

    /**
     * 管理员列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $builder = DB::table('bus_users as bu')
            ->leftJoin('bus_role_user as bru', 'bru.user_id', '=', 'bu.id')
            ->leftJoin('bus_roles as br', 'br.id', '=', 'bru.role_id')
            ->where('pid', $this->parentUserId);

        $fields = [
            'bu.*',
            DB::raw('GROUP_CONCAT(br.name SEPARATOR "，") AS roles'),
        ];

        if ($keyword = trim_blanks($request->get('keyword'))) {
            $builder->where('bu.name', 'like', '%' . $keyword . '%')
                ->orWhere('bu.description', 'like', '%' . $keyword . '%');
        }

        $res = $builder->select($fields)->groupBy('bu.id')->orderBy('id', 'desc')->paginate(20);
        $parentUser = DB::table('bus_users')->where('id', $this->parentUserId)->first();
        return view('business.user-list', [
            'users' => $res,
            'parentUserName' => $parentUser->name,
            'keyword' => !empty($keyword) ? $keyword : '',
        ]);
    }

    /**
     * 退出登录
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->session()->forget('id');
        $request->session()->forget('pid');
        $request->session()->forget('username');
        $request->session()->forget('email');

        \Operation::loginOut('用户('.$username.')于'.date('Y-m-d H:i:s'). '登出商户后台！');

        return $this->response(200, '退出成功', route('business.login'));
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
                return view('business.error', ['code' => 403, 'msg' => '拒绝访问']);
            }
            $user_id = $request->get('id');

            // 检查该用户是否是本账号所有
            $user = DB::table('bus_users')->where('pid', $this->parentUserId)
                ->where('id', $user_id)->first();
            if (!$user) {
                return view('business.error', ['code' => 404, 'msg' => '不存在该用户']);
            }

            // 全部角色
            $roles = DB::table('bus_roles')->where('account_id', $this->parentUserId)
                ->where('status', 1)->select('id', 'name', 'description')->get();
            // 已分配角色
            $allocatedRoles = DB::table('bus_role_user')->where('user_id', $user_id)->lists('role_id');

            return view('business.allocate-role', ['user' => $user, 'roles' => $roles, 'allocatedRoles' => $allocatedRoles]);

        } elseif ($request->isMethod('post')) {
            $data = $request->all();
            if (!intval($data['uid'])) {
                return $this->response(403, '拒绝访问');
            }

            // 检查该用户是否是本账号所有
            $count = DB::table('bus_users')->where('pid', $this->parentUserId)
                ->where('id', $data['uid'])->count();
            if (!$count) {
                return $this->response(404, '不存在该用户');
            }

            // 传递过来的角色id
            $ids = $request->has('ids') ? $request->get('ids') : [];
            // 已经分配的角色id
            $allocatedRoles = DB::table('bus_role_user')->where('user_id', $data['uid'])->lists('role_id');
            // 要新增的角色
            $addIds = array_diff($ids, $allocatedRoles);
            // 要删除的角色
            $delIds = array_diff($allocatedRoles, $ids);

            $comment = '角色分配！';
            $is_update = false;

            // 先删除，后增加
            if ($delIds) {
                foreach ($delIds as $delId) {
                    DB::table('bus_role_user')->where('role_id', $delId)->where('user_id', $data['uid'])->delete();
                }

                //获取分配角色名称
                $del_role_name = DB::table('bus_roles')->whereIn('user_id', $delIds)->lists('name');
                if($del_role_name){
                    $del_role_name = implode(',', $del_role_name);

                    $comment .= '减少角色：' . $del_role_name . '！';

                    $is_update = true;
                }

            }
            if ($addIds) {
                foreach ($addIds as $addId) {
                    DB::table('bus_role_user')->insert(['role_id' => $addId, 'user_id' => $data['uid']]);
                }

                //获取分配角色名称
                $add_role_name = DB::table('bus_roles')->whereIn('user_id', $addIds)->lists('name');
                if($add_role_name){
                    $add_role_name = implode(',', $add_role_name);

                    $comment .= '增加角色：' . $add_role_name . '！';
                    $is_update = true;
                }
            }

            if($is_update) \Operation::update('bus_roles',$comment);

            return $this->response(200, '角色分配成功', route('business.user-list'));
        }
    }

    /**
     * 新增用户
     */
    public function add(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('business.add-user');
        } elseif ($request->isMethod('post')) {
            $data = $request->only('name', 'description', 'password');

            if (!trim_blanks($data['name'])) {
                return $this->response(403, '请填写用户名');
            }
            if (!trim_blanks($data['password'])) {
                return $this->response(403, '请填写密码');
            }
            if (strlen(trim_blanks($data['password'])) < 6) {
                return $this->response(403, '密码长度不能小于6位');
            }
            $existUser = User::where('name', $data['name'])->where('pid', $this->parentUserId)->first();
            if ($existUser) {
                return $this->response(403, '用户名重复');
            }

            $user = new User();
            $user->name = $data['name'];

            $randomStr = str_random(6);

            $user->salt = $randomStr;
            $user->password = md5(md5($data['password']) . $randomStr);
            $user->description = trim_blanks($data['description']);
            $user->pid = $this->parentUserId;
            $user->regtime = time();
            if ($user->save()) {

                \Operation::insert('bus_users' , '添加操作员：['.$data['name'].']',$data);

                return response()->json(['code' => 200, 'msg' => '添加用户成功', 'url' => route('business.user-list')]);
            } else {
                return response()->json(['code' => 500, 'msg' => '添加用户失败']);
            }
        }
    }

    /**
     * 修改用户信息
     */
    public function edit(Request $request)
    {
        if ($request->isMethod('get')) {
            $id = intval($request->get('id'));
            if (!$id) {
                return response()->view('merchant.error', ['code' => 403, 'msg' => '内部错误']);
            }
            $user = DB::table('bus_users')->where('id', $id)->where('pid', $this->parentUserId)->first();
            if (!$user) {
                return response()->view('business.error', ['code' => 404, 'msg' => '该用户不存在']);
            }
            return view('business.edit-user', ['user' => $user]);
        } elseif ($request->isMethod('post')) {
            $data = $request->only('id', 'name', 'description', 'password');
            if (!$data['id']) {
                return $this->response(500, '内部错误');
            }
            if (!trim_blanks($data['name'])) {
                return $this->response(500, '用户名不能为空');
            }

            // 检查该用户是否是本账号所有
            $user = DB::table('bus_users')->where('id', $data['id'])->where('pid', $this->parentUserId)->first();
            if (!$user) {
                return $this->response(404, '该用户不存在');
            }

            $before_data = $user;

            $existUser = User::where('id', '!=', $data['id'])->where('name', $data['name'])
                ->where('pid', $this->parentUserId)->first();
            if ($existUser) {
                return $this->response(403, '用户名重复');
            }

            $user = User::find($data['id']);
            $user->name = $data['name'];
            $user->description = $data['description'];
            if (preg_match('/^[\s]+$/', $data['password']) && !trim_blanks($data['password'])) {
                return $this->response(403, '密码不能为空格');
            } elseif ($password = trim_blanks($data['password'])) {
                if (strlen($password) < 6) {
                    return $this->response(403, '密码不能小于6位');
                }
                $randomStr = str_random(6);
                $user->salt = $randomStr;
                $user->password = md5(md5($data['password']) . $randomStr);
            }
            if ($user->save()) {

                \Operation::update('bus_users' , '修改操作员：['.$data['name'].']' ,$before_data ,$data);

                return $this->response(200, '用户更新成功', route('business.user-list'));
            } else {
                return $this->response(500, '内部错误');
            }
        }
    }

    /**
     * 删除商户用户
     */
    public function delete(Request $request)
    {
        if (!intval($request->get('id'))) {
            return $this - response(500, '内部错误');
        }
        $uid = $request->get('id');

        // 检查该用户是否为主账号所有
        $count = DB::table('bus_users')->where('pid', $this->parentUserId)
            ->where('id', $uid)->count();
        if (!$count) {
            return $this->response(404, '该用户不存在');
        }

        // 删除用户的角色关联
        DB::table('bus_role_user')->where('user_id', $uid)->delete();
        // 删除用户
        DB::table('bus_users')->where('pid', $this->parentUserId)
            ->where('id', $uid)->delete();

        \Operation::delete('bus_users' , '删除操作员：['.$user->name.']' ,$user);

        return $this->response(200, '删除成功', route('business.user-list'));
    }

    /**
     * 获取所属角色已授权的可访问门店ID
     * @param int $roleIds 角色ID数组
     * @return array
     */
    private function getAuthorizedStoreIds($roleIds)
    {
        if ($roleIds) {
            $authorizedStoreIds = DB::table('bus_role_store_access_control')->whereIn('role_id', $roleIds)->lists('store_id');
        } else {
            $authorizedStoreIds = DB::table('bus_stores')->where('userid', $this->user->id)->lists('id');
        }
        return $authorizedStoreIds;
    }

    /**
     * 分配门店
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function allocateStore(Request $request)
    {
        if ($request->isMethod('get')) {
            $id = $request->get('id');

            if (!intval($id)) {
                return view('business.error', ['code' => 403, 'msg' => '请求出错']);
            }

            $user = DB::table('bus_users')->where('pid', $this->parentUserId)->where('id', $id)
                ->select(['id', 'name', 'status'])->first();

            if (!$user) {
                return view('business.error', ['code' => 404, 'msg' => '该账号不存在']);
            }

            if ($user->status == 2) {
                return view('business.error', ['code' => 403, 'msg' => '该账号状态异常，不能分配门店']);
            }

            $allocatedStores = DB::table('bus_store_manager')->where('bus_userid', $user->id)->lists('store_id');

            return view('business.allocate-store', [
                'user' => $user, 'stores' => $this->stores,
                'allocatedStores' => $allocatedStores,
            ]);

        } elseif ($request->isMethod('post')) {
            $data = $request->only('uid', 'store_ids');

            if (!intval($data['uid']) || (!empty($data['store_ids']) && !is_array($data['store_ids']))) {
                return $this->response(403, '请求出错');
            }

            $user = DB::table('bus_users')->where('pid', $this->parentUserId)->where('id', $data['uid'])->first();

            if (!$user) {
                return $this->response(404, '该账号不存在');
            }

            if ($user->status == 2) {
                return $this->response(403, '该账号状态异常，不能分配门店');
            }

            $records = [];
            if (!empty($data['store_ids'])) {
                foreach ($data['store_ids'] as $datum) {
                    $records[] = ['bus_userid' => $user->id, 'store_id' => $datum, 'create_date' => date('Y-m-d H:i:s')];
                }
            }

            DB::beginTransaction();
            try {

                $comment = '分配门店！';
                //获取分配前的门店ID
                $before_allocate = DB::table('bus_store_manager')->where('bus_userid', $user->id)->lists('store_id');
                if($before_allocate){
                    $before_allocate = implode(',', $before_allocate);
                    $comment .= '分配前：' . $before_allocate . '！';
                }

                DB::table('bus_store_manager')->where('bus_userid', $user->id)->delete();
                if ($records) {
                    DB::table('bus_store_manager')->insert($records);
                }
                DB::commit();

                $after_allocate = implode(',', $data['store_ids']);
                $comment .= '分配后：' . $after_allocate . '！';
                \Operation::update('bus_roles',$comment);

                return $this->response(200, '分配门店成功', route('business.user-list'));
            } catch (Exception $e) {
                DB::rollBack();
                return $this->response(500, '分配门店失败');
            }
        }
    }


}

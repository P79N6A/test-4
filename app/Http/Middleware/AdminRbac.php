<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Controller;
use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AdminRbac extends Controller
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    protected $user;

    public function __construct()
    {
        parent::__construct();
        $this->user = session('user');
    }

    public function handle(Request $request, Closure $next)
    {

        $action = $request->path();
        $roles = $this->getRoles();
        $permitted = true;

        if($roles['isRootRole'] != 1){
            if(empty($roles['roleIds'])){
                $permitted = false;
            }else{
                $perms = $this->getPermissions($roles['roleIds']);
                if(empty($perms)){
                    $permitted = false;
                }else{
                    if(!in_array($action, $perms)){
                        $permitted = false;
                    }
                }
            }
        }

        if(!$permitted){
            if ($request->ajax() || $request->isMethod('post')) {
                return $this->response(403, '您无权进行该操作');
            } elseif ($request->isMethod('get')) {
                if ($action == 'overview') {
                    return response()->view('admin.welcome');
                } else {
                    return response()->view('admin.error', ['code' => 403, 'msg' => '您无权进行该操作']);
                }
            }
        }

        /*
        $perms = $this->getPermissions($roles['roleIds']);
        if ($roles['isRootRole'] != 1 && !in_array($action, $perms)) {
            if ($request->ajax() || $request->isMethod('post')) {
                return $this->response(403, '您无权进行该操作');
            } elseif ($request->isMethod('get')) {
                if ($action == 'overview') {
                    return response()->view('admin.welcome');
                } else {
                    return response()->view('admin.error', ['code' => 403, 'msg' => '您无权进行该操作']);
                }
            }
        }
        */

        return $next($request);
    }

    private
    function getRoles()
    {
        $roles = DB::table('admin_role_user as aru')->where('aru.user_id', $this->user->id)
            ->join('admin_roles as ar', function ($join) {
                $join->on('ar.id', '=', 'aru.role_id')->where('ar.status', '=', 1);
            })
            ->select('aru.role_id', 'ar.root')
            ->get();
        $res = [];
        foreach ($roles as $role) {
            $res['roleIds'][] = $role->role_id;
            if ($role->root > 0) {
                $res['isRootRole'] = 1;
            }
        }
        if (empty($res['isRootRole'])) {
            $res['isRootRole'] = 0;
        }
        return $res;
    }

    private
    function getPermissions($roleIds)
    {
        $perms = DB::table('admin_permission_role as apr')
            ->whereIn('apr.role_id', $roleIds)
            ->join('admin_permissions as ap', function ($join) {
                $join->on('ap.id', '=', 'apr.permission_id')->where('ap.disable', '=', 0);
            })
            ->lists('name');
        return $perms;
    }
}

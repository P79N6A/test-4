<?php

namespace App\Http\Middleware;
use App\Services\UserService;

use Closure;

class ApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(!UserService::isLogin()) return response()->json(['code'=>6666,'msg'=>'未登录！']);
        if(!UserService::isActive()) return response()->json(['code'=>7777,'msg'=>'该账户已被禁用']);
        if(!UserService::isBind() && $request->route()->getName() != 'user.bind') return response()->json(['code'=>667,'msg'=>'未绑定！']);
        return $next($request);
    }
}

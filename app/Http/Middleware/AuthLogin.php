<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Models\AuthModel;

class AuthLogin
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
        $token = $request->input('token');
        if(!$token){
            return response('Access Denied !');
        }

        // 从数据库中检查传过来的 token 是否存在，否则拒绝访问
        $info = AuthModel::where('token',$token)->first();
        if(!$info){
            return response('Please Login !');
        }

        return $next($request);
    }
}

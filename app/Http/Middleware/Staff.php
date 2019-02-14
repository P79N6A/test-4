<?php

namespace App\Http\Middleware;
use App\Services\StaffService;

use Closure;

class Staff
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
        if(!StaffService::isStaff()) return response()->json(['code'=>888,'msg'=>'用户身份不为工作人员！']);
        return $next($request);
    }
}
<?php

namespace App\Http\Middleware;
use App\Services\DoctorService;

use Closure;

class Doctor
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
        if(!DoctorService::isDoctor()) return response()->json(['code'=>777,'msg'=>'用户身份不为医生！']);
        return $next($request);
    }
}

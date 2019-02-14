<?php

namespace App\Http\Middleware;

use Closure;

class MerchantAuth
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

        if(!$request->session()->has('id')){
            if($request->isMethod('get')){
                return redirect(route('business.login'));
            }elseif($request->isMethod('post')){
                return response()->json(['msg'=>'Unauthorized'],403);
            }
        }
        return $next($request);
    }
}

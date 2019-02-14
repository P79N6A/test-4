<?php
/**
 * Created by PhpStorm.
 * User: D.Rui
 * Date: 2016/11/18
 * Time: 10:15
 */

namespace App\Http\Middleware;
use App\Http\Controllers\Controller;
use Closure;
use Illuminate\Support\Facades\DB;

class MerchantDataAccessControl extends Controller
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

        return $next($request);
    }


}
<?php
/**
 * Created by PhpStorm.
 * User: AIMPER
 * Date: 2016/10/9
 * Time: 16:09
 */

namespace App\Http\Middleware;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PHPTree;
use Illuminate\Http\Request;
use Closure;

/**
 * 该中间件用于商家后台的访问权限控制
 */
class MerchantRbac extends Controller
{
    public function handle(Request $request, Closure $next)
    {
        $route = $request->path();

        $actions = [];
        foreach ($this->actions as $action) {
            $actions[] = $action->action;
        }
        if ($this->actions) {
            $actionsTree = PHPTree::makeTree($this->actions);   // 操作树形化后的树形数组
        }
        if (!empty($actionsTree)) {
            foreach ($actionsTree as $item) {
                $parentActions[] = $item['action'];     // 操作菜单的最高层父菜单
            }
        } else {
            $parentActions = [];
        }

        $allow = true;
        if (session('pid') > 0 && (!in_array($route, $actions) && !in_array($route, $parentActions))) {
            $allow = false;
        } else {
            if (!in_array($route, $actions)) {
                $allow = false;
            }
        }

        if (!$allow) {
            if ($request->ajax()) {
                return $this->response(403, '您没有操作权限');
            } else {
                if ($request->isMethod('post')) {
                    return $this->response(403, '您没有操作权限');
                } elseif ($request->isMethod('get')) {
                    if($route == 'overview'){
                        return response()->view('business.welcome');
                    }else{
                        $referrer = $_SERVER['HTTP_REFERER'];
                        return response()->view('business.error', ['code' => 403, 'msg' => '您没有操作权限', 'referrer' => $referrer]);
                    }
                }
            }
        }

        return $next($request);
    }
}

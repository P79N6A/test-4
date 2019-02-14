<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
        ],

        'api' => [
            // 'throttle:60,1',
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
//        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
//        'can' => \Illuminate\Foundation\Http\Middleware\Authorize::class,
        //'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
//        'authlogin' => \App\Http\Middleware\AuthLogin::class,

        // DINGO & JWT
        'jwt.auth' => \Tymon\JWTAuth\Middleware\GetUserFromToken::class,
        'jwt.refresh' => \Tymon\JWTAuth\Middleware\RefreshToken::class,

        // 接口访问用户认证
        'user.auth' => \App\Http\Middleware\UserAuth::class,

        // RBAC
        'role' => \Zizaco\Entrust\Middleware\EntrustRole::class,
        'permission' => \Zizaco\Entrust\Middleware\EntrustPermission::class,
        'ability' => \Zizaco\Entrust\Middleware\EntrustAbility::class,
        'merchant.auth' => \App\Http\Middleware\MerchantAuth::class,
        'merchant.rbac' => \App\Http\Middleware\MerchantRbac::class,
        'merchant.dataAccessControl' => \App\Http\Middleware\MerchantDataAccessControl::class,
        'admin.auth' => \App\Http\Middleware\AdminAuth::class,
        'admin.rbac' => \App\Http\Middleware\AdminRbac::class,

        //Api中间件验证
        'api.token' => \App\Http\Middleware\ApiToken::class,
        //Api医生验证
        'api.doctor' => \App\Http\Middleware\Doctor::class,
        //Api工作人员验证
        'api.staff' => \App\Http\Middleware\Staff::class,
    ];
}

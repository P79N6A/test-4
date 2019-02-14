<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        /**** 接口 ****/
        'api/*',
        'test',
        'sms*',
        'user/login',
        'user/password',
        'user/info*',
        'user/password*',
        'user/exchange*',
        'ticket',

        /**** 商家后台 ****/
        'processlogin',

        /* 旧RBAC
        'storerole',
        'updaterole*',
        'alloperm',
        'storeperm',
        'updateperm',
        'user/allorole',
        'storeuser',
        'updateuser',
        'savestore',
        'modifystore',
        */

        // 新 RBAC
        'store-permission',
        'update-permission',
        'allocate-permission',
        'allocate-data-access-permission',
        'allocate-role',

        // 修改密码
        'change-password',
        // 商家后台菜单
        'store-menu',
        'update-menu',
        'order-menu',
        // 上传
        'upload/save',
        // 套餐
        'storepackage',
        'updatepackage',
        // 套餐加入秒杀
        'add-sekill',
        // 活动资讯
        'store-activity-info',
        'update-activity-info',
        // 广告
        'store-add',
        'update-add',
        // 发放代金券
        'post-cash-coupon',
        // 添加卡券
        'add-ticket',
        // 修改卡券
        'edit-ticket',
        // 添加机台
        'add-cabinet',
        // 修改机台
        'edit-cabinet',
        // 发放奖品到奖品池
        'publish-shake-gift',
        // 添加蓝牙设备
        'add-bluetooth-device',

        'RuanJieApi/*',
    ];
}

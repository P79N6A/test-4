<?php
/**
 * Created by PhpStorm.
 * User: AIMPER
 * Date: 2016/12/26  
 * Time: 16:33
 */

/**
 * 商家后台路由
 */
Route::group(['domain' => config('domain.bus_domain'),'middleware'=>'web'], function () {


    // 根据省份id获取城市列表
    Route::get('get-cities', 'CommonController@getCities');
    // 根据城市id获取县区列表
    Route::get('get-blocks', 'CommonController@getBlocks');

    // Merchant 命名空间
    Route::group(['namespace' => 'Merchant'], function () {
        // 登录
        Route::get('login', ['as' => 'business.login', 'uses' => 'UserController@login']);  // 登录页面
        Route::post('processlogin', 'UserController@processlogin');  // 登录处理

        // 需登录后的操作
        Route::group(['middleware' => ['merchant.auth']], function () {
            // 登录用户个人操作
            Route::match(['get', 'post'], 'change-password', ['as' => 'merchant.change-password', 'uses' => 'MeController@changePassword']);    // 修改密码
            Route::get('logout', 'UserController@logout');  // 退出登录

            Route::get('/', ['as' => 'business.index', 'uses' => 'IndexController@index',]);  // 首页
            Route::get('get-packages', 'PackageController@getPackages');      // 根据门店ID以Ajax获取套餐列表，返回 json
            Route::get('filter-ticket', ['as' => 'business.filter-ticket', 'uses' => 'TicketController@filter']);   // 根据门店ID筛选可用卡券，用于商户红包活动
            Route::post('machine-sales-filter', ['as' => 'business.machine-sales-filter', 'uses' => 'TradeController@machineSalesFilter']);   // 机台营收筛选，用于机台营收统计模块
            Route::get('search-user', ['as' => 'business.search-user', 'uses' => 'MsgController@searchUser']);  // 搜索用户

            // 获取首页统计图数据
            Route::get('get-chart-data', ['as' => 'business.get-chart-data', 'uses' => 'IndexController@getChartData']);

            // 获取优惠券领取用户构成
            Route::get('ticket-get-user-structure', ['as' => 'business.ticket-get-user-structure', 'uses' => 'TicketController@getPercent']);

            // 商户端下载
            Route::get('download-link', ['as' => 'business.download-link', 'uses' => 'IndexController@downloadBusApp']);

            // 需要rbac验证的操作
            Route::group(['middleware' => ['merchant.rbac']], function () {

                Route::get('overview', ['as' => 'business.overview', 'uses' => 'IndexController@overview']);  // 首页概览

                // 商家查看二维码
                Route::get('show-qrcode', ['as' => 'business.show-qrcode', 'uses' => 'QrCodeController@show']);
                // 下载二维码
                Route::get('download-qrcode', ['as' => 'business.download-qrcode', 'uses' => 'QrCodeController@download']);

                // 角色
                Route::get('role-list', ['as' => 'business.role-list', 'uses' => 'BusRoleController@index']);  // 角色列表
                Route::match(['get', 'post'], 'add-role', ['as' => 'business.add-role', 'uses' => 'BusRoleController@add']);  // 添加角色
                Route::match(['get', 'post'], 'edit-role', ['as' => 'business.edit-role', 'uses' => 'BusRoleController@edit']);  // 修改角色
                Route::get('delete-role', ['as' => 'business.delete-role', 'uses' => 'BusRoleController@delete']);  // 删除角色
                Route::match(['get', 'post'], 'allocate-permission', ['as' => 'business.allocate-permission', 'uses' => 'BusRoleController@allocatePermission']);  // 分配权限
                Route::match(['get', 'post'], 'allocate-data-access-permission', 'BusRoleController@allocateDataAccessPermission');  // 分配数据访问权限

                // 权限
                Route::get('permission-list', ['as' => 'merchant.permission-list', 'uses' => 'BusPermissionController@index']);// 权限列表
                Route::get('add-permission', ['as' => 'merchant.add-permission', 'uses' => 'BusPermissionController@create']);// 添加权限表单
                Route::post('store-permission', ['as' => 'merchant.store-permission', 'uses' => 'BusPermissionController@store']);// 添加权限
                Route::get('edit-permission', ['as' => 'merchant.edit-permission', 'uses' => 'BusPermissionController@edit']);// 修改权限表单
                Route::post('update-permission', ['as' => 'merchant.update-permission', 'uses' => 'BusPermissionController@update']);// 修改权限
                Route::get('delete-permission', ['as' => 'merchant.delete-permission', 'uses' => 'BusPermissionController@delete']);// 删除权限

                // 管理员用户
                Route::get('user-list', ['as' => 'business.user-list', 'uses' => 'UserController@index']);  // 管理员列表
                Route::match(['get', 'post'], 'add-user', ['as' => 'business.add-user', 'uses' => 'UserController@add']);  // 新增管理员
                Route::match(['get', 'post'], 'edit-user', ['as' => 'business.edit-user', 'uses' => 'UserController@edit']);  // 修改管理员
                Route::get('delete-user', ['as' => 'business.delete-user', 'uses' => 'UserController@delete']);  // 删除管理员
                Route::match(['get', 'post'], 'allocate-role', ['as' => 'business.allocate-role', 'uses' => 'UserController@allocateRole']);  // 分配角色
                Route::match(['get', 'post'], 'allocate-store', ['as' => 'business.allocate-store', 'uses' => 'UserController@allocateStore']);  // 分配门店

                // 商家后台菜单
                Route::get('menu-list', ['as' => 'merchant.menu-list', 'uses' => 'MenuController@index']);    // 菜单列表
                Route::get('add-menu', ['as' => 'merchant.add-menu', 'uses' => 'MenuController@create']);    // 添加菜单表单
                Route::post('store-menu', ['as' => 'merchant.store-menu', 'uses' => 'MenuController@store']);    // 添加菜单
                Route::get('edit-menu', ['as' => 'merchant.edit-menu', 'uses' => 'MenuController@edit']);    // 修改菜单表单
                Route::post('update-menu', ['as' => 'merchant.update-menu', 'uses' => 'MenuController@update']);    // 修改菜单
                Route::get('delete-menu', ['as' => 'merchant.delete-menu', 'uses' => 'MenuController@delete']);    // 删除菜单
                Route::post('order-menu', 'MenuController@orderMenu');   // 菜单排序

                // 门店相关
                Route::get('storelist', ['as' => 'business.storelist', 'uses' => 'StoreController@index']);   // 门店列表
                Route::match(['get', 'post'], 'add-store', ['as' => 'business.add-store', 'uses' => 'StoreController@addstore']);   // 添加门店
                Route::get('delstore', ['as' => 'merchant.delstore', 'uses' => 'StoreController@delstore']);   // 删除
                Route::match(['get', 'post'], 'edit-store', ['as' => 'merchant.edit-store', 'uses' => 'StoreController@editstore']);   // 修改门店
                Route::get('operstore', ['as' => 'merchant.closestore', 'uses' => 'StoreController@operstore']);   // 关停/重开门店操作
                Route::get('store-detail', ['as' => 'merchant.store-detail', 'uses' => 'StoreController@detail']);   // 门店详情
                Route::get('show-bluetooth-device', 'StoreController@showBluetoothDevices');     // 显示门店蓝牙设备列表
                Route::match(['get', 'post'], 'set-score-output-rate', ['as' => 'business.set-score-output-rate', 'uses' => 'StoreController@setScoreOutputRate']);   // 修改门店积分转出率
                Route::match(['get', 'post'], 'set-ticket-output-rate', ['as' => 'business.set-ticket-output-rate', 'uses' => 'StoreController@setTicketOutputRate']);   // 修改奖票赚积分比率
                Route::get('update-member-plan', ['as' => 'business.update-member-plan', 'uses' => 'StoreController@updateMemberPlan']);   // 更新线下门店会员卡套餐
                Route::get('check-store-server-status', ['as' => 'business.check-store-server-status', 'uses' => 'StoreController@checkServerStatus']);   // 检测门店会员卡服务器状态
                Route::match(['get', 'post'], 'set-store-manager', ['as' => 'business.set-store-manager', 'uses' => 'StoreController@setManager']);     // 分配门店管理员
                Route::match(['get', 'post'], 'edit-store-address', ['as' => 'business.edit-store-address', 'uses' => 'StoreController@editAddress']);     // 修改门店地址

                // 套餐/商品相关
                Route::get('packages', ['as' => 'business.package-list', 'uses' => 'PackageController@index']);    // 套餐/商品列表
                Route::get('package-available-stores', ['as' => 'business.package-available-stores', 'uses' => 'PackageController@storeRelation']);    // 套餐可用门店列表
                Route::match(['get', 'post'], 'add-package', ['as' => 'business.add-package', 'uses' => 'PackageController@add']); // 添加套餐
                Route::match(['get', 'post'], 'edit-package', ['as' => 'business.edit-package', 'uses' => 'PackageController@edit']);    // 修改套餐
                Route::get('del-package', ['as' => 'business.del-package', 'uses' => 'PackageController@delete']);    // 删除套餐
                Route::get('package-detail', ['as' => 'business.package-detail', 'uses' => 'PackageController@detail']);    // 套餐/商品详情
                Route::match(['get', 'post'], 'add-sekill', ['as' => 'business.add-sekill', 'uses' => 'PackageController@addToSekill']);   // 添加到秒杀
                Route::match(['get', 'post'], 'edit-sekill', ['as' => 'business.edit-sekill', 'uses' => 'PackageController@editSekill']);   // 编辑秒杀信息
                Route::get('quit-sekill', ['as' => 'business.quit-sekill', 'uses' => 'PackageController@quitSekill']);    // 退出秒杀活动
                Route::post('order-package', ['as' => 'business.order-package', 'uses' => 'PackageController@orderPackage']);    // 套餐排序

                // 资金管理
                Route::get('orders', ['as' => 'business.order-list', 'uses' => 'TradeController@orders']);    // 商品订单
                Route::get('orderdetail', ['as' => 'merchant.orderdetail', 'uses' => 'TradeController@orderDetail']);    // 订单详情
                Route::get('member-orders', ['as' => 'business.member-order-list', 'uses' => 'TradeController@memberCardOrders']);    // 会员卡订单列表
                Route::get('trade-summary', ['as' => 'business.trade-summary', 'uses' => 'TradeController@summary']);    // 交易汇总
                Route::get('machine-sales-overview', ['as' => 'business.machine-sales-overview', 'uses' => 'TradeController@machineSalesOverview']); // 智联宝机台营收概况
                Route::get('refund-member-order', ['as' => 'business.refund-member-order', 'uses' => 'TradeController@refundMemberOrder']);   // 会员卡套餐订单退款
                Route::get('vr-sales-overview', ['as' => 'business.vr-sales-overview', 'uses' => 'TradeController@vrSaleOverview']);   // VR机台营收概况
                Route::get('vr-machine-sales-flow', ['as' => 'business.vr-machine-sales-flow', 'uses' => 'TradeController@vrMachineSalesFlow']);   // VR机台营收订单流水
                Route::get('vr-member-score-log', ['as' => 'business.vr-member-score-log', 'uses' => 'TradeController@vrMemberScoreLog']);   // vr 点数报表

                // 消息管理
                Route::get('messages', ['as' => 'business.bus-msg-list', 'uses' => 'MessageController@index']);   // 消息列表
                Route::get('read-msg', ['as' => 'business.read-msg', 'uses' => 'MessageController@detail']);   // 消息详情
                Route::get('mark-read', ['as' => 'merchant.mark-read', 'uses' => 'MessageController@markRead']);  // 标记已读
                Route::get('del-message', ['as' => 'merchant.del-message', 'uses' => 'MessageController@delete']);  // 删除消息

                // 资讯(活动消息)管理
                Route::get('activity-info-list', ['as' => 'business.activity-info-list', 'uses' => 'ActivityInfoController@index']);  // 资讯列表
                Route::match(['get', 'post'], 'add-activity-info', ['as' => 'business.add-activity-info', 'uses' => 'ActivityInfoController@add']);  // 创建资讯
                Route::match(['get', 'post'], 'edit-activity-info', ['as' => 'business.edit-activity-info', 'uses' => 'ActivityInfoController@edit']);  // 修改资讯表单
                Route::get('del-activity-info', ['as' => 'business.del-activity-info', 'uses' => 'ActivityInfoController@delete']);  // 删除资讯
                Route::get('switch-post-info', ['as' => 'business.switch-post-info', 'uses' => 'ActivityInfoController@switchPostInfo']);  // 发布/取消发布资讯
                Route::get('recommend-act-info', ['as' => 'business.recommend-act-info', 'uses' => 'ActivityInfoController@recommend']);   // 推荐资讯到门店首页资讯轮播
                Route::match(['get', 'post'], 'push-info', ['as' => 'business.push-info', 'uses' => 'ActivityInfoController@push']);     // 推送活动资讯

                // 广告管理
                Route::get('ad-list', ['as' => 'business.ad-list', 'uses' => 'AddController@index']);   // 广告列表
                Route::match(['get', 'post'], 'add-ad', ['as' => 'business.add-ad', 'uses' => 'AddController@add']);   // 发布广告
                Route::match(['get', 'post'], 'edit-ad', ['as' => 'business.edit-ad', 'uses' => 'AddController@edit']);    // 编辑广告
                Route::get('delete-ad', ['as' => 'business.delete-ad', 'uses' => 'AddController@delete']);    // 删除广告
                Route::get('switch-post-ad', ['as' => 'business.switch-post-ad', 'uses' => 'AddController@switchPost']);    // 广告发布/取消发布

                // 卡券
                Route::get('ticket-list', ['as' => 'business.ticket-list', 'uses' => 'TicketController@index']);   // 卡券列表
                Route::get('ticket-analysis', ['as' => 'business.ticket-analysis', 'uses' => 'TicketController@analysis']);   // 优惠券分析
                Route::get('flag-ticket', 'TicketController@flagTicket');   // 推荐/取消推荐卡券到首页
                Route::match(['get', 'post'], 'add-ticket', ['as' => 'business.add-ticket', 'uses' => 'TicketController@addTicket']);   // 添加卡券
                Route::match(['get', 'post'], 'edit-ticket', ['as' => 'business.edit-ticket', 'uses' => 'TicketController@edit']);  // 修改卡券
                Route::get('delete-ticket', ['as' => 'business.delete-ticket', 'uses' => 'TicketController@delete']);  // 删除卡券
                Route::match(['get', 'post'], 'posting-ticket', ['as' => 'business.posting-ticket', 'uses' => 'TicketController@posting']); // 发放代金券（新）
                Route::get('recommend-ticket', ['as' => 'business.recommend-ticket', 'uses' => 'TicketController@recommend']); // 推荐/取消推荐卡券
                Route::get('offline-ticket', ['as' => 'business.offline-ticket', 'uses' => 'TicketController@offline']);   // 上下架卡券
                Route::get('ticket-receive-detail', ['as' => 'business.ticket-receive-detail', 'uses' => 'TicketController@receiveDetail']);   // 卡券领取详情
                Route::match(['get', 'post'], 'ticket-verify', ['as' => 'business.ticket-verify', 'uses' => 'TicketController@verify']); // 卡券核销

                // 摇一摇
                Route::get('shaking', ['as' => 'business.shake-activity-list', 'uses' => 'ShakeController@index']);   // 摇一摇活动列表
                Route::get('history-shake-activity', ['as' => 'business.history-shake-activity', 'uses' => 'ShakeController@history']);   // 已过期摇一摇活动
                Route::match(['get', 'post'], 'add-shake-activity', ['as' => 'business.add-shake-activity', 'uses' => 'ShakeController@add']); // 创建摇一摇活动
                Route::match(['get', 'post'], 'edit-shake-activity', ['as' => 'business.edit-shake-activity', 'uses' => 'ShakeController@edit']); // 修改摇一摇活动
                Route::get('del-shake-activity', ['as' => 'business.del-shake-activity', 'uses' => 'ShakeController@delete']);   // 删除摇一摇活动
                Route::match(['get', 'post'], 'publish-shake-gift', ['as' => 'business.publish-shake-gift', 'uses' => 'ShakeController@publishGift']);   // 投放摇一摇奖品
                Route::post('change-shake-gift-stock', ['as' => 'business.change-shake-gift-stock', 'uses' => 'ShakeController@changeGiftStock']);  // 修改摇一摇奖品库存
                Route::post('change-shake-gift-rate', ['as' => 'business.change-shake-gift-rate', 'uses' => 'ShakeController@changeGiftRate']);  // 修改摇一摇奖品概率
                Route::get('del-shake-gift', ['as' => 'business.del-shake-gift', 'uses' => 'ShakeController@deleteGift']);   // 删除摇一摇活动奖品

                // 硬件设备机台
                Route::get('product-list', ['as' => 'business.product-list', 'uses' => 'ProductController@index']);    // 产品列表
                Route::match(['get', 'post'], 'add-product', ['as' => 'business.add-product', 'uses' => 'ProductController@add']);  // 添加产品
                Route::get('del-product', ['as' => 'business.del-product', 'uses' => 'ProductController@delete']);    // 删除产品
                Route::match(['get', 'post'], 'edit-product', ['as' => 'business.edit-product', 'uses' => 'ProductController@edit']);  // 修改产品

                Route::match(['get', 'post'], 'add-machine', ['as' => 'business.add-machine', 'uses' => 'MachineController@add']); // 添加机台
                Route::match(['get', 'post'], 'edit-machine', ['as' => 'business.edit-machine', 'uses' => 'MachineController@edit']); // 修改机台
                Route::get('machine-list', ['as' => 'business.machine-list', 'uses' => 'MachineController@index']); // 机台列表
                Route::get('del-machine', ['as' => 'business.del-machine', 'uses' => 'MachineController@delete']);   // 删除机台

                // 门店蓝牙设备
                Route::get('bluetooth-device-list', [
                    'as' => 'business.bluetooth-device-list',
                    'uses' => 'BluetoothController@index'
                ]);

                // 红包
                Route::get('red-package-activities', ['as' => 'business.red-package-activities', 'uses' => 'RedPackageController@index']);   // 红包活动列表
                Route::match(['get', 'post'], 'add-red-package-activity', ['as' => 'business.add-red-package-activity', 'uses' => 'RedPackageController@add']);   // 创建红包活动
                Route::match(['get', 'post'], 'edit-red-package-activity', ['as' => 'business.edit-red-package-activity', 'uses' => 'RedPackageController@edit']);   // 修改红包活动
                Route::get('del-red-package-activity', ['as' => 'business.del-red-package-activity', 'uses' => 'RedPackageController@delete']);   // 删除红包活动
                Route::match(['get', 'post'], 'put-gift-to-red-pool', ['as' => 'business.put-gift-to-red-pool', 'uses' => 'RedPackageController@putGift']); // 添加红包到红包池

                Route::get('member-plans', ['as' => 'business.member-plans', 'uses' => 'MemberPlanController@index']);   // 会员卡套餐管理

                Route::get('vr-orders', ['as' => 'business.vr-orders', 'uses' => 'TradeController@vrOrders']);   // VR营收报表

                /* 数据分析模块 */
                Route::get('member-management', ['as' => 'business.member-management', 'uses' => 'MemberController@index']);   // 会员管理
                Route::get('member-analysis', ['as' => 'business.member-analysis', 'uses' => 'MemberController@analysis']);   // 会员分析
                Route::get('order-management', ['as' => 'business.order-management', 'uses' => 'OrderController@index']);   // 订单管理
                Route::get('order-analysis', ['as' => 'business.order-analysis', 'uses' => 'OrderController@analysis']);   // 订单分析
                Route::get('package-analysis', ['as' => 'business.package-analysis', 'uses' => 'PackageController@analysis']);   // 套餐分析
                Route::get('store-analysis', ['as' => 'business.store-analysis', 'uses' => 'StoreController@analysis']);   // 门店分析
                Route::match(['get', 'post'], 'order-deposit', ['as' => 'business.order-deposit', 'uses' => 'OrderController@deposit']);     // 订单退款

                Route::match(['get', 'post'], 'order-verify', ['as' => 'business.order-verify', 'uses' => 'OrderController@verify']);   // 订单核销

                Route::get('sekill-activities', ['as' => 'business.sekill-activities', 'uses' => 'SekillController@index']);   // 秒杀活动列表
                Route::match(['get', 'post'], 'add-sekill-activity', ['as' => 'business.add-sekill-activity', 'uses' => 'SekillController@add']);   // 创建秒杀活动
                Route::match(['get', 'post'], 'edit-sekill-activity', ['as' => 'business.edit-sekill-activity', 'uses' => 'SekillController@edit']);   // 修改秒杀活动
                Route::get('del-sekill-activity', ['as' => 'business.del-sekill-activity', 'uses' => 'SekillController@delete']);   // 删除秒杀活动
                Route::match(['get', 'post'], 'put-sekill-package', ['as' => 'business.put-sekill-package', 'uses' => 'SekillController@putPackage']);   // 投放套餐到秒杀活动

                Route::get('message-center', ['as' => 'business.message-center', 'uses' => 'MsgController@index']);   // 消息中心
                Route::match(['get', 'post'], 'push-message', ['as' => 'business.push-message', 'uses' => 'MsgController@push']); // 消推送息
                Route::get('message-detail', ['as' => 'business.message-detail', 'uses' => 'MsgController@detail']);   // 消息详情

                Route::get('vr-machine-management', ['as' => 'business.vr-machine-management', 'uses' => 'VrController@index']);   // VR 机台管理
                Route::get('store-game-income', ['as' => 'business.store-game-income', 'uses' => 'VrController@storeGameIncome']);   // 门店游戏营收
                Route::get('game-management', ['as' => 'business.game-management', 'uses' => 'VrController@gameManagement']);   // 游戏管理
                Route::get('game-consume-log', ['as' => 'business.game-consume-log', 'uses' => 'VrController@gameConsumeLog']);   // 游戏消费记录


                // 数据导出模块
                Route::get('package-order-export', ['as' => 'business.package-order-export', 'uses' => 'ExportController@packageOrders']);   // 商家商品交易订单导出
                Route::get('member-order-export', ['as' => 'business.member-order-export', 'uses' => 'ExportController@memberOrders']);   // 会员套套餐订单导出
                Route::get('trade-summary-export', ['as' => 'business.trade-summary-export', 'uses' => 'ExportController@tradeSummary']);   // 交易汇总导出
                Route::get('vr-orders-export', ['as' => 'business.vr-orders-export', 'uses' => 'ExportController@vrOrders']);   // VR机台订单导出
                Route::get('smart-link-orders-export', ['as' => 'business.smart-link-orders-export', 'uses' => 'ExportController@smartLinkOrders']); // 智联宝机台营收报表导出
                Route::get('member-management-export', ['as' => 'business.member-management-export', 'uses' => 'ExportController@members']); // 会员明细报表导出
                Route::get('package-analysis-export', ['as' => 'business.package-analysis-export', 'uses' => 'ExportController@packageAnalysis']); // 套餐分析报表导出
                Route::get('ticket-analysis-export', ['as' => 'business.ticket-analysis-export', 'uses' => 'ExportController@ticketAnalysis']); // 卡券分析报表导出

                // 提币机模块
                Route::get('coin-machine-list', ['as' => 'business.coin-machine-list', 'uses' => 'CoinMachineController@index']);   // 机台列表
                Route::match(['get', 'post'], 'add-coin-machine', ['as' => 'business.add-coin-machine', 'uses' => 'CoinMachineController@add']);   // 添加机台
                Route::match(['get', 'post'], 'edit-coin-machine', ['as' => 'business.edit-coin-machine', 'uses' => 'CoinMachineController@edit']);   // 修改机台
                Route::get('del-coin-machine', ['as' => 'business.del-coin-machine', 'uses' => 'CoinMachineController@delete']);   // 删除机台
                // 切换机台启用/禁用状态
                Route::get('switch-coin-machine-status', ['as' => 'business.switch-coin-machine-status', 'uses' => 'CoinMachineController@switchStatus']);

                // 提币机添币记录模块
                Route::get('coin-machine-charge-log', ['as' => 'business.coin-machine-charge-log', 'uses' => 'CoinMachineChargeLogController@index']);
                Route::match(['get', 'post'], 'add-coin-charge-log', ['as' => 'business.add-coin-charge-log', 'uses' => 'CoinMachineChargeLogController@add']);
                Route::get('del-coin-charge-log', ['as' => 'business.del-coin-charge-log', 'uses' => 'CoinMachineChargeLogController@delete']);

				/**************世宇内部编辑路由****************/

                /** 
                 * @author:arcytan
                 * 积分转换详情路由
                 **/
                //门店积分转换详情
                Route::get('get-bus-scores-exchange-log',['as'=>'business.get-bus-scores-exchange-log','uses'=>'BusExchangeLogController@busScoresExchange']);
                Route::get('get-bus-tickets-exchange-log',['as'=>'business.get-bus-tickets-exchange-log','uses'=>'BusExchangeLogController@busTicketsExchange']);
                Route::get('get-bus-member-score-log',['as'=>'business.get-bus-member-score-log','uses'=>'BusExchangeLogController@memberScoreLog']);
                //提现功能
                Route::get('store-balance-list',['as'=>'business.store-balance-list','uses'=>'FinanceController@stores']);
                Route::get('store-balance',['as'=>'business.store-balance','uses'=>'FinanceController@storeBalance']);
                Route::get('store-balance-ajax',['as'=>'business.store-balance-ajax','uses'=>'FinanceController@ajaxStoreBalance']);
                Route::get('store-balance-detail',['as'=>'business.store-balance-detail','uses'=>'FinanceController@storeBalanceDetail']);
                Route::get('store-balance-detail-list',['as'=>'business.store-balance-detail-list','uses'=>'FinanceController@storeBalanceDetailList']);
                Route::get('store-withdraw-submit',['as'=>'business.store-withdraw-submit','uses'=>'FinanceController@storeWithdrawSubmit']);
                Route::get('store-withdraw-flow',['as'=>'business.store-withdraw-flow','uses'=>'FinanceController@storeWithdrawFlow']);
                Route::match(['get','post'],'store-balance-withdraw',['as'=>'business.store-balance-withdraw','uses'=>'FinanceController@withdraw']);

                /**************世宇路由结束******************/				
				
				
				/***************rj--start***************/		
                //活动管理
                Route::get('rj_activity_list', ['as' => 'business.rj_activity_list', 'uses' => 'rj\ManagementController@Activity_list']);		//活动列表
                Route::match(['get', 'post'],'add_activity', ['as' => 'business.add_activity', 'uses' => 'rj\ManagementController@add_activity']);	//添加活动
                Route::match(['get', 'post'],'update_activity', ['as' => 'business.update_activity', 'uses' => 'rj\ManagementController@update_activity']);	//编辑活动
                Route::post('rj_activity_del', ['as' => 'business.rj_activity_del', 'uses' => 'rj\ManagementController@activity_del']);		//删除活动
                Route::post('merchint_list', ['as' => 'business.merchint_list', 'uses' => 'rj\ManagementController@merchint_list']);	//查询商户信息
                
                //活动详情，以及兑换码生成、核销
                Route::match(['get'],'activity_game_info', ['as' => 'business.activity_game_info', 'uses' => 'rj\ManagementController@activity_game_info']);	//活动结果
                Route::match(['post'],'push_redeem_code', ['as' => 'business.push_redeem_code', 'uses' => 'rj\ManagementController@push_redeem_code']);	//生产兑换码
                Route::match(['post'],'Write_off_code', ['as' => 'business.Write_off_code', 'uses' => 'rj\ManagementController@Write_off_code']);	//核销兑换码

                //赛程管理
                Route::get('add_schedule', ['as' => 'business.add_schedule', 'uses' => 'rj\ManagementController@add_schedule']);	//查看赛程
                Route::post('machine_user', ['as' => 'business.machine_user', 'uses' => 'rj\ManagementController@machine_user']);	 //获取某个商户下面的机台列表
                Route::post('insert_schedule', ['as' => 'business.insert_schedule', 'uses' => 'rj\ManagementController@insert_schedule']);	 //赛程入库

                //排名管理 (怪兽猎人)
                Route::match(['get', 'post'],'rj_ranking', ['as' => 'business.rj_ranking', 'uses' => 'rj\RankingController@MonsterHunter']);	//怪兽猎人
                Route::post('seasonSize', ['as' => 'business.seasonSize', 'uses' => 'rj\RankingController@seasonSize']);	 //设置赛季


                //机台管理
                Route::get('rj_machine_list', ['as' => 'business.rj_machine_list', 'uses' => 'rj\MachineController@index']);	//机台列表
                Route::match(['get', 'post'],'rj_machine_save', ['as' => 'business.rj_machine_save', 'uses' => 'rj\MachineController@save']);  //编辑机台
                Route::match(['get', 'post'],'rj_machine_info', ['as' => 'business.rj_machine_info', 'uses' => 'rj\MachineController@info']); //机台详情
                Route::match(['post'],'rj_machine_del', ['as' => 'business.rj_machine_del', 'uses' => 'rj\MachineController@machine_del']);  //解绑机台
                Route::match(['post'],'rj_machine_un_bundling', ['as' => 'business.rj_machine_un_bundling', 'uses' => 'rj\MachineController@rj_machine_un_bundling']);  //删除机台
                Route::match(['get', 'post'],'rj_machine_qrcode', ['as' => 'business.rj_machine_qrcode', 'uses' => 'rj\MachineController@machine_qrcode']);  //二维码生成

                //机台类型管理
                Route::match(['get', 'post'],'rj_machine_model', ['as' => 'business.rj_machine_model', 'uses' => 'rj\MachineController@model_list']);	//机台型号列表
                Route::match(['get', 'post'],'rj_machine_model_add', ['as' => 'business.rj_machine_model_add', 'uses' => 'rj\MachineController@model_add']); //添加机台型号
                
                
                //机台监控
                Route::get('rj_machine_monitor', ['as' => 'business.rj_machine_monitor', 'uses' => 'rj\MachineController@monitor']);	//机台监控列表
                Route::get('rj_machine_monitor_info', ['as' => 'business.rj_machine_monitor_info', 'uses' => 'rj\MachineController@monitor_info']);  //机台监控详情
                
                //OTA升级
                Route::get('rj_ota_list', ['as' => 'business.rj_ota_list', 'uses' => 'rj\OtaController@rj_ota_list']);		//ota升级详情
                Route::post('rj_ota_list_del', ['as' => 'business.rj_ota_list_del', 'uses' => 'rj\OtaController@rj_ota_list_del']);		//移除机台
                Route::match(['get', 'post'],'rj_ota_firmware', ['as' => 'business.rj_ota_firmware', 'uses' => 'rj\OtaController@rj_ota_firmware']);		//固件升级
                Route::post('ota_firmware_update', ['as' => 'business.ota_firmware_update', 'uses' => 'rj\OtaController@ota_firmware_update']);			//固件状态修改
                Route::match(['get', 'post'],'ota_firmware_info', ['as' => 'business.ota_firmware_info', 'uses' => 'rj\OtaController@ota_firmware_info']);		//固件详情
                Route::match(['get', 'post'],'ota_select_machine', ['as' => 'business.ota_select_machine', 'uses' => 'rj\OtaController@ota_select_machine']);		//选择机台



                //订单统计
                Route::get('rj_order_index', ['as' => 'business.rj_order_index', 'uses' => 'rj\OrderController@index']);
                Route::post('rj_order_del', ['as' => 'business.rj_order_del', 'uses' => 'rj\OrderController@del']);
                Route::get('rj_order_detail', ['as' => 'business.rj_order_detail', 'uses' => 'rj\OrderController@detail']);
                Route::get('rj_order_export', ['as' => 'business.rj_order_export', 'uses' => 'rj\OrderController@export']);
                Route::get('rj_order_export_tow', ['as' => 'business.rj_order_export_tow', 'uses' => 'rj\OrderController@exportTow']);


                //营收统计
                Route::get('rj_store_report', ['as' => 'business.rj_store_report', 'uses' => 'rj\ReportController@store_report']);	//门店营收
                Route::match(['get', 'post'],'rj_store_report_detail', ['as' => 'business.rj_store_report_detail', 'uses' => 'rj\ReportController@store_report_detail']);	//门店详情
                Route::get('rj_machint_report', ['as' => 'business.rj_machint_report', 'uses' => 'rj\ReportController@machint_report']);   //机台营收
                Route::match(['get', 'post'],'rj_machint_report_detail', ['as' => 'business.rj_machint_report_detail', 'uses' => 'rj\ReportController@machint_report_detail']);   //机台详情



                //云积分活动模块
                Route::get('rj_yun_list', ['as' => 'business.rj_yun_list', 'uses' => 'rj\YunController@rj_yun_list']);		//云积分活动列表
                Route::post('rj_yun_del', ['as' => 'business.rj_yun_del', 'uses' => 'rj\YunController@rj_yun_del']);		//云积分活动删除
                Route::post('rj_yun_activity_type', ['as' => 'business.rj_yun_activity_type', 'uses' => 'rj\YunController@rj_yun_activity_type']);		//云积分活动开启或关闭
                Route::match(['get', 'post'],'rj_yun_add', ['as' => 'business.rj_yun_add', 'uses' => 'rj\YunController@rj_yun_add']);	//云积分活动添加
                Route::match(['get', 'post'],'rj_yun_edit', ['as' => 'business.rj_yun_edit', 'uses' => 'rj\YunController@rj_yun_edit']);	//云积分活动编辑
                Route::match(['get', 'post'],'rj_yun_details', ['as' => 'business.rj_yun_details', 'uses' => 'rj\YunController@rj_yun_details']);//云积分活动详情
                Route::post('rj_yun_storeLists', ['as' => 'business.rj_yun_storeLists', 'uses' => 'rj\YunController@rj_yun_storeLists']);		//获取门店列表
                Route::post('rj_yun_brand_store', ['as' => 'business.rj_yun_brand_store', 'uses' => 'rj\YunController@rj_yun_brand_store']);		//获取品牌对应的门店
                Route::post('rj_yun_machine_list', ['as' => 'business.rj_yun_machine_list', 'uses' => 'rj\YunController@rj_yun_machine_list']);		//获取门店下对应的机台
                Route::match(['post'],'rj_yun_code', ['as' => 'business.rj_yun_code', 'uses' => 'rj\YunController@rj_yun_code']);	//核销兑换码


                //操作日志
                Route::get('rj_system_list', ['as' => 'business.rj_system_list', 'uses' => 'rj\SystemController@rj_system_list']);		//操作日志
                Route::post('rj_system_del', ['as' => 'business.rj_system_del', 'uses' => 'rj\SystemController@rj_system_del']);		//操作日志
                /***************rj路由end***************/
				
            });

        });

    });

});


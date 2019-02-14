<?php
/**
 * Created by PhpStorm.
 * User: AIMPER
 * Date: 2016/12/26
 * Time: 16:48
 */

Route::group(['domain' => config('domain.admin_domain'), 'middleware' => 'web'], function () {

    Route::group(['namespace' => 'Admin'], function () { // Admin 命名空间

        Route::match(['get', 'post'], 'login', ['as' => 'admin.login', 'uses' => 'UserController@login']); // 登录

        // 需登录的操作
        Route::group(['middleware' => 'admin.auth'], function () {

            Route::get('logout', ['as' => 'admin.logout', 'uses' => 'UserController@logout']); // 退出登录
            Route::get('/', ['as' => 'admin.index', 'uses' => 'IndexController@index']); // 后台首页

            // 需要认证权限的操作
            Route::group(['middleware' => 'admin.rbac'], function () {

                Route::get('overview', ['as' => 'admin.overview', 'uses' => 'IndexController@overview']); // 后台预览页
                Route::match(['get','post'],'overview-order-num', ['as' => 'admin.overview.orderNum', 'uses' => 'IndexController@orderNum']); // 后台预览页


                Route::get('role-list', ['as' => 'admin.role-list', 'uses' => 'RoleController@index']); // 角色列表
                Route::match(['get', 'post'], 'add-role', ['as' => 'admin.add-role', 'uses' => 'RoleController@add']); // 创建角色
                Route::match(['get', 'post'], 'edit-role', ['as' => 'admin.edit-role', 'uses' => 'RoleController@edit']); // 修改角色
                Route::get('delete-role', ['as' => 'admin.delete-role', 'uses' => 'RoleController@delete']); // 删除角色
                Route::match(['get', 'post'], 'allocate-permission', ['as' => 'admin.allocate-permission', 'uses' => 'RoleController@allocatePermission']); // 分配权限

                Route::get('user-list', ['as' => 'admin.user-list', 'uses' => 'UserController@index']); // 管理员列表
                Route::match(['get', 'post'], 'add-user', ['as' => 'admin.add-user', 'uses' => 'UserController@add']); // 创建管理员
                Route::match(['get', 'post'], 'edit-user', ['as' => 'admin.edit-user', 'uses' => 'UserController@edit']); // 修改管理员
                Route::get('delete-user', ['as' => 'admin.delete-user', 'uses' => 'UserController@delete']); // 删除管理员
                Route::match(['get', 'post'], 'allocate-role', ['as' => 'admin.allocate-role', 'uses' => 'UserController@allocateRole']); // 分配角色
                Route::match(['get', 'post'], 'change-password', ['as' => 'admin.change-password', 'uses' => 'UserController@changePwd']); // 已登录管理员修改自己账号的密码

                Route::get('permission-list', ['as' => 'admin.permission-list', 'uses' => 'PermissionController@index']); // 权限列表
                Route::match(['get', 'post'], 'add-permission', ['as' => 'admin.add-permission', 'uses' => 'PermissionController@add']); // 创建权限
                Route::match(['get', 'post'], 'edit-permission', ['as' => 'admin.edit-permission', 'uses' => 'PermissionController@edit']); // 修改权限
                Route::get('delete-permission', ['as' => 'admin.delete-permission', 'uses' => 'PermissionController@delete']); // 删除权限

                /**
                 * 用户管理
                 */
                Route::get('member/list', ['as' => 'admin.member-list', 'uses' => 'MemberController@index']); // 用户列表
                Route::match(['get', 'post'], 'member/add', ['as' => 'admin.add-member', 'uses' => 'MemberController@add']); // 创建用户
                Route::match(['get', 'post'], 'member/edit', ['as' => 'admin.edit-member', 'uses' => 'MemberController@edit']); // 修改用户
                Route::get('member/delete', ['as' => 'admin.delete-member', 'uses' => 'MemberController@delete']); // 删除用户
                Route::get('member/setDoctor', ['as' => 'admin.set-doctor', 'uses' => 'MemberController@setDoctor']); // 设置医生角色
                Route::get('member/unsetDoctor', ['as' => 'admin.unset-doctor', 'uses' => 'MemberController@unsetDoctor']); // 解除绑定医生角色
                Route::get('member/list/json', ['as' => 'admin.member.list.json', 'uses' => 'MemberController@json']); // 用户列表(搜索)

                /**
                 * 医生管理
                 */
                Route::match(['get', 'post'], 'doctor/list', ['as' => 'admin.doctor-list', 'uses' => 'DoctorController@index']); // 医生列表

                Route::get('doctor/record/list', ['as' => 'admin.doctor-record-list', 'uses' => 'DoctorController@recordList']); // 奖金详细列表
                Route::match(['get', 'post'], 'doctor/edit', ['as' => 'admin.edit-doctor', 'uses' => 'DoctorController@edit']); // 修改医生信息
                Route::get('doctor/setCommissionRate', ['as' => 'admin.set-commission-rate', 'uses' => 'DoctorController@setCommissionRate']); // 设置佣金比例
                Route::get('doctor/delete', ['as' => 'admin.delete-doctor', 'uses' => 'DoctorController@delete']); // 删除医生

                /**
                 * 医生提现管理
                 */
                Route::get('doctor/money/list', ['as' => 'admin.doctor-money-list', 'uses' => 'DoctorMoneyRecordController@index']); // 医生提现列表
                Route::get('doctor/money/operate', ['as' => 'admin.doctor-money-operate', 'uses' => 'DoctorMoneyRecordController@operate']); // 提现审核操作

                /**
                 * 城市管理相关路由
                 */
                //城市列表
                Route::match(
                    ['get', 'post'],
                    'city/list',
                    [
                        'as' => 'admin.city.list',
                        'uses' => 'CityController@lists',
                    ]
                );
                //添加城市
                Route::match(
                    ['get', 'post'],
                    'city/add',
                    [
                        'as' => 'admin.city.add',
                        'uses' => 'CityController@add',
                    ]
                );
                //修改城市
                Route::match(
                    ['get', 'post'],
                    'city/modify',
                    [
                        'as' => 'admin.city.modify',
                        'uses' => 'CityController@modify',
                    ]
                );
                //城市列表，返回json格式
                Route::get(
                    'city/list/json',
                    [
                        'as' => 'admin.city.list.json',
                        'uses' => 'CityController@json',
                    ]
                );

                /**
                 * 广告管理相关路由
                 */
                //广告列表
                Route::get(
                    'ads/list',
                    [
                        'as' => 'admin.ads.list',
                        'uses' => 'AdsController@lists',
                    ]
                );
                //广告修改
                Route::match(
                    ['get', 'post'],
                    'ads/modify',
                    [
                        'as' => 'admin.ads.modify',
                        'uses' => 'AdsController@modify',
                    ]
                );
                //广告添加
                Route::match(
                    ['get', 'post'],
                    'ads/add',
                    [
                        'as' => 'admin.ads.add',
                        'uses' => 'AdsController@add',
                    ]
                );
                //广告删除
                Route::get(
                    'ads/delete',
                    [
                        'as' => 'admin.ads.delete',
                        'uses' => 'AdsController@delete',
                    ]
                );

                /**
                 * 课程管理
                 */
                //课程类型列表
                Route::get(
                    'course/type/list',
                    [
                        'as' => 'admin.course.type.list',
                        'uses' => 'CourseTypeController@lists',
                    ]
                );
                //添加课程类型
                Route::match(
                    ['get', 'post'],
                    'course/type/add',
                    [
                        'as' => 'admin.course.type.add',
                        'uses' => 'CourseTypeController@add',
                    ]
                );
                //修改课程类型
                Route::match(
                    ['get', 'post'],
                    'course/type/modify',
                    [
                        'as' => 'admin.course.type.modify',
                        'uses' => 'CourseTypeController@modify',
                    ]
                );
                //删除课程类型
                Route::get(
                    'course/type/delete',
                    [
                        'as' => 'admin.course.type.delete',
                        'uses' => 'CourseTypeController@delete',
                    ]
                );
                //课程列表
                Route::get(
                    'course/list',
                    [
                        'as' => 'admin.course.list',
                        'uses' => 'CourseController@lists',
                    ]
                );
                //课程列表，返回json格式
                Route::get(
                    'business/course/list/json',
                    [
                        'as' => 'admin.business.course.list.json',
                        'uses' => 'CourseController@json',
                    ]
                );
                //添加课程
                Route::match(
                    ['get', 'post'],
                    'course/add',
                    [
                        'as' => 'admin.course.add',
                        'uses' => 'CourseController@add',
                    ]
                );
                //修改课程
                Route::match(
                    ['get', 'post'],
                    'course/modify',
                    [
                        'as' => 'admin.course.modify',
                        'uses' => 'CourseController@modify',
                    ]
                );
                //删除课程
                Route::match(
                    ['get', 'post'],
                    'course/delete',
                    [
                        'as' => 'admin.course.delete',
                        'uses' => 'CourseController@delete',
                    ]
                );
                //课时列表
                Route::get(
                    'course/class/list',
                    [
                        'as' => 'admin.course.class.list',
                        'uses' => 'CourseClassController@lists',
                    ]
                );
                //添加课时
                Route::match(
                    ['get', 'post'],
                    'course/class/add',
                    [
                        'as' => 'admin.course.class.add',
                        'uses' => 'CourseClassController@add',
                    ]
                );
                //修改课时
                Route::match(
                    ['get', 'post'],
                    'course/class/modify',
                    [
                        'as' => 'admin.course.class.modify',
                        'uses' => 'CourseClassController@modify',
                    ]
                );

                //删除课时
                Route::match(
                    ['get', 'post'],
                    'course/class/delete',
                    [
                        'as' => 'admin.course.class.delete',
                        'uses' => 'CourseClassController@delete',
                    ]
                );

                /**
                 * 商户管理
                 */
                //商户列表
                Route::get(
                    'business/list',
                    [
                        'as' => 'admin.business.list',
                        'uses' => 'BusinessController@lists',
                    ]
                );
                //商户列表
                Route::get(
                    'business/list/json',
                    [
                        'as' => 'admin.business.list.json',
                        'uses' => 'BusinessController@json',
                    ]
                );
                //添加商户
                Route::match(
                    ['get', 'post'],
                    'business/add',
                    [
                        'as' => 'admin.business.add',
                        'uses' => 'BusinessController@add',
                    ]
                );
                //修改商户
                Route::match(
                    ['get', 'post'],
                    'business/modify',
                    [
                        'as' => 'admin.business.modify',
                        'uses' => 'BusinessController@modify',
                    ]
                );
                //删除商户
                Route::get(
                    'business/delete',
                    [
                        'as' => 'admin.business.delete',
                        'uses' => 'BusinessController@delete',
                    ]
                );
                //品牌列表
                Route::get(
                    'business/brand/list',
                    [
                        'as' => 'admin.business.brand.list',
                        'uses' => 'BrandController@lists',
                    ]
                );
                //品牌列表
                Route::get(
                    'business/brand/list/json',
                    [
                        'as' => 'admin.business.brand.list.json',
                        'uses' => 'BrandController@json',
                    ]
                );
                //门店列表
                Route::get(
                    'business/store/list',
                    [
                        'as' => 'admin.business.store.list',
                        'uses' => 'StoreController@lists',
                    ]
                );

                //门店列表，返回json格式
                Route::get(
                    'business/store/list/json',
                    [
                        'as' => 'admin.business.store.list.json',
                        'uses' => 'StoreController@json',
                    ]
                );

                //添加门店
                Route::match(
                    ['get', 'post'],
                    'business/store/add',
                    [
                        'as' => 'admin.business.store.add',
                        'uses' => 'StoreController@add',
                    ]
                );
                //修改门店
                Route::match(
                    ['get', 'post'],
                    'business/store/modify',
                    [
                        'as' => 'admin.business.store.modify',
                        'uses' => 'StoreController@modify',
                    ]
                );
                //删除门店
                Route::match(
                    ['get', 'post'],
                    'business/store/delete',
                    [
                        'as' => 'admin.business.store.delete',
                        'uses' => 'StoreController@delete',
                    ]
                );

                //绑定门店工作人员
                Route::match(
                    ['get', 'post'],
                    'business/store/bindStaff',
                    [
                        'as' => 'admin.business.store.bindStaff',
                        'uses' => 'StoreController@bindStaff',
                    ]
                );

                //解绑门店工作人员
                Route::match(
                    ['get', 'post'],
                    'business/store/unbindStaff',
                    [
                        'as' => 'admin.business.store.unbindStaff',
                        'uses' => 'StoreController@unbindStaff',
                    ]
                );

                // 商户菜单角色管理
                Route::get(
                    'bus-menu-role-list',
                    [
                        'as' => 'admin.bus-menu-role-list', 
                        'uses' => 'BusMenuRoleController@index'
                    ]
                );   
                // 创建商户菜单角色
                Route::match(
                    ['get', 'post'], 
                    'add-bus-menu-role',
                    [
                        'as' => 'admin.add-bus-menu-role', 
                        'uses' => 'BusMenuRoleController@add'
                    ]
                );   
                // 修改商户菜单角色
                Route::match(
                    ['get', 'post'], 
                    'edit-bus-menu-role', 
                    [
                        'as' => 'admin.edit-bus-menu-role', 
                        'uses' => 'BusMenuRoleController@edit'
                    ]
                );   
                // 删除商户菜单角色
                Route::get(
                    'delete-bus-menu-role', 
                    [
                        'as' => 'admin.delete-bus-menu-role', 
                        'uses' => 'BusMenuRoleController@delete'
                    ]
                );
                // 为角色分配菜单权限   
                Route::match(
                    ['get', 'post'], 
                    'allocate-menu-for-role', 
                    [
                        'as' => 'admin.allocate-menu-for-role', 
                        'uses' => 'BusMenuRoleController@allocateMenu'
                    ]
                );   
                // 商家菜单管理
                Route::get(
                    'bus-menu-list', 
                    [
                        'as' => 'admin.bus-menu-list', 
                        'uses' => 'BusMenuController@index'
                    ]
                );   
                // 创建商家菜单
                Route::match(['get', 'post'], 'add-bus-menu', ['as' => 'admin.add-bus-menu', 'uses' => 'BusMenuController@add']); 
                // 修改商家菜单   
                Route::match(['get', 'post'], 'edit-bus-menu', ['as' => 'admin.edit-bus-menu', 'uses' => 'BusMenuController@edit']);  
                // 删除商家菜单  
                Route::get('delete-bus-menu', ['as' => 'admin.delete-bus-menu', 'uses' => 'BusMenuController@delete']);  
                // 分配菜单访问角色  
                Route::match(['get', 'post'], 'allocate-menu-role', ['as' => 'admin.allocate-menu-role', 'uses' => 'BusUserController@allocateMenuRole']); 

                /**
                 * 老师管理
                 */
                //老师列表
                Route::get(
                    'business/teacher/list',
                    [
                        'as' => 'admin.teacher.list',
                        'uses' => 'TeacherController@lists',
                    ]
                );
                //添加老师
                Route::match(
                    ['get', 'post'],
                    'business/teacher/add',
                    [
                        'as' => 'admin.teacher.add',
                        'uses' => 'TeacherController@add',
                    ]
                );
                //修改老师
                Route::match(
                    ['get', 'post'],
                    'business/teacher/modify',
                    [
                        'as' => 'admin.teacher.modify',
                        'uses' => 'TeacherController@modify',
                    ]
                );
                //删除老师
                Route::match(
                    ['get', 'post'],
                    'business/teacher/delete',
                    [
                        'as' => 'admin.teacher.delete',
                        'uses' => 'TeacherController@delete',
                    ]
                );
                //获取教师（json）
                Route::get(
                    'business/teacher/list/json',
                    [
                        'as' => 'admin.teacher.list.json',
                        'uses' => 'TeacherController@json',
                    ]
                );

                /**
                 * VIP会员相关
                 */
                Route::match(
                    ['get', 'post'],
                    'vip/modify',
                    [
                        'as' => 'admin.vip.modify',
                        'uses' => 'VipController@modify',
                    ]
                );

                /**
                 * 通知相关
                 */
                //通知列表
                Route::get(
                    'notice/list',
                    [
                        'as' => 'admin.notice.list',
                        'uses' => 'NoticeController@lists',
                    ]
                );
                //添加通知
                Route::match(
                    ['get', 'post'],
                    'notice/add',
                    [
                        'as' => 'admin.notice.add',
                        'uses' => 'NoticeController@add',
                    ]
                );
                //修改通知
                Route::match(
                    ['get', 'post'],
                    'notice/modify',
                    [
                        'as' => 'admin.notice.modify',
                        'uses' => 'NoticeController@modify',
                    ]
                );
                //删除通知
                Route::match(
                    ['get', 'post'],
                    'notice/delete',
                    [
                        'as' => 'admin.notice.delete',
                        'uses' => 'NoticeController@delete',
                    ]
                );
                /*
                 *基本设置
                 */
                Route::match(
                    ['get', 'post'],
                    'setting/list',
                    [
                        'as' => 'admin.setting.list',
                        'uses' => 'SettingController@index',
                    ]
                );
                //添加基本设置
                Route::match(
                    ['get', 'post'],
                    'setting/add',
                    [
                        'as' => 'admin.setting.add',
                        'uses' => 'SettingController@add',
                    ]
                );
                //修改基本设置
                Route::match(
                    ['get', 'post'],
                    'setting/modify',
                    [
                        'as' => 'admin.setting.modify',
                        'uses' => 'SettingController@modify',
                    ]
                );
                //删除基本设置
                Route::match(
                    ['get', 'post'],
                    'setting/delete',
                    [
                        'as' => 'admin.setting.delete',
                        'uses' => 'SettingController@delete',
                    ]
                );

                /**
                 * 机台管理
                 */
                //机台列表
                Route::get(
                    'equipment/list',
                    [
                        'as' => 'admin.equipment.list',
                        'uses' => 'EquipmentController@lists',
                    ]
                );
                //门店列表，返回json格式
                Route::get(
                    'business/equipment/list/json',
                    [
                        'as' => 'admin.business.equipment.list.json',
                        'uses' => 'EquipmentController@json',
                    ]
                );
                //添加机台
                Route::match(
                    ['get', 'post'],
                    'equipment/add',
                    [
                        'as' => 'admin.equipment.add',
                        'uses' => 'EquipmentController@add',
                    ]
                );
                //修改机台
                Route::match(
                    ['get', 'post'],
                    'equipment/modify',
                    [
                        'as' => 'admin.equipment.modify',
                        'uses' => 'EquipmentController@modify',
                    ]
                );
                //机台游戏列表
                Route::match(
                    ['get','post'],
                    'equitment/games/list',
                    [
                        'as' => 'admin.equipment.games.list',
                        'uses' => 'EquipmentController@gamesList'
                    ]
                );
                //添加机台游戏
                Route::match(
                    ['get','post'],
                    'equitment/games/add',
                    [
                        'as' => 'admin.equipment.games.add',
                        'uses' => 'EquipmentController@gamesAdd'
                    ]
                );
                //修改机台游戏
                Route::match(
                    ['get','post'],
                    'equitment/games/modify',
                    [
                        'as' => 'admin.equipment.games.modify',
                        'uses' => 'EquipmentController@gamesModify'
                    ]
                );

                /**
                 *游戏难易度
                 */
                Route::match(
                    ['get','post'],
                    'equitment/games/level',
                    [
                        'as' => 'admin.equipment.games.level',
                        'uses' => 'EquipmentController@gameLevel'
                    ]
                );
            });

            /**
             * 订单相关
             */
            //通知列表
            Route::get(
                'order/list',
                [
                    'as' => 'admin.order.list',
                    'uses' => 'OrderController@lists',
                ]
            );
            Route::match(
                ['get','post'],
                'order/export',
                [
                    'as' => 'admin.order.export',
                    'uses' => 'OrderController@export',
                ]
            );
        });

    });

});

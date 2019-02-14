<?php
/**
 * 提供给小程序API
 * 无访问次数控制，无IP白名单控制
 * 以后再补
 */
Route::group(['domain' => config('domain.api_domain'), 'middleware' => 'api'], function () {

    Route::group(['namespace' => 'Api'], function () { //命名空间
        //订单支付成功回调
        Route::match(['get', 'post'], 'order/pay/success/callback',
            ['as' => 'order.pay.success.callback', 'uses' => 'OrderController@paySuccessCallback']);

        /**
         * 城市相关API
         */
        //获取默认城市
        Route::get(
            'city/default',
            [
                'as' => 'city.default',
                'uses' => 'CityController@getDefault',
            ]
        );
        //按首字母分类获取城市列表
        Route::get(
            'city/list/letter',
            [
                'as' => 'city.list.letter',
                'uses' => 'CityController@listByLetter',
            ]
        );
        //获取热门城市列表
        Route::get(
            'city/list/hot',
            [
                'as' => 'city.list.hot',
                'uses' => 'CityController@getHot',
            ]
        );
        /**
         * 广告相关接口
         */
        //获取特定广告位广告
        Route::get(
            'ads/list',
            [
                'as' => 'ads.list',
                'uses' => 'AdsController@lists',
            ]
        );
        /**
         * 课程相关接口
         */
        //课程分类
        Route::get(
            'course/type/list',
            [
                'as' => 'course.type.list',
                'uses' => 'CourseTypeController@lists',
            ]
        );
        //根据课程类型获取课程列表
        Route::post(
            'course/list',
            [
                'as' => 'course.list',
                'uses' => 'CourseController@lists',
            ]
        );
        //根据门店ID获取课程列表
        Route::get(
            'course/list/bystore',
            [
                'as' => 'course.list.bystore',
                'uses' => 'CourseController@byStore',
            ]
        );
        //推荐课程
        Route::post(
            'course/list/recommend',
            [
                'as' => 'course.list.recommend',
                'uses' => 'CourseController@getRecommend',
            ]
        );
        Route::post(
            'course/list/getRecommendWithoutLocation',
            [
                'as' => 'course.list.getRecommendWithoutLocation',
                'uses' => 'CourseController@getRecommendWithoutLocation',
            ]
        );
        //推荐课程
        Route::post(
            'course/list/hot',
            [
                'as' => 'course.list.hot',
                'uses' => 'CourseController@getHot',
            ]
        );
        //课程详细
        Route::post(
            'course/detail',
            [
                'as' => 'course.detail',
                'uses' => 'CourseController@detail',
            ]
        );
        //课程核销二维码
        Route::get(
            'course/qrcode',
            [
                'as' => 'course.qrcode',
                'uses' => 'CourseController@QRCode',
            ]
        );

        /**
         * 门店相关
         */
        //获取门店详情
        Route::get(
            'store/detail',
            [
                'as' => 'store.detail',
                'uses' => 'StoreController@detail',
            ]
        );

        /**
         * 搜索功能
         */
        //搜索门店
        Route::get(
            'search/store',
            [
                'as' => 'search.store',
                'uses' => 'SearchController@store',
            ]
        );

        //根据cityid搜索门店
        Route::post(
            'search/storeByCityid',
            [
                'as' => 'search.store',
                'uses' => 'SearchController@storeByCityid',
            ]
        );

        /**
         * 根据城市搜索相应门店
         */
        //搜索门店
        Route::get(
            'search/citystore',
            [
                'as' => 'search.citystore',
                'uses' => 'SearchController@cityStore',
            ]
        );
        //搜索课程
        Route::get(
            'search/course',
            [
                'as' => 'search.course',
                'uses' => 'SearchController@course',
            ]
        );

        /**
         * 用户部分
         */
        Route::get(
            'user/code',
            [
                'as' => 'user.code',
                'uses' => 'UserController@getCode',
            ]
        );
        //通过openid获取token
        Route::post(
            'user/token',
            [
                'as' => 'user.token',
                'uses' => 'UserController@getTokenByOpenid',
            ]
        );
        // 解密小程序手机号码
        Route::post(
            'user/decode',
            [
                'as' => 'user.decode',
                'uses' => 'UserController@decode',
            ]
        );
        // 获取注册机台的地点信息
        Route::post(
            'user/getRegisterMenchineinfo',
            [
                'as' => 'user.getRegisterMenchineinfo',
                'uses' => 'UserController@getRegisterMenchineinfo',
            ]
        );
        /**
         * 发送短信部分
         */
        Route::get(
            'sms/send',
            [
                'as' => 'sms.send',
                'uses' => 'SmsController@send',
            ]
        );
        /**
         * 年费会员部分
         */
        //会员介绍
        Route::get(
            'vip/info',
            [
                'as' => 'vip.info',
                'uses' => 'VipController@info',
            ]
        );

        Route::get(
            'suitableAge/list',
            [
                'as' => 'suitable.age.list',
                'uses' => 'SuitableAgeController@lists',
            ]
        );

        /**
         * 根据卡号获取银行卡信息
         */
        Route::post(
            'bankCard/info',
            [
                'as' => 'bankCard.info',
                'uses' => 'BankCardController@getBankCardInfo',
            ]
        );

        /**
         * 结束课程回调
         */
        Route::post(
            'scan/end/course',
            [
                'as' => 'scan.end.course',
                'uses' => 'ScanController@endCourse',
            ]
        );

        //设置用户默认城市
        Route::post(
            'user/city/set',
            [
                'as' => 'user.city.set',
                'uses' => 'CityController@setCity',
            ]
        );

        //根据城市查询门店数量
        Route::post(
            'city/countStore',
            [
                'as' => 'user.city.set',
                'uses' => 'CityController@countStore',
            ]
        );

        Route::group(['middleware' => 'api.token'], function () { //需要验证用户的操作
            /**
             * 用户部分
             */
            Route::get(
                'user/info',
                [
                    'as' => 'user.info',
                    'uses' => 'UserController@info',
                ]
            );
            //绑定用户信息
            Route::post(
                'user/bind',
                [
                    'as' => 'user.bind',
                    'uses' => 'UserController@bind',
                ]
            );

            //用户课程详细
            Route::post(
                'user/course/info',
                [
                    'as' => 'user.user.course.info',
                    'uses' => 'CourseController@userCourseInfo',
                ]
            );
            //用户课程进度
            Route::post(
                'user/course/process',
                [
                    'as' => 'user.course.process',
                    'uses' => 'CourseController@userCourseProcess',
                ]
            );
            /**
             * 收藏部分
             */
            //检测是否收藏
            Route::get(
                'collection/is',
                [
                    'as' => 'collection.is',
                    'uses' => 'CollectionController@is',
                ]
            );
            //添加收藏
            Route::post(
                'collection/add',
                [
                    'as' => 'collection.add',
                    'uses' => 'CollectionController@add',
                ]
            );
            //收藏列表
            Route::get(
                'collection/list',
                [
                    'as' => 'collection.list',
                    'uses' => 'CollectionController@lists',
                ]
            );
            //删除收藏
            Route::post(
                'collection/delete',
                [
                    'as' => 'collection.delete',
                    'uses' => 'CollectionController@delete',
                ]
            );

            /**
             * 订单部分
             */
            //判断订单状态
            Route::post(
                'order/payOrderStatus',
                [
                    'as' => 'order.payOrderStatus',
                    'uses' => 'OrderController@payOrderStatus',
                ]
            );
            //支付完成手动回调
            Route::post(
                'order/rePaySuccessCallback',
                [
                    'as' => 'order.rePaySuccessCallback',
                    'uses' => 'OrderController@rePaySuccessCallback',
                ]
            );
            //VIP会员订单
            Route::post(
                'order/vip',
                [
                    'as' => 'order.vip',
                    'uses' => 'OrderController@makeVipOrder',
                ]
            );
            //课程订单
            Route::post(
                'order/course',
                [
                    'as' => 'order.course',
                    'uses' => 'OrderController@makeCourseOrder',
                ]
            );
            // 未支付课程订单
            Route::post(
                'order/payUnpaidCourse',
                [
                    'as' => 'order.payUnpaidCourse',
                    'uses' => 'OrderController@payUnpaidCourse',
                ]
            );
            //订单详情
            Route::get(
                'order/info',
                [
                    'as' => 'order.info',
                    'uses' => 'OrderController@info',
                ]
            );
            //订单详情
            Route::post(
                'order/paymenyfinish',
                [
                    'as' => 'order/paymenyfinish',
                    'uses' => 'OrderController@paymenyfinish',
                ]
            );
            //取消订单
            Route::post(
                'order/cancel',
                [
                    'as' => 'order.cancel',
                    'uses' => 'OrderController@cancel',
                ]
            );
            //订单列表
            Route::get(
                'order/list',
                [
                    'as' => 'order.list',
                    'uses' => 'OrderController@lists',
                ]
            );

            //用户已购列表
            Route::post(
                'order/has/pay/list',
                [
                    'as' => 'order.has.pay.list',
                    'uses' => 'OrderController@hasPayList',
                ]
            );

            /**
             * 通知相关
             */
            Route::get(
                'notice/list',
                [
                    'as' => 'notice.list',
                    'uses' => 'NoticeController@lists',
                ]
            );
            Route::get(
                'notice/info',
                [
                    'as' => 'notice.info',
                    'uses' => 'NoticeController@info',
                ]
            );
            Route::get(
                'notice/unread',
                [
                    'as' => 'notice.unread',
                    'uses' => 'NoticeController@unRead',
                ]
            );

            /**
             * 开启课程
             */
            Route::post(
                'scan/start/course',
                [
                    'as' => 'scan.start.course',
                    'uses' => 'ScanController@startCourse',
                ]
            );

            /**
             * 医生相关接口
             */
            Route::group(['middleware' => 'api.doctor'], function () { //需要验证是否为医生的操作
                /**
                 * 医生首页
                 */
                Route::post(
                    'doctor/index',
                    [
                        'as' => 'doctor.index',
                        'uses' => 'DoctorController@index',
                    ]
                );

                /**
                 * 消费列表
                 */
                Route::post(
                    'doctor/record/list',
                    [
                        'as' => 'doctor.record.list',
                        'uses' => 'DoctorController@record_list',
                    ]
                );
                /**
                 * 奖金记录
                 */
                Route::post(
                    'doctor/bonus/record',
                    [
                        'as' => 'doctor.bonus.record',
                        'uses' => 'DoctorController@bonus_record',
                    ]
                );
                /**
                 * 记录详细
                 */
                Route::post(
                    'doctor/record/detail',
                    [
                        'as' => 'doctor.record.detail',
                        'uses' => 'DoctorController@record_detail',
                    ]
                );

                /**
                 * 绑定银行卡信息
                 */
                Route::post(
                    'doctor/bankCard/bind',
                    [
                        'as' => 'doctor.bankCard.bind',
                        'uses' => 'BankCardController@bind',
                    ]
                );

                /**
                 * 获取我的银行卡列表
                 */
                Route::post(
                    'doctor/bankCard/list',
                    [
                        'as' => 'doctor.bankCard.list',
                        'uses' => 'BankCardController@myBankCardList',
                    ]
                );

                /**
                 * 可提现余额
                 */
                Route::post(
                    'doctor/money/remainWithdraw',
                    [
                        'as' => 'doctor.money.remainWithdraw',
                        'uses' => 'DoctorMoneyRecordController@remainWithdraw',
                    ]
                );

                /**
                 * 申请提现接口
                 */
                Route::post(
                    'doctor/money/withdraw',
                    [
                        'as' => 'doctor.money.withdraw',
                        'uses' => 'DoctorMoneyRecordController@withdraw',
                    ]
                );

                /**
                 * 医生邀请码信息
                 */
                Route::post(
                    'doctor/invitation/info',
                    [
                        'as' => 'doctor.invitation.info',
                        'uses' => 'DoctorInvitationController@info',
                    ]
                );
            });

            /**
             * 工作人员相关接口
             */
            Route::group(['middleware' => 'api.staff'], function () {
                /**
                 * 工作人员扫码核销课时
                 */
                Route::post(
                    'scan/staff/close/class',
                    [
                        'as' => 'scan.staff.close.class',
                        'uses' => 'ScanController@manualCloseCourseClass',
                    ]
                );

                /**
                 * 工作人员扫码开启机台
                 */
                Route::post(
                    'scan/staff/start/equipment',
                    [
                        'as' => 'scan.staff.start.equipment',
                        'uses' => 'ScanController@manualStartEquipment',
                    ]
                );

                /**
                 * 工作人员扫码完善机台信息
                 */
                Route::post(
                    'scan/staff/register/equipment',
                    [
                        'as' => 'scan.staff.register.equipment',
                        'uses' => 'ScanController@registerEquipment',
                    ]
                );
            });
        });

        /**
         * 定时任务接口
         */

        /**
         * 定时任务结束超时游戏
         */
        Route::get(
            'crontask/close/game',
            [
                'as' => 'crontask.close.game',
                'uses' => 'CronTaskController@autoCloseOverdueGame',
            ]
        );

        /**
         * 定时任务结束每天课时
         */
        Route::get(
            'crontask/close/class',
            [
                'as' => 'crontask.close.class',
                'uses' => 'CronTaskController@autoCloseCourseClass',
            ]
        );

        /**
         * 投币反馈回调接口
         */
        Route::post(
            'equipment/coin/callback',
            [
                'as' => 'equipment.coin.callback',
                'uses' => 'EquipmentController@coinCallback',
            ]
        );

        /**
         * 重置机台流水号
         */
        Route::post(
            'equipment/reset/seq',
            [
                'as' => 'equipment.reset.seq',
                'uses' => 'EquipmentController@resetSeq',
            ]
        );

        /**
         * 初始化游戏参数
         */
        Route::post(
            'equipment/game/init',
            [
                'as' => 'equipment.game.init',
                'uses' => 'EquipmentController@initGame',
            ]
        );

        /**
         * 上报游戏信息
         */
        Route::post(
            'equipment/game/report',
            [
                'as' => 'equipment.game.report',
                'uses' => 'EquipmentController@reportGame',
            ]
        );

        /**
         * 模拟小程序开启游戏
         */
        Route::post(
            'equipment/game/test',
            [
                'as' => 'equipment.game.test',
                'uses' => 'EquipmentController@test',
            ]
        );

        /**
         *返回游戏编号
         */
        Route::post(
            'equipment/game/getGamesModel',
            [
                'as' => 'equipment.game.getGamesModel',
                'uses' => 'EquipmentController@getGamesModel',
            ]
        );
    });
});

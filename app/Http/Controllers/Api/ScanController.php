<?php
namespace App\Http\Controllers\Api;

use App\Helper;
use App\Http\Models\Admin\MemberModel;
use App\Models\EquipmentModel;
use App\Models\OrderModel;
use App\Models\StoreStaffRecordModel;
use App\Models\UserCourseClassModel;
use App\Models\UserCourseModel;
use App\Models\UserCourseRecordModel;
use App\Services\EquipmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Log;

class ScanController extends Controller
{
    /**
     * 启动机台
     */
    public function startCourse(Request $request)
    {
        $order_num = $request->input('order_num');
        $content = $request->input('content');
        $content = Helper::AES($content, false);

        if ($content == false) {
            return $this->error('二维码内容识别错误！');
        }

        list($player, $serial_no) = explode('|', $content);
        if (empty($player) || empty($serial_no)) {
            return $this->error('二维码内容错误！');
        }

        // 判断是否有订单
        $courseId = OrderModel::where('order_num', $order_num)->where('user_id', $this->auth->user_id)->whereIn('status', [1, 5])->value('course_id');
        if (empty($courseId)) {
            return $this->error('订单不存在');
        }

        // 判断课程记录
        $userCourse = UserCourseModel::with('course')->where('order_num', $order_num)->where('user_id', $this->auth->user_id)->first();
        if (empty($userCourse)) {
            return $this->error('没有购买该课程记录');
        }

        if ($userCourse['is_finish']) {
            return $this->error('该课程已结束');
        }

        if ($userCourse['finish_num'] >= $userCourse['class_num']) {
            return $this->error('课程已结束');
        }

        // 判断该设备是否在适用门店内
        $equipmentInfos = EquipmentModel::where('code', $serial_no)->first();
        $storeIds = explode(',', $userCourse->course->store_ids);
        if (!in_array($equipmentInfos['store_id'], $storeIds)) {
            return $this->error('该课程不适用于当前门店');
        }

        // 获取最新课时信息
        $userCourseClass = UserCourseClassModel::with(['class', 'store'])->where('id', $userCourse['next_class_id'])->first();

        // 判断课时是否已在别的适用门店核销
        if (!empty($userCourseClass['store_id']) && $userCourseClass['store_id'] != $equipmentInfos['store_id']) {
            return $this->error('您已经在 ' . $userCourseClass->store->name . ' 核销了该课时哦');
        }

        // 判断已玩课时次数是否超出
        if ($userCourseClass['times'] >= $userCourseClass->total_times) {
            return $this->error('课时游戏次数已达上限');
        }

        // 判断是否正在游戏中（一次只能开启一个机台游戏）
        if (!empty(UserCourseRecordModel::where('user_id', $this->auth->user_id)->whereNull('finish_at')->first())) {
            return $this->error('您开启的游戏还没结束哦');
        }

        // 判断机台状态
        $t1 = microtime(true);
        $resp = EquipmentService::getMachineStatus($serial_no);
        $t2 = microtime(true);
        Log::debug('1. 判断机台状态耗时：' . round($t2 - $t1, 3) . '秒');

        if (empty($resp) || $resp['code']) {
            return $this->error('设备状态出错');
        }

        switch ($resp['data'][$player]) {
            case 1: // 空闲
                // 发送用户ID 到机台
                $t1 = microtime(true);
                EquipmentService::sendUserId($serial_no, $player, $this->auth->user_id);
                $t2 = microtime(true);
                Log::debug('2. 发送用户ID耗时：' . round($t2 - $t1, 3) . '秒');

                // 启动机台
                $t1 = microtime(true);
                $resp = EquipmentService::startMachine($serial_no, $player);
                $t2 = microtime(true);
                Log::debug('3. 启动机台耗时：' . round($t2 - $t1, 3) . '秒');

                if ($resp['code']) {
                    return $this->error('启动机台失败，请稍后再试');
                }

                // 再次判断机台状态, 失败尝试判断状态三次
                $times = 0;
                $isStart = 0;
                while ($times < 3) {
                    $t1 = microtime(true);
                    $resp = EquipmentService::getMachineStatus($serial_no);
                    $t2 = microtime(true);
                    Log::debug('4. 再次判断机台状态耗时：' . round($t2 - $t1, 3) . '秒');

                    if ($resp['code'] || $resp['data'][$player] != 2) {
                        $times++;
                        sleep(1);
                        continue;
                    }

                    $isStart = 1;
                    break;
                }

                if (!$isStart) {
                    return $this->error('开启机台玩家位失败，请重新再试');
                }

                $startAt = date('Y-m-d H:i:s');
                // 更新课程记录
                if (empty($userCourse['class_id'])) {
                    // 首次消费课程
                    UserCourseModel::where('id', $userCourse['id'])
                        ->update([
                            'started_at' => $startAt,
                        ]);
                }

                // 更新课时记录
                if (empty($userCourseClass['start_at'])) {
                    // 首次消费课时
                    UserCourseClassModel::where('id', $userCourse['next_class_id'])
                        ->update([
                            'start_at' => $startAt,
                            'store_id' => $equipmentInfos['store_id'],
                        ]);
                }

                // 记录课时每次游戏信息（记录设备信息）
                $userCourseRecordModel = new UserCourseRecordModel();
                $userCourseRecordModel->user_course_class_id = $userCourse['next_class_id'];
                $userCourseRecordModel->user_id = $this->auth->user_id;
                $userCourseRecordModel->equipment = $serial_no; // 设备号
                $userCourseRecordModel->pos = $player; // 位号
                $userCourseRecordModel->start_at = $startAt;
                $userCourseRecordModel->save();

                return $this->success($userCourseClass->class);
                break;

            case 2: // 正在游戏
                return $this->error('该玩家位正在游戏中，请换个玩家位');
                break;

            case 3: // 无法使用待维修
                return $this->error('设备维修中');
                break;

            case 4: // 已禁用
                return $this->error('设备已禁用');
                break;
        }
    }

    /**
     * 游戏结束回调
     *
     * @param Request $request
     * @return void
     */
    public function endCourse(Request $request)
    {
        $serial_no = $request->input('serial_no');
        $player = (int) $request->input('player');

        if (empty($serial_no) || empty($player)) {
            return $this->error('请求无效');
        }

        // Log::debug($serial_no . '|' . $player);

        $userCourseRecord = UserCourseRecordModel::where('equipment', $serial_no)
            ->where('pos', $player)
            ->whereNull('finish_at')
            ->first();
        if (!empty($userCourseRecord)) {
            // 开启事务
            DB::beginTransaction();

            try {
                $resp1 = DB::table('user_course_record')->where('id', $userCourseRecord['id'])
                    ->update(['finish_at' => date('Y-m-d H:i:s')]);

                $resp2 = DB::table('user_course_class')->where('id', $userCourseRecord['user_course_class_id'])
                    ->increment('times'); //  课时已玩次数+1

                if ($resp1 && $resp2) {
                    DB::commit();

                    // 判断游戏次数是否已达上限来结束课程
                    $userCourseClass = UserCourseClassModel::with(['class'])->where('id', $userCourseRecord['user_course_class_id'])->first();
                    if ($userCourseClass->times >= $userCourseClass->total_times) {
                        $userCourse = UserCourseModel::where('id', $userCourseClass['user_course_id'])->first()->toArray();
                        // 获取下一节课
                        $nextClassInfo = UserCourseClassModel::where('user_course_id', $userCourseClass['user_course_id'])
                            ->where('id', '>', $userCourseClass['id'])
                            ->first();
                        $next_class_id = $nextClassInfo['id'] ?? 0;

                        // 已完成课时ID
                        $class_id = '';
                        if (empty($userCourse['class_id'])) {
                            $class_id = $userCourseClass['class_id'];
                        } else {
                            $classIdArr = explode(',', $userCourse['class_id']);
                            array_push($classIdArr, $userCourseClass['class_id']);
                            $class_id = implode(',', array_unique($classIdArr));
                        }

                        // 完成课程数
                        $finish_num = count(array_unique(explode(',', $class_id)));

                        // 是否完成课程
                        $is_finish = ($finish_num == $userCourse['class_num']) ? 1 : 0;

                        // 开启事务
                        DB::beginTransaction();

                        try {
                            $resp1 = DB::table('user_course_class')->where('id', $userCourseClass['id'])
                                ->update(['finish_at' => date('Y-m-d H:i:s')]);

                            if ($is_finish) {
                                $data = [
                                    'next_class_id' => $next_class_id,
                                    'prev_class_id' => $userCourseClass['id'],
                                    'class_id' => $class_id,
                                    'finish_num' => $finish_num,
                                    'is_finish' => $is_finish,
                                    'finish_at' => date('Y-m-d H:i:s'),
                                ];
                            } else {
                                $data = [
                                    'next_class_id' => $next_class_id,
                                    'prev_class_id' => $userCourseClass['id'],
                                    'class_id' => $class_id,
                                    'finish_num' => $finish_num,
                                    'is_finish' => $is_finish,
                                ];
                            }

                            $resp2 = DB::table('user_course')->where('id', $userCourseClass['user_course_id'])
                                ->update($data);

                            if ($resp1 && $resp2) {
                                DB::commit();
                            } else {
                                DB::rollback();
                            }
                        } catch (\Exception $e) {
                            DB::rollback();
                            Log::error($e->getMessage());
                        }
                    }

                    return $this->success([]);
                } else {
                    DB::rollback();
                    return $this->error('结束游戏失败');
                }
            } catch (\Exception $e) {
                DB::rollback();
                Log::error($e->getMessage());
                return $this->error($e->getMessage());
            }
        } else {
            return $this->error('无未完成游戏信息');
        }
    }

    /**
     * 用户出示核销二维码给工作人员扫码核销，消耗1课时【暂时弃用，需考虑兼容课时次数核销】
     *
     * 场景：网络问题，智联宝问题等等
     *
     * @return void
     */
    // public function manualCloseCourseClass(Request $request)
    // {
    //     $content = $request->input('content');
    //     $content = Helper::AES($content, false);

    //     if ($content == false) {
    //         return $this->error('二维码内容识别错误！');
    //     }

    //     list($orderNum, $userCourseId, $userId) = explode('|', $content);
    //     if (empty($orderNum) || empty($userCourseId) || empty($userId)) {
    //         return $this->error('二维码内容错误！');
    //     }

    //     // 判断课程适用门店
    //     $storeId = MemberModel::where('id', $this->auth->user_id)->where('role', 3)->where('status', 1)->value('store_id');
    //     if (empty($storeId)) {
    //         return $this->error('您没有该门店操作权限');
    //     }

    //     $userCourse = UserCourseModel::with('course')->where('id', $userCourseId)->where('user_id', $userId)->first();
    //     if (empty($userCourse)) {
    //         return $this->error('没有课程信息');
    //     }

    //     $storeIds = explode(',', $userCourse->course->store_ids);

    //     if (!in_array($storeId, $storeIds)) {
    //         return $this->error('该课程不适用于当前门店');
    //     }

    //     // 获取最新未完成课时
    //     $newestUnfinishClass = UserCourseClassModel::where('user_course_id', $userCourseId)
    //         ->where('user_id', $userId)
    //         ->whereNull('finish_at')
    //         ->first();

    //     if (empty($newestUnfinishClass)) {
    //         return $this->error('课程已结束');
    //     }

    //     // 获取下一节课
    //     $nextClassInfo = UserCourseClassModel::where('user_course_id', $userCourseId)
    //         ->where('id', '>', $newestUnfinishClass['id'])
    //         ->first();
    //     $next_class_id = $nextClassInfo['id'] ?? 0;

    //     // 已完成课时ID
    //     $class_id = '';
    //     if (empty($userCourse['class_id'])) {
    //         $class_id = $newestUnfinishClass['class_id'];
    //     } else {
    //         $classIdArr = explode(',', $userCourse['class_id']);
    //         array_push($classIdArr, $newestUnfinishClass['class_id']);
    //         $class_id = implode(',', array_unique($classIdArr));
    //     }

    //     // 完成课时数
    //     $finish_num = count(array_unique(explode(',', $class_id)));

    //     // 是否完成课程
    //     $is_finish = ($finish_num == $userCourse['class_num']) ? 1 : 0;

    //     // 开启事务
    //     DB::beginTransaction();

    //     try {
    //         $resp1 = DB::table('user_course_class')
    //             ->where('id', $newestUnfinishClass['id'])
    //             ->update([
    //                 'start_at' => date('Y-m-d H:i:s'),
    //                 'finish_at' => date('Y-m-d H:i:s'),
    //                 'operator' => $this->auth->user_id,
    //                 'store_id' => $storeId,
    //             ]);

    //         if ($is_finish) {
    //             $data = [
    //                 'next_class_id' => $next_class_id,
    //                 'prev_class_id' => $newestUnfinishClass['id'],
    //                 'class_id' => $class_id,
    //                 'finish_num' => $finish_num,
    //                 'is_finish' => $is_finish,
    //                 'finish_at' => date('Y-m-d H:i:s'),
    //             ];
    //         } else {
    //             $data = [
    //                 'next_class_id' => $next_class_id,
    //                 'prev_class_id' => $newestUnfinishClass['id'],
    //                 'class_id' => $class_id,
    //                 'finish_num' => $finish_num,
    //                 'is_finish' => $is_finish,
    //             ];

    //             if (empty($userCourse['started_at'])) {
    //                 $data['started_at'] = date('Y-m-d H:i:s');
    //             }
    //         }

    //         $resp2 = DB::table('user_course')->where('id', $newestUnfinishClass['user_course_id'])
    //             ->update($data);

    //         if ($resp1 && $resp2) {
    //             DB::commit();
    //             return $this->success([]);
    //         } else {
    //             DB::rollback();
    //             return $this->error('核销课程失败, 请稍后再试');
    //         }
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         Log::error($e->getMessage());
    //         return $this->error($e->getMessage());
    //     }
    // }

    /**
     * 工作人员扫码开启机台
     *
     * @param Request $request
     * @return void
     */
    public function manualStartEquipment(Request $request)
    {
        $content = $request->input('content');
        $content = Helper::AES($content, false);

        if ($content == false) {
            return $this->error('二维码内容识别错误！');
        }

        list($player, $serial_no) = explode('|', $content);
        if (empty($player) || empty($serial_no)) {
            return $this->error('二维码内容错误！');
        }

        // 判断该设备是否在适用门店内
        $equipmentInfos = EquipmentModel::where('code', $serial_no)->first();
        if (empty($equipmentInfos)) {
            return $this->error('该机台还未绑定门店');
        }

        $storeId = MemberModel::where('id', $this->auth->user_id)->where('role', 3)->where('status', 1)->value('store_id');
        if (empty($storeId)) {
            return $this->error('您没有该门店操作权限');
        }

        if ($equipmentInfos['store_id'] != $storeId) {
            return $this->error('您没有该设备操作权限');
        }

        // 判断机台状态
        $t1 = microtime(true);
        $resp = EquipmentService::getMachineStatus($serial_no);
        $t2 = microtime(true);
        Log::debug('1. 判断机台状态耗时：' . round($t2 - $t1, 3) . '秒');

        if (empty($resp) || $resp['code']) {
            return $this->error('设备状态出错');
        }

        switch ($resp['data'][$player]) {
            case 1: // 空闲
                // 启动机台
                $t1 = microtime(true);
                $resp = EquipmentService::startMachine($serial_no, $player);
                $t2 = microtime(true);
                Log::debug('2. 启动机台耗时：' . round($t2 - $t1, 3) . '秒');

                if ($resp['code']) {
                    return $this->error('启动机台失败，请稍后再试');
                }

                // 再次判断机台状态, 失败尝试判断状态三次
                $times = 0;
                $isStart = 0;
                while ($times < 3) {
                    $t1 = microtime(true);
                    $resp = EquipmentService::getMachineStatus($serial_no);
                    $t2 = microtime(true);
                    Log::debug('3. 再次判断机台状态耗时：' . round($t2 - $t1, 3) . '秒');

                    if (empty($resp) || $resp['code'] || $resp['data'][$player] != 2) {
                        $times++;
                        sleep(1);
                        continue;
                    }

                    $isStart = 1;
                    break;
                }

                // 记录工作人员启动信息（记录设备信息）
                $record = new StoreStaffRecordModel();
                $record->store_id = $storeId;
                $record->user_id = $this->auth->user_id;
                $record->equipment = $serial_no; // 设备号
                $record->pos = $player; // 位号
                $record->save();

                return $this->success([]);
                break;

            case 2: // 正在游戏
                return $this->error('该玩家位正在游戏中，请换个玩家位');
                break;

            case 3: // 无法使用待维修
                return $this->error('设备维修中');
                break;

            case 4: // 已禁用
                return $this->error('设备已禁用');
                break;
        }
    }

    /**
     * 扫码完善机台信息
     *
     * @param Request $request
     * @return void
     */
    public function registerEquipment(Request $request)
    {
        $cityId = $request->input('city_id');
        $storeId = $request->input('store_id');
        $model = $request->input('model', '');
        $name = $request->input('name');
        $content = $request->input('content');

        if (empty($cityId)) {
            return $this->error('请选择城市');
        }

        if (empty($storeId)) {
            return $this->error('请选择门店');
        }

        if (!is_numeric($cityId) || !is_numeric($storeId)) {
            return $this->error('参数错误，请稍后再试');
        }

        if (empty($name)) {
            return $this->error('请输入设备名称');
        }

        $content = Helper::AES($content, false);
        if ($content == false) {
            return $this->error('二维码内容识别错误！');
        }

        list($player, $serial_no) = explode('|', $content);
        if (empty($player) || empty($serial_no)) {
            return $this->error('二维码内容错误！');
        }

        $equipmentInfos = EquipmentModel::where('code', $serial_no)->first();
        if (!empty($equipmentInfos)) {
            if($equipmentInfos['store_id'] != 1){
                // 默认中山世宇店为测试门店，绑定此门店的可再次绑定到新的门店
                return $this->error('该机台已注册绑定');
            }

            $res = EquipmentModel::where('code', $serial_no)->update([
                'city_id' => $cityId,
                'store_id' => $storeId,
                'model' => $model,
                'code' => $serial_no,
                'name' => $name,
            ]);
        } else {
            $res = EquipmentModel::create([
                'city_id' => $cityId,
                'store_id' => $storeId,
                'model' => $model,
                'code' => $serial_no,
                'name' => $name,
            ]);
        }

        if ($res) {
            return $this->success(['serial_no' => $serial_no]);
        } else {
            return $this->error('该机台绑定失败，请稍后再试');
        }
    }
}

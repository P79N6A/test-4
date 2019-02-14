<?php
namespace App\Http\Controllers\Api;

use App\Models\UserCourseClassModel;
use App\Models\UserCourseModel;
use App\Models\UserCourseRecordModel;
use Illuminate\Support\Facades\DB;
use Log;

/**
 * 定时任务
 */
class CronTaskController extends Controller
{
    // 每分钟定时查询结束游戏超时状态(游戏结束后7分钟如果还没结束则自动完结)
    public function autoCloseOverdueGame()
    {
        $records = UserCourseRecordModel::where('start_at', '<', date('Y-m-d H:i:s', (time() - 7 * 60)))
            ->whereNull('finish_at')
            ->get();

        foreach ($records as $record) {
            // 开启事务
            DB::beginTransaction();

            try {
                $resp1 = DB::table('user_course_record')->where('id', $record['id'])
                    ->update(['finish_at' => date('Y-m-d H:i:s')]);

                $resp2 = DB::table('user_course_class')->where('id', $record['user_course_class_id'])
                    ->increment('times'); //  课时已玩次数+1

                if ($resp1 && $resp2) {
                    DB::commit();

                    // 判断游戏次数是否已达上限来结束课程
                    $userCourseClass = UserCourseClassModel::with(['class'])->where('id', $record['user_course_class_id'])->first();
                    if($userCourseClass->times >= $userCourseClass->total_times){
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
                } else {
                    DB::rollback();
                }
            } catch (\Exception $e) {
                DB::rollback();
                Log::error($e->getMessage());
            }
        }

        return $this->success([], 0, '执行成功');
    }

    // // 每天零点定时结束课时超时状态
    // public function autoCloseCourseClass()
    // {
    //     $list = UserCourseClassModel::where('start_at', '<', date('Y-m-d H:i:s'))
    //         ->whereNull('finish_at')->get();

    //     foreach ($list as $key => $userCourseClass) {
    //         $userCourse = UserCourseModel::where('id', $userCourseClass['user_course_id'])->first()->toArray();
    //         // 获取下一节课
    //         $nextClassInfo = UserCourseClassModel::where('user_course_id', $userCourseClass['user_course_id'])
    //             ->where('id', '>', $userCourseClass['id'])
    //             ->first();
    //         $next_class_id = $nextClassInfo['id'] ?? 0;

    //         // 已完成课时ID
    // $class_id = '';
            // if (empty($userCourse['class_id'])) {
            //     $class_id = $userCourseClass['class_id'];
            // } else {
            //     $classIdArr = explode(',', $userCourse['class_id']);
            //     array_push($classIdArr, $userCourseClass['class_id']);
            //     $class_id = implode(',', array_unique($classIdArr));
            // }

    //         // 完成课程数
    //         $finish_num = count(array_unique(explode(',', $class_id)));

    //         // 是否完成课程
    //         $is_finish = ($finish_num == $userCourse['class_num']) ? 1 : 0;

    //         // 开启事务
    //         DB::beginTransaction();

    //         try {
    //             $resp1 = DB::table('user_course_class')->where('id', $userCourseClass['id'])
    //                 ->update(['finish_at' => date('Y-m-d H:i:s')]);

    //             if ($is_finish) {
    //                 $data = [
    //                     'next_class_id' => $next_class_id,
    //                     'prev_class_id' => $userCourseClass['id'],
    //                     'class_id' => $class_id,
    //                     'finish_num' => $finish_num,
    //                     'is_finish' => $is_finish,
    //                     'finish_at' => date('Y-m-d H:i:s'),
    //                 ];
    //             } else {
    //                 $data = [
    //                     'next_class_id' => $next_class_id,
    //                     'prev_class_id' => $userCourseClass['id'],
    //                     'class_id' => $class_id,
    //                     'finish_num' => $finish_num,
    //                     'is_finish' => $is_finish,
    //                 ];
    //             }

    //             $resp2 = DB::table('user_course')->where('id', $userCourseClass['user_course_id'])
    //                 ->update($data);

    //             if ($resp1 && $resp2) {
    //                 DB::commit();
    //             } else {
    //                 DB::rollback();
    //             }
    //         } catch (\Exception $e) {
    //             DB::rollback();
    //             Log::error($e->getMessage());
    //         }
    //     }

    //     return $this->success([], 0, '执行成功');
    // }

    // 每天零点定时结束课时超时状态
    public function autoCloseCourseClass()
    {
        $list = UserCourseClassModel::with(['class'])->where('start_at', '<', date('Y-m-d H:i:s'))
            ->whereNull('finish_at')->get();

        foreach ($list as $key => $userCourseClass) {
            // 判断课时游戏次数是否已达到上限
            if($userCourseClass->times >= $userCourseClass->total_times){
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

        }

        return $this->success([], 0, '执行成功');
    }
}

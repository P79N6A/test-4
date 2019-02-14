<?php
namespace App\Http\Controllers\Api;

use App\Models\EquipmentSeqModel;
use App\Models\GamesLevelModel;
use App\Models\UserCourseRecordModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Log;

class EquipmentController extends Controller
{
    /**
     * 重置机台流水号【暂时弃用】
     */
    // public function resetSeq(Request $request)
    // {
    //     $serialNo = $request->input('serial_no');
    //     if (empty($serialNo)) {
    //         return $this->error('请求无效');
    //     }

    //     EquipmentSeqModel::where('serial_no', $serialNo)->delete();

    //     return $this->success([]);
    // }

    /**
     * 投币反馈【暂时弃用】
     *
     * @param Request $request
     * @return void
     */
    // public function coinCallback(Request $request)
    // {
    //     $serialNo = $request->input('serial_no');
    //     $params = $request->input('params');

    //     if (empty($serialNo) || empty($params)) {
    //         return $this->error('请求无效');
    //     }

    //     Log::debug($serialNo . '|' . json_encode($params));

    //     // 判断是否重复请求
    //     $has = EquipmentSeqModel::where('serial_no', $serialNo)->where('seq', $params['seq'])->first();
    //     if ($has) {
    //         return $this->error('重复请求');
    //     }

    //     // 开启事务
    //     DB::beginTransaction();

    //     try {
    //         // 添加新单号记录
    //         $resp1 = DB::table('iot_equipments_seq')
    //             ->insert([
    //                 'serial_no' => $serialNo,
    //                 'seq' => $params['seq'],
    //                 'orderid' => $params['orderId'],
    //                 'created_at' => date('Y-m-d H:i:s'),
    //             ]);

    //         // 添加投币营收数据
    //         $resp2 = DB::table('iot_equipments_revenue')
    //             ->insert([
    //                 'serial_no' => $serialNo,
    //                 'type' => $params['type'],
    //                 'pos' => $params['pos'],
    //                 'seq' => $params['seq'],
    //                 'new_coin' => $params['newCoin'],
    //                 'total_coin' => $params['totalCoin'],
    //                 'orderid' => $params['orderId'],
    //                 'created_at' => time(),
    //             ]);

    //         if ($resp1 && $resp2) {
    //             DB::commit();
    //             return $this->success([]);
    //         } else {
    //             DB::rollback();
    //             return $this->error('记录投币反馈失败');
    //         }
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         Log::error($e->getMessage());
    //         return $this->error($e->getMessage());
    //     }
    // }

    /**
     * 初始化游戏
     */
    public function initGame(Request $request)
    {
        $raw = file_get_contents('php://input'); //接收body数据
        $data = json_decode($raw, true);

        Log::debug('---init-start---');
        Log::debug($raw);
        Log::debug($data);
        Log::debug('---init-end---');

        if (!is_array($data)) {
            return $this->error('参数错误', 10000);
        }

        if (!isset($data['user_id']) || empty($data['user_id']) || !is_numeric($data['user_id'])) {
            return $this->error('请输入正确的user_id', 10001);
        }

        if (!isset($data['game_id']) || empty($data['game_id']) || strpos($data['game_id'], 'G-') !== 0) {
            return $this->error('请输入正确的game_id', 10002);
        }

        $last = UserCourseRecordModel::where('user_id', $data['user_id'])
            ->where('game_id', $data['game_id'])
            ->orderBy('created_at', 'desc')
            ->first();
        if (empty($last)) {
            // 默认参数
            return $this->error('请启用默认游戏参数', 10009);
        } else {
            // if(strlen($last['game_id'])>5){
            //     $result = $this->gamesAnalysis($last);
            // }else{
            //     $result = $this->gamesAnalysis_old($last);
            // }
            $result = $this->gamesAnalysis_new($last);
        }

        return $this->success([$result]);
    }

    /**
     *游戏数据分析（新）参数不写死
     */
    public function gamesAnalysis_new($last){
        $result = GamesLevelModel::where('model',$last['game_id'])->get();
        $result = json_decode(json_encode($result),true);

        foreach ($result as $arr => $val) {
            if($val['score_begin'] < $last['score'] && $last['score'] < $val['score_end']){
                $data = json_decode(json_encode(json_decode($val['data'])),true);
                break;
            }
        }
        return $data;
    }

    /**
     *游戏数据分析（新机台游戏） 参数写死
     */
    public function gamesAnalysis($last){
        switch ($last['game_id']) {
            case 'G-00201':
                if ($last->score > 1.4) {
                    $speed = 5;
                    $scene = 0;
                } elseif ($last->score > 1.05) {
                    $speed = 4;
                    $scene = 1;
                } elseif ($last->score > 0.7) {
                    $speed = 3;
                    $scene = 2;
                } elseif ($last->score > 0.35) {
                    $speed = 2;
                    $scene = 3;
                } else {
                    $speed = 1;
                    $scene = 4;
                }
                break;

            case 'G-00202':
                if ($last->score > 8) {
                    $speed = 5;
                    $scene = 1;
                } elseif ($last->score > 7.2) {
                    $speed = 4;
                    $scene = 2;
                } elseif ($last->score > 6.4) {
                    $speed = 3;
                    $scene = 3;
                } elseif ($last->score > 5.6) {
                    $speed = 2;
                    $scene = 4;
                } else {
                    $speed = 1;
                    $scene = 5;
                }
                break;

            default:
                return $this->error('暂不支持该游戏，请启用默认游戏参数', 10010);
                break;
        }
        $data['speed'] = $speed;
        $data['scene'] = $scene;
        return $data;
    }

    /**
     *游戏数据分析（旧机台游戏）参数写死
     */
    public function gamesAnalysis_old($last){
        switch ($last['game_id']) {
            case 'G-001':
                if ($last->score > 1600) {
                    $speed = 5;
                    $scene = 1;
                } elseif ($last->score > 1200) {
                    $speed = 4;
                    $scene = 2;
                } elseif ($last->score > 800) {
                    $speed = 3;
                    $scene = 3;
                } elseif ($last->score > 400) {
                    $speed = 2;
                    $scene = 4;
                } else {
                    $speed = 1;
                    $scene = 5;
                }
                break;

            case 'G-002':
                if ($last->score > 1.4) {
                    $speed = 5;
                    $scene = 0;
                } elseif ($last->score > 1.05) {
                    $speed = 4;
                    $scene = 1;
                } elseif ($last->score > 0.7) {
                    $speed = 3;
                    $scene = 2;
                } elseif ($last->score > 0.35) {
                    $speed = 2;
                    $scene = 3;
                } else {
                    $speed = 1;
                    $scene = 4;
                }
                break;

            case 'G-003':
                if ($last->score > 8) {
                    $speed = 5;
                    $scene = 0;
                } elseif ($last->score > 7.2) {
                    $speed = 4;
                    $scene = 1;
                } elseif ($last->score > 6.4) {
                    $speed = 3;
                    $scene = 2;
                } elseif ($last->score > 5.6) {
                    $speed = 2;
                    $scene = 3;
                } else {
                    $speed = 1;
                    $scene = 4;
                }
                break;

            case 'G-004':
                if ($last->score > 8) {
                    $speed = 5;
                    $scene = 1;
                } elseif ($last->score > 7.2) {
                    $speed = 4;
                    $scene = 2;
                } elseif ($last->score > 6.4) {
                    $speed = 3;
                    $scene = 3;
                } elseif ($last->score > 5.6) {
                    $speed = 2;
                    $scene = 4;
                } else {
                    $speed = 1;
                    $scene = 5;
                }
                break;

            case 'G-005':
                if ($last->score > 4000) {
                    $speed = 5;
                    $scene = 3;
                } elseif ($last->score > 3000) {
                    $speed = 4;
                    $scene = 3;
                } elseif ($last->score > 2000) {
                    $speed = 3;
                    $scene = 2;
                } elseif ($last->score > 1000) {
                    $speed = 2;
                    $scene = 2;
                } else {
                    $speed = 1;
                    $scene = 1;
                }
                break;

            case 'G-006':
                switch ($last->score) {
                    case 10:
                        $speed = rand(1, 5);
                        $scene = rand(1, 3);
                        break;

                    case 9:
                        $speed = 5;
                        $scene = 1;
                        break;

                    case 8:
                        $speed = 4;
                        $scene = 1;
                        break;

                    case 7:
                        $speed = 3;
                        $scene = 1;
                        break;

                    case 6:
                        $speed = 2;
                        $scene = 1;
                        break;
                    
                    default:
                        $speed = 1;
                        $scene = 1;
                        break;
                }
                break;

            case 'G-007':
                if ($last->score > 0.66) {
                    $speed = 0;
                    $scene = 1;
                } elseif ($last->score > 0.33) {
                    $speed = 1;
                    $scene = 1;
                } else {
                    $speed = 2;
                    $scene = 1;
                }
                break;

            case 'G-008':
                if ($last->score > 12) {
                    $speed = 5;
                    $scene = 1;
                } elseif ($last->score > 9) {
                    $speed = 4;
                    $scene = 1;
                } elseif ($last->score > 6) {
                    $speed = 3;
                    $scene = 1;
                } elseif ($last->score > 3) {
                    $speed = 2;
                    $scene = 1;
                } else {
                    $speed = 1;
                    $scene = 1;
                }

                break;

            case 'G-010':
                if ($last->score >= 9) {
                    $speed = 5;
                    $scene = 1;
                } elseif ($last->score >= 8) {
                    $speed = 4;
                    $scene = 1;
                } elseif ($last->score >= 6) {
                    $speed = 3;
                    $scene = 1;
                } elseif ($last->score >= 4) {
                    $speed = 2;
                    $scene = 1;
                } else {
                    $speed = 1;
                    $scene = 1;
                }

                break;

            case 'G-011':
                if ($last->score > 1000) {
                    $speed = 3;
                    $scene = 2;
                } elseif ($last->score > 500) {
                    $speed = 2;
                    $scene = 2;
                } else {
                    $speed = 1;
                    $scene = 1;
                }

                break;

            case 'G-016':
                if ($last->score >= 9) {
                    $speed = 5;
                    $scene = 1;
                } elseif ($last->score >= 8) {
                    $speed = 4;
                    $scene = 1;
                } elseif ($last->score >= 6) {
                    $speed = 3;
                    $scene = 1;
                } elseif ($last->score >= 4) {
                    $speed = 2;
                    $scene = 1;
                } else {
                    $speed = 1;
                    $scene = 1;
                }

                break;

            default:
                return $this->error('暂不支持该游戏，请启用默认游戏参数', 10010);
                break;
        }
        $data['speed'] = $speed;
        $data['scene'] = $scene;
        return $data;
    }

    /**
     * 游戏结束上报
     */
    public function reportGame(Request $request)
    {
        $raw = file_get_contents('php://input'); //接收body数据
        $data = json_decode($raw, true);

        Log::debug('---report-start---');
        Log::debug($raw);
        Log::debug($data);
        Log::debug('---report-end---');

        if (!is_array($data)) {
            return $this->error('参数错误', 10000);
        }

        if (!isset($data['user_id']) || empty($data['user_id']) || !is_numeric($data['user_id'])) {
            return $this->error('请输入正确的user_id', 10001);
        }

        if (!isset($data['game_id']) || empty($data['game_id']) || strpos($data['game_id'], 'G-') !== 0) {
            return $this->error('请输入正确的game_id', 10002);
        }

        if (!isset($data['score']) || !is_numeric($data['score']) || $data['score'] < 0) {
            return $this->error('请输入正确的score', 10003);
        }

        if (!isset($data['speed']) || !is_numeric($data['speed']) || $data['speed'] < 0) {
            return $this->error('请输入正确的speed', 10004);
        }

        if (isset($data['scene']) && !empty($data['scene']) && !is_numeric($data['scene']) || $data['scene'] < 0) {
            return $this->error('请输入正确的scene', 10005);
        } else {
            $data['scene'] = $data['scene'] ? : 1;
        }

        if (!isset($data['equipment']) || empty($data['equipment'])) {
            return $this->error('请输入正确的智联宝设备号', 10006);
        }

        if (!isset($data['pos']) || empty($data['pos']) || !is_numeric($data['pos'])) {
            $data['pos'] = 1;
        }

        // 查询最新的游戏
        $last = UserCourseRecordModel::where('user_id', $data['user_id'])
            ->where('equipment', $data['equipment'])
            ->where('pos', $data['pos'])
            ->orderBy('created_at', 'desc')
            ->first();

        if (empty($last)) {
            return $this->error('无游戏记录', 10007);
        } else {
            if (!empty($last['game_id'])) {
                return $this->error('重复记录', 10008);
            }
        }

        // 保存游戏信息
        UserCourseRecordModel::where('id', $last['id'])->update([
            'game_id' => $data['game_id'],
            'score' => $data['score'],
            'speed' => $data['speed'],
            'scene' => $data['scene'],
        ]);

        return $this->success([], 0, '保存成功');
    }

    /**
     *返回游戏编号
     */
    public function getGamesModel(Request $request){
        $raw = file_get_contents('php://input'); //接收body数据
        $data = json_decode($raw, true);

        if (!is_array($data)) {
            return $this->error('参数错误', 10000);
        }

        if (!isset($data['user_id']) || empty($data['user_id']) || !is_numeric($data['user_id'])) {
            return $this->error('请输入正确的user_id', 10001);
        }

        if (!isset($data['equipment_model']) || empty($data['equipment_model'])) {
            return $this->error('请输入正确的机台型号', 10012);
        }

        $equipment_model = DB::table('iot_equipments')->where('model',$data['equipment_model'])->first();
        if(empty($equipment_model)){
            return $this->error('请输入正确的机台型号', 10012);
        }

        $games_model = DB::table('iot_equipments as equipment')
                        ->leftjoin('iot_equipments_games as games','games.equipment_id','=','equipment.id')
                        ->where('equipment.model',$data['equipment_model'])
                        ->where('games.disabled',0)
                        ->select('games.model')
                        ->inRandomOrder()->first();

        

        if(!isset($games_model)){
            return $this->error('该机台没有绑定的游戏', 10002);
        }

        Log::debug('---getGamesModel-start---');
        Log::debug('data:'.$raw);
        Log::debug('games_model:'.$games_model->model);
        Log::debug('---getGamesModel-end---');

        return $this->success([
            'games_model' => $games_model->model,
        ]);
    }

    /**
     * 调试接口
     */
    public function test(Request $request){
        $raw = file_get_contents('php://input'); //接收body数据
        $data = json_decode($raw, true);

        Log::debug('---report-start---');
        Log::debug($raw);
        Log::debug($data);
        Log::debug('---report-end---');

        if (!is_array($data)) {
            return $this->error('参数错误', 10000);
        }

        if (!isset($data['user_id']) || empty($data['user_id']) || !is_numeric($data['user_id']) || $data['user_id'] < 20000) {
            return $this->error('请输入正确的user_id', 10001);
        }

        if (!isset($data['equipment']) || empty($data['equipment'])) {
            return $this->error('请输入正确的智联宝设备号', 10006);
        }

        if (!isset($data['pos']) || empty($data['pos']) || !is_numeric($data['pos'])) {
            $data['pos'] = 1;
        }

        $userCourseRecord = new UserCourseRecordModel();
        $userCourseRecord->user_course_class_id = 888;
        $userCourseRecord->user_id = $data['user_id'];
        $userCourseRecord->equipment = $data['equipment'];
        $userCourseRecord->pos = $data['pos'];
        $userCourseRecord->start_at = date('Y-m-d H:i:s');
        $userCourseRecord->finish_at = date('Y-m-d H:i:s');
        $userCourseRecord->created_at = date('Y-m-d H:i:s');
        $userCourseRecord->updated_at = date('Y-m-d H:i:s');

        $userCourseRecord->save();

        return $this->success([], 0, '开启成功');
    }
}

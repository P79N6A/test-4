<?php
namespace App\Http\Controllers\Api;

use App\Helper;
use App\Models\CourseClassModel;
use App\Models\CourseModel;
use App\Models\DoctorMoneyRecordModel;
use App\Models\OrderModel;
use App\Models\OrderRecordModel;
use App\Models\UserCourseClassModel;
use App\Models\UserCourseModel;
use App\Models\UsersModel;
use App\Models\StoreModel;
use App\Models\VipModel;
use App\Services\WechatService;
use EasyWeChat\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Log;

class OrderController extends Controller
{

    /**
     * 生成课程订单
     */
    public function makeCourseOrder(Request $request)
    {
        $course_id = $request->input('course_id');
        if (!Helper::isId($course_id)) {
            return $this->error('ID非法');
        }

        //获取课程信息
        $course = CourseModel::find($course_id);
        if (empty($course)) {
            return $this->error('课程非法！');
        }

        // 判断是否限购
        if (!empty($course['buy_limit'])) {
            $buyCount = OrderModel::where('user_id', $this->auth->user_id)->where('course_id', $course_id)->whereIn('status', [1, 4, 5])->count();
            if ($buyCount >= $course['buy_limit']) {
                return $this->error('课程已达到最大购买次数！');
            }
        }

        $order = new OrderModel();
        $order->order_num = Helper::makeOrderNum(); //生成订单order_num, 微信支付商户订单号
        $order->type = 3;
        $order->total = $course->price;
        $order->course_id = $course->id;
        $order->img = $course->img;
        $order->user_id = $this->auth->user_id;
        $order->status = 4; //待支付
        if ($order->save()) {
            $data['is_vip'] = 0;
            //如果用户是年费会员，则直接支付成功
            if ($this->auth->user->is_vip == 1) {
                $result = $this->makeUserCourse($course, $order->order_num);
                if ($result) {
                    //更新订单状态
                    $order->status = 5;
                    $order->save();
                    $data['is_vip'] = 1;
                }
            } else {
                $openid = $this->auth->user->openid;
                $result = WechatService::getWcPayment($course, $openid, $order->order_num);
                $data['result'] = $result;
                $data['price'] = $course->price;
            }
            $data['order_num'] = $order->order_num;

            return $this->success($data);
        } else {
            return $this->error('生成订单失败！');
        }
    }

    /*
     *判断支付订单状态
     */
    public function payOrderStatus(Request $request)
    {
        $order = OrderModel::where('order_num', $request->input('order_num'))->first();

        if (empty($order)) {
            return $this->error('没有该订单信息');
        }

        if ($order->status == 2) {
            return $this->error('取消的订单不能支付！');
        }

        if ($order->status == 3) {
            return $this->error('本次交易已超时！');
        }

        if ($order->status == 1) {
            return $this->error('请勿重复支付！');
        }

        return $this->success([]);
    }
    /*
     *待支付页面进行支付
     */
    public function payUnpaidCourse(Request $request)
    {
        $course_id = $request->input('course_id');
        if (!Helper::isId($course_id)) {
            return $this->error('ID非法');
        }

        //获取课程信息
        $course = CourseModel::find($course_id);
        if (empty($course)) {
            return $this->error('没有课程信息');
        }

        $order_num = $request->input('order_num'); //生成订单order_num、微信支付订单号
        $order = OrderModel::where('order_num', $order_num)->first();
        if ($order->status == 2) {
            return $this->error('取消的订单不能支付！');
        }

        if ($order->status == 3) {
            return $this->error('本次交易已超时！');
        }

        $openid = $this->auth->user->openid;
        $result = WechatService::getWcPayment($course, $openid, $order_num);
        $data['result'] = $result;
        $data['price'] = $course->price;
        $data['order_num'] = $order_num;

        return $this->success($data);
    }

    /**
     * 生成VIP订单
     */
    public function makeVipOrder(Request $request)
    {
        //获取年费会员信息
        $vip = VipModel::select(['price', 'id'])->where('id', 1)->first();

        $vip->name = '成为麦麦天空VIP';
        $openid = $this->auth->user->openid;
        $order_num = Helper::makeOrderNum();
        $result = WechatService::getWcPayment($vip, $openid, $order_num);
        $data['result'] = $result;
        $data['order_num'] = $order_num;

        //生成课程订单
        $order = new OrderModel();
        $order->order_num = $order_num;
        $order->type = 1;
        $order->total = $vip->price;
        $order->user_id = $this->auth->user_id;
        $order->status = 4; //待支付
        $order_num = $order->order_num;
        if ($order->save()) {
            return $this->success($data);
        } else {
            return $this->error('生成订单失败');
        }
    }

    /**
     * 取消订单（针对未支付订单，已支付订单需走退款流程）
     */
    public function cancel(Request $request)
    {
        $order_id = $request->input('order_id');
        if (!Helper::isId($order_id)) {
            return $this->error('ID非法');
        }

        $order = OrderModel::find($order_id);
        if (empty($order)) {
            return $this->error('订单不存在！');
        }

        if ($order->user_id != $this->auth->user_id) {
            return $this->error('无权限更改！');
        }

        switch ($order->status) {
            case 1: // 已支付
                return $this->error('已支付订单不能取消！');
                break;

            case 2: // 已取消
                return $this->error('订单已取消！');
                break;

            case 3: // 已超时
                return $this->error('订单已超时！');
                break;

            case 4: // 待支付（可取消）
                $order->status = 2;
                if ($order->save()) {
                    // 取消佣金
                    // $this->cancelCommission($order->order_num);
                    return $this->success([]);
                } else {
                    return $this->error('取消失败！');
                }

                break;

            case 5: // 年费会员支付成功
                return $this->error('已支付订单不能取消！');
                break;
        }
    }

    /**
     * 退款（针对已支付的订单）
     *
     * [TODO] 需更改订单状态为已退款，同时修改佣金记录状态和增加一条退款记录
     */
    public function refund(Request $request)
    {
    }

    /**
     * 订单详情（倒计时）
     */
    public function info(Request $request)
    {
        $order_num = $request->input('order_num');
        if (empty($order_num)) {
            return $this->error('订单号有误！');
        }

        $where[] = ['user_id', $this->auth->user_id];
        $where[] = ['order_num', $order_num];
        $order = OrderModel::with(['course', 'pic'])->where($where)->first();

        if (empty($order)) {
            return $this->error('订单不存在！');
        }

        // if(in_array($order->status,[1,5])){
        //     return $this->error('订单已支付！');
        // }
        // if($order->type != 3) return $this->error('订单类型不正确！');
        // if($order->status == 3 ) return $this->error('订单已过期！');
        // if($order->status == 2 ) return $this->error('订单已取消！');

        $order->content = Helper::resetImg($order->content);
        $order = json_decode(json_encode($order),true); 
        // return $order;
        
        $times = 0;
        $class = CourseClassModel::where('course_id',$order['course']['id'])->select('times')->get();
        foreach ($class as $key => $value) {
            $times += $value['times'];
        }
        $order['course']['total_times'] = $times;
        $stores = StoreModel::where('id',$order['course']['store_ids'])->select('name as store_name')->first();
        $order['course']['store_name'] = $stores['store_name'];

        return $this->success($order);
    }

    /**
     * 已购订单列表
     */
    public function hasPayList(Request $request)
    {
        // $order_num = $request->input('order_num');
        // if(empty($order_num)) return $this->error('订单号非法！');

        // $where[] = ['order',$order_num];
        $where[] = ['user_id', $this->auth->user_id];
        $select = ['id','img','name','class_num','order_num','user_id','started_at','finish_at','course_id'];
        $user_course = UserCourseModel::with(['pic','user_class'])->where($where)->orderBy('created_at','DESC')->select($select)->paginate(10);
        if(empty($user_course)) $user_course = [];
        foreach($user_course as &$item){
            $item->status = 1;
        }
        $user_course = json_decode(json_encode($user_course),true);
        foreach ($user_course['data']as $key => $val) {
            $total_times = 0;
            foreach ($val['user_class'] as $keys => $vals) {
                $total_times += $vals['total_times'];
            }
            $user_course['data'][$key]['total_times'] = $total_times;

            $stores = CourseModel::leftjoin('stores as s','s.id','=','course.store_ids')->where('course.id','=',$val['course_id'])->select('s.name')->first();
            $user_course['data'][$key]['store_name'] = $stores->name;


            if($val['finish_at'] !== null && $val['started_at'] !== null){
                $user_course['data'][$key]['isDone'] = true;//已完结标记

            }else{
                $user_course['data'][$key]['isDone'] = false;//已完结标记
            }
            if($val['started_at'] == null){
                $user_course['data'][$key]['isBegin'] = false;//已经开始上课标记
            }else{
                $user_course['data'][$key]['isBegin'] = true;//已经开始上课标记
            }
            $user_course['data'][$key]['isPlay'] = false;//正在游戏标记
            if($user_course['data'][$key]['isDone'] == false){
                $user_course_class = UserCourseClassModel::leftjoin('course_class as cc ','user_course_class.class_id','=','cc.id')->where('user_course_id',$val['id'])->select('user_course_class.*','cc.name as course_class_name')->get()->toArray();
                $count = count($user_course_class);
                foreach ($user_course_class as $arr => $value) {
                    // if(!empty($value['start_at']) && !empty($value['finish_at'])){
                    //     $user_course['data'][$key]['completed'] = $value['course_class_name'];//已完成课程名称
                    //     $user_course['data'][$key]['count'] = $count;
                    //     $user_course['data'][$key]['arr'] = $arr;
                    //     if($count>$arr+1){
                    //         $user_course['data'][$key]['next'] = $user_course_class[$arr+1]['course_class_name'];//下一节课程名称
                    //     }else{
                    //         $user_course['data'][$key]['next'] = "";//下一节课程名称
                    //     }   
                    // }
                    // dump($value['start_at']);
                    // dump($value['finish_at']);
                    if(!empty($value['start_at']) && empty($value['finish_at'])){
                        $user_course['data'][$key]['isPlay'] = true;//正在游戏标记
                    }
                }
            }
        }

        return $this->success($user_course);
    }

    /**
     * 获取订单列表
     */
    public function lists(Request $request)
    {
        $status = $request->input('status');
        if (!in_array($status, [1, 2, 3, 4])) {
            return $this->error('订单状态非法！');
        }

        $select = ['id', 'order_num', 'img', 'total', 'course_id', 'user_id', 'status', 'created_at', 'pay_at'];
        $where = [
            ['status', $status],
            ['type', 3],
            ['user_id', $this->auth->user_id],
        ];
        $list = OrderModel::with(['course', 'pic'])->select($select)->where($where)->orderBy('created_at', 'DESC')->paginate(10);
        $temp['total'] = $list->total();
        $temp['cur_page'] = $list->currentPage();
        $temp['page_size'] = $list->perPage();
        $temp['lists'] = $list->items();
       // return $temp['lists'];
        if(empty($temp['lists'])){return [];}
        foreach ($temp['lists'] as $arr => $val) {
            $times = 0;
            $class = CourseClassModel::where('course_id',$val['course_id'])->select('times')->get();
            foreach ($class as $key => $value) {
                $times += $value['times'];
            }
            $temp['lists'][$arr]['total_times'] = $times;
            $stores = StoreModel::where('id',$val['course']['store_ids'])->select('name as store_name')->first();
            $temp['lists'][$arr]['store_name'] = $stores['store_name'];
        }
        return $this->success($temp);
    }

    // /**
    //  * 订单支付成功回调
    //  */
    // public function paySuccessCallback_old(Request $request)
    // {
    //     $options = config('wechat.options');
    //     $app = new Application($options);
    //     $response = $app->payment->handleNotify(function ($message, $successful) {
    //         $order_num = $message->out_trade_no;
    //         if (empty($order_num) || !is_numeric($order_num)) {
    //             return $this->error('订单号不能为空！');
    //         }

    //         //获取订单号相关信息
    //         $order = OrderModel::where('order_num', $order_num)->first();
    //         if (empty($order)) {
    //             return $this->error('订单不存在！');
    //         }

    //         if ($order->status == 1 || $order->status == 2 || $order->status == 3) {
    //             return true;
    //         } else {
    //             if ($message->result_code == 'SUCCESS' && ($order->total == $message->total_fee)) {
    //                 $order_record = new OrderRecordModel();
    //                 $order_record->price = $message->total_fee;
    //                 $order_record->user_id = $order->user_id;
    //                 $order_record->created_at = date('Y-m-d H:i:s');
    //                 $order_record->updated_at = date('Y-m-d H:i:s');
    //                 $order_record->order_num = $order_num;
    //                 $order_record->out_trade_no = $message->out_trade_no;
    //                 $order_record->transaction_id = $message->transaction_id;

    //                 switch ($order->type) {
    //                     case 1: // 购买vip
    //                         $order_record->type = 2;

    //                         $user = UsersModel::find($order->user_id);
    //                         if ($user->is_vip == 0 && empty($user->vip_expire)) {
    //                             $user->is_vip = 1;
    //                             $time = time() + (365 * 24 * 60 * 60);
    //                             $user->vip_expire = date('Y-m-d H:i:s', $time);
    //                         } elseif ($user->is_vip == 0 && !empty($user->vip_expire)) {
    //                             $user->is_vip = 1;
    //                             $time = time() + (365 * 24 * 60 * 60);
    //                             $user->vip_expire = date('Y-m-d H:i:s', $time);
    //                         } elseif ($user->is_vip == 1) {
    //                             $time = strtotime($user->vip_expire) + (365 * 24 * 60 * 60);
    //                             $user->vip_expire = date('Y-m-d H:i:s', $time);
    //                         }

    //                         $user->save();

    //                         // 当用户有推荐人时，记录一笔佣金，status=1
    //                         // if(!empty($order->user->invite_parent_id)) $this->makeCommission($order, 1);
    //                         break;

    //                     case 2: // 购买单次课程
    //                         // 当用户有推荐人时，记录一笔佣金，status=0，只有当课程消费时才更改状态为1
    //                         // if(!empty($order->user->invite_parent_id)) $this->makeCommission($order, 0);
    //                         break;

    //                     case 3: // 购买组合课程
    //                         $order_record->course_id = $order->course_id;
    //                         $order_record->type = 1;

    //                         $result = $this->makeUserCourse($order->course, $order_num);
    //                         // 当用户有推荐人时，记录一笔佣金，status=0，只有当课程消费时才更改状态为1
    //                         // if(!empty($order->user->invite_parent_id)) $this->makeCommission($order, 0);
    //                         break;
    //                 }
    //                 $order->status = 1;
    //                 $order->save();
    //                 $order_record->save(); //记账表
    //                 return true;
    //             } else {
    //                 return false;
    //             }
    //         }
    //     });
    //     return $response;
    // }

    public function paySuccessCallback(Request $request)
    {
        $options = config('wechat.options');
        $app = new Application($options);
        $response = $app->payment->handleNotify(function ($message, $successful) {
            $order_num = $message->out_trade_no;
            if (empty($order_num) || !is_numeric($order_num)) {
                return false;
            }

            //获取订单号相关信息
            $order = OrderModel::where('order_num', $order_num)->first();
            if (empty($order)) {
                return false;
            }
            $user = UsersModel::find($order->user_id);

            if ($order->status == 1 || $order->status == 2 || $order->status == 3) {
                return true;
            } else {
                if ($message->result_code == 'SUCCESS' && ($order->total == $message->total_fee)) {
                    $order_record_data['price'] = $message->total_fee;
                    $order_record_data['user_id'] = $order->user_id;
                    $order_record_data['created_at'] = date('Y-m-d H:i:s');
                    $order_record_data['updated_at'] = date('Y-m-d H:i:s');
                    $order_record_data['order_num'] = $order_num;
                    $order_record_data['out_trade_no'] = $message->out_trade_no;
                    $order_record_data['transaction_id'] = $message->transaction_id;
                    DB::beginTransaction();
                    //生成用户课程和课时
                    try {
                        switch ($order->type) {
                            case 1: // 购买vip

                                $order_record_data['type'] = 2;
                                if ($user->is_vip == 0 && empty($user->vip_expire)) {
                                    $user->is_vip = 1;
                                    $user_data['is_vip'] = 1;
                                    $time = time() + (365 * 24 * 60 * 60);
                                    $user_data['vip_expire'] = date('Y-m-d H:i:s', $time);
                                } elseif ($user->is_vip == 0 && !empty($user->vip_expire)) {
                                    $user_data['is_vip'] = 1;
                                    $time = time() + (365 * 24 * 60 * 60);
                                    $user_data['vip_expire'] = date('Y-m-d H:i:s', $time);
                                } elseif ($user->is_vip == 1) {
                                    $time = strtotime($user->vip_expire) + (365 * 24 * 60 * 60);
                                    $user_data['vip_expire'] = date('Y-m-d H:i:s', $time);
                                }

                                $user_data_result = DB::table('users')->where('id', $user->id)->update($user_data);
                                if (!$user_data_result) {
                                    DB::rollback();
                                    return false;
                                }

                                break;

                            case 2: // 购买单次课程

                                break;

                            case 3: // 购买组合课程
                                $order_record['course_id'] = $order->course_id;
                                $order_record['type'] = 1;
                                $result = $this->makeUserCourse($order->course, $order_num);
                                if (!$result) {
                                    DB::rollback();
                                    return false;
                                }

                                break;

                        }
                    } catch (\Exception $e) {
                        DB::rollback();
                        Log::error('error message:' . $e->getMessage());
                        return $this->error($e->getMessage());
                    }
                    $order_data['status'] = 1;
                    $order_result = DB::table('order')->where('order_num', $order_num)->update($order_data);
                    $order_record_result = DB::table('order_record')->insert($order_record_data);
                    if ($order_result && $order_record_result) {
                        DB::commit();
                        return true;
                    } else {
                        DB::rollback();
                        return false;
                    }
                }
            }
        });
        return $response;
    }

    /*
     *支付完成手动回调
     */
    public function rePaySuccessCallback(Request $request)
    {
        $order_num = $request->input('order_num');
        if (empty($order_num) || !is_numeric($order_num)) {
            return $this->error('订单号不能为空！');
        }

        //获取订单号相关信息
        $order = OrderModel::where('order_num', $order_num)->first();
        if (empty($order)) {
            return $this->error('订单不存在，无法操作！');
        }

        $order_wc = WechatService::getPaymentOrder($order_num);
        if($order_wc->return_code == 'SUCCESS'){
            if ($order_wc->result_code == 'SUCCESS') {
                if ($order->status == 1) {
                    return $this->success('已完成支付！');
                }
                if($order_wc->trade_state !== 'SUCCESS'){
                    Log::error($order_num.':'.$order_wc);
                    return $this->error($order_wc->trade_state_desc);
                }
            }else{
                Log::error($order_num.':'.$order_wc);
                return $this->error($order_wc->err_code_des);
            }
        }else{
            Log::error($order_num.':'.$order_wc);
            return $this->error('支付失败');
        }
        
        $user = UsersModel::find($order->user_id);
	
        $order_record_data['price'] = $order_wc->total_fee;
        $order_record_data['user_id'] = $order->user_id;
        $order_record_data['created_at'] = date('Y-m-d H:i:s');
        $order_record_data['updated_at'] = date('Y-m-d H:i:s');
        $order_record_data['order_num'] = $order_num;
        $order_record_data['out_trade_no'] = $order_wc->out_trade_no;
        $order_record_data['transaction_id'] = $order_wc->transaction_id;
        DB::beginTransaction();
        //生成用户课程和课时
        try {
            switch ($order->type) {
                case 1: // 购买vip

                    $order_record_data['type'] = 2;
                    if ($user->is_vip == 0 && empty($user->vip_expire)) {
                        $user->is_vip = 1;
                        $user_data['is_vip'] = 1;
                        $time = time() + (365 * 24 * 60 * 60);
                        $user_data['vip_expire'] = date('Y-m-d H:i:s', $time);
                    } elseif ($user->is_vip == 0 && !empty($user->vip_expire)) {
                        $user_data['is_vip'] = 1;
                        $time = time() + (365 * 24 * 60 * 60);
                        $user_data['vip_expire'] = date('Y-m-d H:i:s', $time);
                    } elseif ($user->is_vip == 1) {
                        $time = strtotime($user->vip_expire) + (365 * 24 * 60 * 60);
                        $user_data['vip_expire'] = date('Y-m-d H:i:s', $time);
                    }

                    $user_data = DB::table('users')->where('id', $user->id)->update($user_data);
                    if (!$user_data) {
                        DB::rollback();
                        return $this->error('购买VIP失败.');
                    }

                    break;

                case 2: // 购买单次课程

                    break;

                case 3: // 购买组合课程
                    $order_record['course_id'] = $order->course_id;
                    $order_record['type'] = 1;
                    // dump($order->course);
                    $result = $this->makeUserCourse($order->course, $order_num);
                    if ($result !== true) {
                        DB::rollback();
                        return $this->error('购买课程失败.');
                    }

                    break;

            }
            $order_data['status'] = 1;
            $order_result = DB::table('order')->where('order_num', $order_num)->update($order_data);
            $order_record_result = DB::table('order_record')->insert($order_record_data);
            if ($order_result && $order_record_result) {
                DB::commit();
                return $this->success('支付成功');
            } else {
                DB::rollback();
                return $this->error('支付失败.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return $this->error($e->getMessage());
        }
    }

    // /**
    //  * 生成用户独立课程和课时
    //  */
    // private function makeUserCourse_old(CourseModel $course, $order_num)
    // {
    //     //获取课程信息
    //     $user_course = new UserCourseModel();
    //     $user_course->name = $course->name;
    //     $user_course->type_id = $course->type_id;
    //     $user_course->class_num = $course->class_num;
    //     $user_course->price = $course->price;
    //     $user_course->suitable_age = $course->suitable_age;
    //     $user_course->img = $course->img;
    //     $user_course->course_id = $course->id;
    //     $order = OrderModel::where('order_num', $order_num)->first();
    //     $user_course->user_id = $order->user_id;
    //     $user_course->order_num = $order_num;

    //     if ($user_course->save()) {
    //         //保存用户课程成功，获取课程课时，并保存为用户课时
    //         //获取课程课时
    //         $class_ids = explode(',', $course->class_id);
    //         $course_class = CourseClassModel::whereIn('id', $class_ids)->get();
    //         foreach ($course_class as $item) {
    //             //组合课程信息，插入到用户课时表
    //             $insert[] = [
    //                 'class_id' => $item->id,
    //                 'user_course_id' => $user_course->id,
    //                 'user_id' => $order->user_id,
    //                 'updated_at' => date('Y-m-d H:i:s'),
    //                 'created_at' => date('Y-m-d H:i:s'),
    //             ];
    //         }
    //         $user_course_class = UserCourseClassModel::insert($insert);
    //         //获取下一节课,并保存到用户课程表中
    //         $where = [
    //             ['user_id', $order->user_id],
    //             ['user_course_id', $user_course->id],
    //         ];
    //         $next_course = UserCourseClassModel::where($where)->orderBy('created_at', 'ASC')->first();
    //         if (!empty($next_course)) {
    //             $user_course->next_class_id = $next_course->id;
    //             $user_course->save();
    //         }

    //         return true;
    //     } else {
    //         return false;
    //     }
    // }

    /**
     * 生成用户独立课程和课时
     */
    private function makeUserCourse(CourseModel $course, $order_num)
    {
        //获取课程信息
        $order = OrderModel::where('order_num', $order_num)->first();

        $user_course['name'] = $course->name;
        $user_course['type_id'] = $course->type_id;
        $user_course['class_num'] = $course->class_num;
        $user_course['price'] = $course->price;
        $user_course['suitable_age'] = $course->suitable_age;
        $user_course['img'] = $course->img;
        $user_course['course_id'] = $course->id;
        $user_course['user_id'] = $order->user_id;
        $user_course['order_num'] = $order_num;
        $user_course['created_at'] = date('Y-m-d H:i:s');

        DB::beginTransaction();
        try {
            $user_course_result = DB::table('user_course')->insertGetId($user_course);
            if (!$user_course_result) {
                DB::rollback();
                return false;
            }

            if ($user_course_result) {
                //保存用户课程成功，获取课程课时，并保存为用户课时
                //获取课程课时
                $class_ids = explode(',', $course->class_id);
                //$course_class = CourseClassModel::whereIn('id',$class_ids)->get();
                $course_class = DB::table('course_class')->whereIn('id', $class_ids)->get();
                foreach ($course_class as $item) {
                    //组合课程信息，插入到用户课时表
                    $insert[] = [
                        'class_id' => $item->id,
                        'user_course_id' => $user_course_result,
                        'user_id' => $order->user_id,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'created_at' => date('Y-m-d H:i:s'),
                        'total_times'=>$item->times
                    ];
                }
                //$user_course_class = UserCourseClassModel::insert($insert);
                $user_course_class = DB::table('user_course_class')->insert($insert);
                if (!$user_course_class) {
                    DB::rollback();
                    return false;
                }
                //获取下一节课,并保存到用户课程表中
                $where = [
                    ['user_id', $order->user_id],
                    ['user_course_id', $user_course_result],
                ];
                // $next_course = UserCourseClassModel::where($where)->orderBy('created_at','ASC')->first();
                $next_course = DB::table('user_course_class')->where($where)->orderBy('created_at', 'ASC')->first();
                if (!empty($next_course)) {
                    $user_course_update['next_class_id'] = $next_course->id;
                    $user_course_result_update = DB::table('user_course')->where('id', $user_course_result)->update($user_course_update);
                }

                if ($user_course_result_update) {
                    DB::commit();
                    return true;
                } else {
                    DB::rollback();
                    return false;
                }
            } else {
                DB::rollback();
                return false;
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return $this->error($e->getMessage());
        }
    }

    /**
     * 分配佣金
     */
    private function makeCommission(OrderModel $order, $status)
    {
        $record = new DoctorMoneyRecordModel();
        $record->record_id = Helper::makeOrderNum();
        $record->user_id = $order->user->invite_parent_id;
        $record->type = 1;
        $commissionRate = UsersModel::where('id', $order->user->invite_parent_id)->value('commission_rate'); // 邀请人佣金比例
        $record->money = $order->total * $commissionRate;
        $record->source = '麦麦天空';
        $record->desc = '邀请推广费';
        $record->order_amount = $order->total;
        $record->order_num = $order->order_num;
        $record->status = $status;

        return $record->save();
    }

    /**
     * 取消佣金
     */
    private function cancelCommission($orderNum)
    {
        return DoctorMoneyRecordModel::where('order_num', $orderNum)->where('status', 0)->update(['status' => -2]);
    }
}

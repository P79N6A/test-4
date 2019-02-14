<?php

namespace App\Jobs;

use App\Http\Controllers\PushController;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class BusDeliverTicket extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $ticket;      // 要发放的卡券对象
    protected $storeIds;    // 门店ID数组
    protected $target;  // 目标人群
    protected $data;    // 内容数组

    /**
     * Create a new job instance.
     * @param $ticket
     * @param $storeIds
     * @param $target
     * @param $data
     */
    public function __construct($ticket, $storeIds, $target, $data)
    {
        $this->ticket = $ticket;
        $this->storeIds = $storeIds;
        $this->target = $target;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->target == 1) {
            $builder = DB::table('store_visit_log')->whereIn('store_id', $this->storeIds)->distinct();
        } elseif ($this->target == 2) {
            $builder = DB::table('order')->whereIn('store_id', $this->storeIds)->distinct();
        }

        // 创建用户消息
        $msg = [
            'title' => '卡券领取通知',
            'content' => '您收到一张商家发放的卡券，请到【我的卡券】中查收',
            'addtime' => time()
        ];
        $msgId = DB::table('message')->insertGetId($msg);

        // 体验券兑换码schema
        $schemaData = DB::table('qrcode_type')->where('id', 47)->first();

        // 对目标用户群进行分批处理
        $builder->select('userid')->chunk(100, function ($users) use ($schemaData, $msgId) {
            foreach ($users as $user) {
                $uids[] = $user->userid;
            }
            if (!empty($uids)) {
                foreach ($uids as $uid) {
                    $records[] = ['userid' => $uid, 'ticket_id' => $this->ticket->id, 'presented' => 1, 'addtime' => time()];
                    $logs[] = ['ticket_id' => $this->ticket->id, 'userid' => $uid, 'addtime' => time()];
                    $userMsgs[] = ['message_id' => $msgId, 'userid' => $uid];

                    if ($this->ticket->type == 3) {
                        do {
                            $code = random_string(8, false, true);
                            $repeat = DB::table('convert_code')->where('code', $code)->count();
                        } while ($repeat);

                        $convertCodes[] = [
                            'code' => $code,
                            'data' => $this->ticket->id,
                            'type' => 47,
                            'coschema' => $schemaData->app_url . '?' . $schemaData->params . '=' . $code
                        ];
                    }
                }

                DB::beginTransaction();
                try {
                    // 给用户发卡券
                    DB::table('ticket_get_record')->insert($records);
                    // 往兑换码表插入体验券的兑换码
                    if (!empty($convertCodes)) {
                        DB::table('convert_code')->insert($convertCodes);
                    }
                    // 卡券库存量相应减少
                    DB::table('ticket')->where('id', $this->ticket->id)->decrement('circulation', count($uids));
                    // 记录发放日志
                    DB::table('ticket_posting_log')->insert($logs);
                    // 推送用户消息到消息箱
                    DB::table('user_message')->insert($userMsgs);

                    DB::commit();
                } catch (Exception $e) {
                    DB::rollBack();
                }

                // 执行批量推送
                $data['uids'] = $uids;
                $data = array_merge($data, $this->data);
                PushController::send($data);
            }

            $this->delete();
        });
    }
}

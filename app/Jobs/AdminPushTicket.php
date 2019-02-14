<?php

namespace App\Jobs;

use App\Http\Controllers\PushController;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class AdminPushTicket extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $ticketId;
    protected $timeRegion;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($ticketId, $timeRegion)
    {
        $this->ticketId = $ticketId;
        $this->timeRegion = $timeRegion;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $builder = DB::table('order')->where('status', 1)->where('type', 2)
            ->whereBetween('addtime', $this->timeRegion)
            ->distinct()->select('userid');

        // 创建用户消息
        $msg = [
            'title' => '卡券领取通知',
            'content' => '您收到一张平台发放的卡券，请到【我的卡券】中查收',
            'addtime' => time()
        ];
        $msgId = DB::table('message')->insertGetId($msg);

        $builder->chunk(100, function ($users) use ($msgId) {
            foreach ($users as $user) {
                $uids[] = $user->userid;
                $getRecords[] = [
                    'ticket_id' => $this->ticketId,
                    'userid' => $user->userid,
                    'presented' => 2,
                    'addtime' => time(),
                ];
                $deliverLogs[] = [
                    'ticket_id' => $this->ticketId,
                    'userid' => $user->userid,
                    'addtime' => time(),
                ];
                $userMsgs[] = ['message_id' => $msgId, 'userid' => $user->userid];
            }
            // 向用户发放卡券
            DB::table('ticket_get_record')->insert($getRecords);
            // 卡券总库存对应减少
            DB::table('ticket')->where('id', $this->ticketId)->decrement('circulation', count($users));
            // 写发放记录表
            DB::table('ticket_posting_log')->insert($deliverLogs);
            // 推送用户消息到消息箱
            DB::table('user_message')->insert($userMsgs);

            // 向用户推送通知
            $scheme = DB::table('qrcode_type')->where('id', 17)->select('app_url')->first();
            $data = [
                'uids' => $uids,
                'title' => '卡券领取通知',
                'content' => '您好，平台给你发放了一张现金券，请到【我的卡券】中查收！',
                'scheme' => $scheme->app_url
            ];
            PushController::send($data);
        });
    }
}

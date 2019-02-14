<?php

namespace App\Jobs;

use App\Http\Controllers\PushController;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class AdminPushMessage extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    public $msg = null;
    public $target = null;
    public $uids = null;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($msg, $target, $uids = [])
    {
        $this->msg = $msg;
        $this->target = $target;
        $this->uids = $uids;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->msg) {
            if (intval($this->target) > 0) {
                $scheme = DB::table('qrcode_type')->where('id', 23)->first();
                switch ($this->target) {
                    case 1:     // 指定用户
                        if (!empty($this->uids) && is_array($this->uids)) {
                            $msgId = DB::table('message')->insertGetId($this->msg);
                            if ($msgId) {
                                DB::table(config('tables.base') . '.users')->whereIn('id', $this->uids)
                                    ->select('id')->chunk(100, function ($users) use ($msgId) {
                                        foreach ($users as $user) {
                                            $data = [
                                                'message_id' => $msgId,
                                                'userid' => $user->id,
                                            ];
                                            DB::table('user_message')->insert($data);
                                        }
                                    });
                            }
                            $data = [
                                'uids' => [$this->uids],
                                'title' => '消息查收通知',
                                'content' => '您收到一条消息，请往消息中心查看',
                                'scheme' => $scheme->app_url . '?' . $scheme->params . '=' . $msgId
                            ];
                            PushController::send($data);
                        }
                        break;
                    case 2:     // 未消费用户
                        $msgId = DB::table('message')->insertGetId($this->msg);
                        if ($msgId) {
                            $uids = DB::table('order')->distinct()->lists('userid');
                            DB::table(config('tables.base') . '.users as u')
                                ->whereNotIn('u.id', $uids)
                                ->distinct()->select('u.id')->chunk(100, function ($users) use ($msgId, $scheme) {
                                    $uids = [];
                                    foreach ($users as $user) {
                                        $uids[] = $user->id;
                                        $data = [
                                            'message_id' => $msgId,
                                            'userid' => $user->id,
                                        ];
                                        DB::table('user_message')->insert($data);
                                    }
                                    $data = [
                                        'uids' => [$uids],
                                        'title' => '消息查收通知',
                                        'content' => '您收到一条消息，请往消息中心查看',
                                        'scheme' => $scheme->app_url . '?' . $scheme->params . '=' . $msgId
                                    ];
                                    PushController::send($data);
                                });

                        }
                        break;
                    case 3:     // 已消费用户
                        $uids = DB::table('order')->distinct()->lists('userid');
                        if ($uids) {
                            $msgId = DB::table('message')->insertGetId($this->msg);
                            if ($msgId) {
                                DB::table(config('tables.base') . '.users')->whereIn('id', $uids)
                                    ->distinct()->select('id')->chunk(100, function ($users) use ($msgId, $scheme) {
                                        $uids = [];
                                        foreach ($users as $user) {
                                            $uids[] = $user->id;
                                            $data = [
                                                'message_id' => $msgId,
                                                'userid' => $user->id,
                                            ];
                                            DB::table('user_message')->insert($data);
                                        }
                                        $data = [
                                            'uids' => [$uids],
                                            'title' => '消息查收通知',
                                            'content' => '您收到一条消息，请往消息中心查看',
                                            'scheme' => $scheme->app_url . '?' . $scheme->params . '=' . $msgId
                                        ];
                                        PushController::send($data);
                                    });
                            }
                        }
                        break;
                    case 4:     // 全部用户
                        // 不写用户消息记录
                        $this->msg['all'] = 1;
                        $msgId = DB::table('message')->insertGetId($this->msg);
                        // 推送通知
                        DB::table(config('tables.base') . '.users')->select('id')->chunk(1000, function ($users) use ($msgId, $scheme) {
                            $uids = [];
                            foreach ($users as $user) {
                                $uids[] = $user->id;
                            }
                            $data = [
                                'title' => '消息查收通知',
                                'content' => '您收到一条消息，请往消息中心查看',
                                'scheme' => $scheme->app_url . '?' . $scheme->params . '=' . $msgId
                            ];
                            PushController::send($data, 1);
                        });
                        break;
                    default:
                        break;
                }
            }
        }
    }
}

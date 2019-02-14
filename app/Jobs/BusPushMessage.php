<?php

namespace App\Jobs;

use App\Http\Controllers\PushController;
use App\Http\Requests\Request;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class BusPushMessage extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    public $msg = null;
    public $target = null;
    public $uids = null;
    public $storeIds = null;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($msg, $target, $uids = [], $storeIds)
    {
        $this->msg = $msg;
        $this->target = $target;
        $this->uids = $uids;
        $this->storeIds = $storeIds;
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
                        $vs = $this->getStoreVisitors();
                        $cs = $this->getConsumers();
                        $uids = array_merge($vs, $cs);
                        $allowUsers = array_intersect($uids, $this->uids);
                        if (!empty($this->uids) && is_array($this->uids)) {
                            $msgId = DB::table('message')->insertGetId($this->msg);
                            if ($msgId) {
                                DB::table(config('tables.base') . '.users')
                                    ->whereIn('id', $allowUsers)
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
                            $vs = $this->getStoreVisitors();
                            $cs = $this->getConsumers();
                            $allowUsers = array_diff($vs, $cs);
                            // $allowUsers = array_unique($vs);
                            DB::table(config('tables.base') . '.users as u')
                                ->whereIn('u.id', $allowUsers)
                                ->join('order as o', 'o.userid', '=', 'u.id')
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
                                        'uids' => $uids,
                                        'title' => '消息查收通知',
                                        'content' => '您收到一条消息，请往消息中心查看',
                                        'scheme' => $scheme->app_url . '?' . $scheme->params . '=' . $msgId
                                    ];

                                    PushController::send($data);
                                });

                        }
                        break;
                    case 3:     // 已消费用户
                        $cs = $this->getConsumers();
                        if ($cs) {
                            $msgId = DB::table('message')->insertGetId($this->msg);
                            if ($msgId) {
                                DB::table(config('tables.base') . '.users')->whereIn('id', $cs)
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
                                            'uids' => $uids,
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
                        $msgId = DB::table('message')->insertGetId($this->msg);
                        $vs = $this->getStoreVisitors();
                        $cs = $this->getConsumers();
                        $allowUsers = array_unique(array_merge($cs, $vs));
                        // 推送通知
                        DB::table(config('tables.base') . '.users')
                            ->whereIn('id', $allowUsers)
                            ->select('id')->chunk(100, function ($users) use ($msgId, $scheme) {
                                $uids = [];
                                $msgs = [];
                                foreach ($users as $user) {
                                    $uids[] = $user->id;
                                    $msgs[] = [
                                        'message_id' => $msgId,
                                        'userid' => $user->id,
                                    ];
                                }
                                if ($msgs) {
                                    DB::table('user_message')->insert($msgs);
                                }
                                if ($uids) {
                                    $data = [
                                        'uids' => $uids,
                                        'title' => '消息查收通知',
                                        'content' => '您收到一条消息，请往消息中心查看',
                                        'scheme' => $scheme->app_url . '?' . $scheme->params . '=' . $msgId
                                    ];
                                PushController::send($data);
                                }
                            });
                        break;
                    default:
                        break;
                }
            }
        }
    }

    /**
     * 获取访客
     * @param $storeIds
     * @return mixed
     */
    private function getStoreVisitors()
    {
        $uids = DB::table('store_visit_log')->whereIn('store_id', $this->storeIds)->distinct()->lists('userid');
        return $uids;
    }

    /**
     * 获取在门店消费过的用户
     * @return mixed
     */
    private function getConsumers()
    {
        $uids = DB::table('order')->whereIn('store_id', $this->storeIds)->distinct()->lists('userid');
        return $uids;
    }
}

<?php

namespace App\Jobs;

use App\Http\Controllers\PushController;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class AdminPushActivity extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    private $id;
    private $target;
    private $uids;

    /**
     * AdminPushActivity constructor.
     * @param int $id 活动ID
     * @param int $target 目标人群类型：1-指定用户，此值时必须指定用户；2-未消费用户；3-已消费用户；4-全部用户
     * @param array $uids 用户ID列表
     */
    public function __construct($id, $target, $uids = [])
    {
        $this->id = $id;
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
        $act = DB::table('activity')->where('id', $this->id)->first();
        if ($act && $act->end_date > time()) {
            $qrcodeType = DB::table('qrcode_type')->where('id', 50)->first();
            $scheme = $qrcodeType->app_url . '?' . $qrcodeType->params . '=' . $act->link;
            $title = '活动通知';
            $content = '你收到一个周边活动通知，请查收';

            switch ($this->target) {
                case 1:
                    PushController::send([
                        'uids' => $this->uids,
                        'title' => $title,
                        'content' => $content,
                        'scheme' => $scheme
                    ]);
                    break;
                case 2:
                    $consumers = DB::table('order')->where('from', 1)->distinct()->lists('userid');
                    DB::table(config('tables.base') . '.users as u')
                        ->whereNotIn('id', $consumers)
                        ->select('id')
                        ->chunk(100, function ($users) use ($title, $content, $scheme) {
                            $uids = [];
                            foreach ($users as $user) {
                                $uids[] = $user->id;
                            }
                            if ($uids) {
                                PushController::send([
                                    'uids' => $uids,
                                    'title' => $title,
                                    'content' => $content,
                                    'scheme' => $scheme
                                ]);
                            }
                        });
                    break;
                case 3:
                    $consumers = DB::table('order')->where('from', 1)->distinct()->lists('userid');
                    DB::table(config('tables.base') . '.users as u')
                        ->whereIn('u.id', $consumers)
                        ->select('u.id')
                        ->chunk(100, function ($users) use ($title, $content, $scheme) {
                            $uids = [];
                            foreach ($users as $user) {
                                $uids[] = $user->id;
                            }
                            if ($uids) {
                                PushController::send([
                                    'uids' => $uids,
                                    'title' => $title,
                                    'content' => $content,
                                    'scheme' => $scheme
                                ]);
                            }
                        });
                    break;
                case 4:
                    DB::table(config('tables.base') . '.users')
                        ->chunk(100, function ($users) use ($title, $content, $scheme) {
                            $uids = [];
                            foreach ($users as $user) {
                                $uids[] = $user->id;
                            }
                            if ($uids) {
                                PushController::send([
                                    'uids' => $uids,
                                    'title' => $title,
                                    'content' => $content,
                                    'scheme' => $scheme
                                ]);
                            }
                        });
                    break;
            }
        }
    }
}

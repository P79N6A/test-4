<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class PushBusMessage extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $id;  // 公告ID
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::table('bus_users')->select('id')->chunk(100,function($users){
            foreach($users as $user){
                $records[] = [
                    'bus_user_id'=>$user->id,
                    'message_id'=>$this->id,
                    'addtime'=>time()
                ];
            }
            if(!empty($records)){
                DB::table('bus_user_message')->insert($records);
            }
        });
    }
}

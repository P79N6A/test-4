<?php

namespace App\Jobs;

use App\Http\Controllers\PushController;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class PushActivityInfoMessage extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $data = null;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if(!empty($this->data)){
            PushController::send($this->data);
        }
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ModifyMlhxUserMemberId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'modifyMlhxUserMemberId';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $start = microtime();
        static $counter = 0;

        DB::table('mlhx.t_member')->chunk(100, function ($users) use ($counter) {
            foreach ($users as $user) {
                $time = round(microtime(true) * 1000, 0);
                $s = DB::table('mlhx.t_member')->where('id', $user->id)->update(['member_no' => $time]);
                if ($s) {
                    $counter++;
                }
            }
        });

        $end = microtime();

        $str = 'Time cost in ' . ($end - $start) . 'seconds,' . $counter . 'rows affected';

        file_put_contents(app()->basePath() . DIRECTORY_SEPARATOR . 'elapsed-time.txt', $str);
    }
}

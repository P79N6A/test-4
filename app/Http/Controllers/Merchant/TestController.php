<?php

namespace App\Http\Controllers\Merchant;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Jiusem\PHPTree\PHPTree;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\PushController as Push;
use App\Jobs\BusPushMessage;

class TestController extends BaseController
{

    public function index(){
        // $table = 'visit_log_'.date('Y-m-d');
        // if(!Schema::connection('visit_log')->hasTable($table)){
        //     Schema::connection('visit_log')->create($table,function($table){
        //         $table->increments('id');                   // 自增ID，主键
        //         $table->integer('userid');                  // APP用户ID
        //         $table->integer('store_id');                // 门店ID
        //         $table->integer('package_id');              // 套餐ID
        //         $table->integer('ticket_id');               // 卡券ID
        //         $table->integer('cabinet_id');              // 机台ID
        //         $table->integer('activity_info_id');        // 活动资讯ID
        //         $table->integer('add_id');                  // 广告ID
        //         $table->string('name',255);                 // 访问对象名称，如：50元游币套餐
        //         $table->string('description','1000');       // 行为描述，如：访问了游币套餐
        //         $table->integer('addtime');                 // 访问时间，时间戳
        //     });
        // }


        

    }

    public function show_push_log(){
        $file = 'logs/jiguang_push.log';
        $content = Storage::get($file);

        echo '<pre>';
        echo $content;
        echo '</pre>';
        
        // $targets[0] = ['1','2'];
        // Push::push_log($targets);
    }

    public function test_push(){
        $msg = [
            'account_id' => $this->parentUserId,
            'operator' => session()->get('id'),
            'title' => '内部测试推送',
            'content' => '这是一条内部测试推送',
            'from' => 2,
            'addtime' => time()
        ];

        var_dump($this->storeIds);

        var_dump($msg);

        $job = new BusPushMessage($msg, 2, [], $this->storeIds);

        $job->handle();
    }


}

<?php
/**
 * Created by PhpStorm.
 * User: AIMPER
 * Date: 2016/9/27
 * Time: 17:56
 */

namespace App\Services;
use App\Contracts\TestContract;
use Illuminate\Support\Facades\DB;

class TestService implements TestContract
{
    public function hello(){
        $res = DB::table('permissions')->get();
        return $res;
    }

}
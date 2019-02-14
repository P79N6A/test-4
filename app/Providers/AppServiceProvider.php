<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use App\Helper;
// use Illuminate\Support\Facades\DB;
// use Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // DB::listen(function($query) {
        //     $tmp = str_replace('?', '"'.'%s'.'"', $query->sql);
        //     $tmp = vsprintf($tmp, $query->bindings);
        //     $tmp = str_replace("\\","",$tmp);
        //     Log::info($tmp."\n\n\t");
        // });

        // DB::listen(function ($query) {
        //     $sql = str_replace("?", "'%s'", $query->sql);
        //     $log = vsprintf($sql, $query->bindings);
        //     $log = '[' . date('Y-m-d H:i:s') . '] ' . $log . "\r\n";
        //     $filepath = storage_path('logs\sql.log');
        //     file_put_contents($filepath, $log, FILE_APPEND);
        // });

        Validator::extend('mobile', function($attribute, $value, $parameters, $validator) {
            return Helper::isMobile($value);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //注册LOG单例模型
        $this->app->singleton('operation', function () {
            return $this->app->make(\App\Services\OperationService::class);
        });
    }
}

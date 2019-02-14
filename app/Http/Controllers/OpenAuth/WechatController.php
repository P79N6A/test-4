<?php

namespace App\Http\Controllers\OpenAuth;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\URL;
use EasyWeChat\Foundation\Application;

class WechatController extends Controller {

    protected $app;

    function __construct() {
        URL::setRootControllerNamespace('App\Http\Controllers\OpenAuth');
        
        
        $config = config('wechat');
        $config['oauth']['callback'] = action('WechatController@getCallback');
        
        $this->app = new Application($config);
    }

    function getIndex(Request $request) {
        $request->session()->put('wechat.callback', $request->input('callback'));
        return $this->app->oauth->redirect();
    }
    
    
    function getCallback(Request $request){
        $callback = $request->session()->get('wechat.callback');
        $code = $request->input('code');
        if($code){
            if(!str_contains($callback, '?')){
                $callback .= '?';
            }
            $callback .= '&code='.$code;
            return redirect($callback);
        }
        return redirect(action('WechatController@getIndex', ['callback' => $callback]));
    }

}

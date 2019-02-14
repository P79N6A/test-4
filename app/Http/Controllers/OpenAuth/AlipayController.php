<?php

namespace App\Http\Controllers\OpenAuth;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\URL;

class AlipayController extends Controller {

    function __construct() {
        URL::setRootControllerNamespace('App\Http\Controllers\OpenAuth');
    }

    function getIndex(Request $request) {
        $callback = $request->input('callback');
        $request->session()->put('alipay.callback', $callback);
        $redirect_uri = urlencode(action('AlipayController@getCallback'));
        return redirect('https://openauth.alipay.com/oauth2/appToAppAuth.htm?app_id=' . config('alipay.appid') . '&scope=SCOPE&redirect_uri=' . $redirect_uri);
    }

    function getCallback(Request $request) {
        $callback = $request->session()->get('alipay.callback');
        $app_auth_code = $request->input('app_auth_code');
        
        
        if($app_auth_code){
            if(!str_contains($callback, '?')){
                $callback .= '?';
            }
            if(str_contains($callback, '&')){
                $callback .= '&';
            }
            $callback .= 'app_auth_code='.$app_auth_code;
            return redirect($callback);
        }
        return redirect(action('AlipayController@getIndex', ['callback' => $callback]));
    }
}

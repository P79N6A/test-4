<?php

namespace App\Http\Controllers\Merchant;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class BluetoothController extends Controller
{

    public function index(){
        if($this->storeIds){
            $devices = DB::table('store_device AS sd')->join('bus_stores AS bs',function($join){
                $join->on('bs.id','=','sd.store_id')->whereIn('bs.id',$this->storeIds);
            })->select('sd.*','bs.name as store_name')->orderBy('sd.ibeacon_id','desc')->paginate(10);
        }else{
            $devices = null;
        }

        return view('business.bluetooth-dev-list',['devices'=>$devices]);
    }

}

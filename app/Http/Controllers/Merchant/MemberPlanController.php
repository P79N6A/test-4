<?php

namespace App\Http\Controllers\Merchant;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class MemberPlanController extends Controller
{

    /**
     * 会员卡套餐管理
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(){
        if(empty($this->storeIds)){
            return view('business.member-plans');
        }else{
            $list = DB::table('membership_card_plan as mcp')->whereIn('mcp.store_id',$this->storeIds)
                ->leftJoin('bus_stores as bs','bs.id','=','mcp.store_id')
                ->select('mcp.*','bs.name as store_name')
                ->orderBy('addtime','desc')->paginate(20);
            return view('business.member-plans',['packages'=>$list]);
        }

    }

}

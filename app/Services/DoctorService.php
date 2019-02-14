<?php
/**
 * User: Arcy
 * Date: 2018/7/18
 * Time: 11:07
 */

namespace App\Services;
use Illuminate\Http\Request;
use App\Models\UserTokenModel;
use App\Models\UsersModel;

class DoctorService{
    public static function isDoctor(){
         $token = request()->input('token');
         if(empty($token)) return false;
         $info = UserTokenModel::where('token',$token)->first();
         if(empty($info)) return false;
         $info_user = UsersModel::where('id',$info->user_id)->select('role')->first();
         if($info_user->role != 2){return false;}
        return $info;
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;

class AttachmentController extends Controller
{

    /**
     * 附件列表
     * @param $count 记录数
     * @return mixed
     */
    public static function fetch($count = 100){
        $attachments = DB::table('attachment')->select('id','path')->orderBy('id')->paginate($count);
        return $attachments;
    }

    /**
     * 删除附件
     * @param $ids ，附件ID，可以是数字或者ID数组
     * @return bool
     */
    public static function delete($ids){
        if(!is_array($ids)){
            $data[] = $ids;
        }
        if(empty($data)){
            return false;
        }
        $paths = DB::table('attachment')->whereIn('id',$data)->lists('path');
        $baseUploadPath = APP_ROOT.'/'.config('upload.root_path').'/';
        if(DB::table('attachment')->whereIn('id',$data)->delete()){
            foreach($paths as $path){
                @unlink($baseUploadPath.$path);
            }
            return true;
        }else{
            return false;
        }
    }

}

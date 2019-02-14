<?php

namespace App\Http\Controllers;

use App\Http\Controllers\RuanJieApi\GamelogController;
use App\Http\Requests\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Debug\Debug;

class Controller extends BaseController
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

    protected $user;            // 已登录用户信息
    protected $parentUserId;    // 当前登录账号的主账号id
    protected $stores;          // 可访问的门店数组
    protected $storeIds;        // 可访问门店id数组
    protected $roleIds;         // 用户拥有的角色数组
    protected $actions;         // 用户可访问的操作

    public function __construct()
    {
        DB::enableQueryLog();   // 开启 SQL 查询追踪
        $this->getParentUserId();
        $this->roleIds = $this->getAllocatedRoleIds();
        // $this->stores = $this->getStores();
        // if ($this->stores) {
        //     foreach ($this->stores as $store) {
        //         $this->storeIds[] = $store->id;
        //     }
        // } else {
        //     $this->storeIds = [];
        // }
        $this->actions = $this->getAuthorizedActions($this->roleIds);
    }

    /**
     * 获取当前登录商家的主账号用户id
     */
    private function getParentUserId()
    {
        if (session('pid') > 0) {
            $this->parentUserId = session('pid');
        } else {
            $this->parentUserId = session('id');
        }
    }

    /**
     * 统一响应方法
     * @param int $code
     * @param string $msg
     * @param string $url
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function response($code = 200, $msg = '操作成功', $url = '', $data = [])
    {
        if(!request()->ajax()){
            return response()->view('admin.error', ['code' => $code, 'msg' => $msg]);
        }

        $res = ['code' => $code, 'msg' => $msg, 'url' => $url];
        if ($data) {
            $res['data'] = $data;
        }
        return response()->json($res);
    }

    /**
     * 获取最后查询 SQL 语句
     * @return object
     */
    public function getQueryLog()
    {
        return DB::getQueryLog();
    }

    /**
     * 检查电子邮箱格式
     * @param string $email 要检查的邮件地址
     * @return bool
     */
    public function checkEmailFormat($email)
    {
        $pattern = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
        return preg_match($pattern, $email);
    }

    /**
     * 获取左侧菜单
     * @param array $roleIds 角色ID数组
     */
    protected function getMenus($roleIds = [])
    {
        if (session('pid') == 0) {
            // 如果是主账号，直接返回所有菜单
//            $menus = DB::table('bus_menus')->where('display',1)
//                ->select('id','parent_id','action','name','icon','display','display_order')
//                ->where('status',1)->orderBy('display_order','asc')->get();
            // 返回受总后台控制的菜单
            $menus = DB::table('bus_menu_role_relation as bmrr')
                ->join('bus_menus as bm', function ($join) {
                    $join->on('bm.id', '=', 'bmrr.menu_id')->where('status', '=', 1)->where('bm.display', '=', 1);
                })
                ->whereIn('bmrr.role_id', $this->roleIds)
                ->select(['bm.id', 'bm.parent_id', 'bm.action', 'bm.name', 'bm.icon', 'bm.display', 'bm.display_order'])
                ->orderBy('bm.display_order')
                ->orderBy('bm.id','asc')
                ->get();
        } else {
            // 如果是子账号，返回对应权限的菜单
            $menus = DB::table('bus_role_permission as brp')->whereIn('brp.role_id', $roleIds)
                ->join('bus_menus as bm', function ($join) {
                    $join->on('bm.action', '=', 'brp.permission_name')->where('bm.display', '=', 1)
                        ->where('bm.status', '=', 1);
                })
                ->select('bm.id', 'bm.parent_id', 'bm.action', 'bm.name', 'bm.icon', 'bm.display', 'bm.display_order')
                ->orderBy('bm.display_order', 'asc')->orderBy('bm.id','asc')->get();
        }

        return $menus;
    }

    /**
     * 获取当前商家旗下门店的用户
     * @param null $store_id 门店ID
     * @return array
     */
    protected function getUserList($store_id = null)
    {
        if ($store_id) {  // 旗下指定门店的用户
            if (in_array($store_id, $this->storeIds)) {
                $users = DB::table('order as o')
                    ->join('bus_stores as bs', function ($join) use ($store_id) {
                        $join->on('bs.id', '=', 'o.store_id')->where('bs.userid', '=', $this->parentUserId)
                            ->where('o.store_id', '=', $store_id);
                    })->join('base.users as u', 'u.id', '=', 'o.userid')
                    ->select('u.id', 'u.username', 'bs.name as store_name')->get();
            } else {
                $users = [];
            }
        } else {  // 旗下所有授权门店的用户
            $users = DB::table('order as o')
                ->join('bus_stores as bs', function ($join) {
                    $join->on('bs.id', '=', 'o.store_id')
                        ->where('bs.userid', '=', $this->parentUserId)
                        ->whereIn('bs.id', $this->storeIds);
                })->join('base.users as u', 'u.id', '=', 'o.userid')->select('u.id', 'u.username')
                ->select('bs.name as store_name', 'u.id', 'u.username', 'u.nickname')->get();
        }

        return $users;

    }

    /**
     * 获取已登录用户的角色ID列表
     */
    public function getAllocatedRoleIds()
    {
        if (session()->get('pid') > 0) {
            $allocatedRoleIds = DB::table('bus_role_user as bru')
                ->where('bru.user_id', session('id'))
                ->join('bus_roles as br', 'br.id', '=', 'bru.role_id')->lists('br.id');
        } else {
            $allocatedRoleIds = DB::table('bus_menu_role_user')->where('userid', $this->parentUserId)->lists('role_id');
        }
        return $allocatedRoleIds;
    }

    /**
     * 获取角色授权操作
     * @param $roleIds
     * @return mixed
     */
    public function getAuthorizedActions($roleIds)
    {
        $actions = [];
        if (session('pid') == 0) {
            // 如果是主账号，直接返回所有菜单
            //$actions = DB::table('bus_menus')
            //->select('id', 'parent_id', 'action', 'name', 'display', 'display_order')
            //->where('status', 1)->orderBy('display_order', 'asc')->get();
            // 返回受总后台控制的菜单
            $actions = DB::table('bus_menu_role_relation as bmrr', 'bmrr.role_id', '=', 'bmru.role_id')
                ->join('bus_menus as bm', function ($join) {
                    $join->on('bm.id', '=', 'bmrr.menu_id')->where('bm.status', '=', 1);
                })
                ->whereIn('bmrr.role_id', $roleIds)
                ->select('bm.id', 'bm.parent_id', 'bm.action', 'bm.name', 'bm.display', 'bm.display_order')
                ->orderBy('bm.display_order','asc')
                ->orderBy('bm.id','asc')
                ->get();
        } else {
            // 如果是子账号，返回对应权限的菜单
            if ($roleIds) {
                $actions = DB::table('bus_role_permission as brp')->whereIn('brp.role_id', $roleIds)
                    ->join('bus_menus as bm', function ($join) {
                        $join->on('bm.action', '=', 'brp.permission_name')
                            ->where('bm.status', '=', 1);
                    })
                    ->select('bm.id', 'bm.parent_id', 'bm.action', 'bm.name', 'bm.display', 'bm.display_order')
                    ->orderBy('bm.display_order', 'asc')->orderBy('bm.id','asc')->get();
            }
        }
        return $actions;
    }



	  /**
     * 系统操作日志
     * @param $action   操作行为
     * @param $ac_type  总控台   商户平台
     */
    public function system_log($action,$ac_type=''){

    	$data = array(
    			'action'=>$action,
    			'execution_name'=>session('username'),
    			'execution_time'=>date('Y-m-d H:i:s',time()),
    			'ac_type'=>$ac_type
    	);

    	if($ac_type=='admin'){
    		$data['user_id']=session('uid');
    	}else{
    		$data['user_id']=session('id');
    	}

    	DB::table(config('tables.base').'.rj_system_log')->insert($data);

    }

    /**
     * @name 输出 所有sql 信息
     */
    public function _sql($table=''){
    	DB::connection()->enableQueryLog(); // 开启查询日志

    	DB::table($table); // 要查看的sql

    	$queries = DB::getQueryLog(); // 获取查询日志

    	echo '<pre>';
    	print_r($queries);exit; // 即可查看执行的sql，传入的参数等等
    }

    /**
     * @name 输出返回部分sql 信息
     */
    public function _sqls($table=''){
    	DB::connection()->enableQueryLog(); // 开启查询日志

    	DB::table($table); // 要查看的sql

    	$queries = DB::getQueryLog(); // 获取查询日志


    	return $queries;
    }


    /**
     * @name 获取订单唯一单号
     * @param write {string or array or obj} 要写入的内容 (支持：数组、对象)
     * @param filepath {string} 写入文件的地址
     * @param type {int} 日志记录方式 1--强制空行并输出头部 2--强制空行不输出头部  3--不空行并且不输出头部信息
     * @param level  日志级别 debug (未加上日志调试模式)可在index.php入口定义之后加上
     * @param Debug
     * [ 2017-07-21T00:01:52+08:00 ] 127.0.0.1 /Home/Notice/get_notify
     * 操作内容
     */
    function getOrderUniqid()
    {
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        return $yCode[intval(date('Y')) - 2016] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
    }


    /**
     * @name 通用记录日志
     * @param write {string or array or obj} 要写入的内容 (支持：数组、对象)
     * @param filepath {string} 写入文件的地址
     * @param type {int} 日志记录方式 1--强制空行并输出头部 2--强制空行不输出头部  3--不空行并且不输出头部信息
     * @param level  日志级别 debug (未加上日志调试模式)可在index.php入口定义之后加上
     * @param Debug
     * [ 2017-07-21T00:01:52+08:00 ] 127.0.0.1 /Home/Notice/get_notify
     * 操作内容
     */
    public function write_log($write='',$filePath='',$type=1){
        date_default_timezone_set("PRC");  //设置时区

        $filePath =  APP_ROOT.'/storage/logs/';//写入文件的地址
        //echo $filePath;exit;

    	$subPath = date('Y_m_d_H',time()).'.txt';
    	if(!file_exists($filePath)){
    		mkdir($filePath,0777,true);
    	}

    	$file = $filePath.$subPath;	 //完善文件路径

	    switch ($type){
	    	case 1:
	    		//空开一行
	    		file_put_contents($file,'',FILE_APPEND);
	    		file_put_contents($file,PHP_EOL,FILE_APPEND);
	    		//header头记录时间   IP  操作地址
	    		$header =  '[ '.date('Y-m-d H:i:s',time()).' ] '.$_SERVER['REMOTE_ADDR'].' '.$_SERVER['REQUEST_URI'];
	    		file_put_contents($file,$header,FILE_APPEND);
	    		file_put_contents($file,PHP_EOL,FILE_APPEND);	//换行效果

	    		break;
	    	case 2:
	    		//空开一行
	    		file_put_contents($file,'',FILE_APPEND);
	    		file_put_contents($file,PHP_EOL,FILE_APPEND);
	    		break;
	    	default:
	    		break;
	    }

    	//记录日志
    	file_put_contents($file,var_export($write,true),FILE_APPEND);
    	file_put_contents($file,PHP_EOL,FILE_APPEND);	//换行效果
    	return true;
    }

    //并发请求
    function curl_multi_fetch($urlarr = array())
    {
        $result = $res = $ch = array();
        $nch = 0;
        $mh = curl_multi_init();
        foreach ($urlarr as $nk => $url) {
            $timeout = 300;
            $ch[$nch] = curl_init();
            curl_setopt_array($ch[$nch], array(
                CURLOPT_URL => $url['url'],
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $url['content'],
                CURLOPT_HEADER => false,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => $timeout,
            ));
            curl_multi_add_handle($mh, $ch[$nch]);
            ++$nch;
        }
        /* wait for performing request */
        do {
            $mrc = curl_multi_exec($mh, $running);
        } while (CURLM_CALL_MULTI_PERFORM == $mrc);
        while ($running && $mrc == CURLM_OK) {
            // wait for network
            if (curl_multi_select($mh, 300) > -1) {
                // pull in new data;
                do {
                    $mrc = curl_multi_exec($mh, $running);
                } while (CURLM_CALL_MULTI_PERFORM == $mrc);
            }
        }
        if ($mrc != CURLM_OK) {
            error_log("CURL Data Error");
        }
        /* get data */
        $nch = 0;
        foreach ($urlarr as $moudle => $node) {
            if (($err = curl_error($ch[$nch])) == '') {
                $res[$nch] = curl_multi_getcontent($ch[$nch]);
                $result[$moudle] = $res[$nch];
            } else {
                error_log("curl error");
            }

            curl_multi_remove_handle($mh, $ch[$nch]);
            curl_close($ch[$nch]);
            ++$nch;
        }
        curl_multi_close($mh);
        return $result;
    }

}

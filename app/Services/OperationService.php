<?php
/**
 * User: Arcy
 * Date: 2017/9/4
 * Time: 11:07
 * Description: record operation logic service
 */

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\BusOperationLog;
use QQWry;

class OperationService
{
    /* 定义操作行为类型 */
    const LOGIN_SUCCESS = 1;
    const LOGIN_FAIL = 2;
    const LOGIN_OUT = 3;
    const INSERT = 4;
    const UPDATE = 5;
    const DELETE = 6;

    private $selector = null;
    private $model = null;
    private $request;

    /* 过滤信息 */
    private $filter = null;

    /* 信息数据 */
    private $ip;
    private $ip_area;
    private $user_agent;
    private $user_id;
    private $uri;
    private $param;
    private $create_at;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * 登陆成功
     */
    public function loginSuccess($comment)
    {
        $this->filter = ['password'];
        $model = $this->init();
        $model->action = SELF::LOGIN_SUCCESS;
        $model->table = config('tables.base').'.users';
        $model->comment = $comment;
        $model->save();
    }

    /**
     * 登陆失败
     */
    public function loginFail($comment)
    {
        $model = $this->init();
        $model->action = SELF::LOGIN_FAIL;
        $model->table = config('tables.base').'.users';
        $model->comment = $comment;
        $model->save();
    }

    /**
     * 登出
     */
    public function loginOut($comment)
    {
        $model = $this->init();
        $model->action = SELF::LOGIN_OUT;
        $model->table = config('tables.base').'.users';
        $model->comment = $comment;
        $model->save();
    }

    /**
     * 记录数据库插入操作
     */
    public function insert($tb, $comment, $diff=null, $tb_prefix=null)
    {
        if (is_null($tb_prefix)) {
            $tb_prefix = config('tables.youyibao');
        }
        $this->filter = ['password'];
        $model = $this->init();
        $model->action = SELF::INSERT;
        $model->table = $tb_prefix . '.' . $tb;
        $model->comment = $comment;
        if (!empty($diff)) {
            $model->after = json_encode($diff);
        }

        $model->save();
    }

    /**
     * 记录数据库删除操作
     */
    public function delete($tb, $comment, $diff=null, $tb_prefix=null)
    {
        if (is_null($tb_prefix)) {
            $tb_prefix = config('tables.youyibao');
        }
        $this->filter = ['password'];
        $model = $this->init();
        $model->action = SELF::DELETE;
        $model->table = $tb_prefix . '.' . $tb;
        $model->comment = $comment;
        if (!empty($diff)) {
            $model->before = json_encode($diff);
        }

        $model->save();
    }

    /**
     * 记录数据库修改操作
     */
    public function update($tb, $comment, $before=null, $after=null, $tb_prefix=null)
    {
        if (is_null($tb_prefix)) {
            $tb_prefix = config('tables.youyibao');
        }
        $this->filter = ['password'];
        $model = $this->init();
        $model->action = SELF::UPDATE;
        $model->table = $tb_prefix . '.' . $tb;
        $model->comment = $comment;
        if (!empty($before)) {
            $model->before = json_encode($before);
        }
        if (!empty($after)) {
            $model->after = json_encode($after);
        }

        $model->save();
    }

    /**
     * init 初始化操作表数据
     * @return MODEL 操作表实例
     */
    protected function init()
    {
        /* 初始化记录数据 */
        $model = new BusOperationLog();
        $model->ip = $this->request->getClientIp();
        if ($model->ip == '::1' || $model->ip == '127.0.0.1') {
            $model->ip_area = 'localhost';
        } else {
            $ip_info = QQWry::query($model->ip); // 取出IP区域信息
            $model->ip_area = $ip_info['country'] . ' ' . $ip_info['area'];
        }
        
        $model->user_agent = $this->request->server('HTTP_USER_AGENT');
        $model->userid = empty(session('id'))?0:session('id');
        $model->create_at = date('Y-m-d H:i:s');
        $model->uri = $this->request->getUri();
        $param = $this->request->instance()->getContent();
        if (!is_null($this->filter)) {
            $param = parse_str($param);

            foreach ($this->filter as $value) {
                $param[$value] = '******';
            }

            if (is_array($param)) {
                $param = http_build_query($param);
            } else {
                $param = '';
            }

            $param = urldecode($param);
        }
        $model->param = $param;

        $model->method = $this->request->method();

        return $model;
    }
}

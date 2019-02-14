<?php
/**
 * Created by PhpStorm.
 * User: AIMPER
 * Date: 2016/10/26
 * Time: 10:15
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UploadController extends Controller
{
    protected $request;             // 请求实体
    protected $root_path;           // 上传的根目录
    protected $allow_extension;     // 允许的文件扩展
    protected $max_size;            // 单文件允许最大大小
    protected $dir;                 // 目标上传目录
    protected $auto_subdir;         // 是否开启自动按日期创建子目录

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->root_path = APP_ROOT . '/' . config('upload.root_path');
        // if (!file_exists($this->root_path)) {
        if (!is_dir($this->root_path)) {
            mkdir($this->root_path);
        }
        $this->allow_extension = config('upload.allow_extention');
        $this->max_size = config('upload.max_size');
        // $this->dir = $request->has('dir') ? $request->get('dir') : 'images';
        $this->dir = 'upload';
        $this->auto_subdir = config('upload.auto_subdir');
    }

    /**
     * 上传表单
     */
    public function create()
    {
        return view('uploadform');
    }

    /**
     * 上传
     */
    public function store()
    {
    //   var_dump($this->request->file());

        if (!$this->request->hasFile('file') && !$this->request->hasFile('upload') && !$this->request->hasFile('upfile')) {
            $res = [
                'error_code' => 0,
                'msg' => "specified file doesn't exist"
            ];
            return response()->json($res);
        }
        $files = $this->request->file('file')?$this->request->file('file'):$this->request->file('upload');
        if (empty($files)) {
            $files = $this->request->file('upfile');
        }

        if (is_object($files)) {
            if (!$files->isValid()) {
                $res = [
                    'error_code' => $files->getError(),
                    'msg' => $files->getErrorMessage()
                ];
                return response()->json($res);
            }
            $uploadFiles[] = $files;
        } else if (is_array($files)) {
            $uploadFiles = $files;
        }
        if ($this->auto_subdir) {
            $subdir = '/' . date('Ymd');
        } else {
            $subdir = '';
        }
        // $dest_dir = $this->root_path . '/' . $this->dir . $subdir;
        $dest_dir = $this->root_path . '/' . $subdir;
        $allow_extensions = explode(',', $this->allow_extension);
        $res = [];
        $msg = [];
        foreach ($uploadFiles as $k => $file) {
            $extension = $file->getClientOriginalExtension();
            // 过滤不支持的扩展名
            if (!in_array($extension, $allow_extensions)) {
                $msg[] = $file->getClientOriginalName() . ' 不支持扩展名 ' . $extension . '，取消上传';
                continue;
            }
            // 过滤超出大小的文件
            $size = $file->getClientSize();
            if ($size > $this->max_size) {
                $msg[] = $file->getClientOriginalName() . ' 超出大小，取消上传';
                continue;
            }
            // 记录上传失败文件的错误
            if (!$file->isValid()) {
                $msg[] = $file->getErrorMessage();
            }
            $save_name = time() . '-' . md5($file->getClientOriginalName()) . str_random(32);
            $newfile = $file->move($dest_dir, $save_name . '.' . $extension);
            $absolute_path = config('static.base_url') . '/' . $this->dir . $subdir . '/' . $save_name . '.' . $extension;
            $relative_dir = $this->dir . $subdir . '/' . $save_name . '.' . $extension;
            $links[] = $absolute_path;  // 返回给 ckeditor 的链接
            // $attid = DB::table('attachment')->insertGetId(['path' => $relative_dir, 'addtime' => time()]);
            $attid = DB::table('attachment')->insertGetId(['path' => $absolute_path, 'addtime' => time()]);
            $res[$k]['id'] = $attid;
            $res[$k]['relative_path'] = $relative_dir;
            $res[$k]['absolute_path'] = $absolute_path;
        }
        $data['error_code'] = 0;
        $data['data'] = $res;
        $data['msg'] = $msg;

        // 针对 ckeditor 返回对应数据
        if ($this->request->has('CKEditor') && $this->request->get('CKEditor') == 'content') {
            $callback = $this->request->get("CKEditorFuncNum");
            $link = "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($callback,'" . $links[0] . "','');</script>";
            return $link;
        } elseif ($this->request->has('editorid') && $this->request->get('editorid') == 'container') { // umeditor 上传
            /*
            $arr = [
                "original"=>"{$file->getClientOriginalName()}",
                "size"=>"{$file->getClientSize()}",
                "name"=>"{$file->getClientOriginalName()}",
                "state"=>"SUCCESS",
                "type"=>".{$file->getClientOriginalExtension()}",
                "url"=>"$links[0]",
            ];
            return response()->json($arr);
            */
            return '{"original":"' . $file->getClientOriginalName() . '","name":"'
                . $file->getClientOriginalName() . '","url":"'
                . $links[0] . '","size":"' . $file->getClientSize() . '","type":".'
                . $file->getClientOriginalExtension() . '","state":"SUCCESS"}';
        }

        return response()->json($data);
    }
}

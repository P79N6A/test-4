<?php
/**
 * Created by PhpStorm.
 * User: D.Rui
 * Date: 2016/11/8
 * Time: 15:31
 */

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use League\Flysystem\Exception;

class AddController extends Controller
{
    protected $table = 'ads';

    /**
     * 广告列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        if (!$this->storeIds) {
            return view('business.ad-list');
        }

        $ads = DB::table('ads as a')
            ->where('a.userid', $this->parentUserId)
            ->where('a.publisher', 1)
            ->join('ads_store_relation as asr', function ($join) {
                $join->on('asr.add_id', '=', 'a.id')
                    ->whereIn('asr.store_id', $this->storeIds);
            })
            ->leftJoin('attachment', 'attachment.id', '=', 'a.image')
            ->select('a.*', 'attachment.path')->orderBy('a.addtime', 'desc')->paginate(20);

        return view('business.ad-list', ['ads' => $ads]);
    }

    /**
     * 发布广告
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function add(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('business.add-ad', ['stores' => $this->stores]);

        } elseif ($request->isMethod('post')) {
            $data = $request->only([
                'id', 'title', 'type', 'store_ids', 'store_id', 'image', 'url', 'flag', 'content', 'package_id','platform'
            ]);

            if (!preg_replace('/\s+/', '', $data['title'])) {
                return $this->response(403, '标题不能为空');
            }

            // 广告投放的门店
            if ($data['type'] != 3 && (empty($data['store_ids']) || !is_array($data['store_ids']))) {
                return $this->response(403, '请选择广告显示的门店');
            }
            $store_ids = $data['store_ids'];
            unset($data['store_ids']);

            if (!preg_replace('/\s+/', '', $data['image']) || !intval($data['image'])) {
                return $this->response(403, '封面图片不能为空');
            }

            // 处理广告类型
            switch ($data['type']) {
                case 1:     // 图文
                    if (preg_replace('/\s/', '', $data['content'])){
                        unset($data['url']);
                    } elseif(preg_replace('/\s/', '', $data['url'])) {
                        unset($data['content']);
                    }else{
                        return $this->response(403, '请输入广告内容/广告链接!');
                    }
                    break;
                case 2:
                    if (!preg_replace('/\s/', '', $data['url'])) {
                        return $this->response(403, '请输入外链');
                    }
                    $data['content'] = $data['url'];
                    break;
                case 3:     // 产品
                    if (!intval($data['store_id'])) {
                        return $this->response(403, '请选择一家门店');
                    }
                    if (!intval($data['package_id'])) {
                        return $this->response(403, '请选择一家门店内产品类型');
                    }

                    $store_ids = DB::table('package_store_relation')->where('package_id', $data['package_id'])->lists('store_id');

                    $data['url'] = config('schema.package_detail') . '?storeId=' . $data['store_id'] . '&packageId=' . $data['package_id'];   // 生成schema URL
                    $arr['type'] = 'package';
                    $param[] = ['id' => $data['package_id']];
                    $arr['param'] = $param;
                    $data['content'] = json_encode($arr);
                    break;
            }

            $data['userid'] = $this->parentUserId;
            $data['place'] = 3;
            $data['addtime'] = time();
            unset($data['package_id']);

            DB::beginTransaction();
            try {
                $addId = DB::table($this->table)->insertGetId($data);
                foreach ($store_ids as $store_id) {
                    $ad_store_relations[] = ['add_id' => $addId, 'store_id' => $store_id];
                }
                DB::table('ads_store_relation')->insert($ad_store_relations);
                DB::commit();
                return $this->response(200, '发布成功', route('business.ad-list'));
            } catch (Exception $e) {
                DB::rollBack();
                return $this->response(500, '发布失败');
            }
        }

    }

    /**
     * 修改广告
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function edit(Request $request)
    {
        if ($request->isMethod('get')) {
            if (!$id = intval($request->get('id'))) {
                return view('merchant.error', ['code' => 500, 'msg' => '内部错误']);
            }

            $add = DB::table($this->table)
                ->where($this->table . '.id', $id)
                ->where($this->table . '.publisher', 1)
                ->where($this->table . '.userid', $this->parentUserId)
                ->leftJoin('attachment as a', 'a.id', '=', $this->table . '.image')
                ->select($this->table . '.*', 'a.path')->first();
            if (!$add) {
                return view('merchant.error', ['code' => 404, 'msg' => '该广告不存在']);
            }

            // 如果广告位置为门店内部广告，则取出对应的显示门店列表
            if ($add->place == 3) {
                $relatedStores = DB::table('ads_store_relation')->where('add_id', $add->id)->lists('store_id');
            } else {
                $relatedStores = [];
            }

            // 广告内容转格式
            if ($add->type == 3) {
                $arr = json_decode($add->content);
                $add->package_id = $arr->param[0]->id;
            }

            $stores = $this->getStores();
            return view('business.edit-ad', ['ad' => $add, 'stores' => $stores, 'relatedStores' => $relatedStores]);

        } elseif ($request->isMethod('post')) {
            $data = $request->only([
                'id', 'title', 'type', 'store_ids', 'store_id', 'image', 'url', 'flag', 'content', 'package_id','platform'
            ]);

            if (!intval($data['id'])) {
                return $this->response(403, '内部错误');
            }
            if (!$data['title']) {
                return $this->response(403, '标题不能为空');
            }
            // 广告显示门店
            if (empty($data['store_ids']) || !is_array($data['store_ids'])) {
                return $this->response(403, '请选择广告显示的门店');
            }
            $store_ids = $data['store_ids'];
            unset($data['store_ids']);

            if (!intval($data['type'])) {
                return $this->response(403, '类型不能为空');
            }
            if (!intval($data['image'])) {
                return $this->response(403, '封面不能为空');
            }
            // if (!preg_replace('/\s+/', '', $data['content'])) {
            //     return $this->response(403, '内容不能为空');
            // }
            $id = $data['id'];
            unset($data['id']);

            switch ($data['type']) {
                case 1:
                    if (preg_replace('/\s/', '', $data['content'])){
                        $data['url'] = '';
                    } elseif(preg_replace('/\s/', '', $data['url'])) {
                        $data['content'] = '';
                    }else{
                        return $this->response(403, '请输入广告内容/广告链接!');
                    }
                    break;
                case 2:
                    $data['content'] = $data['url'];
                    unset($data['url']);
                    break;
                case 3:
                    $data['url'] = config('schema.package_detail') . '?storeId=' . $data['store_id'] . '&packageId=' . $data['package_id'];   // 生成schema URL
                    $arr['type'] = 'package';
                    $param[] = ['id' => $data['package_id']];
                    $arr['param'] = $param;
                    $data['content'] = json_encode($arr);
                    break;
            }
            unset($data['package_id']);
            if ($data['type'] != 3) {
                $data['store_id'] = '';
            }

            $add = DB::table($this->table)->where($this->table . '.id', $id)
                ->where($this->table . '.publisher', 1)
                ->where($this->table . '.userid', $this->parentUserId)
                ->leftJoin('attachment as at', 'at.id', '=', $this->table . '.image')
                ->select($this->table . '.*', 'at.path')->first();

            if (!$add) {
                return $this->response(404, '该广告不存在');
            }

            // 如果新广告图片和旧图片不同，则删除旧图片及相关数据
            if ($data['image'] != $add->image) {
                // 删除附件
                $file = APP_ROOT . '/' . config('upload.root_path') . '/' . $add->path;
                @unlink($file);
                // 删除附件数据
                DB::table('attachment')->where('id', $add->image)->delete();
            }

            DB::beginTransaction();
            try {
                foreach ($store_ids as $store_id) {
                    // 传递进来的新分配门店
                    $add_store_relations[] = ['add_id' => $add->id, 'store_id' => $store_id];
                }
                //  更新广告信息
                DB::table($this->table)->where('id', $id)->update($data);

                // 更新广告关联门店信息
                DB::table('ads_store_relation')->where('add_id', $add->id)->delete();
                DB::table('ads_store_relation')->insert($add_store_relations);
                /**
                 * 备注：总后台修改广告的位置时需要删除对应的广告和显示门店关联
                 * 从其他位置修改为门店广告时需指定显示的门店
                 */


                // 如果是门店内部广告转为其他广告，则删除 schema URL
                if ($add->type = 3 && $data['type'] != 3) {
                    // DB::table('ads')->where('id', $add->id)->update(['url' => '']);
                }

                DB::commit();
                return $this->response(200, '修改成功', route('business.ad-list'));
            } catch (Exception $e) {
                DB::rollBack();
                return $this->response(500, '修改失败');
            }
        }

    }

    /**
     * 删除广告
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        if (!intval($request->get('id'))) {
            return $this->response(500, '内部错误');
        }
        $id = $request->get('id');
        $add = DB::table($this->table)->where($this->table . '.id', $id)
            ->where($this->table . '.userid', $this->parentUserId)
            ->leftJoin('attachment as a', 'a.id', '=', $this->table . '.image')
            ->select($this->table . '.*', 'a.path')->first();
        if (!$add) {
            return $this->response(404, '该广告不存在');
        }

        $upload_path = config('upload.root_path');
        $absolute_path = APP_ROOT . '/' . $upload_path . '/' . $add->path;

        if (!$add) {
            return $this->response(404, '不存在该广告');
        }
        // 删除广告附件
        @unlink($absolute_path);

        DB::transaction(function () use ($add, $id) {
            // 删除附件表对应记录
            DB::table('attachment')->where('id', $add->image)->delete();
            // 删除广告表对应记录
            DB::table($this->table)->where('id', $id)->where('userid', $this->parentUserId)->delete();
        });

        return $this->response(200, '删除成功', route('business.ad-list'));
    }

    /**
     * 广告的发布/取消切换
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function switchPost(Request $request)
    {
        $data = $request->only('id', 's');
        if (!intval($data['id']) || !intval($data['s'])) {
            return $this->response(403, '内部错误');
        }
        $ad = DB::table('ads')->where('id', $data['id'])
            ->join('ads_store_relation as asr', function ($join) {
                $join->on('asr.add_id', '=', 'ads.id')->whereIn('asr.store_id', $this->storeIds);
            })->where('userid', $this->parentUserId)->select('ads.*')->first();
        if (!$ad) {
            return $this->response(404, '该广告不存在');
        }
        if ($ad->place == 2) {  // 启动页广告只能总后台控制发布
            return $this->response(403, '您不能操作该广告');
        }

        if ($data['s'] == 1) {     // 发布广告
            if ($ad->flag == 1) {
                return $this->response(403, '该广告已发布，无需重复操作');
            }
            $flag = 1;
        } elseif ($data['s'] == 2) {    // 取消发布
            if ($ad->flag == 0) {
                return $this->response(403, '该广告没有发布，无需取消操作');
            }
            $flag = 0;
        }

        if (DB::table('ads')->where('id', $data['id'])->update(['flag' => $flag])) {
            return $this->response(200, '操作成功');
        } else {
            return $this->response(500, '操作失败');
        }

    }


}
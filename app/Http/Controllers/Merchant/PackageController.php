<?php
/**
 * Created by PhpStorm.
 * User: AIMPER
 * Date: 2016/10/27
 * Time: 15:50
 */

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Libraries\BaseQrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PackageController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    protected $fields = [
        'id', 'name', 'price', 'stock', 'expire_date', 'expire_time', 'buy_limit', 'type',
        'coins', 'ticket_name', 'description', 'image', 'gallery_photos', 'store_ids', 'flag'
    ];

    /**
     * 获取预留供选择的图片列表
     * @return mixed
     */
    private function getReservedImages()
    {
        $imgs = DB::table('attachment')->where('reserve', 1)->select('id', 'path')->get();
        return $imgs;
    }

    /**
     * 商品列表
     */
    public function index(Request $request)
    {

        if (empty($this->storeIds)) {
            return view('business.package-list', [
                'packages' => [],
                'sekill' => !empty($sekill) ? $sekill : 0,
                'expire' => !empty($expire) ? $expire : 0,
                'keyword' => !empty($keyword) ? $keyword : '',
                'store' => !empty($store) ? $store : ''
            ]);
        }

        $builder = DB::table('packages as p')->where('p.userid', $this->parentUserId)
            ->where('p.is_delete', 0)->join('package_store_relation as psr', function ($join) {
                $join->on('psr.package_id', '=', 'p.id')->whereIn('psr.store_id', $this->storeIds);
            })
            ->select([
                'p.id', 'p.name', 'p.price', 'p.coins', 'p.expire_date', 'p.stock',
                'p.is_sekill', 'p.sales', 'p.display_order', 'p.addtime', 'p.flag',
                DB::raw('COUNT(psr.store_id) as available_store_count'),
            ]);

        // 套餐关键字搜索
        if ($keyword = $request->get('keyword')) {
            $builder->where('p.name', 'like', '%' . $keyword . '%');
        }
        // 是否过期搜索
        if (intval($request->get('expire')) > 0) {
            $expire = $request->get('expire');
            if ($expire == 1) {   // 未过期
                $builder->where('p.expire_date', '>=', time());
            } elseif ($expire == 2) {  // 已过期
                $builder->where('p.expire_date', '<', time());
            }
        }
        // 显示状态搜索
        if (intval($request->get('visible')) > 0) {
            $visible = $request->get('visible');
            if ($visible == 1) {
                $builder->where('flag', 1);
            } elseif ($visible == 2) {
                $builder->where('flag', 0);
            }
        }
        // 可用门店关键字搜索
        if ($request->has('store')) {
            $store = $request->get('store');
            $sids = DB::table('bus_stores')->whereIn('id', $this->storeIds)
                ->where('name', 'like', '%' . $store . '%')->lists('id');
            $builder->whereIn('psr.store_id', $sids);
        }

        $packages = $builder->orderBy('is_sekill', 'desc')->orderBy('display_order', 'asc')->orderBy('id', 'desc')->groupBy('p.id')->paginate(20);

        return view('business.package-list', [
            'packages' => $packages,
            'sekill' => !empty($sekill) ? $sekill : 0,
            'expire' => !empty($expire) ? $expire : 0,
            'keyword' => !empty($keyword) ? $keyword : '',
            'store' => !empty($store) ? $store : '',
            'visible' => !empty($visible) ? $visible : 0,
        ]);
    }

    /**
     * 查看可用门店列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function storeRelation(Request $request)
    {
        if (!intval($request->get('id'))) {
            return view('business.error', ['code' => 500, 'msg' => '内部错误']);
        }
        $stores = DB::table('packages as p')->where('p.userid', $this->parentUserId)
            ->where('p.id', $request->get('id'))
            ->leftJoin('package_store_relation as psr', 'psr.package_id', '=', 'p.id')
            ->leftJoin('bus_stores as bs', 'bs.id', '=', 'psr.store_id')
            ->select('bs.name')->get();
        if (!$stores) {
            return view('business.error', ['code' => 404, 'msg' => '该套餐不存在']);
        }
        return view('business.package-available-stores', ['stores' => $stores]);
    }

    /**
     * 获取门店ID套餐列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPackages(Request $request)
    {
        if (!$store_id = intval($request->get('id'))) {
            return response()->json(['code' => 200, '非法操作']);
        }

        $sids = $this->storeIds;
        $packages = DB::table('packages as p')->join('package_store_relation as psr', function ($join) use ($store_id, $sids) {
            $join->on('psr.package_id', '=', 'p.id')->where('psr.store_id', '=', $store_id)->whereIn('psr.store_id', $sids);
        })->select('p.id', 'p.name')->get();
        return response()->json($packages);
    }

    /**
     * 添加套餐
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function add(Request $request)
    {
        if ($request->isMethod('get')) {
            if (!$this->storeIds) {
                return view('business.error', ['code' => 403, 'msg' => '您没有授权门店，不能创建套餐']);
            }
            $reserveImgs = $this->getReservedImages();
            return view('business.add-package', ['stores' => $this->stores, 'reservedImgs' => $reserveImgs]);

        } elseif ($request->isMethod('post')) {
            $data = $request->only($this->fields);

            if (empty($data['name'])) {
                return $this->response(403, '商品名称不能为空');
            }
            if (!floatval($data['price'])) {
                return $this->response(403, '商品价格不能为空');
            }
            if (!intval($data['stock'])) {
                return $this->response(403, '商品库存不能为空');
            }
            if (!$data['expire_date']) {
                return $this->response(403, '请选择过期时间');
            }

            if ($data['type'] == 1) {
                if (!floatval($data['coins'])) {
                    return $this->response(403, '出币数量不能为空');
                }
                unset($data['ticket_name']);
            } elseif ($data['type'] == 2) {
                if (!preg_replace('/\s/', '', $data['ticket_name'])) {
                    return $this->response(403, '门票名称不能为空');
                }
                unset($data['coins']);
            } else {
                return $this->response(500, '内部错误');
            }

            if (!intval($data['image'])) {
                return $this->response(403, '请上传商品图片');
            }
            $data['expire_date'] .= ' ' . $data['expire_time'];
            $data['expire_date'] = strtotime($data['expire_date']);
            unset($data['expire_time']);

            if (empty($data['store_ids'])) {
                return $this->response(403, '可用门店不能为空');
            }

            if (!empty($data['gallery_photos'])) {
                $data['gallery'] = implode(',', $data['gallery_photos']);
            }
            unset($data['gallery_photos'], $data['id']);

            // 检测所选门店是否在授权的门店列表范围内
            if (array_diff($data['store_ids'], $this->storeIds) == $data['store_ids']) {
                return $this->response(403, '您无权选择其中一些门店');
            }
            $store_ids = $data['store_ids'];
            unset($data['store_ids']);

            if (!empty($data['buy_limit']) && !is_numeric($data['buy_limit'])) {
                return $this->response(403, '限购数必须为正整数');
            }
            if ($data['buy_limit'] > $data['stock']) {
                return $this->response(403, '限购数不能大于库存');
            }

            $data['userid'] = $this->parentUserId;
            $data['addtime'] = time();

            if ($packageId = DB::table('packages')->insertGetId($data)) {
                foreach ($store_ids as $id) {
                    DB::table('package_store_relation')->insert(['package_id' => $packageId, 'store_id' => $id]);
                    // 生成二维码
                    BaseQrCode::create(39, ['storeId' => $id, 'packageId' => $packageId]);
                }

                \Operation::insert('packages','添加套餐['.$data['name'].']！',$data);

                return $this->response(200, '添加成功', route('business.package-list'));
            } else {
                return view('merchant.error', ['code' => 500, 'msg' => '内部错误，添加失败']);
            }
        }
    }

    /**
     * 修改套餐
     */
    public function edit(Request $request)
    {
        if ($request->isMethod('get')) {
            if (!$id = $request->get('id')) {
                return view('merchant.error', ['code' => 500, 'msg' => '内部错误']);
            }
            // 套餐详情
            $package = DB::table('packages as p')->where('p.id', $id)
                ->leftJoin('attachment as a', 'a.id', '=', 'p.image')->select('p.*', 'a.path')->first();
            if (!$package) {
                return view('merchant.error', ['code' => 404, 'msg' => '该套餐不存在']);
            }
            $storeIds = DB::table('package_store_relation')->where('package_id', $package->id)
                ->lists('store_id');
            if (array_diff($this->storeIds, $storeIds) == $this->storeIds) {
                return view('business.error', ['code' => 403, 'msg' => '访问被拒绝']);
            }
            // 已经分配的可用商店
            $availableStores = DB::table('package_store_relation')->where('package_id', $id)->lists('store_id');
            // 套餐相册
            $gallery = DB::table('attachment')->whereIn('id', explode(',', $package->gallery))->select('id', 'path')->get();
            // 预留图片
            $reservedImgs = $this->getReservedImages();

            return view('business.edit-package', [
                'stores' => $this->stores,
                'package' => $package,
                'available_stores' => $availableStores,
                'gallery' => $gallery,
                'reservedImgs' => $reservedImgs,
            ]);

        } elseif ($request->isMethod('post')) {
            $data = $request->only($this->fields);

            if (!intval($data['id'])) {
                return $this->response(500, '内部错误');
            }
            if (empty($data['name'])) {
                return $this->response(403, '商品名称不能为空');
            }
            if (!floatval($data['price'])) {
                return $this->response(403, '商品价格不能为空');
            }
            if (!intval($data['stock'])) {
                return $this->response(403, '商品库存不能为空');
            }
            if (!$data['expire_date']) {
                return $this->response(403, '请选择过期时间');
            }

            if ($data['type'] == 1) {
                if (!floatval($data['coins'])) {
                    return $this->response(403, '出币数量不能为空');
                }
                $data['ticket_name'] = '';
            } elseif ($data['type'] == 2) {
                if (!preg_replace('/\s/', '', $data['ticket_name'])) {
                    return $this->response(403, '门票名称不能为空');
                }
                $data['coins'] = 0;
            } else {
                return $this->response(500, '内部错误');
            }

            if (!intval($data['image'])) {
                return $this->response(403, '请上传商品图片');
            }
            $data['expire_date'] .= ' ' . $data['expire_time'];
            $data['expire_date'] = strtotime($data['expire_date']);
            unset($data['expire_time']);

            if (empty($data['store_ids'])) {
                return $this->response(403, '适用门店不能为空');
            }

            if (!empty($data['buy_limit']) && !is_numeric($data['buy_limit'])) {
                return $this->response(403, '限购数必须为正整数');
            }
            if ($data['buy_limit'] > $data['stock']) {
                return $this->response(403, '限购数不能大于库存');
            }

            // 检测所选门店是否在授权的门店列表范围内
            if (array_diff($data['store_ids'], $this->storeIds) == $data['store_ids']) {
                return $this->response(403, '您无权选择其中一些门店');
            }
            $store_ids = $data['store_ids'];
            unset($data['store_ids']);

            $id = $data['id'];

            // 判断该套餐是否是本账号所发
            $package = DB::table('packages as p')->where('p.id', $id)->where('userid', $this->parentUserId)->first();
            if (!$package) {
                return response()->json(['code' => 403, 'msg' => '您无权修改该套餐']);
            }

            //记录修改前的套餐信息
            $before_data = $package;

            // 删除旧图片
            $image = DB::table('attachment')->where('id', $package->image)->select('path', 'reserve')->first();
            // 如果旧图片和新图片不同，并且该图片为非系统预留图片则删除旧图片
            if ($image) {
                if (($package->image != $data['image']) && ($image->reserve == 0)) {
                    @unlink(APP_ROOT . '/' . config('upload.root_path') . '/' . $image->path);
                    DB::table('attachment')->where('id', $package->image)->delete();
                }
            }

            // 上传基础路径
            $dir = APP_ROOT . '/' . config('upload.root_path');

            // 删除旧相册图片
            if (!empty($data['gallery_photos'])) {
                $data['gallery'] = implode(',', $data['gallery_photos']);
                if ($delIds = array_diff(explode(',', $package->gallery), $data['gallery_photos'])) {
                    $paths = DB::table('attachment')->whereIn('id', $delIds)->lists('path');
                    foreach ($paths as $item) {
                        @unlink($dir . '/' . $item);
                    }
                    DB::table('attachment')->whereIn('id', $delIds)->delete();
                }
            } else {
                $delAtts = DB::table('attachment')->whereIn('id', explode(',', $package->gallery))->select('id', 'path')->get();
                foreach ($delAtts as $delAtt) {
                    @unlink($dir . '/' . $delAtt->path);
                    DB::table('attachment')->where('id', $delAtt->id)->delete();
                }
                $data['gallery'] = '';
            }
            unset($data['id'], $data['gallery_photos']);

            if (DB::table('packages')->where('id', $id)->update($data) !== false) {
                // 删除旧门店关联并添加新门店关联
                DB::table('package_store_relation')->where('package_id', $id)->delete();
                foreach ($store_ids as $sid) {
                    DB::table('package_store_relation')->insert(['package_id' => $id, 'store_id' => $sid]);
                }

                // 记录更新日志
                $data['package_id'] = $id;
                $data['userid'] = session()->get('id');
                $data['addtime'] = time();
                $data['ip'] = $request->getClientIp();
                $data['available_stores'] = implode(',',$store_ids);
                $this->setUpdateLog($id,$data);

                \Operation::update('packages','更新套餐['.$data['name'].']！',$before_data,$data);

                return response()->json(['code' => 200, 'msg' => '修改成功', 'url' => route('business.package-list')]);
            } else {
                return response()->json(['code' => 500, 'msg' => '内部错误']);
            }
        }

    }

    /**
     * 写套餐更新日志
     * @param $id
     * @param $data
     */
    private function setUpdateLog($id, $data){
        $package = DB::table('packages')->find($id);
        $package = array_merge((Array)$package,$data);
        unset($package['id']);
        return DB::table('package_update_log')->insert($package);
    }

    /**
     * 删除套餐
     */
    public function delete(Request $request)
    {
        if (!intval($request->get('id'))) {
            return $this->response(500, '内部错误');
        }

        // 判断改套餐是否是本账号所发
        $count = DB::table('packages as p')->where('p.id', $request->get('id'))
            ->where('userid', $this->parentUserId)->count();

        if (!$count) {
            return response()->json(['code' => 403, 'msg' => '禁止操作']);
        }
        // 判断所属
        $sids = DB::table('package_store_relation')->where('package_id', $request->get('id'))->lists('store_id');
        if (array_diff($sids, $this->storeIds) == $sids) {
            return $this->response(403, '您没有权限删除该套餐');
        }

        if (DB::table('packages')->where('id', $request->get('id'))->update(['is_delete' => 1]) !== false) {

            //删除成功记录操作
            \Operation::delete('packages','删除套餐['.$data->name.']！',$data);

            return $this->response(200, '删除成功', route('business.package-list'));
        } else {
            return $this->response(500, '删除失败');
        }
    }

    /**
     * 套餐/商品 详情
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function detail(Request $request)
    {
        if (!$id = intval($request->get('id'))) {
            return view('business.error', ['code' => 500, '内部错误']);
        }

        $detail = DB::table('packages as p')->where('p.id', $id)
            ->leftJoin('attachment as at', 'at.id', '=', 'p.image')
            ->where('p.userid', '=', $this->parentUserId)
            ->select('p.*', 'at.path')->first();

        if (!$detail) {
            return view('business.error', ['code' => 404, 'msg' => '该套餐不存在']);
        }
        $gallery = DB::table('attachment')->whereIn('id', explode(',', $detail->gallery))->lists('path');
        $detail->gallery = $gallery;

        // 检测是否有参与秒杀，有的话把秒杀数据拿出来
        $sekill_data = DB::table('sekill_package')->where('package_id', $id)->first();
        if ($sekill_data) {
            $detail->sekill_data = $sekill_data;
        }

        // 套餐可用门店
        $availableStores = DB::table('package_store_relation as psr')->where('psr.package_id', $id)
            ->join('bus_stores as bs', 'bs.id', '=', 'psr.store_id')
            ->select('bs.id', 'bs.name')->get();

        return view('business.package-detail', ['detail' => $detail, 'availableStores' => $availableStores]);
    }

    /**
     * 加入到秒杀活动
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addToSekill(Request $request)
    {
        if ($request->isMethod('get')) {
            if (!$id = $request->get('id')) {
                return $this->response(500, '内部错误');
            }

            $package = DB::table('packages')->where('id', $id)->where('userid', $this->parentUserId)->first();
            if (!$package) {
                return $this->response(404, '该套餐不存在');
            }
            if ($package->is_sekill == 1) {
                return $this->response(403, '该套餐已参与秒杀');
            }

            return view('business.add-sekill', ['package' => $package]);

        } elseif ($request->isMethod('post')) {
            $data = $request->only('package_id', 'price', 'stock', 'buy_limit', 'start_date', 'end_date');
            if (!$id = floatval($data['package_id'])) {
                return $this->response(500, '内部错误');
            }
            if (!floatval($data['price'])) {
                return $this->response(403, '秒杀价格不能为空');
            }
            if (!intval($data['stock'])) {
                return $this->response(403, '秒杀库存不能为空');
            }
            if (!intval($data['buy_limit'])) {
                return $this->response(403, '秒杀限购数不能为空');
            }
            if (!strtotime($data['start_date']) || !strtotime($data['end_date'])) {
                return $this->response(403, '起始时间不能为空');
            }

            $package = DB::table('packages')->where('id', $id)->where('userid', $this->parentUserId)->first();
            if (!$package) {
                return $this->response(404, '该套餐不存在');
            }
            if ($package->is_sekill == 1) {
                return $this->response(403, '该套餐已参与秒杀');
            }
            if ($data['stock'] > $package->stock) {
                return $this->response(403, '秒杀库存超出总库存 ' . $package->stock);
            }

            $data['userid'] = $this->parentUserId;
            $data['addtime'] = time();
            $data['start_date'] = strtotime($data['start_date']);
            $data['end_date'] = strtotime($data['end_date']);

            DB::beginTransaction();
            try {
                DB::table('sekill_package')->insert($data);
                DB::table('packages')->where('id', $id)->update(['is_sekill' => 1]);
                DB::table('packages')->where('id', $id)->decrement('stock', $data['stock']);
                return $this->response(200, '操作成功', route('business.package-list'));
            } catch (Exception $e) {
                DB::roleBack();
                return $this->response(500, '操作失败');
            }
        }

    }

    /**
     * 修改秒杀信息
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function editSekill(Request $request)
    {
        if ($request->isMethod('get')) {
            if (!$id = $request->get('id')) {
                return view('business.error', ['code' => 500, 'msg' => '内部错误']);
            }

            $package = DB::table('packages as p')->where('p.id', $id)->where('p.userid', $this->parentUserId)
                ->join('package_store_relation as psr', function ($join) {
                    $join->on('psr.package_id', '=', 'p.id')->whereIn('psr.store_id', $this->storeIds);
                })->select('p.*')->first();
            if (!$package) {
                return view('business.error', ['code' => 404, 'msg' => '该套餐不存在']);
            }

            $sekillInfo = DB::table('sekill_package')->where('package_id', $package->id)
                ->where('userid', $this->parentUserId)->first();
            if (!$sekillInfo) {
                return view('business.error', ['code' => 403, 'msg' => '该套餐未参与秒杀，不能修改相关信息']);
            }

            $package->sekillInfo = $sekillInfo;
            return view('business.edit-sekill', ['package' => $package]);

        } elseif ($request->isMethod('post')) {
            $data = $request->only('id', 'stock');
            if (!intval($data['id'])) {
                return $this->response(500, '内部错误');
            }
            if (!intval($data['stock'])) {
                return $this->response(500, '请设置库存量');
            }

            $package = DB::table('packages as p')->where('p.id', $data['id'])->where('p.userid', $this->parentUserId)
                ->join('package_store_relation as psr', function ($join) {
                    $join->on('psr.package_id', '=', 'p.id')->whereIn('psr.store_id', $this->storeIds);
                })->select('p.*')->first();
            if (!$package) {
                return $this->response(404, '该套餐不存在');
            }

            $sekillInfo = DB::table('sekill_package')->where('package_id', $package->id)
                ->where('userid', $this->parentUserId)->first();
            if (!$sekillInfo) {
                return $this->response(403, '该套餐未参与秒杀，不能修改相关信息');
            }

            if ($data['stock'] < $sekillInfo->stock) {
                return $this->response(403, '库存不能低于' . $sekillInfo->stock);
            }
            if (($data['stock'] - $sekillInfo->stock) > $package->stock) {
                return $this->response(403, '总库存不够，请设置合适的秒杀库存');
            }

            DB::beginTransaction();
            try {
                DB::table('sekill_package')->where('package_id', $data['id'])->update(['stock' => $data['stock']]);
                DB::table('packages')->where('id', $package->id)->decrement('stock', ($data['stock'] - $sekillInfo->stock));
                return $this->response(200, '修改成功', route('business.package-list'));
            } catch (Exception $e) {
                DB::rollBack();
                return $this->response(500, '修改失败');
            }

        }
    }

    /**
     * 退出秒杀活动
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function quitSekill(Request $request)
    {
        if (!$id = intval($request->get('id'))) {
            return $this->response(500, '内部错误');
        }

        $package = DB::table('packages as p')->where('p.id', $id)->where('p.userid', $this->parentUserId)
            ->join('package_store_relation as psr', function ($join) {
                $join->on('psr.package_id', '=', 'p.id')->whereIn('psr.store_id', $this->storeIds);
            })->select('p.*')->first();
        if (!$package) {
            return $this->response(404, '该套餐不存在');
        }

        $sekillInfo = DB::table('sekill_package')->where('package_id', $package->id)
            ->where('userid', $this->parentUserId)->first();
        if (!$sekillInfo) {
            return $this->response(403, '该套餐未参与秒杀，不能进行操作');
        }

        DB::beginTransaction();
        try {
            DB::table('packages')->where('id', $package->id)->update(['is_sekill' => 0]);
            // 原库存增加秒杀返回的部分
            DB::table('packages')->where('id', $package->id)->increment('stock', $sekillInfo->stock);
            // 提交事物
            DB::commit();
            return $this->response(200, '退出成功', route('business.package-list'));
        } catch (Exception $e) {
            DB::rollBack();
            return $this->response(500, '退出失败');
        }

    }

    /**
     * 套餐排序
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function orderPackage(Request $request)
    {
        $data = $request->only('orders');
        foreach ($data['orders'] as $k => $order) {
            DB::table('packages')->where('id', intval($k))->update(['display_order' => intval($order)]);
        }
        return $this->response(200, '排序成功', route('business.package-list'));
    }

    /**
     * 套餐分析
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function analysis(Request $request)
    {
        $data = $request->only('start_date', 'end_date', 'money', 'orders', 'payers');

        if (!$this->storeIds) {
            return view('business.package-analysis', [
                'start_date' => $data['start_date'] ? $data['start_date'] : '',
                'end_date' => $data['end_date'] ? $data['end_date'] : '',
                'packages' => [],
                'money' => 0,
                'orders' => 0,
                'payers' => 0,
            ]);
        }

        $sids = implode(',', $this->storeIds);

        $fields = [
            'p.id', 'p.name',
            'o2.order_sum',
            'o2.success_order_count',
            'o2.payed_order_count',
            'o2.unpayed_order_count',
            'b.name as brand_name',
            'bs.name as store_name',
            DB::raw("CONCAT((ROUND(( IF(o2.payed_order_count IS NOT NULL, o2.payed_order_count, 0) / o2.unpayed_order_count) * 100,2)),'%') AS pay_percent"),
        ];

        if ($data['start_date'] && !$data['end_date']) {
            $subCondition = 'AND addtime >= ' . strtotime($data['start_date']);
        } elseif (!$data['start_date'] && $data['end_date']) {
            $subCondition = 'AND addtime <= ' . strtotime($data['end_date']);
        } elseif ($data['start_date'] && $data['end_date']) {
            $subCondition = 'AND addtime BETWEEN ' . strtotime($data['start_date']) . ' AND ' . strtotime($data['end_date']);
        } else {
            $subCondition = '';
        }

        $joinSql3 = DB::raw('(
            SELECT
                good_id, addtime,
                ROUND(SUM(IF(`status` = 2,price,0)),2) AS order_sum,
                SUM(IF(`status` = 2,1,0)) AS success_order_count,
                SUM(IF(`status` = 1 OR `status` = 2 OR `status` = 5 OR (`status` = 3 AND `pay_date` > 0),1,0)) AS payed_order_count,
                SUM(1) AS unpayed_order_count
            FROM `order`
            WHERE `type` = 2 AND `status` IN(0,1,2)
            AND store_id IN(' . $sids . ')
            ' . $subCondition . '
            GROUP BY good_id) AS o2
        ');
        $limit = 20;
        $builder = DB::table('packages as p')
            ->where('p.userid', $this->parentUserId)
            ->join('package_store_relation as psr', function ($join) {
                $join->on('psr.package_id', '=', 'p.id')
                    ->whereIn('psr.store_id', $this->storeIds);
            })
            ->leftJoin($joinSql3, 'o2.good_id', '=', 'p.id')
            ->leftJoin('bus_stores as bs', 'bs.id', '=', 'psr.store_id')
            ->leftJoin(config('tables.base') . '.brand as b', 'b.id', '=', 'bs.brand_id')
            ->select($fields);

        // 排序控制
        if (!$data['money'] && !$data['orders'] && !$data['payers']) {
            $builder->orderBy('p.id', 'desc');
        } else {
            if ($data['money'] && $data['money'] == 1) {
                $builder->orderBy('order_sum', 'asc');
            } elseif ($data['money'] && $data['money'] == 2) {
                $builder->orderBy('order_sum', 'desc');
            }

            if ($data['orders'] && $data['orders'] == 1) {
                $builder->orderBy('success_order_count', 'asc');
            } elseif ($data['orders'] && $data['orders'] == 2) {
                $builder->orderBy('success_order_count', 'desc');
            }

            if ($data['payers'] && $data['payers'] == 1) {
                $builder->orderBy('payed_order_count', 'asc');
            } elseif ($data['payers'] && $data['payers'] == 2) {
                $builder->orderBy('payed_order_count', 'desc');
            }
        }

        $packages = $builder->paginate($limit);

        return view('business.package-analysis', [
            'start_date' => $data['start_date'] ? $data['start_date'] : '',
            'end_date' => $data['end_date'] ? $data['end_date'] : '',
            'money' => $data['money'] ? $data['money'] : 0,
            'orders' => $data['orders'] ? $data['orders'] : 0,
            'payers' => $data['payers'] ? $data['payers'] : 0,
            'packages' => $packages
        ]);

    }

}
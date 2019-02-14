<?php
/**
 * Created by PhpStorm.
 * User: AIMPER
 * Date: 2016/10/21
 * Time: 11:21
 */

namespace App\Http\Controllers\Merchant;

use App\Libraries\BaseQrCode;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Merchant\UserModel as User;
use Mockery\Exception;

class StoreController extends Controller
{

    // 添加门店时的可接受字段
    protected $fields = [
        'id', 'brand_id', 'name', 'system_type', 'member_card_system_id', 'description',
        'home_image', 'coin_price', 'gallery_photos', 'mobile', 'logo'
    ];

    /**
     * 门店列表
     */
    public function index(Request $request)
    {

        // 只拉取该账号能访问的门店
        $builder = DB::table('bus_stores')
            ->where('bus_stores.userid', $this->parentUserId)->whereIn('bus_stores.id', $this->storeIds)->where('is_delete', 0)
            ->leftJoin('store_device as sd', 'sd.store_id', '=', 'bus_stores.id')
            ->leftJoin(config('tables.base') . '.brand as brand', 'brand.id', '=', 'bus_stores.brand_id')
            ->leftJoin('base.region as r', 'r.id', '=', 'bus_stores.region_id')
            ->select(DB::raw('count(sd.ibeacon_id) as device_count,bus_stores.*,brand.name as brand_name,r.full_name as region'))
            ->orderBy('addtime', 'desc')->orderBy('id', 'desc')->groupBy('bus_stores.id');

        if ($keyword = preg_replace('/\s/', '', $request->get('keyword'))) {
            $builder->where('bus_stores.name', 'like', '%' . $keyword . '%');
        }
        if (intval($request->get('status')) > 0) {
            $builder->where('bus_stores.status', $request->get('status'));
        }

        $stores = $builder->paginate(20);

        return view('business.store-list', [
            'stores' => $stores,
            'status' => (intval($request->get('status')) > 0) ? $request->get('status') : 0,
            'keyword' => !empty($keyword) ? $keyword : '',
        ]);
    }

    /**
     * 添加门店
     */
    public function addstore(Request $request)
    {
        if ($request->isMethod('get')) {
            $brand = $this->getBrand();
            $provinces = $this->getProvince();
            return view('business.add-store', ['brand' => $brand, 'provinces' => $provinces]);
        } elseif ($request->isMethod('post')) {
            $brand = $this->getBrand();
            $data = $request->only($this->fields);
            $data['brand_id'] = $brand->id;

            if (empty($data['name'])) {
                return $this->response(403, '门店名称不能为空');
            }

            if (!intval($data['logo'])) {
                return $this->response(403, '门店logo不能为空');
            }
            /*
            if (!intval($data['home_image'])) {
                return $this->response(403, '门店首图不能为空');
            }
            */
            if (!empty($data['gallery_photos'])) {
                $data['gallery'] = implode(',', $data['gallery_photos']);
            }
            unset($data['gallery_photos']);

            $data['addtime'] = time();
            $data['userid'] = $this->parentUserId;

            $storeId = DB::table('bus_stores')->insertGetId($data);
            if ($storeId) {
                if (session('role_id')) {   // 如果是子账号创建门店，则给所在角色加上该门店的访问权限
                    $role_id = session('role_id');
                    DB::table('bus_role_store_access_control')->insert([
                        'role_id' => $role_id,
                        'store_id' => $storeId,
                    ]);
                }
                // 生成二维码
                BaseQrCode::create(11, ['storeId' => $storeId]);

                \Operation::insert('bus_stores','添加门店['.$data['name'].']！',$data);

                return $this->response(200, '门店添加成功', route('business.storelist'));
            } else {
                return $this->response(500, '门店添加失败');
            }
        }
    }

    /**
     * 删除门店
     * 删除门店还要删除与其关联的 卡券、套餐、商品等等，待优化
     */
    public function delstore(Request $request)
    {
        if (!$store_id = intval($request->get('id'))) {
            return $this->response(500, '内部错误');
        }
        if (!in_array($store_id, $this->storeIds)) {
            return $this->response(403, '您无权删除该门店');
        }

        // 判断要删除的门店是否属于本账号开设的门店
        $count = DB::table('bus_stores as bs')->where('bs.id', $store_id)->whereIn('bs.id', $this->storeIds)
            ->where('userid', $this->parentUserId)->count();
        if (!$count) {
            return response()->json(['code' => 403, 'msg' => '该门店不存在']);
        }

        //获取
        $data = DB::table('bus_stores as bs')->where('bs.id', $store_id)->whereIn('bs.id', $this->storeIds)
            ->where('userid', $this->parentUserId)->first();

        if ($request->has('force') && intval($request->get('force')) == 1) {  // 物理删除，附件待删除
            DB::beginTransaction();
            try {
                DB::table('activity_info')->where('store_id', $store_id)->delete();
                DB::table('favorite')->where('favorite_id', $store_id)->where('type', 1)->delete();

                DB::table('packages as p')->join('package_store_relation as psr', function ($join) use ($store_id) {
                    $join->on('psr.package_id', '=', 'p.id')->where('psr.store_id', '=', $store_id);
                })->delete();

                DB::table('ticket as t')->join('ticket_extend as te', function ($join) use ($store_id) {
                    $join->on('te.ticket_id', '=', 't.id')->where('te.store_id', '=', $store_id);
                })->delete();

                DB::table('bus_stores')->where('id', $store_id)->delete();

                DB::commit();

                \Operation::delete('bus_stores','删除门店['.$data->name.']！',$data);

                return $this->response(200, '删除成功', route('business.storelist'));
            } catch (Exception $e) {
                DB::roleBack();
                return $this->response(500, '删除失败');
            }
        } else {  // 软删除
            /**
             * 删除活动资讯
             * 删除机台
             * 删除收藏门店
             * 删除门店套餐
             * 删除卡券
             * 用户事务进行操作
             */
            DB::transaction(function () use ($store_id) {
                if (DB::table('activity_info')->where('store_id', $store_id)->count()) {
                    DB::table('activity_info')->where('store_id', $store_id)->update(['is_delete' => 1]);
                }
                if (DB::table('favorite')->where('favorite_id', $store_id)->where('type', 1)->count()) {
                    DB::table('favorite')->where('favorite_id', $store_id)->where('type', 1)->update(['is_delete' => 1]);
                }
                // 删除门店对应套餐
                $pcount = DB::table('packages as p')->join('package_store_relation as psr', function ($join) use ($store_id) {
                    $join->on('psr.package_id', '=', 'p.id')->where('psr.store_id', '=', $store_id);
                })->count();
                if ($pcount) {
                    DB::table('packages as p')->join('package_store_relation as psr', function ($join) use ($store_id) {
                        $join->on('psr.package_id', '=', 'p.id')->where('psr.store_id', '=', $store_id);
                    })->update(['is_delete' => 1]);
                }
                // 删除门店卡券
                $tcount = DB::table('ticket as t')->join('ticket_extend as te', function ($join) use ($store_id) {
                    $join->on('te.ticket_id', '=', 't.id')->where('te.store_id', '=', $store_id);
                })->count();
                if ($tcount) {
                    DB::table('ticket as t')->join('ticket_extend as te', function ($join) use ($store_id) {
                        $join->on('te.ticket_id', '=', 't.id')->where('te.store_id', '=', $store_id);
                    })->update(['t.is_delete' => 1]);
                }
                // 删除对应产品
                DB::table('iot_product')->where('store_id', $store_id)->update(['del_flag' => 1]);

                DB::table('bus_stores')->where('id', $store_id)->update(['is_delete' => 1]);
            });

            \Operation::delete('bus_stores','删除门店['.$data->name.']！',$data);

            return $this->response(200, '删除成功', route('business.storelist'));
        }


    }

    /**
     * 修改门店
     */
    public function editstore(Request $request)
    {
        if ($request->isMethod('get')) {
            if (!$store_id = intval($request->get('id'))) {
                return view('business.error', ['code' => 500, 'msg' => '内部错误']);
            }
            if (empty($this->storeIds) || !in_array($store_id, $this->storeIds)) {
                return view('business.error', ['code' => 403, 'msg' => '您无权限访问']);
            }

            $referrer = get_http_referrer();

            $store = DB::table('bus_stores as bs')->where('bs.id', $store_id)
                ->where('bs.userid', $this->parentUserId)
                ->leftJoin('attachment as a', 'a.id', '=', 'bs.logo')
                ->leftJoin('attachment as a1', 'a1.id', '=', 'bs.home_image')
                ->leftJoin('base.region as br', 'br.id', '=', 'bs.region_id')
                ->select('bs.*', 'a.path', 'a1.path as home_image_pic', 'br.province', 'br.city', 'br.county')->first();

            if (!$store) {
                return $this->response(403, '该门店不存在');
            }

            $district = DB::table(config('tables.base') . '.region')->where('id', $store->region_id)->select('id', 'name')->first();
            $cityId = DB::table(config('tables.base') . '.region')->where('id', $store->region_id)->select('parent_id')->first();
            if (!empty($cityId)) {
                $city = DB::table(config('tables.base') . '.region')->where('id', $cityId->parent_id)->select('id', 'parent_id', 'name')->first();
            }
            if (!empty($city)) {
                $province = DB::table(config('tables.base') . '.region')->where('id', $city->parent_id)->select('id', 'name')->first();
            }
            $store->province = !empty($province) ? $province : null;
            $store->city = !empty($city) ? $city : null;
            $store->district = !empty($district) ? $district : null;

            $brand = $this->getBrand();
            $provinces = $this->getProvince();
            $gallery = DB::table('attachment')->whereIn('id', explode(',', $store->gallery))->select('id', 'path')->get();

            return view('business.edit-store', [
                'store' => $store,
                'brand' => $brand,
                'provinces' => $provinces,
                'galleryImages' => $gallery,
                'referrer' => $referrer
            ]);
        } elseif ($request->isMethod('post')) {
            $data = $request->only($this->fields);
            $referrer = $request->get('referrer');

            if (!intval($data['id'])) {
                return $this->response(500, '内部错误');
            }
            if (empty($this->storeIds) || !in_array($data['id'], $this->storeIds)) {
                return $this->response(403, '您无权访问');
            }
            if (empty($data['name'])) {
                return $this->response(403, '门店名称不能为空');
            }

            if (!intval($data['logo'])) {
                return $this->response(403, '门店logo不能为空');
            }
            /*
            if (!intval($data['home_image'])) {
                return $this->response(403, '门店首图不能为空');
            }
            */

            if (!empty($data['gallery_photos'])) {
                $data['gallery'] = implode(',', $data['gallery_photos']);
            }

            $store = DB::table('bus_stores as bs')->where('bs.id', $request->get('id'))
                ->where('bs.userid', $this->parentUserId)
                ->leftJoin('attachment as a', 'a.id', '=', 'bs.logo')
                ->select('bs.*', 'a.path')->first();
            if (!$store) {
                return response()->json(['code' => 403, 'msg' => '该门店不存在']);
            }

            $before_data = $store;


            // 新logo不同于旧logo，则更新删除附件和附件数据
            if ($store->logo != $data['logo']) {
                @unlink(APP_ROOT . '/' . config('upload.root_path') . '/' . $store->path);
                DB::table('attachment')->where('id', $store->logo)->delete();
            }

            // 有删除的附件
            if (!empty($data['gallery_photos'])) {
                if ($delIds = array_diff(explode(',', $store->gallery), $data['gallery_photos'])) {
                    $attachments = DB::table('attachment')->whereIn('id', $delIds)->lists('path');
                    $dir = APP_ROOT . '/' . config('upload.root_path');
                    foreach ($attachments as $attachment) {
                        // 删除旧附件
                        @unlink($dir . '/' . $attachment);
                    }
                    DB::table('attachment')->whereIn('id', $delIds)->delete();
                }
            } else {
                $data['gallery'] = '';
            }
            unset($data['gallery_photos']);

            $data['brand_id'] = $this->getBrand()->id;
            //$data['status'] = 2;
            unset($data['id']);

            if (DB::table('bus_stores')->where('id', $request->get('id'))->update($data) !== false) {

                \Operation::update('bus_stores','修改门店['.$before_data->name.']！',$before_data,$data);

                return $this->response(200, '修改成功', $referrer);
            } else {
                return $this->response(500, '修改失败');
            }
        }

    }

    /**
     * 重开/关停门店
     */
    public function operstore(Request $request)
    {

        if (!intval($request->get('id')) || !intval($request->get('s'))) {
            return $this->response(500, '内部错误');
        }
        $id = $request->get('id');
        $s = $request->get('s');

        if (!in_array($id, $this->storeIds)) {
            return $this->response(403, '操作被拒绝');
        }
        // 判断门店是否是本账号所有
        $count = DB::table('bus_stores')->where('id', $id)->where('userid', $this->parentUserId)->count();
        if (!$count) {
            return $this->response(404, '该门店不存在');
        }

        if (DB::table('bus_stores')->where('id', $request->get('id'))->update(['status' => $request->get('s')])) {
            
            \Operation::update('bus_stores','开放/关停门店['.$data->name.']！' ,[] ,[]);

            return $this->response(200, '操作成功', route('business.storelist'));
        } else {
            return $this->response(500, '内部错误');
        }
    }

    /**
     * 门店详情
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function detail(Request $request)
    {
        if (!$id = intval($request->get('id'))) {
            return view('business.error', ['code' => 500, 'msg' => '内部错误']);
        }
        if (!in_array($id, $this->storeIds)) {
            return view('business.error', ['code' => 403, 'msg' => '访问被决绝']);
        }

        $detail = DB::table('bus_stores as bs')->where('bs.id', $id)
            ->join('bus_users as bu', function ($join) {
                $join->on('bu.id', '=', 'bs.userid')->where('bu.id', '=', $this->parentUserId);
            })
            ->leftJoin('base.region as r', 'r.id', '=', 'bs.region_id')
            ->leftJoin('base.brand as b', 'b.id', '=', 'bs.brand_id')
            ->leftJoin('attachment as at', 'at.id', '=', 'bs.logo')
            ->select(['bs.*', 'b.name as brand_name', 'r.province', 'r.city', 'r.county', 'at.path',])->first();

        $packageCount = DB::table('package_store_relation as psr')
            ->join('packages as p', function ($join) use ($detail) {
                $join->on('p.id', '=', 'psr.package_id')
                    ->where('p.is_delete', '=', 0)
                    ->where('psr.store_id', '=', $detail->id);
            })->count('psr.package_id');
        $machineCount = DB::table('iot_product as ip')->where('ip.store_id', $id)
            ->where('ip.del_flag', 0)->count('ip.id');
        $ticketCount = DB::table('ticket_extend as te')->where('te.store_id', $id)
            ->join('ticket as t', function ($join) {
                $join->on('t.id', '=', 'te.ticket_id')->where('t.is_delete', '=', 0);
            })->count('te.ticket_id');
        $detail->package_count = $packageCount;
        $detail->machine_count = $machineCount;
        $detail->ticket_count = $ticketCount;

        if (!$detail) {
            return view('business.error', ['code' => 404, 'msg' => '该门店不存在']);
        }

        $gallery = DB::table('attachment')->whereIn('id', explode(',', $detail->gallery))->lists('path');
        $detail->gallery = $gallery;

        return view('business.store-detail', ['store' => $detail]);
    }

    /**
     * 显示门店蓝牙设备
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showBluetoothDevices(Request $request)
    {
        if (!$request->has('id') || !$id = intval($request->get('id'))) {
            return view('merchant.error', ['code' => 500, 'msg' => '内部错误']);
        }
        if (!in_array($id, $this->storeIds)) {
            return view('merchant.error', ['code' => 403, 'msg' => '您无权查看该门店的设备']);
        }
        $devices = DB::table('store_device')->where('store_id', $id)->get();

        return view('merchant.store-bluetooth-devices', ['devices' => $devices]);
    }

    /**
     * 设置会员卡积分转出率 和 奖票积分转平台积分比率 和 智联宝比率
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function setScoreOutputRate(Request $request)
    {
        if ($request->isMethod('get')) {
            if (!$request->get('id')) {
                return view('business.error', ['code' => 500, 'msg' => '内部错误']);
            }
            $store = DB::table('bus_stores')->where('id', $request->get('id'))
                ->where('userid', $this->parentUserId)->first();

            if (!$store) {
                return view('business.error', ['code' => 404, 'msg' => '该门店不存在']);
            }
            if ($store->score_output_switch == 0) {
                return view('business.error', ['code' => 403, 'msg' => '该门店未授权设置积分转出率']);
            }
            return view('business.set-score-output-rate', ['store' => $store]);

        } elseif ($request->isMethod('post')) {
            $data = $request->only('id', 'rate');

            if (!intval($data['id'])) {
                return $this->response(500, '内部错误');
            }
            if (!floatval($data['rate'])) {
                return $this->response(403, '请输入积分转出率');
            }
            $store = DB::table('bus_stores')->where('id', $request->get('id'))
                ->where('userid', $this->parentUserId)->first();

            if (!$store) {
                return $this->response(404, '该门店不存在');
            }
            if ($store->score_output_switch == 0) {
                return $this->response(403, '该门店未授权设置积分转出率');
            }
            $saveData = [
                'member_score_out_rate' => $data['rate'],
            ];

            if (DB::table('bus_stores')->where('id', $store->id)->update($saveData) !== false) {
                return $this->response(200, '设置成功', route('business.storelist'));
            } else {
                return $this->response(500, '设置失败');
            }
        }
    }

    /**
     * 设置奖票转平台积分比率
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function setTicketOutputRate(Request $request)
    {
        if ($request->isMethod('get')) {
            if (!$request->get('id')) {
                return view('business.error', ['code' => 500, 'msg' => '内部错误']);
            }
            $store = DB::table('bus_stores')->where('id', $request->get('id'))
                ->where('userid', $this->parentUserId)->first();

            if (!$store) {
                return view('business.error', ['code' => 404, 'msg' => '该门店不存在']);
            }
            if ($store->ticket_output_switch == 0) {
                return view('business.error', ['code' => 403, 'msg' => '该门店未授权奖票转出积分']);
            }
            return view('business.set-ticket-output-rate', ['store' => $store]);

        } elseif ($request->isMethod('post')) {
            $data = $request->only('id', 'ticket_rate');

            if (!intval($data['id'])) {
                return $this->response(500, '内部错误');
            }
            if (!floatval($data['ticket_rate'])) {
                return $this->response(403, '请输入奖票转积分比例');
            }
            $store = DB::table('bus_stores')->where('id', $request->get('id'))
                ->where('userid', $this->parentUserId)->first();

            if (!$store) {
                return $this->response(404, '该门店不存在');
            }
            if ($store->ticket_output_switch == 0) {
                return $this->response(403, '该门店未授权奖票转出积分');
            }
            $saveData = [
                'prize_ticket_out_rate' => $data['ticket_rate'],
            ];

            if (DB::table('bus_stores')->where('id', $store->id)->update($saveData) !== false) {
                return $this->response(200, '设置成功', route('business.storelist'));
            } else {
                return $this->response(500, '设置失败');
            }
        }
    }

    /**
     * 更新门店会员卡套餐
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateMemberPlan(Request $request)
    {
        if (!intval($request->get('id'))) {
            return $this->response(500, '内部错误');
        }

        $store = DB::table('bus_stores')
            ->where('id', $request->get('id'))
            ->where('is_delete', 0)
            ->where('userid', $this->parentUserId)
            ->whereIn('id', $this->storeIds)
            ->first();

        if (!$store) {
            return $this->response(404, '该门店不存在或者您未被授权该门店的管理权限');
        }

        if ($store->member_flag == 0) {
            return $this->response(403, '该门店未开启会员卡系统');
        }

        if (!$store->member_card_system_id) {
            return $this->response(403, '该门店不存在线下会员卡系统，不能更新套餐');
        }

        $client = new Client();
        $url = config('misc.update_member_plan_url') . '?storeId=' . $store->id;
        $res = $client->get($url);

        if ($res->getStatusCode() != 200) {
            return $this->response(500, '通讯错误，更新失败');
        }

        $content = json_decode($res->getBody()->getContents());
        if ($content->retCode == 0) {
            return $this->response(200, '套餐更新成功');
        }

        return $this->response(500, '获取门店会员卡套餐信息出错，更新失败');

    }

    /**
     * 检测门店服务器异常状态
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkServerStatus(Request $request)
    {
        if (!intval($request->get('id'))) {
            return $this->response(500, '内部错误');
        }

        $store = DB::table('bus_stores')
            ->where('userid', $this->parentUserId)
            ->whereIn('id', $this->storeIds)
            ->where('id', $request->get('id'))
            ->where('is_delete', 0)
            ->first();

        if (!$store) {
            return $this->response(404, '该门店不存在');
        }

        if ($store->member_flag == 0) {
            return $this->response(403, '该门店未开启会员卡系统');
        }
        if (empty($store->member_card_system_id)) {
            return $this->response(403, '该门店不存在线下会员卡系统，不能检测服务器状态');
        }

        $url = config('misc.store_server_checker_url') . '?storeId=' . $store->id;
        $client = new Client();
        $res = $client->get($url);

        if (!$res || $res->getStatusCode() != 200) {
            return $this->response(500, '通讯异常，检测失败');
        }

        $content = json_decode($res->getBody()->getContents());
        if ($content->retCode == 0) {
            return $this->response(200, '门店服务器通讯正常');
        }
        return $this->response(500, '门店服务器通讯异常，可能会影响门店会员卡套餐的购买');

    }

    /**
     * 分配门店管理员
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function setManager(Request $request)
    {
        if ($request->isMethod('get')) {
            $id = $request->get('id');

            if (!intval($id)) {
                return view('business.error', ['code' => 403, 'msg' => '请求出错']);
            }

            $store = DB::table('bus_stores')
                ->where('userid', $this->parentUserId)
                ->where('id', $id)
                ->where('is_delete', 0)
                ->select('id', 'name', 'status')
                ->first();

            if (!$store) {
                return view('business.error', ['code' => 404, 'msg' => '该门店不存在']);
            }

            if ($store->status == 3) {
                return view('business.error', ['code' => 404, 'msg' => '该门店已关停，不能分配管理员']);
            }

            $users = DB::table('bus_users')->where('pid', $this->parentUserId)
                ->where('status', 1)->select('id', 'name')->get();

            $manager = DB::table('bus_store_manager')->where('store_id', $store->id)->first();

            return view('business.set-store-manager', ['store' => $store, 'users' => $users, 'manager' => $manager]);

        } elseif ($request->isMethod('post')) {
            $data = $request->only('store_id', 'userid');

            if (!intval($data['store_id']) || !intval($data['userid'])) {
                return $this->response(404, '请求出错');
            }

            $store = DB::table('bus_stores')
                ->where('userid', $this->parentUserId)
                ->where('id', $data['store_id'])
                ->where('is_delete', 0)
                ->select('id', 'name', 'status')
                ->first();

            if (!$store) {
                return $this->response(404, '该门店不存在');
            }

            if ($store->status == 3) {
                return $this->response(403, '该门店已关停，不能分配管理员');
            }

            $user = DB::table('bus_users')->where('pid', $this->parentUserId)
                ->where('id', $data['userid'])->select('id', 'status')->first();

            if (!$user) {
                return $this->response(404, '该子账号不存在');
            }

            if ($user->status != 1) {
                return $this->response(403, '该子账号状态异常，不能被分配为管理员');
            }

            $exist = DB::table('bus_store_manager')->where('store_id', $store->id)
                ->where('bus_userid', $user->id)->first();

            if ($exist) {
                return $this->response(403, '该门店已分配给该子账号，无需重复操作');
            }

            $save = ['bus_userid' => $user->id, 'store_id' => $store->id, 'create_date' => date('Y-m-d H:i:s')];

            DB::beginTransaction();
            try {
                DB::table('bus_store_manager')->where('store_id', $store->id)->delete();
                DB::table('bus_store_manager')->insert($save);
                DB::commit();
                return $this->response(200, '门店管理员设置成功', route('business.storelist'));
            } catch (Exception $e) {
                DB::rollBack();
                return $this->response(500, '门店管理员设置失败');
            }
        }
    }

    /**
     * 门店分析
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function analysis(Request $request)
    {
        $data = $request->only('start_date', 'end_date', 'keyword');

        // 时间段筛选
        $st = strtotime($data['start_date']);
        $et = strtotime($data['end_date']);
        $visitCondition = '';   // 访客表时间筛选条件
        $orderCondition = '';   // 订单表时间筛选条件
        $iotCondition = '';     // 机台订单时间筛选
        $scoreCondition = '';   // 转入积分时间筛选

        if ($st && !$et) {
            $visitCondition = 'WHERE last_visit_time >=' . $st;
            $orderCondition = 'AND convert_time >=' . $st;
            $iotCondition = 'AND pay_date >="' . $data['start_date'] . '"';
            $scoreCondition = 'WHERE create_date >="' . $data['start_date'] . '"';
        } elseif (!$st && $et) {
            $visitCondition = 'WHERE last_visit_time <=' . $et;
            $orderCondition = 'AND convert_time <=' . $et;
            $iotCondition = 'AND pay_date <="' . $data['end_date'] . '"';
            $scoreCondition = 'WHERE create_date <="' . $data['end_date'] . '"';
        } elseif ($st && $et) {
            $visitCondition = 'WHERE last_visit_time BETWEEN ' . $st . ' AND ' . $et;
            $orderCondition = 'AND convert_time BETWEEN ' . $st . ' AND ' . $et;
            $iotCondition = 'AND pay_date BETWEEN "' . $data['start_date'] . '" AND "' . $data['end_date'] . '"';
            $scoreCondition = 'WHERE create_date BETWEEN "' . $data['start_date'] . '" AND "' . $data['end_date'] . '"';
        }

        $joinSql1 = '(
            SELECT 
                `store_id`,
                COUNT(DISTINCT IF(userid > 0,userid,NULL)) + SUM(IF(userid = 0,1,0))  AS total_visitor_count
            FROM `store_visit_log`
            ' . $visitCondition . '
            GROUP BY `store_id`
            ) svl';
        $joinSql2 = '(
            SELECT 
                store_id,
                COUNT(DISTINCT IF(o.type = 2 OR o.type = 3, o.userid, null)) AS consumer_count,
                ROUND(SUM(IF(o.type = 3, pay_price, 0)),2) AS member_package_income,
                SUM(IF(o.type = 3, 1, 0)) AS member_package_count,
                ROUND(SUM(IF(o.type = 2, pay_price, 0)),2) AS package_income,
                SUM(IF(o.type = 2, 1, 0)) AS package_count,
                SUM(IF(o.type = 3 OR o.type = 2, 1, 0)) AS all_package_count,
                -- (ROUND(SUM(IF(o.type = 2, pay_price, 0)),2) + ROUND(SUM(IF(o.type = 3, pay_price, 0)),2)) / (ROUND(SUM(IF(o.type = 3, pay_price, 0)),2) + ROUND(SUM(IF(o.type = 2, pay_price, 0)),2)) AS average_price
                ROUND( (ROUND(SUM(IF(o.type = 3, pay_price, 0)),2) + ROUND(SUM(IF(o.type = 2, pay_price, 0)),2)) / SUM(IF(o.type = 3 OR o.type = 2, 1, 0)), 2) AS average_price
            FROM `order` AS `o` 
            WHERE o.`status` = 2 AND `from` = 1 AND type IN(2,3)' . $orderCondition . '
            GROUP BY o.`store_id`
            ) o';
        $joinSql3 = '(
            SELECT
                store_id,
            ROUND(SUM(io.coin_price * io.coin_qty * io.num / 100),2) AS income
            FROM iot_order AS io
            JOIN iot_machine AS im ON im.id = io.machine_id AND im.del_flag = 0
            JOIN iot_product AS ip ON ip.id = im.product_id AND ip.del_flag = 0
            WHERE io.status = 3 ' . $iotCondition . '
            GROUP BY ip.store_id
        ) AS io';
        $joinSql4 = '(
            SELECT SUM(score) AS scores, store_id
            FROM member_score_output_log
            ' . $scoreCondition . '
            GROUP BY store_id
        ) AS ms';

        $builder = DB::table('bus_stores as bs')->where('is_delete', 0)->where('status', 1)
            ->leftJoin(DB::raw($joinSql1), 'svl.store_id', '=', 'bs.id')
            ->leftJoin(DB::raw($joinSql2), 'o.store_id', '=', 'bs.id')
            ->leftJoin(DB::raw($joinSql3), 'io.store_id', '=', 'bs.id')
            ->leftJoin(DB::raw($joinSql4), 'ms.store_id', '=', 'bs.id')
            ->leftJoin('ticket_out_log as tol', 'tol.store_id', '=', 'bs.id')
            ->whereIn('bs.id', $this->storeIds);

        $fields = [
            'bs.id as store_id', //门店ID
            'bs.name as store_name',    // 门店名称
            'svl.total_visitor_count', // 总访客数
            'o.consumer_count',   // 消费人数
            'o.member_package_income',  // 会员卡套餐销售额
            'o.member_package_count',  // 会员卡套餐销量
            'o.package_income',  // 非会员卡套餐销售额
            'o.package_count',  // 非会员卡套餐销量
            'io.income AS iot_order_income',  // 智联宝机台收入
            DB::raw('o.member_package_income + o.package_income + IF(io.income, io.income, 0) AS all_income'),  // 总收入
            'o.all_package_count',  // 套餐交易笔数 = 非会员套餐交易笔数 + 会员套餐交易笔数
            'o.average_price',  // 套餐客单价，会员非会员交易额/会员非会员交易量
            'ms.scores',  // 线下门店转入平台积分
            // DB::raw('ROUND(SUM(tol.rate * tol.real_ticket),2) AS ticket_transferred_scores')  // 彩票转出积分
            DB::raw('ROUND(SUM(tol.real_ticket / tol.rate),2) AS ticket_transferred_scores')  // 彩票转出积分
        ];

        // 门店筛选
        if ($data['keyword']) {
            $builder->where('bs.name', 'like', '%' . $data['keyword'] . '%');
        }

        $list = $builder->select($fields)->groupBy('bs.id')->orderBy('bs.id', 'desc')->paginate(20);

        return view('business.store-analysis', ['list' => $list, 'params' => $data]);
    }

    /**
     * 修改门店地址
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function editAddress(Request $request)
    {
        if ($request->isMethod('get')) {
            $id = $request->get('id');
            if (!intval($id)) {
                return view('business.error', ['code' => 403, 'msg' => '请求出错']);
            }

            $store = DB::table('bus_stores')
                ->where('userid', $this->parentUserId)
                ->whereIn('id', $this->storeIds)
                ->where('id', $id)
                ->select(['id', 'region_id', 'address', 'longitude', 'latitude'])
                ->first();

            if (!$store) {
                return view('business.error', ['code' => 404, 'msg' => '该门店不存在或者您没有该门店的管理权限']);
            }

            $district = DB::table(config('tables.base') . '.region')->where('id', $store->region_id)->first();
            if ($district) {
                $city = $district->parent_id;
                $province = DB::table(config('tables.base') . '.region')->where('id', $city)
                    ->select('parent_id')->first();

                $store->province_id = $province->parent_id;
                $store->city_id = $city;
                $store->district_id = $district->id;
            } else {
                $store->province_id = 0;
                $store->city_id = 0;
                $store->district_id = 0;
            }

            $provinces = $this->getProvince();

            return view('business.edit-store-address', ['store' => $store, 'provinces' => $provinces]);

        } elseif ($request->isMethod('post')) {
            $data = $request->only('id', 'region_id', 'address', 'longitude', 'latitude');

            if (!intval($data['id'])) {
                return $this->response(403, '请求出错');
            }

            if (!intval($data['region_id'])) {
                return $this->response(403, '请为门店选择一个地区');
            }

            if (!trim_blanks($data['address'])) {
                return $this->response(403, '请填写门店具体地址');
            }

            if (!floatval($data['longitude']) || !floatval($data['latitude'])) {
                return $this->response(403, '门店地址经纬度获取失败，请重新定位');
            }

            $store = DB::table('bus_stores')
                ->where('userid', $this->parentUserId)
                ->whereIn('id', $this->storeIds)
                ->where('id', $data['id'])
                ->select(['id', 'region_id', 'address', 'longitude', 'latitude'])
                ->first();

            if (!$store) {
                return $this->response(404, '该门店不存在或者您没有该门店的管理权限');
            }

            unset($data['id']);

            if (DB::table('bus_stores')->where('id', $store->id)->update($data) !== false) {
                return $this->response(200, '门店地址修改成功', route('business.storelist'));
            } else {
                return $this->response(500, '门店地址修改失败');
            }

        }
    }


}
<?php
/**
 * Created by PhpStorm.
 * User: D.Rui
 * Date: 2016/11/10
 * Time: 11:34
 */

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Jobs\BusDeliverTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    // 允许接收字段
    protected $fields = [
        'id', 'name', 'type', 'denomination', 'discount', 'circulation', 'get_start_date',
        'get_end_date', 'start_date', 'expire_date', 'instruction', 'flag', 'store_ids'
    ];

    /**
     * 门店卡券列表，不包括平台券
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $limit = 20;     // 每页显示数量

        if (!empty($this->storeIds)) {
            $builder = DB::table('ticket as t')->where('t.userid', $this->parentUserId)->where('is_delete', 0)
                ->join('ticket_extend as te', function ($join) {
                    $join->on('te.ticket_id', '=', 't.id')->where('te.store_id', '!=', 0)->whereIn('te.store_id', $this->storeIds);
                })->leftJoin('attachment as a', 'a.id', '=', 't.image')
                ->leftJoin('ticket_get_record as tgr', 'tgr.ticket_id', '=', 't.id')
                ->orderBy('t.id', 'desc')
                ->select('t.*', 'a.path', DB::raw('(SELECT COUNT(ticket_id) FROM ticket_get_record as got_count WHERE ticket_id = t.id) as got_count'))
                ->groupBy('t.id');
        } else {
            return view('business.ticket-list', [
                'type' => !empty($type) ? $type : 0,
                'expire' => !empty($expire) ? $expire : '',
                'recommend' => !empty($recommend) ? $recommend : 0,
                'keyword' => !empty($keyword) ? $keyword : '',
            ]);
        }

        $type = intval($request->get('type'));
        $expire = intval($request->get('expire'));
        $recommend = intval($request->get('recommend'));
        $keyword = preg_replace('/\s/', '', $request->get('keyword'));

        if ($type > 0) {
            $builder->where('t.type', $type);
        }
        if ($expire > 0) {
            if ($expire == 1) {
                $builder->where('t.get_start_date', '<', time())->where('t.expire_date', '>=', time());
            } elseif ($expire == 2) {
                $builder->where('t.expire_date', '<', time());
            }
        }
        if ($recommend > 0) {
            if ($recommend == 1) {
                $builder->where('t.flag', 1);
            } elseif ($recommend == 2) {
                $builder->where('t.flag', 0);
            }
        }
        if ($keyword) {
            $builder->where('t.name', 'like', '%' . $keyword . '%');
        }

        $tickets = $builder->paginate($limit);

        return view('business.ticket-list', [
            'tickets' => !empty($tickets) ? $tickets : [],
            'type' => !empty($type) ? $type : 0,
            'expire' => !empty($expire) ? $expire : '',
            'recommend' => !empty($recommend) ? $recommend : 0,
            'keyword' => !empty($keyword) ? $keyword : '',
        ]);

    }

    /**
     * 添加卡券
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function addTicket(Request $request)
    {
        if ($request->isMethod('get')) {  // get，显示表单
            return view('business.add-ticket', ['stores' => $this->stores]);
        } elseif ($request->isMethod('post')) {    // post，处理数据
            $data = $request->only([
                'image', 'type', 'name', 'denomination', 'discount', 'circulation', 'get_start_date',
                'get_end_date', 'start_date', 'expire_date', 'instruction', 'store_ids', 'visible'
            ]);
            if ($data['type'] == 3 && !$data['name']) {
                return $this->response(403, '名称不能为空');
            }
            if (empty($data['type']) || !intval($data['type'])) {
                return $this->response(403, '类型不能为空');
            }
            if (!intval($data['image'])) {
                return $this->response(403, '图片不能为空');
            }

            switch ($data['type']) {
                case 1:
                    unset($data['discount']);
                    if (!floatval($data['denomination'])) {
                        return $this->response(403, '面额不能为空');
                    }
                    $data['name'] = $data['denomination'] . '元现金券';
                    break;
                case 2:
                    unset($data['denomination']);
                    if (!floatval($data['discount'])) {
                        return $this->response(403, '折扣不能为空');
                    }
                    if (floatval($data['discount']) < 1) {
                        return $this->response(403, '折扣率不能小于1折');
                    }
                    if (floatval($data['discount']) > 9.9) {
                        return $this->response(403, '折扣率不能大于9.9');
                    }
                    $data['name'] = $data['discount'] . '折优惠券';
                    $data['discount'] = $data['discount'] / 10;
                    break;
                case 3:
                    unset($data['denomination']);
                    unset($data['discount']);
                    break;
            }

            if (!$data['get_start_date']) {
                return $this->response(403, '领取开始时间不能为空');
            }
            if (!$data['get_end_date']) {
                return $this->response(403, '领取结束时间不能为空');
            }
            if (!$data['start_date']) {
                return $this->response(403, '有效期开始时间不能为空');
            }
            if (!$data['expire_date']) {
                return $this->response(403, '有效期结束时间不能为空');
            }
            if (!$data['instruction']) {
                return $this->response(403, '使用说明不能为空');
            }
            if (!intval($data['circulation']) || intval($data['circulation']) < 1) {
                return $this->response(403, '发放量必须大于1');
            }
            if (!empty($data['flag']) && intval($data['flag'])) {
                $data['flag'] = 1;
            }

            if (!empty($data['get_start_date'])) {
                $data['get_start_date'] = strtotime($data['get_start_date']);
            }
            if (!empty($data['get_end_date'])) {
                $data['get_end_date'] = strtotime($data['get_end_date']);
            }

            if (empty($data['store_ids'])) {
                return $this->response(403, '请选择可用门店');
            }

            $store_ids = $data['store_ids'];
            unset($data['store_ids']);

            // 检测所选门店是否是授权门店
            if ($store_ids && is_array($store_ids)) {
                foreach ($store_ids as $store_id) {
                    if (!in_array($store_id, $this->storeIds)) {
                        return $this->response(500, '内部错误');
                    }
                }
            }

            $data['start_date'] = strtotime($data['start_date']);
            $data['expire_date'] = strtotime($data['expire_date']);
            $data['addtime'] = time();
            $data['userid'] = $this->parentUserId;

            if ($ticketId = DB::table('ticket')->insertGetId($data)) {
                // 写卡券可用门店关联表
                foreach ($store_ids as $id) {
                    DB::table('ticket_extend')->insert(['ticket_id' => $ticketId, 'store_id' => $id]);
                }

                $data['store_ids'] = $store_ids;
                \Operation::insert('ticket','添加卡券['.$data['name'].']！',$data);

                return $this->response(200, '添加成功', route('business.ticket-list'));
            } else {
                return $this->response(500, '添加失败');
            }

        }
    }

    /**
     * 修改卡券
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function edit(Request $request)
    {
        if ($request->isMethod('post')) {     // post，处理数据
            $data = $request->only('id', 'circulation', 'visible'); // 只允许改 推荐标识和发放量
            if (!$id = intval($data['id'])) {
                return $this->response(500, '内部错误');
            }

            if (!$circulation = intval($data['circulation'])) {
                return $this->response(500, '发放量必须为大于0的整数');
            }
            // 判断是否是本账号的卡券，如果该账号只被授权门店一，则不能在门店一使用的卡券是无权修改的
            $ticket = DB::table('ticket as t')->where('t.id', $id)->where('t.userid', $this->parentUserId)
                ->join('ticket_extend as te', function ($join) {
                    $join->on('te.ticket_id', '=', 't.id')->whereIn('te.store_id', $this->storeIds);
                })->first();
            if (!$ticket) {
                return $this->response(404, '该卡券不存在');
            }
            if ($ticket->expire_date < time()) {
                return $this->response(401, '该卡券已过期，不能修改');
            }
            if ($data['circulation'] < $ticket->circulation) {
                return $this->response(403, '卡券的库存不能小于原库存');
            }

            $before_data = $ticket;

            if (DB::table('ticket')->where('id', $id)->update(['circulation' => $circulation, 'visible' => $data['visible']]) !== false) {

                \Operation::update('ticket','修改卡券！',$before_data,$data);

                return $this->response(200, '修改成功', route('business.ticket-list'));
            } else {
                return $this->response(500, '修改失败');
            }

        } elseif ($request->isMethod('get')) {     // get，显示表单
            if (!$id = intval($request->get('id'))) {
                return view('business.error', ['code' => 500, 'msg' => '内部错误']);
            }
            $ticket = DB::table('ticket as t')->where('t.id', $id)->where('userid', $this->parentUserId)
                ->leftJoin('attachment as a', 'a.id', '=', 't.image')
                ->join('ticket_extend as te', function ($join) {
                    $join->on('te.ticket_id', '=', 't.id')->whereIn('te.store_id', $this->storeIds);
                })
                ->select('t.*', 'a.path')->first();
            if (!$ticket) {
                return view('business.error', ['code' => 404, 'msg' => '该卡券不存在']);
            }
            if ($ticket->expire_date < time()) {
                return view('business.error', ['code' => 401, 'msg' => '该卡券已过期，不能修改']);
            }
            $stores = $this->getStores();
            // 已分配的可用门店
            $availableStores = DB::table('ticket_extend')->where('ticket_id', $id)->lists('store_id');

            return view('business.edit-ticket', ['ticket' => $ticket, 'stores' => $stores, 'availableStores' => $availableStores]);
        }

    }

    /**
     * 发放卡券
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function posting(Request $request)
    {
        if ($request->isMethod('get')) {
            if (!intval($request->get('id'))) {
                return view('business.error', ['code' => 500, 'msg' => '背部错误']);
            }
            $id = $request->get('id');
            $ticket = DB::table('ticket as t')->where('t.id', '=', $id)->where('userid', $this->parentUserId)
                ->where('t.is_delete', 0)->where('t.get_start_date', '<', time())
                ->where('t.expire_date', '>=', time())->first();
            $availableStoreIds = DB::table('ticket_extend')->where('ticket_id', $ticket->id)->lists('store_id');
            if (!$ticket) {
                return view('business.error', ['code' => 404, 'msg' => '该卡券不存在']);
            }
            if (array_diff($this->storeIds, $availableStoreIds) == $this->storeIds) {
                return view('business.error', ['code' => 403, 'msg' => '您无权发放该卡券']);
            }
            // 该卡券已发放数量
            $postedCount = DB::table('ticket_posting_log')->where('ticket_id', $id)->count('userid');
            $ticket->postedCount = $postedCount;
            // 授权门店范围内消费过的用户数
            $consumerCount = DB::table('order')->whereIn('store_id', $this->storeIds)->distinct()->count('userid');
            $ticket->consumerCount = $consumerCount;
            // 授权门店访客数
            $visitorCount = DB::table('store_visit_log')->whereIn('store_id', $this->storeIds)->distinct()->count('userid');
            $ticket->visitorCount = $visitorCount;

            return view('business.posting-ticket', ['ticket' => $ticket, 'stores' => $this->stores]);

        } elseif ($request->isMethod('post')) {
            $data = $request->only('id', 'store_ids', 'target');

            if (!intval($data['id'])) {
                return $this->response(500, '内部错误');
            }
            if (empty($data['store_ids']) || !is_array($data['store_ids'])) {
                return $this->response(500, '请选择门店');
            }
            if (!intval($data['target'])) {
                return $this->response(500, '请选择目标人群');
            }

            $ticket = DB::table('ticket as t')->where('t.id', $data['id'])->where('t.userid', $this->parentUserId)
                ->where('t.is_delete', 0)->where('t.get_start_date', '<', time())
                ->where('t.expire_date', '>=', time())->first();
            if (!$ticket) {
                return $this->response(404, '该卡券不存在');
            }
            $availableStoreIds = DB::table('ticket_extend')->where('ticket_id', $ticket->id)->lists('store_id');
            if (array_diff($this->storeIds, $availableStoreIds) == $this->storeIds) {
                return view('business.error', ['code' => 403, 'msg' => '您无权发放该卡券']);
            }

            // 获取目标人群用户ID
            if ($data['target'] == 1) {   // 访客
                $count = DB::table('store_visit_log')->whereIn('store_id', $data['store_ids'])->distinct()->count('userid');
            } elseif ($data['target'] == 2) {  // 消费过的用户
                $count = DB::table('order')->whereIn('store_id', $data['store_ids'])->distinct()->count('userid');
            } else {
                return $this->response(500, '内部错误');
            }

            // 如果库存量不足以支撑发放量，则不发放
            if ($ticket->circulation < $count) {
                return $this->response(403, '该卡券库存量不足以支撑发放量，请增加库存后在操作');
            }

            // 执行发放操作并推送通知
            $schema = DB::table('qrcode_type')->where('id', 17)->select('app_url')->first();
            $job = new BusDeliverTicket($ticket, $data['store_ids'], $data['target'], [
                'title' => '获得卡券提醒',
                'content' => '您收到一张卡券，请到[我的卡券]查收哦！',
                'scheme' => $schema->app_url
            ]);
            $this->dispatch($job);

            return $this->response(200, '操作成功，数据量大的情况下推送可能稍有延迟', route('business.ticket-list'));
        }
    }

    /**
     * 推荐在APP首页显示
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function recommend(Request $request)
    {
        if (!intval($request->get('id'))) {
            return $this->response(500, '内部错误');
        }
        if (!intval($request->get('f'))) {
            return $this->response(500, '内部错误');
        }
        if ($request->get('f') == 1) {
            $flag = 1;
        } elseif ($request->get('f') == 2) {
            $flag = 0;
        }
        $id = $request->get('id');
        $ticket = DB::table('ticket')->where('ticket.id', $id)->join('ticket_extend as te', function ($join) {
            $join->on('te.ticket_id', '=', 'ticket.id')->whereIn('te.store_id', $this->storeIds);
        })->first();

        if (!$ticket) {
            return $this->response(403, '该卡券不存在');
        }

        if ($ticket->expire_date < time()) {
            return $this->response(403, '该卡券已过期，不能推荐');
        }
        if (DB::table('ticket')->where('id', $id)->update(['flag' => $flag]) !== false) {
            return $this->response(200, '操作成功', route('business.ticket-list'));
        } else {
            return $this->response(200, '操作失败', route('business.ticket-list'));
        }
    }

    /**
     * 删除
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        if (!$id = intval($request->get('id'))) {
            return $this->response(500, '内部错误');
        }

        $ticket = DB::table('ticket as t')->where('t.id', $id)->where('t.userid', $this->parentUserId)
            ->join('ticket_extend as te', function ($join) {   // 只取授权门店能使用的卡券
                $join->on('te.ticket_id', '=', 't.id')->whereIn('te.store_id', $this->storeIds);
            })
            ->leftJoin('attachment as a', 'a.id', '=', 't.image')
            ->select('t.*', 'a.path')->first();
        if (!$ticket) {
            return $this->response(404, '该卡券不存在');
        }
        // 卡券必须是还未开放领取或者已过期的才能删除，以防有人领取了删除就出现
        if (time() > $ticket->get_start_date && $ticket->expire_date > time()) {
            return $this->response(401, '卡券已开放领取，不能删除');
        }

        if (DB::table('ticket')->where('id', $ticket->id)->update(['is_delete' => 1]) !== false) {
            
            \Operation::delete('ticket','修改卡券！',$ticket);

            return $this->response(200, '删除成功', route('business.ticket-list'));
        } else {
            return $this->response(500, '删除失败');
        }

    }

    /**
     * 上下架卡券
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function offline(Request $request)
    {
        $id = intval($request->get('id'));
        if (!$id) {
            return view('business.error', ['code' => 500, 'msg' => '内部错误']);
        }
        $ticket = DB::table('ticket')->where('id', $id)->first();
        if ($ticket->expire_date < time()) {
            return $this->response(403, '该卡券已过期，不能进行上下架操作');
        }
        $offline = $ticket->offline == 1 ? 0 : 1;
        if (DB::table('ticket')->where('id', $ticket->id)->update(['offline' => $offline]) !== false) {
            return $this->response(200, '操作成功', route('business.ticket-list'));
        } else {
            return $this->response(500, '操作失败');
        }
    }

    /**
     * 根据门店ID筛选可用的卡券
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function filter(Request $request)
    {
        if ($request->has('store_ids')) {
            if (!is_array($request->get('store_ids'))) {
                $sids[] = $request->get('store_ids');
            } elseif (is_array($request->get('store_ids'))) {
                $sids = $request->get('store_ids');
            }
            if ($this->storeIds) {
                $sids = array_intersect($this->storeIds, $sids);
                $tickets = DB::table('ticket as t')->where('t.is_delete', 0)->where('t.admin', 0)
                    ->where('t.userid', $this->parentUserId)
                    ->join('ticket_extend as te', function ($join) use ($sids) {
                        $join->on('te.ticket_id', '=', 't.id')->whereIn('te.store_id', $sids);
                    })
                    ->where('t.expire_date', '>', time())->select('t.id', 't.name', 't.circulation as stock')
                    ->groupBy('t.id')->get();
                return response()->json($tickets);
            }
        }
    }

    /**
     * 卡券核销
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function verify(Request $request)
    {
        if ($request->isMethod('get')) {
            if (!$this->storeIds) {
                return view('business.error', ['code' => 500, 'msg' => '您没有被授权任何门店，不能进行此操作']);
            }
            return view('business.ticket-verify');

        } elseif ($request->isMethod('post')) {
            if ($request->has('type') && $request->get('type') == 'getInfo') {
                if (!$this->storeIds) {
                    return $this->response(500, '您没有被授权任何门店，不能进行此操作');
                }
                $code = $request->get('convert_code');
                if (!empty($code)) {
                    $convertCode = DB::table('convert_code')->where('code', $code)->where('type', 47)->first();
                    if (!$convertCode) {
                        return $this->response(404, '该兑换码不存在');
                    }
                    $record = DB::table('ticket_get_record as tgr')
                        ->where('tgr.id', $convertCode->data)
//                        ->whereIn('tgr.store_id', $this->storeIds)
                        ->leftJoin('ticket as t', function ($query) {
                            $query->on('t.id', '=', 'tgr.ticket_id')->where('t.type', '=', 3);
                        })
                        ->select('tgr.*', 't.name as ticket_name', 't.expire_date')
                        ->first();

                    if (!$record) {
                        return $this->response(404, '该兑换码没有匹配到体验券的领取记录');
                    }
                    if ($record->use_status = 1 && $record->convert_time > 0) {
                        return $this->response(403, '该兑换码对应体验券已经核销，不能重复操作');
                    }
                    $data = [
                        'convert_code' => $code,
                        'expire_date' => date('Y-m-d H:i:s', $record->expire_date),
                        'timestamp' => $record->expire_date,
                        'ticket_name' => $record->ticket_name
                    ];
                    return $this->response(200, '数据获取成功', '', $data);
                }
            } else {
                if (!$this->storeIds) {
                    return $this->response(500, '您没有被授权任何门店，不能进行此操作');
                }
                $code = $request->get('convert_code');
                $convertCode = DB::table('convert_code')->where('code', $code)->where('type', 47)->first();
                if (!$convertCode) {
                    return $this->response(404, '该兑换码不存在');
                }
                $record = DB::table('ticket_get_record as tgr')
                    ->where('tgr.id', $convertCode->data)
//                    ->whereIn('tgr.store_id', $this->storeIds)
                    ->leftJoin('ticket as t', 't.id', '=', 'tgr.ticket_id')
                    ->select('tgr.*', 't.expire_date')
                    ->first();
                if (!$record) {
                    return $this->response(404, '该兑换码没有匹配到体验券的领取记录');
                }
                if ($record->expire_date < time()) {
                    return $this->response(403, '该体验券已过期，不能核销');
                }
                if ($record->use_status = 1 && $record->convert_time > 0) {
                    return $this->response(403, '该兑换码对应体验券已经核销，不能重复操作');
                }

                $save = [
                    'use_status' => 1,
                    'convert_time' => time()
                ];
                $operateLog = [
                    'bus_userid' => session()->get('id'),
                    'target_type' => 2,
                    'target_id' => $record->id,
                    'description' => 'ID为 ' . session()->get('id') . ' 的商户进行了体验券兑换操作',
                    'addtime' => time()
                ];
                DB::beginTransaction();
                try {
                    DB::table('ticket_get_record')->where('id', $record->id)->update($save);
                    DB::table('bus_user_operation_log')->insert($operateLog);
                    DB::commit();
                    return $this->response(200, '核销成功');
                } catch (Exception $e) {
                    DB::rollBack();
                    return $this->response(500, '内部错误，卡券核销失败');
                }
            }
        }
    }

    /**
     * 卡券领取详情
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function receiveDetail(Request $request)
    {
        if (!intval($request->get('id'))) {
            return view('business.error', ['code' => 500, 'msg' => '内部错误']);
        }

        $data = $request->only('status', 'keyword');

        if (!$this->storeIds) {
            return view('business.error', ['code' => 403, 'msg' => '您未被授权任何门店，不能查看该卡券的领取详情']);
        }

        $ticket = DB::table('ticket as t')
            ->where('t.id', $request->get('id'))
            ->where('t.is_delete', 0)
            ->where('t.admin', 0)
            ->join('ticket_extend as te', function ($join) {
                $join->on('te.ticket_id', '=', 't.id')
                    ->whereIn('te.store_id', $this->storeIds);
            })
            ->select('t.*')->first();

        if (!$ticket) {
            return view('business.error', ['code' => 404, 'msg' => '该卡券不存在']);
        }

        $fields = [
            'u.mobile as username', 'tgr.addtime as get_time', 'tgr.use_status',
            'o.addtime as use_time', 'bs.name as store_name', 'p.name as package_name',
        ];

        $builder = DB::table('ticket_get_record as tgr')
            ->where('tgr.ticket_id', $ticket->id)
            ->leftJoin(config('tables.base') . '.users as u', 'u.id', '=', 'tgr.userid');

        // 使用状态筛选
        if (!empty($data['status'])) {
            if ($data['status'] == 1 || $data['status'] == 2) {
                $s = $data['status'] == 1 ? 0 : 1;
                $builder->where('tgr.use_status', $s);
            }
        }
        // 使用门店关键字筛选
        if (!empty($data['keyword'])) {
            $sids = DB::table('bus_stores')
                ->whereIn('id', $this->storeIds)
                ->where('name', 'like', '%' . $data['keyword'] . '%')
                ->lists('id');
        }

        if (!empty($sids)) {
            $builder->join('order as o', function ($join) use ($sids) {
                $join->on('o.cash_ticket', '=', 'tgr.id')
                    ->whereIn('o.store_id', $sids)
                    ->where('tgr.use_status', '=', 1);
            });
        } else {
            $builder->leftJoin('order as o', function ($join) {
                $join->on('o.cash_ticket', '=', 'tgr.id')
                    ->where('tgr.use_status', '=', 1);
            });
        }

        $builder->leftJoin('bus_stores as bs', 'bs.id', '=', 'o.store_id')
            ->leftJoin('packages as p', function ($join) {
                $join->on('p.id', '=', 'o.good_id')
                    ->where('o.type', '=', 2);
            })
            ->groupBy('tgr.id')
            ->orderBy('tgr.addtime', 'desc')
            ->select($fields);

        $list = $builder->paginate(20);

        return view('business.ticket-receive-detail', [
            'ticket' => $ticket,
            'users' => $list,
            'status' => !empty($data['status']) ? $data['status'] : 0,
            'keyword' => !empty($data['keyword']) ? $data['keyword'] : '',
        ]);

    }

    /**
     * 数据分析--优惠券列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function analysis(Request $request)
    {
        $data = $request->only('start_date', 'end_date');
        if ($data['start_date'] || $data['end_date']) {
            $sd = strtotime($data['start_date']);
            $ed = strtotime($data['end_date']);
        }

        if ($this->storeIds) {
            $fields = [
                't.id', 't.name',
                DB::raw('COUNT(tgr.id) as got_count'),
                DB::raw('COUNT(o.discount_ticket) as used_count'),
                DB::raw("CONCAT( ROUND(COUNT(o.discount_ticket)/COUNT(tgr.id), 2), '%') AS percent"),
            ];
            $limit = 20;

            $builder = DB::table('ticket as t')
                ->where('t.type', 2)
                ->where('t.is_delete', 0)
                ->where('t.userid', $this->parentUserId)
                ->join('ticket_extend as te', function ($join) {
                    $join->on('te.ticket_id', '=', 't.id')->whereIn('te.store_id', $this->storeIds);
                })
                ->leftJoin('ticket_get_record as tgr', 'tgr.ticket_id', '=', 't.id')
                ->leftJoin('order as o', 'o.discount_ticket', '=', 't.id')
                ->groupBy('t.id');

            if (!empty($sd) && !empty($ed)) {
                $builder->whereBetween('t.addtime', [$sd, $ed]);
            }

            $list = $builder->select($fields)->paginate($limit);
            $percent = $this->getPercent($sd = '', $ed = '');

            return view('business.ticket-analysis', [
                'tickets' => $list,
                'percent' => $percent,
                'start_date' => !empty($data['start_date']) ? $data['start_date'] : '',
                'end_date' => !empty($data['end_date']) ? $data['end_date'] : '',
            ]);
        } else {
            return view('business.ticket-analysis', [
                'percent' => ['newCount' => 0, 'oldCount' => 0, 'total' => 1],
                'start_date' => !empty($data['start_date']) ? $data['start_date'] : '',
                'end_date' => !empty($data['end_date']) ? $data['end_date'] : '',
            ]);
        }

    }

    /**
     * 获取优惠券领取用户新老用户比例
     * @param string $sd 卡券创建时间
     * @param string $ed 卡券创建时间
     * @return array
     */
    public function getPercent($sd = '', $ed = '')
    {
        $newSql = '
            SELECT
                userid
            FROM
                (SELECT
                    userid,
                    COUNT(id) AS visit_count
                FROM
                    store_visit_log
                WHERE userid > 0
                GROUP BY userid) AS tmp
            WHERE visit_count = 1
        ';

        $oldSql = '
            SELECT
                userid
            FROM
                (SELECT
                    userid,
                    COUNT(id) AS visit_count
                FROM
                    store_visit_log
                WHERE userid > 0
                GROUP BY userid) AS tmp
            WHERE visit_count > 1
        ';

        $nuids = [];
        $ouids = [];
        // 新用户
        $newUsers = DB::select($newSql);
        if ($newUsers) {
            foreach ($newUsers as $new) {
                $nuids[] = $new->userid;
            }
        }
        // 老用户
        $oldUsers = DB::select($oldSql);
        if ($oldUsers) {
            foreach ($oldUsers as $old) {
                $ouids[] = $old->userid;
            }
        }

        // 新用户领取卡券数
        $newBuilder = DB::table('ticket_get_record as tgr')
            ->join('ticket_extend as te', 'te.ticket_id', '=', 'tgr.ticket_id')
            ->whereIn('tgr.userid', $nuids);

        // 老用户领取卡券数
        $oldBuilder = DB::table('ticket_get_record as tgr')
            ->join('ticket_extend as te', 'te.ticket_id', '=', 'tgr.ticket_id')
            ->whereIn('tgr.userid', $ouids);

        if ($sd && !$ed) {
            $newBuilder
                ->join('ticket as t', function ($join) use ($sd) {
                    $join->on('t.id', '=', 'te.ticket_id')
                        ->where('t.userid', '=', $this->parentUserId)
                        ->where('t.type', '=', 2)
                        ->where('t.addtime', '>=', $sd)
                        ->where('t.is_delete', '=', 0);
                });
            $oldBuilder
                ->join('ticket as t', function ($join) use ($sd) {
                    $join->on('t.id', '=', 'te.ticket_id')
                        ->where('t.userid', '=', $this->parentUserId)
                        ->where('t.type', '=', 2)
                        ->where('t.addtime', '>=', $sd)
                        ->where('t.is_delete', '=', 0);
                });
        } elseif (!$sd && $ed) {
            $newBuilder
                ->join('ticket as t', function ($join) use ($ed) {
                    $join->on('t.id', '=', 'te.ticket_id')
                        ->where('t.userid', '=', $this->parentUserId)
                        ->where('t.type', '=', 2)
                        ->where('t.addtime', '<=', $ed)
                        ->where('t.is_delete', '=', 0);
                });
            $oldBuilder
                ->join('ticket as t', function ($join) use ($ed) {
                    $join->on('t.id', '=', 'te.ticket_id')
                        ->where('t.userid', '=', $this->parentUserId)
                        ->where('t.type', '=', 2)
                        ->where('t.addtime', '<=', $ed)
                        ->where('t.is_delete', '=', 0);
                });
        } elseif ($sd && $ed) {
            $newBuilder
                ->join('ticket as t', function ($join) use ($sd, $ed) {
                    $join->on('t.id', '=', 'te.ticket_id')
                        ->where('t.userid', '=', $this->parentUserId)
                        ->where('t.type', '=', 2)
                        ->whereBetween('t.addtime', [$sd, $ed])
                        ->where('t.is_delete', '=', 0);
                });
            $oldBuilder
                ->join('ticket as t', function ($join) use ($sd, $ed) {
                    $join->on('t.id', '=', 'te.ticket_id')
                        ->where('t.userid', '=', $this->parentUserId)
                        ->where('t.type', '=', 2)
                        ->whereBetween('t.addtime', [$sd, $ed])
                        ->where('t.is_delete', '=', 0);
                });
        } elseif (!$sd && !$ed) {
            $newBuilder
                ->join('ticket as t', function ($join) {
                    $join->on('t.id', '=', 'te.ticket_id')
                        ->where('t.userid', '=', $this->parentUserId)
                        ->where('t.type', '=', 2)
                        ->where('t.is_delete', '=', 0);
                });
            $oldBuilder
                ->join('ticket as t', function ($join) {
                    $join->on('t.id', '=', 'te.ticket_id')
                        ->where('t.userid', '=', $this->parentUserId)
                        ->where('t.type', '=', 2)
                        ->where('t.is_delete', '=', 0);
                });
        }

        $newGetters = $newBuilder->distinct()->count('tgr.id');
        $oldGetters = $oldBuilder->distinct()->count('tgr.id');

        return [
            'newCount' => $newGetters,
            'oldCount' => $oldGetters,
            'total' => ($newGetters + $oldGetters) > 0 ? ($newGetters + $oldGetters) : 1
        ];
    }

}
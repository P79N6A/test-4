<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Admin\MemberModel;
use App\Helper;
use App\Services\WechatService;

/**
 * 用户管理
 */
class MemberController extends Controller
{
    private $validate = [
        'mobile' => 'required|mobile',
        'nickname' => 'required|between:1,80',
    ];
    private $messages = [
        'required' => ':attribute 不能为空',
        'between'  => ':attribute 长度必须在 :min 和 :max 之间',
    ];
    private $attributes = [
        'nickname' => '昵称',
        'mobile' => '手机号'
    ];

    /**
     * 用户列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = MemberModel::where('status', 1);

        $storeId = $request->input('store_id');

        if($storeId){
            $query->where('store_id', $storeId);
        }

        if($request->has('role')){
            $role = $request->get('role');
            $query->where('role',$role);
        }

        if($request->has('vip')){
            $vip = $request->get('vip');
            $query->where('is_vip',$vip);
        }

        if ($request->has('keyword')) {
            $keyword = $request->get('keyword');
            $query->where('nickname', 'like', '%' . $keyword . '%')->orWhere('mobile', 'like', '%' . $keyword . '%');
        }

        $members = $query->orderBy('id','DESC')->paginate(20);

        foreach($members as $key => $member){
            $members[$key]['inviter'] = MemberModel::where('id', $member['invite_parent_id'])->value('nickname');
        }

        return view('admin.member.list', [
            'members' => $members,
            'keyword' => isset($keyword) ? $keyword : '',
            'role' => isset($role) ? $role : '',
            'vip' => isset($vip) ? $vip : '',
        ]);
    }

    /**
     * 修改用户信息
     */
    public function edit(Request $request)
    {
         $id = intval($request->input('id'));
         if(empty($id)) return $this->response(500,'ID非法！');
         $member = MemberModel::find($id);
        if($request->isMethod('POST')){
            try{
                $this->validate($request,$this->validate,$this->messages,$this->attributes);
            }catch(\Exception $e){
                return $this->response(502,$e->validator->errors()->first());
            }

            $num=MemberModel::where('id','=',$id)->update(['nickname'=>$request->input('nickname')]);

            if($num>0){
                return $this->response(200,'修改成功！',route('admin.member-list'));
            }else{
                return $this->response(422,'处理失败，请重试！');
            }
        }

        return view('admin.member.edit',[
            'info'=>$member
        ]);
    }

    /**
     * 软删除用户信息
     */
    public function delete(Request $request)
    {
        $memberId = $request->get('id');
        if (!intval($memberId)) {
            return $this->response('500', '内部错误');
        }

        $member = MemberModel::find($memberId);
        if (!$member) {
            return $this->response(404, '该用户不存在');
        }

        if (MemberModel::where('id', $memberId)->update(['status' => '-1', 'delete_at' => date('Y-m-d H:i:s')])) {
            return $this->response(200, '用户删除成功', route('admin.member-list'));
        } else {
            return $this->response(500, '用户删除失败');
        }
    }

    /**
     * 设置为医生
     */
    public function setDoctor(Request $request)
    {
        $memberId = $request->get('id');
        if (!intval($memberId)) {
            return $this->response('500', '内部错误');
        }

        $member = MemberModel::find($memberId);
        if (!$member) {
            return $this->response(404, '该用户不存在');
        }

        if($member['invite_code'] && $member['role'] == 2){
            return $this->response(404, '该用户已经是医生');
        }
        
        $inviteCode = $this->getInviteCode();
        $inviteCodePath = WechatService::getWxaCodeUnlimit('invitecode=' . $inviteCode, env('INVITE_PAGE'), 200, 1);

        if (MemberModel::where('id', $memberId)->update(['role' => '2', 'invite_code' => $inviteCode, 'invite_code_path' => $inviteCodePath])) {
            return $this->response(200, '设置医生成功', route('admin.member-list'));
        } else {
            return $this->response(500, '设置医生失败');
        }
    }

    /**
     * 解除绑定医生
     */
    public function unsetDoctor(Request $request)
    {
        $memberId = $request->get('id');
        if (!intval($memberId)) {
            return $this->response('500', '内部错误');
        }

        $member = MemberModel::find($memberId);
        if (!$member) {
            return $this->response(404, '该用户不存在');
        }

        if($member['role'] != 2){
            return $this->response(404, '该用户不是医生');
        }
        
        if (MemberModel::where('id', $memberId)->update(['role' => '1', 'invite_code' => '', 'invite_code_path' => ''])) {
            return $this->response(200, '解绑医生成功');
        } else {
            return $this->response(500, '解绑医生失败');
        }
    }

    /**
     * 生成唯一邀请码
     */
    private function getInviteCode()
    {
        $inviteCode = Helper::makeUniqueNum(6);

        if(MemberModel::where('invite_code', $inviteCode)->first()){
            return $this->getInviteCode();
        } else {
            return $inviteCode;
        }
    }

    /**
     * 返回json格式列表
     */
    public function json(Request $request){
        $keyword = $request->get('keyword');
        $list = MemberModel::where('nickname', 'like', '%' . $keyword . '%')->orWhere('mobile', 'like', '%' . $keyword . '%')->get();

        return json_encode($list);
    }
}
<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\MemberModel;
use App\Models\StoreModel;
use App\Models\CityModel;
use App\Helper;

class StoreController extends Controller{

    private $validate = [
        'name' => 'required|between:2,30',
        'tel' => 'required',
        'brand_id' => 'required|integer|min:1',
        'address' => 'required|between:3,200',
        'imgs' => 'required|array',
        // 'teachers' => 'required|array',
        'city_id' => 'required|numeric',
        'longitude' => 'required|numeric',
        'latitude' => 'required|numeric'
    ];
    private $messages = [

    ];
    private $attributes = [
        'name' => '门店名',
        'tel' => '门店电话',
        'brand_id' => '品牌',
        'address' => '门店地址',
        'imgs' => '门店图片',
        // 'teachers' => '门店老师',
        'city_id' => '城市',
        'longitude' => '经度',
        'latitude' => '纬度'
    ];

    /**
     * 列表
     */
    public function lists(Request $request){
        $list = StoreModel::with(['brand','brand.belongs','city']);
        

        if($request->has('keyword')){
            $keyword = $request->input('keyword');
            $list->where('name','like', '%' . $keyword . '%')->orWhere('tel', 'like', '%' . $keyword . '%')->orWhere('address', 'like', '%' . $keyword . '%');
        }

        if($request->has('city')){
            $city = $request->input('city');
            $list->where('city_id',$city);
        }

        $store_list = $list->paginate(20);
        $city_list = CityModel::select('id','name')->orderBy('is_hot')->get();
        return view('admin.store.list',[
            'list' => $store_list,
            'city_list'=>$city_list,
            'keyword' => isset($keyword) ? $keyword : '',
            'city' => isset($city) ? $city : '',
        ]);
    }

    /**
     * 添加
     */
    public function add(Request $request){
        if($request->isMethod('post')){
            try{
                $this->validate($request,$this->validate,$this->messages,$this->attributes);
            }catch(\Exception $e){
                return $this->response(502,$e->validator->errors()->first());
            }

            // 处理imgs和teachers数据
            $imgs = array_unique($request->input('imgs'));
            // $teachers = array_unique($request->input('teachers'));
            $imgs = array_filter($imgs,function($v){
                return Helper::isId($v);
            });
            // $teachers = array_filter($teachers,function($v){
            //     return Helper::isId($v);
            // });

            //保存数据
            $store = new StoreModel();
            $store->name = $request->input('name');
            $store->tel = $request->input('tel');
            $store->brand_id = $request->input('brand_id');
            $store->address = $request->input('address');
            $store->img = $imgs[0];
            $store->imgs = implode(',',$imgs);
            $store->longitude = $request->input('longitude');
            $store->latitude = $request->input('latitude');
            $store->city_id = $request->input('city_id');
            // $store->teachers = implode(',',$teachers);

            if($store->save()){
                return $this->response(200,'添加成功！',route('admin.business.store.list'));
            }else{
                return $this->response(422,'处理失败，请重试！');
            }
        }

        return view('admin.store.add',[
            'key'=>config('lbsmap.webkey')
        ]);
    }

    /**
     * 修改（保留拓展）
     */
    public function modify(Request $request){
        $id = intval($request->input('id'));
        if(empty($id)) return $this->response(500,'ID非法！');
        $store = StoreModel::find($id);
        if(empty($store)) return redirect(route('admin.business.store.list'));

        if($request->isMethod('POST')){
            try{
                $this->validate($request,$this->validate,$this->messages,$this->attributes);
            }catch(\Exception $e){
                return $this->response(502,$e->validator->errors()->first());
            }

            // 处理imgs和teachers数据
            $imgs = array_unique($request->input('imgs'));
            // $teachers = array_unique($request->input('teachers'));
            $imgs = array_filter($imgs,function($v){
                return Helper::isId($v);
            });
            // $teachers = array_filter($teachers,function($v){
            //     return Helper::isId($v);
            // });

            //保存数据
            $store->name = $request->input('name');
            $store->tel = $request->input('tel');
            $store->brand_id = $request->input('brand_id');
            $store->address = $request->input('address');
            $store->img = $imgs[0];
            $store->imgs = implode(',',$imgs);
            $store->longitude = $request->input('longitude');
            $store->latitude = $request->input('latitude');
            $store->city_id = $request->input('city_id');
            // $store->teachers = implode(',',$teachers);

            if($store->save()){
                return $this->response(200,'修改成功！',route('admin.business.store.list'));
            }else{
                return $this->response(422,'处理失败，请重试！');
            }
        }

        //获取门店图片
        $imgs = \App\Models\AttachmentModel::whereIn('id',explode(',',$store->imgs))->get();

        return view('admin.store.modify',[
            'info' => $store,
            'imgs' => $imgs,
            'key'=>config('lbsmap.webkey')
        ]);
    }

    /**
     * 删除（保留拓展）
     */
    public function delete(Request $request){
        $id = intval($request->input('id'));
        if(empty($id)) return $this->response(500,'ID非法！');

        if(StoreModel::destroy($id)){
            return $this->response(200,'删除成功！',route('admin.ads.list'));
        }else{
            return $this->response(422,'删除失败');
        }
    }

    /**
     * 返回json格式列表
     */
    public function json(Request $request){
        $list = StoreModel::where('city_id', $request->input('city_id', ''))->get();

        return json_encode($list);
    }

    /**
     * 用户绑定门店
     *
     * @param Request $request
     * @return void
     */
    public function bindStaff(Request $request)
    {
        if($request->isMethod('post')){
            $storeId = $request->input('store_id');
            $userId = $request->input('user_id');

            if(empty($storeId) || ! Helper::isId($storeId)){
                return $this->response(500,'门店信息错误');
            }

            if(empty($userId) || ! Helper::isId($userId)){
                return $this->response(500,'新绑定店员信息错误');
            }

            if(MemberModel::where('id', $userId)->value('store_id')) return $this->response(500,'该用户已有绑定的门店');

            //保存数据
            if(MemberModel::where('id', $userId)->update(['role' => 3, 'store_id' => $storeId])){
                return $this->response(200,'绑定成功！',route('admin.business.store.list'));
            }else{
                return $this->response(422,'绑定失败，请重试！');
            }
        }

        $store = StoreModel::find($request->input('id'));

        return view('admin.store.bind-staff', ['store' => $store]);
    }

    /**
     * 解绑店员
     */
    public function unbindStaff(Request $request)
    {
        $memberId = $request->get('id');
        if (!intval($memberId)) {
            return $this->response('500', '内部错误');
        }

        $member = MemberModel::find($memberId);
        if (!$member) {
            return $this->response(404, '该用户不存在');
        }

        if($member['role'] != 3){
            return $this->response(404, '该用户不是店员');
        }
        
        if (MemberModel::where('id', $memberId)->update(['role' => '1', 'store_id' => ''])) {
            return $this->response(200, '解绑店员成功');
        } else {
            return $this->response(500, '解绑店员失败');
        }
    }
}

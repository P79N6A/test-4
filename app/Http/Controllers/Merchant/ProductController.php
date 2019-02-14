<?php

namespace App\Http\Controllers\Merchant;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{

    // 添加/修改产品时的允许字段
    protected $fields = [
        'id','store_id','product_type_id','name','coin_qty','image',
        'gallery_photos','introduction','guide','remarks'
    ];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 产品列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(){
        $list = DB::table('iot_product as ip')->whereIn('ip.store_id',$this->storeIds)->where('ip.del_flag',0)
            ->join('iot_product_type as ipt','ipt.id','=','ip.product_type_id')
            ->leftJoin('bus_stores as bs','bs.id','=','ip.store_id')
            ->leftJoin('bus_users as bu','bu.id','=','ip.create_by')
            ->leftJoin('attachment as at','at.id','=','ip.image')
            ->select('ip.*','bs.name as store_name','ipt.name as product_type_name','bu.name as create_user','at.path')
            ->orderBy('ip.id','desc')->paginate(20);

        return view('business.product-list',['products'=>$list]);
    }


    /**
     * 添加产品
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function add(Request $request){
        if($request->isMethod('get')){
            $types = DB::table('iot_product_type')->get();
            return view('business.add-product',[
                'stores'=>$this->stores,
                'types'=>$types,
            ]);
        }elseif($request->isMethod('post')){
            $data = $request->only($this->fields);

            if(!$data['store_id'] || !intval($data['store_id'])){
                return $this->response(403,'请选择门店');
            }
            if(!$data['product_type_id'] || !intval($data['product_type_id'])){
                return $this->response(403,'请选择类别');
            }
            if(!$data['name'] || !preg_replace('/\s/','',$data['name'])){
                return $this->response(403,'请填写名称');
            }
            if(!$data['coin_qty'] || !intval($data['coin_qty'])){
                return $this->response(403,'请输入每局币数');
            }
            if(!$data['image'] || !intval($data['image'])){
                return $this->response(403,'请上传产品图片');
            }
            if(!$data['gallery_photos']){
                return $this->response(403,'请上传产品相册');
            }
            if(!$data['introduction'] || !preg_replace('/\s/','',$data['introduction'])){
                return $this->response(403,'请输入游戏介绍');
            }
            if(!$data['guide'] || !preg_replace('/\s/','',$data['guide'])){
                return $this->response(403,'请输入玩法攻略');
            }

            $data['gallery'] = implode(',',$data['gallery_photos']);
            unset($data['gallery_photos']);
            $data['create_date'] = date('Y-m-d H:i:s');
            $data['create_by'] = session('id');

            if(DB::table('iot_product')->insert($data)){

                \Operation::insert('iot_product','添加产品['.$data['name'].']！',$data);

                return $this->response(200,'添加成功',route('business.product-list'));
            }else{
                return $this->response(500,'添加失败');
            }


        }
    }

    /**
     * 删除产品
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request){
        if(!$id = intval($request->get('id'))){
            return $this->response(500,'内部错误');
        }

        $product = DB::table('iot_product')->where('id',$id)->whereIn('store_id',$this->storeIds)->first();
        if(!$product){
            return $this->response(404,'该产品不存在');
        }
        $machine = DB::table('iot_machine')->where('product_id',$product->id)->where('del_flag',0)->first();
        if($machine){
            return $this->response(403,'某一个几台已选择该产品，不能删除');
        }

        if(DB::table('iot_product')->where('id',$id)->update(['del_flag'=>1]) !== false){

            \Operation::delete('iot_product','删除产品['.$product->name.']！',$product);

            return $this->response(200,'删除成功',route('business.product-list'));
        }else{
            return $this->response(500,'删除失败');
        }
    }

    /**
     * 修改产品信息
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function edit(Request $request){
        if($request->isMethod('get')){
            if(!$id = intval($request->get('id'))){
                return $this->response(500,'内部错误');
            }
            $product = DB::table('iot_product as ip')->where('ip.id',$id)
                ->leftJoin('attachment as at','at.id','=','ip.image')
                ->whereIn('ip.store_id',$this->storeIds)->select('ip.*','at.path')->first();
            if(!$product){
                return $this->response(404,'该产品不存在');
            }
            $gallery = DB::table('attachment')->whereIn('id',explode(',',$product->gallery))->select('id','path')->get();
            $product->galleryImages = $gallery;

            $types = DB::table('iot_product_type')->get();

            return view('business.edit-product',['product'=>$product,'stores'=>$this->stores,'types'=>$types]);

        }elseif($request->isMethod('post')){
            $data = $request->only($this->fields);

            if(!intval($data['id'])){
                return $this->response(500,'内部错误');
            }
            if(!$data['store_id'] || !intval($data['store_id'])){
                return $this->response(403,'请选择门店');
            }
            if(!$data['product_type_id'] || !intval($data['product_type_id'])){
                return $this->response(403,'请选择类别');
            }
            if(!$data['name'] || !preg_replace('/\s/','',$data['name'])){
                return $this->response(403,'请填写名称');
            }
            if(!$data['coin_qty'] || !intval($data['coin_qty'])){
                return $this->response(403,'请输入每局币数');
            }
            if(!$data['image'] || !intval($data['image'])){
                return $this->response(403,'请上传产品图片');
            }
            if(!$data['gallery_photos'] || !is_array($data['gallery_photos'])){
                return $this->response(403,'请上传产品相册');
            }
            if(!$data['introduction'] || !preg_replace('/\s/','',$data['introduction'])){
                return $this->response(403,'请输入游戏介绍');
            }
            if(!$data['guide'] || !preg_replace('/\s/','',$data['guide'])){
                return $this->response(403,'请输入玩法攻略');
            }

            $product = DB::table('iot_product as ip')->where('id',$data['id'])
                ->whereIn('store_id',$this->storeIds)->first();
            if(!$product){
                return $this->response(404,'该产品不存在');
            }

            $before_data = $product;

            // 删除旧相册附件
            $delIds = array_diff(explode(',',$product->gallery),$data['gallery_photos']);
            if($delIds){
                $attachments = DB::table('attachment')->whereIn('id',$delIds)->select('id','path')->get();
                $dir = config('upload.root_path');
                foreach($attachments as $attachment){
                    @unlink($dir.'/'.$attachment->path);
                }
                DB::table('attachment')->whereIn('id',$delIds)->delete();
            }

            $data['gallery'] = implode(',',$data['gallery_photos']);
            $id = $data['id'];
            unset($data['id'],$data['gallery_photos']);

            if(DB::table('iot_product')->where('id',$id)->update($data) !== false){

                \Operation::update('iot_product','修改产品['.$before_data->name.']！',$before_data,$data);

                return $this->response(200,'修改成功',route('business.product-list'));
            }else{
                return $this->response(500,'修改失败');
            }
        }

    }




}

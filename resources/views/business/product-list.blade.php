@extends('business.layouts.frame-parent')
@section('page-title','产品列表')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>产品列表</h5>
                    <div class="ibox-tools">
                        <a href="{{ route('business.add-product') }}" class="btn btn-primary btn-xs">添加产品</a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>名称</th>
                                    <th>图片</th>
                                    <th>门店</th>
                                    <th>类别</th>
                                    <th>每局币数</th>
                                    <th width="150">游戏介绍</th>
                                    <th width="200">玩法攻略</th>
                                    <th>创建时间</th>
                                    <th>创建用户</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($products as $product)
                                <tr>
                                    <td> {{ $product->name }}</td>
                                    <td><img width="100" height="100" src="{{ config('static.base_url').'/'.$product->path }}" alt="图片"></td>
                                    <td> {{ $product->store_name }}</td>
                                    <td> {{ $product->product_type_name }} </td>
                                    <td> {{ $product->coin_qty }} </td>
                                    <td> {{ $product->introduction }}</td>
                                    <td> {{ $product->guide }}</td>
                                    <td> {{ $product->create_date }}</td>
                                    <td> {{ $product->create_user }}</td>
                                    <td>
                                        <a href="{{ route('business.edit-product',['id'=>$product->id]) }}" class="btn btn-white btn-sm">
                                            <i class="fa fa-pencil"></i> 修改
                                        </a>
                                        <a href="#" data-url="{{ route('business.del-product') }}" data-type="id" data-id="{{ $product->id }}" class="btn btn-white btn-sm btn-del"><i class="fa fa-trash"></i> 删除 </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-right">
                        @if( !empty($products->links()) )
                            {{ $products->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function(){
            $('.btn-del').click(function(){
                var $this = $(this);
                layer.msg('您确定要删除该产品吗？',{
                    time:0,
                    btn:['是','否'],
                    yes:function(index){
                        layer.close(index);
                        youyibao.httpSend($this,'get',1);
                    }
                });
            });
        });
    </script>
@endsection

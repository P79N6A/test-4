@extends('business.layouts.frame-parent')
@section('page-title','添加机台')
@section('main')
    <div class="row">
            <div class="col-sm-12">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>@yield('page-title')</h5>
                    </div>
                    <div class="ibox-content">
                        <form action="{{ route('business.add-machine') }}" method="post" class="form-add-machine">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>产品资料</label>
                                        <select class="form-control m-b" name="product_id">
                                            <option>请选择产品资料</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>设备序列号</label>
                                        <select class="form-control m-b" name="dev_id">
                                            <option>请选择设备</option>
                                            @foreach($devices as $device)
                                                <option value="{{ $device->id }}">{{ $device->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>是否可用</label>
                                        <select class="form-control m-b" name="usable">
                                            <option value="1">是</option>
                                            <option value="0">否</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>名称</label>
                                        <input type="text" name="name" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>备注</label>
                                        <input type="text" name="remarks" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <button class="btn btn-sm btn-primary btn-add-machine" type="button">添加</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <script>
        $(function(){
            $('.btn-add-machine').click(function(){
                youyibao.httpSend($('.form-add-machine'),'post',1);
            });
        });
    </script>
@endsection

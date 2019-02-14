@extends('business.layouts.frame-parent')
@section('page-title','创建蓝牙设备')
@section('main')
    <div class="row">
            <div class="col-sm-12">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>添加机台</h5>
                    </div>
                    <div class="ibox-content">
                        <form action="{{ route('business.edit-machine') }}" method="post" class="form-edit-machine">
                            {{ csrf_field() }}
                            <input type="hidden" name="id" value="{{ $machine->id }}">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>产品类型</label>
                                        <select class="form-control m-b" name="product_id">
                                            <option>请选择产品</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" @if($machine->product_id = $product->id) selected @endif >{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>设备类型</label>
                                        <select class="form-control m-b" name="dev_id">
                                            <option>请选择设备</option>
                                            @foreach($devices as $device)
                                                <option value="{{ $device->id }}" @if($machine->dev_id == $device->id) selected @endif >
                                                    {{ $device->name }} @if($machine->dev_id == $device->id) （当前设备） @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>是否可用</label>
                                        <select class="form-control m-b" name="usable">
                                            <option value="1" @if($machine->usable == 1) selected @endif >是</option>
                                            <option value="0" @if($machine->usable == 0) selected @endif >否</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>名称</label>
                                        <input type="text" name="name" value="{{ $machine->name }}" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>备注</label>
                                        <input type="text" name="remarks" value="{{ $machine->remarks }}" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <button class="btn btn-sm btn-primary btn-edit-machine" type="button">保存</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <script>
        $(function(){
            $('.btn-edit-machine').click(function(){
                youyibao.httpSend($('.form-edit-machine'),'post',1);
            });
        });
    </script>
@endsection

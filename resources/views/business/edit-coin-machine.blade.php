@extends('business.layouts.frame-parent')
@section('page-title','添加机台')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>@yield('page-title')</h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-sm-6">
                            <form action="{{ route('business.edit-coin-machine') }}" method="post">
                                {{ csrf_field() }}
                                <input type="hidden" name="id" value="{{ $machine->id }}">
                                <div class="form-group">
                                    <label>序列号</label>
                                    <span class="form-control">{{ $machine->serial_no }}</span>
                                </div>
                                <div class="form-group">
                                    <label>名称</label>
                                    <input class="form-control" name="name" value="{{ $machine->name }}" placeholder="请输入机台名称">
                                </div>
                                <div class="form-group">
                                    <label>门店</label>
                                    <select class="form-control" name="store">
                                        <option value="0">请选择门店</option>
                                        @if(!empty($stores))
                                            @foreach($stores as $store)
                                                <option value="{{ $store->id }}" @if($store->id == $machine->store_id) selected @endif>{{ $store->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>备注</label>
                                    <textarea class="form-control" name="remarks" placeholder="可选">{{ $machine->remarks }}</textarea>
                                </div>
                                <div class="hr-line-dashed"></div>
                                <button type="submit" class="btn btm-sm btn-primary">提交</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(function () {
            $('form').submit(function (e) {
                e.preventDefault();
                youyibao.httpSend($(this), 'post', 1);
            });
        });
    </script>
@endsection
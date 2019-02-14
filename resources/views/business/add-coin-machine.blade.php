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
                            <form action="{{ route('business.add-coin-machine') }}" method="post">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label>名称</label>
                                    <input class="form-control" name="name" placeholder="请输入机台名称">
                                </div>
                                <div class="form-group">
                                    <label>序列号</label>
                                    <input class="form-control" name="serial" placeholder="请输入机台序列号">
                                </div>
                                <div class="form-group">
                                    <label>门店</label>
                                    <select class="form-control" name="store">
                                        <option value="0">请选择门店</option>
                                        @if(!empty($stores))
                                            @foreach($stores as $store)
                                                <option value="{{ $store->id }}">{{ $store->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>备注</label>
                                    <textarea class="form-control" name="remarks" placeholder="可选"></textarea>
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
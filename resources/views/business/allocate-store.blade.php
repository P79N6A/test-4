@extends('business.layouts.frame-parent')
@section('page-title','分配门店')
@section('main')
    <link rel="stylesheet" href="/business/css/plugins/iCheck/custom.css">

    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>@yield('page-title')</h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-sm-10 col-md-6 col-lg-4">
                            <form action="{{ route('business.allocate-store') }}" method="post">
                                <div class="form-group">
                                    <label>账号</label>
                                    <p>{{ $user->name }}</p>
                                    {{ csrf_field() }}
                                    <input type="hidden" name="uid" value="{{ $user->id }}">
                                </div>
                                <div class="form-group">
                                    <label>门店</label>
                                    <div>
                                        @if(!empty($stores))
                                            @foreach($stores as $store)
                                                <label class="checkbox-inline i-checks">
                                                    <input type="checkbox" name="store_ids[]" value="{{ $store->id }}"
                                                           @if(in_array($store->id,$allocatedStores)) checked @endif>
                                                    {{ $store->name }}
                                                </label>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>
                                <button class="btn btn-sm btn-primary" type="submit">提交</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="/business/js/plugins/iCheck/icheck.min.js"></script>
    <script>
        $(document).ready(function () {
            $(".i-checks").iCheck({checkboxClass: "icheckbox_square-green", radioClass: "iradio_square-green"});
            $('form').submit(function (e) {
                e.preventDefault();
                youyibao.httpSend($(this), 'post', 1);
            });
        });
    </script>
@endsection
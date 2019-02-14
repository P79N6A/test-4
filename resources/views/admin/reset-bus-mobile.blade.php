@extends('admin.layouts.parent')
@section('page-title','修改手机号')
@section('main')
    <div class="ibox float-e-margins">
        <div class="ibox-content">
            <div class="row">
                <div class="col-sm-6">
                    <form role="form" action="{{ route('admin.reset-bus-mobile') }}" method="post">
                        <div class="form-group">
                            <label>商户登录名</label>
                            <span class="form-control">{{ $user->name }}</span><br>
                            <input type="hidden" name="id" value="{{ $user->id }}">
                            {{ csrf_field() }}
                        </div>
                        <div class="form-group">
                            <label>手机号</label>
                            <input name="mobile" value="{{ $user->mobile }}" placeholder="手机号码" class="form-control">
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div>
                            <button class="btn btn-sm btn-primary" type="submit"><strong>确认</strong>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        $('form').submit(function (e) {
            e.preventDefault();
            youyibao.httpSend($(this), 'post', 1);
        });
    </script>
@endsection
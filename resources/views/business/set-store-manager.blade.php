@extends('business.layouts.frame-parent')
@section('page-title','设置门店管理员')
@section('main')

    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>@yield('page-title')</h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <form action="{{ route('business.set-store-manager') }}" method="post">
                            <div class="col-sm-6 col-md-4 col-lg-3">
                                {{ csrf_field() }}
                                <input type="hidden" name="store_id" value="{{ $store->id }}">
                                <div class="form-group">
                                    <label>门店名称</label>
                                    <p>{{ $store->name }}</p>
                                </div>
                                <div class="form-group">
                                    <label>管理员</label>
                                    <select class="form-control" name="userid">
                                        <option value="0">请选择管理员</option>
                                        @if(!empty($users))
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" @if(!empty($manager) && $user->id == $manager->bus_userid) selected @endif>
                                                    {{ $user->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="hr-line-dashed"></div>
                                <button type="submit" class="btn btn-sm btn-primary">提交</button>
                            </div>
                        </form>
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
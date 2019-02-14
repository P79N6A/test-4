@extends('business.layouts.frame-parent')
@section('page-title','设置积分转出率')
@section('main')
    <div class="wrapper wrapper-content animated fadeInUp">
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>@yield('page-title')</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <form action="{{ route('business.set-ticket-output-rate') }}">
                                {{ csrf_field() }}
                                <input type="hidden" name="id" value="{{ $store->id }}">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>奖票转积分比例（平台积分:奖票积分 = 1:N，N 可以为小数）</label>
                                        <input id="confirm-pwd" class="form-control" name="ticket_rate"
                                               value="{{ $store->prize_ticket_out_rate }}" placeholder="正整数">
                                    </div>
                                    <div class="hr-line-dashed"></div>
                                    <button class="btn btn-sm btn-primary" type="submit">保存</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {
            $('form').submit(function (e) {
                e.preventDefault();
                youyibao.httpSend($(this), 'post', 1);
            });
        });
    </script>
@endsection

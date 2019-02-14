@extends('business.layouts.frame-parent')
@section('page-title','订单退款')
@section('main')

    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>@yield('page-title')</h5>
                </div>
            </div>
            <div class="ibox-content">
                <div class="form-group">
                    <label>套餐名称</label>
                    <p><span class="label label-primary">{{ $order->name }}</span></p><br>
                    <label>套餐价格</label>
                    <p><span class="label label-primary">￥{{ $order->price }}</span></p><br>
                    <label>退款金额</label>
                    <p>
                        金额：<span class="label label-primary">{{ $order->pay_price }}</span><br><br>
                    </p>
                </div>
                <form action="{{ route('business.order-deposit') }}" method="post">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" value="{{ $order->id }}">
                    <button type="submit" class="btn btn-sm btn-primary">确认退款</button>
                </form>
            </div>
        </div>
    </div>
    <script>
        $(function () {
            $('form').submit(function (e) {
                e.preventDefault();
                var $this = $(this);
                layer.msg('您确认要对该订单执行退款操作吗？', {
                    time: 0,
                    btn: ['是', '否'],
                    yes: function (index) {
                        layer.close(index);
                        youyibao.httpSend($this, 'post', 1);
                    }
                });
            });
        });
    </script>
@endsection
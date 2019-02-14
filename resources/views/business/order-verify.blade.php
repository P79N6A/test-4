@extends('business.layouts.frame-parent')
@section('page-title','订单核销')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>@yield('page-title')</h5>
                </div>
                <div class="ibox-content">
                    <form action="{{ route('business.order-verify') }}" method="post" id="form-verify">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>兑换码</label>
                                    <input type="text" id="convert-code" name="convert_code" class="form-control"
                                           placeholder="鼠标光标聚焦于输入框，输入兑换码或者用扫描枪扫描兑换二维码">
                                </div>
                                <p>或者</p>
                                <div class="form-group">
                                    <label>订单号</label>
                                    <input type="text" id="order-no" name="order_no" class="form-control"
                                           placeholder="输入订单号">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div id="result"></div>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <button class="btn btn-sm btn-primary btn-add-dev" type="submit">执行核销</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {

            // 检测输入框
            $('#convert-code').on('input propertychange', function (e) {
                getOrderInfoByConvertCode($(this).val());
            });
            $('#order-no').on('input propertychange', function (e) {
                getOrderInfoByOrderNo($(this).val());
            });

            // 输入框切换
            $('#convert-code').focus(function(){
                $('#order-no').val('');
            });
            $('#order-no').focus(function(){
                $('#convert-code').val('');
            });

            $('form').submit(function (e) {
                e.preventDefault();
                if($('#convert-code').val().length == 0 && $('#order-no').val().length == 0){
                    layer.msg('请输入兑换码或者订单号',{icon:5});
                    return false;
                }
                var $this = $(this);
                layer.msg('确定核销该订单吗？', {
                    time: 0,
                    btn: ['是', '否'],
                    yes: function (index) {
                        layer.close(index);
                        youyibao.httpSend($this, 'post', 1);
                    }
                });
            });

            function getOrderInfoByConvertCode(convertCode) {
                if (!convertCode) {
                    $('#result').empty();
                    return false;
                }

                $.ajax({
                    type: 'post',
                    url: $('#form-verify').attr('action'),
                    data: {
                        _token: $('input[name=_token]').val(),
                        type: 'getInfo',
                        convert_code: convertCode
                    },
                    success: function (data) {
                        if (data.code != 200) {
                            var msg = '<span class="label label-danger">' + data.msg + '</span>';
                            $('#result').empty().append(msg);
                        } else {
                            var value = data.data;
                            var str = '';
                            str += '<p>套餐名称：' + value.name + '</p>';
                            str += '<p>套餐价格：' + value.price + ' 元</p>';
                            str += '<p>购买数量：' + value.price + '</p>';
                            if ((value.expire_date * 1000) < new Date().getTime()) {
                                str += '<p><span class="label label-danger">该套餐已过期，仍然可以核销</span></p>';
                            }
                            if (value.convert_code != undefined && value.convert_code.length > 0){
                                $('input[name=convert_code]').val(value.convert_code);
                            }
                            $('#result').empty().append(str);
                        }
                    }
                });
            }

            function getOrderInfoByOrderNo(convertCode) {
                if (!convertCode) {
                    $('#result').empty();
                    return false;
                }

                $.ajax({
                    type: 'post',
                    url: $('#form-verify').attr('action'),
                    data: {
                        _token: $('input[name=_token]').val(),
                        type: 'getInfo',
                        order_no: convertCode
                    },
                    success: function (data) {
                        if (data.code != 200) {
                            var msg = '<span class="label label-danger">' + data.msg + '</span>';
                            $('#result').empty().append(msg);
                        } else {
                            var value = data.data;
                            var str = '';
                            str += '<p>套餐名称：' + value.name + '</p>';
                            str += '<p>套餐价格：' + value.currentPrice.toFixed(2) + ' 元</p>';
                            str += '<p>购买数量：' + value.qty + '</p>';
                            if ((value.expire_date * 1000) < new Date().getTime()) {
                                str += '<p><span class="label label-danger">该套餐已过期</span></p>';
                            }
                            $('#result').empty().append(str);
                        }
                    }
                });
            }

        });
    </script>
@endsection

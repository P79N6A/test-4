@extends('business.layouts.frame-parent')
@section('page-title','卡券核销')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>@yield('page-title')</h5>
                </div>
                <div class="ibox-content">
                    <form action="{{ route('business.ticket-verify') }}" method="post" id="form-verify">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>兑换码</label>
                                    <input type="text" id="convert-code" name="convert_code" class="form-control"
                                           placeholder="鼠标光标聚焦于输入框，输入兑换码或者用扫描枪扫描兑换二维码">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div id="result"></div>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <button id="submit" class="btn btn-sm btn-primary btn-add-dev" type="submit">执行核销</button>
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

            $('form').submit(function (e) {
                e.preventDefault();
                if ($('#convert-code').val().length == 0) {
                    layer.msg('请输入兑换码', {icon: 5});
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
                            str += '<p>兑换码：' + value.convert_code + '</p>';
                            str += '<p>体验券名称：' + value.ticket_name + '</p>';
                            str += '<p>过期时间：' + value.expire_date + '</p>';
                            if ((value.timestamp * 1000) < new Date().getTime()) {
                                str += '<p><span class="label label-danger">该体验券已过期，不能核销</span></p>';
                                $('#submit').attr('disabled', true);
                            } else {
                                $('#submit').attr('disabled', false);
                            }
                            if (value.convert_code != undefined && value.convert_code.length > 0) {
                                $('input[name=convert_code]').val(value.convert_code);
                            }
                            $('#result').empty().append(str);
                        }
                    }
                });
            }

        });
    </script>
@endsection

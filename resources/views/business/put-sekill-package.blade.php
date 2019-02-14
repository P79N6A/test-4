@extends('business.layouts.frame-parent')
@section('page-title','投放套餐')
@section('main')
    <link href="/business/css/plugins/iCheck/custom.css" rel="stylesheet">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>已投放的套餐</h5>
                </div>
                <div class="ibox-content">
                    <table class="table table-striped table-responsive">
                        <thead>
                        <tr>
                            <th>套餐名称</th>
                            <th>秒杀价格</th>
                            <th>秒杀限购</th>
                            <th>秒杀库存</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(!empty($putPackages))
                            @foreach($putPackages as $p)
                            <tr>
                                <td>{{ $p->name }}</td>
                                <td>￥{{ $p->price }}</td>
                                <td>{{ $p->buy_limit }}</td>
                                <td>{{ $p->stock }}</td>
                            </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="ibox">
                <div class="ibox-title">
                    <h5>您将为以下秒杀活动添加套餐：{{ $activity->title }}</h5>
                </div>
                <div class="ibox-content">
                    <form action="{{ route('business.put-sekill-package') }}" method="post">
                        <input type="hidden" name="activity_id" value="{{ $activity->id }}">
                        {{ csrf_field() }}
                        <table class="table table-responsive table-striped table-borderd">
                            <thead>
                            <tr>
                                <th>当前库存</th>
                                <th>套餐名称</th>
                                <th>套餐原价</th>
                                <th>秒杀价格</th>
                                <th>秒杀限购数</th>
                                <th>秒杀库存</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($packages))
                                @foreach($packages as $package)
                                    <tr>
                                        <td>{{ $package->stock }}</td>
                                        <td>
                                            <label class="checkbox-inline i-checks">
                                                <div class="icheckbox_square-green" style="position: relative;">
                                                    <div class="icheckbox_square-green" style="position: relative;">
                                                        <input type="checkbox" class="package_ids" name="package_ids[]"
                                                               value="{{ $package->id }}"
                                                               style="position: absolute; opacity: 0;">
                                                        <ins class="iCheck-helper"
                                                             style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;">
                                                        </ins>
                                                    </div>
                                                    <ins class="iCheck-helper"
                                                         style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; border: 0px; opacity: 0; background: rgb(255, 255, 255);"></ins>
                                                </div>
                                                {{ $package->name }}
                                            </label>
                                        </td>
                                        <th>￥ {{ $package->price }}</th>
                                        <td>
                                            <div class="form-group" style="width:110px;">
                                                <input type="text" id="price_{{ $package->id }}"
                                                       name="price_{{ $package->id }}"
                                                       class="form-control"
                                                       placeholder="不填默认为0"
                                                       maxlength="6">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group" style="width:60px;">
                                                <input type="text" id="buy_limit_{{ $package->id }}"
                                                       name="buy_limit_{{ $package->id }}"
                                                       class="form-control"
                                                       maxlength="4">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group" style="width:60px;">
                                                <input type="text" id="stock_{{ $package->id }}"
                                                       name="stock_{{ $package->id }}"
                                                       class="stocks form-control"
                                                       maxlength="4">
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                        <div class="hr-line-dashed"></div>
                        <div>
                            <button class="btn btn-sm btn-primary" type="submit">提交</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="/business/js/plugins/iCheck/icheck.min.js"></script>
    <script>
        $(function () {
            $(".i-checks").iCheck({
                checkboxClass: "icheckbox_square-green",
                radioClass: "iradio_square-green"
            }).on('ifChecked', function (event) {

            });

            var packages = [];
            var stocks = [];
            var sekillPrices = [];
            var buyLimits = [];

            $('form').submit(function (e) {
                e.preventDefault();

                var $packages = $('.package_ids:checked');
                packages.splice(0, packages.length);
                stocks.splice(0, stocks.length);
                sekillPrices.splice(0, sekillPrices.length);
                buyLimits.splice(0, buyLimits.length);

                $.each($packages, function (index, value) {
                    var stock = $('#stock_' + value.value).val() ? $('#stock_' + value.value).val() : 0;
                    var sekillPrice = $('#price_' + value.value).val() ? $('#price_' + value.value).val() : 0;
                    var buyLimit = $('#buy_limit_' + value.value).val() ? $('#buy_limit_' + value.value).val() : 0;
                    packages.push(value.value);
                    stocks.push(stock);
                    sekillPrices.push(sekillPrice);
                    buyLimits.push(buyLimit);
                });

                $.ajax({
                    type: 'post',
                    url: $(this).attr('action'),
                    data: {
                        _token: $('input[name=_token]').val(),
                        activity_id: $('input[name=activity_id]').val(),
                        packages: packages,
                        stocks: stocks,
                        prices: sekillPrices,
                        buy_limits: buyLimits
                    },
                    success: function (data) {
                        if (data.msg != undefined) {
                            layer.msg(data.msg, {
                                icon: data.code == 200 ? 6 : 5
                            });
                            if (data.url != undefined && data.url.length > 0) {
                                setTimeout(function () {
                                    location.href = data.url;
                                }, 2000);
                            }
                        }
                    }
                });
            });

        });
    </script>
@endsection
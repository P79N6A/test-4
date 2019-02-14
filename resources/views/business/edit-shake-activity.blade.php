@extends('business.layouts.frame-parent')
@section('page-title','修改摇一摇活动')
@section('main')
    <link href="/business/css/plugins/iCheck/custom.css" rel="stylesheet">
    <div class="row">
        <div class="col-sm-8">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>@yield('page-title')</h5>
                </div>
                <div class="ibox-content">
                    <form action="{{ route('business.edit-shake-activity') }}" method="post" class="form-add-shake">
                        {{ csrf_field() }}
                        <input type="hidden" name="id" value="{{ $activity->id }}">
                        <div class="row">
                            <div class="col-sm-8">
                                <div class="form-group">
                                    <label>标题</label>
                                    <input type="text" name="title" value="{{ $activity->title }}" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>描述</label>
                                    <textarea class="form-control" name="description"
                                              rows="3">{{ $activity->description }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label>有效期</label>
                                    <div>
                                        <input placeholder="开始时间" name="start_date" class="form-control layer-date"
                                               @if($activity->start_date <= time() && time() <= $activity->end_date)
                                               disabled
                                               @endif
                                               value="{{ date('Y-m-d H:i:s',$activity->start_date) }}" id="start_date">
                                        <input placeholder="结束时间" name="end_date" class="form-control layer-date"
                                               value="{{ date('Y-m-d H:i:s',$activity->end_date) }}" id="end_date">
                                    </div>
                                </div>
                                <div class="form-group discount-container">
                                    <label>中奖限制次数</label>
                                    <input type="number" name="win_limit" value="{{ $activity->win_limit }}"
                                           class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>举办门店</label>
                                    <div>
                                        @foreach($stores as $store)
                                            <label class="checkbox-inline i-checks">
                                                <div class="icheckbox_square-green" style="position: relative;">
                                                    <input type="checkbox" disabled
                                                           @if($store->id == $activity->store_id) checked @endif
                                                           style="position: absolute; opacity: 0;">
                                                    <ins class="iCheck-helper"
                                                         style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;">
                                                    </ins>
                                                </div>{{ $store->name }}
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="bs-example bs-example-bg-classes">
                                    <p class="bg-danger">注意：一个时间段内只能有一个活动进行；勾选多个门店会产生对应数量的活动</p>
                                </div>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <button class="btn btn-sm btn-primary btn-add-shake" type="submit">保存</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="/business/js/plugins/layer/laydate/laydate.js"></script>
    <script src="/business/js/plugins/iCheck/icheck.min.js"></script>
    <script src="/business/js/youyibao.js"></script>
    <script type="text/javascript">
        $(".i-checks").iCheck({

            checkboxClass: "icheckbox_square-green",
            radioClass: "iradio_square-green"
        });

        var start_date = {
            elem: "#start_date",
            format: "YYYY-MM-DD hh:mm:ss",
            min: laydate.now(),
            max: "2099-06-16 23:59:59",
            istime: true,
            istoday: false,
            choose: function (datas) {
                end_date.start = datas
            }
        };
        var end_date = {
            elem: "#end_date",
            format: "YYYY-MM-DD hh:mm:ss",
            min: laydate.now(),
            max: "2099-06-16 23:59:59",
            istime: true,
            istoday: false,
            choose: function (datas) {
                start_date.max = datas
            }
        };
        laydate(start_date);
        laydate(end_date);
    </script>

    <script>
        $(function () {

            $('input[name=type]').change(function () {
                if ($(this).val() == 1) {
                    $('.discount-container').hide();
                    $('.denominatioin-container').show();
                } else if ($(this).val() == 2) {
                    $('.denominatioin-container').hide();
                    $('.discount-container').show();
                } else if ($(this).val() == 3) {
                    $('.denominatioin-container').hide();
                    $('.discount-container').hide();
                }
            });

            // 提交表单
            $('form.form-add-shake').submit(function (e) {
                e.preventDefault();
                youyibao.httpSend($(this), 'post', 1);
            });

        });
    </script>
@endsection

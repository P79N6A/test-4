@extends('business.layouts.frame-parent')
@section('page-title','创建秒杀活动')
@section('main')
    <link href="/business/css/plugins/iCheck/custom.css" rel="stylesheet">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>@yield('page-title')</h5>
                </div>
                <div class="ibox-content">
                    <form action="{{ route('business.add-sekill-activity') }}" method="post">
                        <div class="row">
                            {{ csrf_field() }}
                            <div class="col-sm-8">
                                <div class="form-group ticket-name">
                                    <label>标题</label>
                                    <input type="text" name="title" class="form-control" placeholder="20字以内">
                                </div>
                                <div class="form-group denominatioin-container">
                                    <label>描述</label>
                                    <textarea class="form-control" name="description"></textarea>
                                </div>
                                <div class="form-group">
                                    <label>起始时间</label>
                                    <div>
                                        <input placeholder="开始时间" name="start_date" class="form-control layer-date"
                                               id="start_date">
                                        <input placeholder="结束时间" name="end_date" class="form-control layer-date"
                                               id="end_date">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>状态</label>
                                    <select class="form-control" name="status">
                                        <option value="1">正常</option>
                                        <option value="0">暂停</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>举行门店</label>
                                    <div>
                                        @if(!empty($stores))
                                            @foreach($stores as $store)
                                                <label class="checkbox-inline i-checks">
                                                    <div class="icheckbox_square-green" style="position: relative;">
                                                        <input type="checkbox" name="store_ids[]" value="{{ $store->id }}" style="position: absolute; opacity: 0;">
                                                        <ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins>
                                                    </div>
                                                    {{ $store->name }}
                                                </label>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <button class="btn btn-sm btn-primary" type="submit">保存</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
    <script src="/business/js/plugins/iCheck/icheck.min.js"></script>
    <script src="/business/js/plugins/layer/laydate/laydate.js"></script>
    <script type="text/javascript">
        $(function () {
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

            $(".i-checks").iCheck({
                checkboxClass: "icheckbox_square-green",
                radioClass: "iradio_square-green",
            });

            // 提交表单
            $('form').submit(function (e) {
                e.preventDefault();
                youyibao.httpSend($(this), 'post', 1);
            });

        });
    </script>
@endsection

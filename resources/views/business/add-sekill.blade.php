@extends('business.layouts.frame-parent')
@section('page-title','参与秒杀')
@section('main')
    <div class="row">
            <div class="col-sm-12">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>秒杀</h5>
                    </div>
                    <form action="{{ route('business.add-sekill') }}" method="post" class="form-add-sekill">
                        {{ csrf_field() }}
                        <input type="hidden" name="package_id" value="{{ $package->id }}">
                        <div class="ibox-content">
                            <blockquote>
                                <p>{{ $package->name }}</p>
                            </blockquote>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>秒杀价格</label>
                                        <input type="text" name="price" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>秒杀库存</label>
                                        <input type="text" name="stock" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>秒杀限购</label>
                                        <input type="text" name="buy_limit" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>秒杀时间</label>
                                        <div>
                                            <input placeholder="开始时间" name="start_date" class="form-control layer-date" id="start">
                                            <input placeholder="结束时间" name="end_date" class="form-control layer-date" id="end">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <button class="btn btn-sm btn-primary btn-add-sekill" type="button">保存</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="/business/js/plugins/layer/laydate/laydate.js"></script>
    <script>
    var start = {
        elem: "#start",
        format: "YYYY-MM-DD hh:mm:ss",
        min: laydate.now(),
        max: "2099-06-16 23:59:59",
        istime: true,
        istoday: false,
        choose: function(datas) {
            end.min = datas;
            end.start = datas
        }
    };
    var end = {
        elem: "#end",
        format: "YYYY-MM-DD hh:mm:ss",
        min: laydate.now(),
        max: "2099-06-16 23:59:59",
        istime: true,
        istoday: false,
        choose: function(datas) {
            start.max = datas
        }
    };
    laydate(start);
    laydate(end);

    $('.btn-add-sekill').click(function(){
        youyibao.httpSend($('.form-add-sekill'),'post',1);
    });

    </script>
@endsection

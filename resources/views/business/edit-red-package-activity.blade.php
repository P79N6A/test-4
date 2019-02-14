@extends('business.layouts.frame-parent')
@section('page-title','修改红包活动')
@section('main')
    <div class="row">
            <div class="col-sm-8">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>@yield('page-title')</h5>
                    </div>
                    <div class="ibox-content">
                        <form action="{{ route('business.edit-red-package-activity') }}" method="post" class="form-edit">
                            {{ csrf_field() }}
                            <input type="hidden" name="id" value="{{ $detail->id }}">
                            <div class="row">
                                <div class="col-sm-8">
                                    <div class="form-group">
                                        <label>标题</label>
                                        <input type="text" name="title" value="{{ $detail->title }}" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>描述</label>
                                        <textarea class="form-control" name="description" rows="3">{{ $detail->description }}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>有效期</label>
                                        <div>
                                            <input placeholder="开始时间" name="start_date" value="{{ date('Y-m-d H:i:s',$detail->start_date) }}" class="form-control layer-date" id="start_date">
                                            <input placeholder="结束时间" name="end_date" value="{{ date('Y-m-d H:i:s',$detail->end_date) }}" class="form-control layer-date" id="end_date">
                                        </div>
                                    </div>
                                    <div class="bs-example bs-example-bg-classes">
                                        <p class="bg-danger">注意：一个时间段内只能有一个活动进行</p>
                                    </div>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <button class="btn btn-sm btn-primary btn-edit" type="submit">保存</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    <script src="/business/js/plugins/layer/laydate/laydate.js"></script>
    <script src="/business/js/youyibao.js"></script>
    <script type="text/javascript">
        var start_date = {
            elem: "#start_date",
            format: "YYYY-MM-DD hh:mm:ss",
            min: laydate.now(),
            max: "2099-06-16 23:59:59",
            istime: true,
            istoday: false,
            choose: function(datas) {
                end_date.min = datas;
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
            choose: function(datas) {
                start_date.max = datas
            }
        };
        laydate(start_date);
        laydate(end_date);
    </script>

    <script>
        $(function(){

            $('input[name=type]').change(function(){
                if($(this).val() == 1){
                    $('.discount-container').hide();
                    $('.denominatioin-container').show();
                }else if($(this).val() == 2){
                    $('.denominatioin-container').hide();
                    $('.discount-container').show();
                }else if($(this).val() == 3){
                    $('.denominatioin-container').hide();
                    $('.discount-container').hide();
                }
            });

            // 提交表单
            $('form.form-edit').submit(function(e){
                e.preventDefault();
                youyibao.httpSend($(this),'post',1);
            });

        });
    </script>
@endsection

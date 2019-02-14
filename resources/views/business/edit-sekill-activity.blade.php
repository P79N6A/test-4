@extends('business.layouts.frame-parent')
@section('page-title','修改秒杀活动')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>@yield('page-title')</h5>
                </div>
                <div class="ibox-content">
                    <form action="{{ route('business.edit-sekill-activity') }}" method="post">
                        <div class="row">
                            {{ csrf_field() }}
                            <input type="hidden" name="id" value="{{ $activity->id }}">
                            <div class="col-sm-8">
                                <div class="form-group ticket-name">
                                    <label>标题</label>
                                    <input type="text" name="title" value="{{ $activity->title }}" class="form-control" placeholder="20字以内">
                                </div>
                                <div class="form-group">
                                    <label>描述</label>
                                    <textarea class="form-control" name="description">{{ $activity->description }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label>起始时间</label>
                                    <div>
                                        <input placeholder="开始时间" name="start_date" value="{{ $activity->start_date }}" class="form-control layer-date"
                                               id="start_date">
                                        <input placeholder="结束时间" name="end_date" value="{{ $activity->end_date }}" class="form-control layer-date"
                                               id="end_date">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>状态</label>
                                    <select class="form-control" name="status">
                                        <option value="1" @if($activity->status == 1) selected @endif >正常</option>
                                        <option value="0" @if($activity->status == 0) selected @endif >暂停</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>举行门店</label>
                                    <div>
                                        <span class="label label-primary">{{ $activity->store_name }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <button class="btn btn-sm btn-primary" type="submit">保存</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
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

            // 提交表单
            $('form').submit(function(e){
                e.preventDefault();
                youyibao.httpSend($(this),'post',1);
            });

        });
    </script>
@endsection

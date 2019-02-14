@extends('admin.layouts.parent')
@section('page-title','修改课时')
@section('main')
    <div class="row">
            <div class="col-sm-12">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>修改课时</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <form action="{{ route('admin.course.class.modify') }}" class="forms">
                                {{ csrf_field() }}
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>课程</label>
                                        <input type="text" value="{{$info->course->name}}" placeholder="数字或者小写字母" class="form-control" disabled>
                                        <input type="hidden" name="id" value="{{$info->id}}">
                                    </div>
                                    <div class="form-group">
                                        <label>课时名称</label>
                                        <input type="text" name="name" class="form-control" value="{{$info->name}}">
                                    </div>
                                    <div class="form-group">
                                        <label>课时可开启游戏最大次数</label>
                                        <input type="number" name="times" class="form-control" min="1" value="{{$info->times}}">
                                    </div>
                                    <div class="form-group">
                                        <label>类型</label>
                                        <div class="radio i-checks">
                                            <label>
                                                <input type="radio" checked="" value="1" name="type" @if($info->type == 1) checked @endif>
                                                <i></i> 机台
                                            </label>
                                            <!-- <label>
                                                <input type="radio" value="2" name="type" @if($info->type == 2) checked @endif>
                                                <i></i> 游乐
                                            </label> -->
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <button class="btn btn-sm btn-primary btn-options" type="submit">修改</button>
                    </div>
                </div>
            </div>
        </div>
    <script src="/admin/js/plugins/iCheck/icheck.min.js"></script>
    <link href="/admin/css/plugins/iCheck/custom.css" rel="stylesheet">
    <script type="text/javascript">
        $(document).ready(function(){
            $('.btn-options').click(function(){
                youyibao.httpSend($('form.forms'),'post',1);
            });
            $(".i-checks").iCheck({checkboxClass: "icheckbox_square-green", radioClass: "iradio_square-green",})
        });
    </script>
@endsection

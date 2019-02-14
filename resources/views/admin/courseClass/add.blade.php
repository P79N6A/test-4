@extends('admin.layouts.parent')
@section('page-title','添加课时')
@section('main')
    <div class="row">
            <div class="col-sm-12">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>添加课时</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <form action="{{ route('admin.course.class.add') }}" class="forms">
                                {{ csrf_field() }}
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>课程</label>
                                        <input type="text" value="{{$course->name}}" placeholder="数字或者小写字母" class="form-control" disabled>
                                        <input type="hidden" name="course_id" value="{{$course->id}}">
                                    </div>
                                    <div class="form-group">
                                        <label>课时名称</label>
                                        <input type="text" name="name" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>课时可开启游戏最大次数</label>
                                        <input type="number" name="times" class="form-control" min="1" value="99">
                                    </div>
                                    <div class="form-group">
                                        <label>类型</label>
                                        <div class="radio i-checks">
                                            <label>
                                                <input type="radio" checked="" value="1" name="type">
                                                <i></i> 机台
                                            </label>
                                            <!-- <label>
                                                <input type="radio" value="2" name="type">
                                                <i></i> 游乐
                                            </label> -->
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <button class="btn btn-sm btn-primary btn-options" type="submit">创建</button>
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

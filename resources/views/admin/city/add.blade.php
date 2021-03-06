@extends('admin.layouts.parent')
@section('page-title','添加城市')
@section('main')
    <div class="row">
            <div class="col-sm-12">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>添加城市</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <form action="{{ route('admin.city.add') }}" class="forms">
                                {{ csrf_field() }}
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>城市名称</label>
                                        <input type="text" name="name" placeholder="数字或者小写字母" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>首字母</label>
                                        <select class="form-control m-b" name="first_letter">
                                        @for($i=65;$i<=90;$i++)
                                            <option>{{ chr($i) }}</option>
                                        @endfor
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>是否热门城市</label>
                                        <div class="radio i-checks">
                                            <label>
                                                <input type="radio" checked="" value="0" name="is_hot">
                                                <i></i> 否
                                            </label>
                                            <label>
                                                <input type="radio" value="1" name="is_hot">
                                                <i></i> 是
                                            </label>
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

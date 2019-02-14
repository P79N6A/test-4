@extends('admin.layouts.parent')
@section('page-title','年费介绍')
@section('main')
    <div class="row">
            <div class="col-sm-12">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>年费介绍</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <form action="{{ route('business.vip.modify') }}" class="forms">
                                {{ csrf_field() }}
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>价格(元)</label>
                                        <input type="text" name="price" placeholder="" class="form-control" value="{{$info->price/100}}">
                                    </div>
                                    <div class="form-group">
                                        <label>课程内容</label>
                                        <script id="content" name="content" type="text/plain">{!!$info->content!!}</script>
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
    <script src="/admin/js/webuploader.min.js"></script>
    <link href="/admin/css/webuploader.css" rel="stylesheet">
    <script type="text/javascript" src="/ueditor/ueditor.config.modified.js"></script>
    <script type="text/javascript" src="/ueditor/ueditor.all.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $('.btn-options').click(function(){
                youyibao.httpSend($('form.forms'),'post',1);
            });
            $(".i-checks").iCheck({checkboxClass: "icheckbox_square-green", radioClass: "iradio_square-green",});

            //UE
            var ue = UE.getEditor('content',{
                initialFrameHeight:400
            });
        });
    </script>
@endsection

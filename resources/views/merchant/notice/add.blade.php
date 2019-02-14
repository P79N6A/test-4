@extends('admin.layouts.parent')
@section('page-title','添加通知')
@section('main')
    <div class="row">
            <div class="col-sm-12">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>添加通知</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <form action="{{ route('business.notice.add') }}" class="forms">
                                {{ csrf_field() }}
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>通知名称</label>
                                        <input type="text" name="title" placeholder="" class="form-control" value="">
                                    </div>
                                    <div class="form-group">
                                        <label>通知内容</label>
                                        <script id="content" name="content" type="text/plain"></script>                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <button class="btn btn-sm btn-primary btn-options" type="submit">添加</button>
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

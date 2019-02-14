@extends('admin.layouts.parent')
@section('page-title','添加教师')
@section('main')
    <div class="row">
            <div class="col-sm-12">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>添加教师</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <form action="{{ route('admin.teacher.add') }}" class="forms">
                                {{ csrf_field() }}
                                <div class="col-sm-8">
                                    <div class="form-group">
                                        <label>名字</label>
                                        <input type="text" name="name" placeholder="" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>任职</label>
                                        <input type="text" name="job" placeholder="" class="form-control">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>封面图片</label>
                                        <div class="input-group">
                                            <div id="image-picker">上传</div>
                                            <input type="hidden" name="img">
                                            <div class="ibox-content no-padding border-left-right image-preview" style="margin-top:10px">
                                            </div>
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
    <script src="/admin/js/webuploader.min.js"></script>
    <link href="/admin/css/webuploader.css" rel="stylesheet">
    <script type="text/javascript">
        $(document).ready(function(){
            $('.btn-options').click(function(){
                youyibao.httpSend($('form.forms'),'post',1);
            });
            $(".i-checks").iCheck({checkboxClass: "icheckbox_square-green", radioClass: "iradio_square-green",});

            // 图片上传
            var imageUploader = WebUploader.create({
                auto:true,
                swf:'/admin/js/Uploader.swf',
                server:'/upload/save',
                pick:'#image-picker',
                accept:{
                    title:'Images',
                    extensions:'jpg,png,bmp,gif',
                    mimeTypes:'image/*'
                }
            });
            imageUploader.on('uploadSuccess',function(file, response){

                if(response.msg[0]){
                    layer.msg(response.mgs[0]);
                }else{
                    $('.image-preview').empty().append($('<img class="img-preview-sm">').attr('src',response.data[0].absolute_path).css({width:130,height:'auto'}));
                    $('input[name=img]').val(response.data[0].id);
                }
            });
        });
    </script>
@endsection

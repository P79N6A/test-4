@extends('admin.layouts.parent')
@section('page-title','添加课程类型')
@section('main')
    <div class="row">
            <div class="col-sm-12">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>添加课程类型</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <form action="{{ route('admin.course.type.add') }}" class="forms">
                                {{ csrf_field() }}
                                <div class="col-sm-8">
                                    <div class="form-group">
                                        <label>课程类型名称</label>
                                        <input type="text" name="name" placeholder="" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>停用</label>
                                        <div class="radio i-checks">
                                            <label>
                                                <input type="radio" checked="" value="0" name="disabled">
                                                <i></i> 否
                                            </label>
                                            <label>
                                                <input type="radio" value="1" name="disabled">
                                                <i></i> 是
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>排序</label>
                                        <input type="text" name="sort" placeholder="" class="form-control" value="0">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>图标</label>
                                        <div class="input-group">
                                            <div id="image-picker">上传</div>
                                            <input type="hidden" name="icon">
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
                    $('.image-preview').empty().append($('<img class="img-preview-sm">').attr('src',response.data[0].absolute_path).css({width:100,height:'auto'}));
                    $('input[name=icon]').val(response.data[0].id);
                }
            });
        });
    </script>
@endsection

@extends('admin.layouts.parent')
@section('page-title','修改医生信息')
@section('main')
    <div class="row">
            <div class="col-sm-12">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>修改医生信息</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <form action="{{ route('business.edit-doctor') }}" method="post" class="forms">
                                {{ csrf_field() }}
                                <div class="col-sm-5">
                                    <div class="form-group">
                                        <label>姓名</label>
                                        <input type="text" name="realname" placeholder="" value="{{$info->realname}}" class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label>昵称</label>
                                        <input type="text" name="nickname" placeholder="" value="{{$info->nickname}}" class="form-control">
                                    </div>
                                     <input type="hidden" name="id" value="{{$info->id}}">
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
    <script type="text/javascript">
        $(document).ready(function(){
            $('.btn-options').click(function(){
                youyibao.httpSend($('form.forms'),'post',1);
            });
            $(".i-checks").iCheck({checkboxClass: "icheckbox_square-green", radioClass: "iradio_square-green",});

            // 相册上传
            var galleryUploader = WebUploader.create({
                swf:'merchant/js/Uploader.swf',
                server:'/upload/save',
                pick:'#gallery-picker',
                resize:false,
                auto:true
            });

            var $gallery = $('.gallery-container');
            galleryUploader.on('uploadSuccess',function(file,response){
                var attid = '<input type="hidden" name="imgs[]" value="' + response.data[0].id +'">';
                var img = '<img alt="image" class="img-preview-sm" src="' + response.data[0].absolute_path + '">';
                var item = '<div class="file-box"><div class="file"><span class="corner"></span><div class="image">' + attid + img + '</div><div class="file-name text-center"><button class="btn btn-warning btn-circle btn-del-photo" type="button"><i class="fa fa-times"></i></button> </div></div></div>';
                $gallery.append($(item));
            });

            // 删除上传的相册图片
            $('.gallery-container').delegate('.btn-del-photo','click',function(){
                $(this).parents('div.file-box').empty().remove();
            });

            //获取教师信息
            $.get('/business/teacher/list/json',function(data){
                var html = '';
                for(var i in data){
                    html += '<option value="'+data[i].id+'">'+data[i].name+'('+data[i].job+')</option>';
                }
                $('#teacher').html(html);
            },'json');

            //获取品牌信息
            $.get('/business/brand/list/json',function(data){
                var html = '';
                for(var i in data){
                    html += '<option value="'+data[i].id+'">'+data[i].name+'</option>';
                }
                $('#brand').html(html);
            },'json');
        });
    </script>
@endsection

@extends('admin.layouts.parent')
@section('page-title','添加机台')
@section('main')
    <div class="row">
            <div class="col-sm-12">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>添加机台</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <form action="{{ route('business.equipment.add') }}" class="forms">
                                {{ csrf_field() }}
                                <div class="col-sm-8">
                                    <div class="form-group">
                                        <label>城市</label>
                                        <select class="form-control m-b" name="city_id" id="city">
                                            <option value="0">请选择城市</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>门店</label>
                                        <select class="form-control m-b" name="store_id" id="store">
                                            <option value="0">请选择门店</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>机台名称</label>
                                        <input type="text" name="name" placeholder="" class="form-control">
                                    </div>
                                    <!-- <div class="form-group">
                                        <label>机台型号</label>
                                        <input type="text" name="model" placeholder="" class="form-control">
                                    </div> -->
                                    <div class="form-group">
                                        <label>智联宝硬件编码</label>
                                        <input type="text" name="code" placeholder="" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>停用</label>
                                        <select class="form-control m-b" name="disabled">
                                            <option value="0">否</option>
                                            <option value="1">是</option>
                                        </select>
                                    </div>
                                    <button class="btn btn-sm btn-primary btn-options" type="submit">创建</button>
                                </div>
                            </form>
                        </div>
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
            // $(".i-checks").iCheck({checkboxClass: "icheckbox_square-green", radioClass: "iradio_square-green",});

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
                    $('.image-preview').empty().append($('<img class="img-preview-sm">').attr('src',response.data[0].absolute_path).css({width:300,height:'auto'}));
                    $('input[name=img]').val(response.data[0].id);
                }
            });

            //UE
            var ue = UE.getEditor('content',{
                initialFrameHeight:400
            });

            //获取城市信息
            $.get('/city/list/json',function(data){
                var html = '';
                for(var i in data){
                    html += '<option value="'+data[i].id+'">'+data[i].name+'</option>';
                }
                $('#city').append(html);
            },'json');

            $("#city").change(function(){
                $("#city").find("option[value='0']").attr("selected",false);
                $.get('/business/store/list/json', { city_id: $('#city').val() }, function(data){
                    if(data.length == 0){
                        layer.msg('该城市没有分店信息，请重新选择');
                        $("#city").find("option[value='0']").attr("selected",true);
                        $('#store').html('<option value="0">请选择门店</option>');
                        return false;
                    }

                    var html = '<option value="0">请选择门店</option>';
                    for(var i in data){
                        html += `
                            <option value='${data[i].id}'>${data[i].name}</option>
                        `;
                    }
                    $('#store').html(html);
                },'json');
            });
        });
    </script>
@endsection

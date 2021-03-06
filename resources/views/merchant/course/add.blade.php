@extends('admin.layouts.parent')
@section('page-title','添加课程')
@section('main')
    <div class="row">
            <div class="col-sm-12">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>添加课程</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <form action="{{ route('admin.course.add') }}" class="forms">
                                {{ csrf_field() }}
                                <div class="col-sm-8">
                                    <div class="form-group">
                                        <label>课程名称</label>
                                        <input type="text" name="name" placeholder="" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>课程分类</label>
                                        <select class="form-control m-b" name="type_id">
                                            @if(!empty($course_type))
                                            @foreach($course_type as $v)
                                            <option value="{{$v->id}}">{{$v->name}}</option>
                                            @endforeach
                                            @else
                                            <option>暂无选项</option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>价格(元)</label>
                                        <input type="text" name="price" placeholder="" class="form-control">
                                    </div>
                                    <!-- <div class="form-group">
                                        <label>合适年龄段</label>
                                        <select class="form-control m-b" name="suitable_age">
                                            @if(!empty($suitable))
                                            @foreach($suitable as $v)
                                            <option value="{{$v->id}}">{{$v->name}}</option>
                                            @endforeach
                                            @else
                                            <option>暂无选项</option>
                                            @endif
                                        </select>
                                    </div> -->
                                    <div class="form-group">
                                        <label>课程内容</label>
                                        <script id="content" name="content" type="text/plain"></script>
                                    </div>
                                    <div class="form-group">
                                        <label>停用</label>
                                        <select class="form-control m-b" name="disabled">
                                            <option value="0">否</option>
                                            <option value="1">是</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>推荐</label>
                                        <select class="form-control m-b" name="is_recommend">
                                            <option value="0">否</option>
                                            <option value="1">是</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>热门</label>
                                        <select class="form-control m-b" name="is_hot">
                                            <option value="0">否</option>
                                            <option value="1">是</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>城市</label>
                                        <select class="form-control m-b" name="city_id" id="city">
                                            <option value="0">请选择城市</option>
                                        </select>
                                    </div>
                                    <!-- <div class="form-group">
                                        <label style="display:block">适用门店</label>
                                        @if(!empty($stores))
                                        @foreach($stores as $store)
                                        <label class="checkbox-inline i-checks" for="store_[{{$store->id}}]">
                                            <input type="checkbox" id="store_[{{$store->id}}]" name="store_ids[]" value="{{$store->id}}">{{$store->name}}
                                        </label>
                                        @endforeach
                                        @endif
                                    </div> -->
                                    <div class="form-group store" style="display:none">
                                        <label style="display:block">适用门店</label>
                                    </div>
                                    <div class="form-group">
                                        <label>排序</label>
                                        <input type="text" name="sort" placeholder="" class="form-control" value="0">
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
                $.get('/business/store/list/json', { city_id: $('#city').val() }, function(data){
                    if(data.length == 0){
                        layer.msg('该城市没有分店信息，请重新选择');
                        $("#city").find("option[value='0']").attr("selected",true);
                        $('.store').css('display','none');
                        $(".i-checks").remove();
                        return false;
                    }
                    $(".i-checks").remove();
                    var html = '';
                    for(var i in data){
                        html += `
                            <label class="checkbox-inline i-checks" for="store_[${data[i].id}]">
                                <input type="checkbox" id="store_[${data[i].id}]" name="store_ids[]" value="${data[i].id}">${data[i].name}
                            </label>
                        `;
                    }
                    $('.store').append(html);
                    $(".i-checks").iCheck({checkboxClass: "icheckbox_square-green", radioClass: "iradio_square-green",});
                    $('.store').css('display','block');
                },'json');
            });
        });
    </script>
@endsection

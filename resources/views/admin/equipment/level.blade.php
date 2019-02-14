@extends('admin.layouts.parent')
@section('page-title','设置游戏难易度')
@section('main')
    <div class="row">
            <div class="col-sm-12">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>设置游戏难易度</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <form action="{{ route('admin.equipment.games.level') }}" method="post" >
                                {{ csrf_field() }}
                                <input type="hidden" name="id"  value="{{$id}}">
                                <div class="form-inline">
                                    <div class="form-group" id="init">
                                        <label>初始化游戏难度</label>
                                        @if(!empty($result))
                                        @foreach($result as $key=>$list)
                                        @if($list->is_init == 1)
                                        @foreach($list->date as $date)
                                        <input type="text" name="init_type[]" placeholder="请输入游戏参数" class="form-control" style="width: 130px" value="{{$date->type}}">
                                        <label>：</label>
                                        <input type="text" name="init_value[]" placeholder="请输入参数值" class="form-control" style="width: 130px" value="{{$date->value}}">
                                        @endforeach
                                        @endif
                                        @endforeach
                                        @else
                                        <input type="text" name="init_type[]" placeholder="请输入游戏参数" class="form-control" style="width: 130px">
                                        <label>：</label>
                                        <input type="text" name="init_value[]" placeholder="请输入参数值" class="form-control" style="width: 130px">
                                        @endif
                                    </div>
                                    <a onclick="addInit()" class="label label-primary">+</a>
                                    
                                </div>
                                <br>
                                <div id="according">
                                    <div class="form-group">
                                        <label>难易度判定依据:</label>
                                        <a class="btn btn-primary btn-sm" onclick="addAccording()">添加难度判断依据</a>
                                        <a class="btn btn-primary btn-sm" onclick="addData()">添加游戏难易度参数</a>
                                         <button class="btn btn-sm btn-primary btn-options" type="submit" style="">保存</button>
                                    </div>
                                    @if(!empty($result))
                                    @foreach($result as $key=>$res)
                                    @if($res->is_init == 0)
                                    <div class="form-inline" id="according_line" style="margin-top: 10px">
                                        <div class="form-group">
                                            <input type="text" name="score_begin[]" placeholder="请输入游戏判断依据区间起始值" class="form-control" style="width: 230px" value="{{$res->score_begin}}">
                                            <label>~</label>
                                            <input type="text" name="score_end[]" placeholder="请输入游戏判断依据区间结束值" class="form-control" style="width: 230px" value="{{$res->score_end}}">
                                        </div>
                                        <label>=></label>
                                        
                                        <div class="form-group games" id="data_games" style="">
                                            @foreach($res->date as $date)
                                            <input type="text" name="type[]" placeholder="请输入游戏参数" class="form-control" style="width: 130px" value="{{$date->type}}">
                                            <label>：</label>
                                            <input type="text" name="value[]" placeholder="请输入参数值" class="form-control" style="width: 130px" value="{{$date->value}}">
                                            @endforeach
                                        </div>
                                        
                                        <a id="test" class="label label-primary" onclick="">-</a>
                                        
                                    </div>
                                    @endif
                                    @endforeach
                                    @else
                                    <div class="form-inline" id="according_line" style="margin-top: 10px">
                                        <div class="form-group">
                                            <input type="text" name="score_begin[]" placeholder="请输入游戏判断依据区间起始值" class="form-control" style="width: 230px">
                                            <label>~</label>
                                            <input type="text" name="score_end[]" placeholder="请输入游戏判断依据区间结束值" class="form-control" style="width: 230px">
                                        </div>
                                        <label>=></label>
                                       
                                        <div class="form-group games" id="data_games">
                                            <input type="text" name="type[]" placeholder="请输入游戏参数" class="form-control" style="width: 130px">
                                            <label>：</label>
                                            <input type="text" name="value[]" placeholder="请输入参数值" class="form-control" style="width: 130px">
                                        </div>
                                       
                                        <a id="test" class="label label-primary" onclick="">-</a>
                                    </div>
                                    @endif

                                
                                   
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
    <script type="text/javascript">
        function addInit(){
            $('#init').append('+<input type="text" name="init_type[]" placeholder="请输入游戏参数" class="form-control" style="width: 130px"><label>：</label><input type="text" name="init_value[]" placeholder="请输入参数值" class="form-control" style="width: 130px">');
        }

        function addData(){
            // var arr = document.getElementById('data_games');
            // console.log(arr);
            // for(var i=0;i<arr.length;i++){
            //     $(arr[i]).append('<input type="text" name="name" placeholder="请输入游戏参数" class="form-control" style="width: 130px"><label>：</label><input type="text" name="name" placeholder="请输入参数值" class="form-control" style="width: 130px">')
            // }
            var i = 0;
            $(".games").each(function(){
                i++;
                console.log(i);
                $(this).append('+<input type="text" name="type[]" placeholder="请输入游戏参数" class="form-control" style="width: 130px"><label>：</label><input type="text" name="value[]" placeholder="请输入参数值" class="form-control" style="width: 130px">')
            });
        }

        function addAccording(){
            
            var a = $('#according_line').clone().appendTo("#according");
            console.log(a.length);
            if(a.length == 0){
                $('#according').append('<div class="form-inline" id="according_line"  style="margin-top: 10px"><div class="form-group"><input type="text" name="score_begin[]" placeholder="请输入游戏判断依据区间起始值" class="form-control" style="width: 230px"><label>~</label><input type="text" name="score_end[]" placeholder="请输入游戏判断依据区间结束值" class="form-control" style="width: 230px"></div><label>=></label><div class="form-group games" id="data_games"><input type="text" name="type[]" placeholder="请输入游戏参数" class="form-control" style="width: 130px"><label>：</label><input type="text" name="value[]" placeholder="请输入参数值" class="form-control" style="width: 130px"></div><a id="test" class="label label-primary" onclick="">-</a></div><br>')
            }
        }

        $(document).on('click','#test',function(){
            $(this).parent().remove();
        })
    </script>
@endsection

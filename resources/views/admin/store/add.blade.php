@extends('admin.layouts.parent')
@section('page-title','添加门店')
@section('main')
    <div class="row">
            <div class="col-sm-12">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>添加门店</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <form action="{{ route('admin.business.store.add') }}" class="forms">
                                {{ csrf_field() }}
                                <div class="col-sm-5">
                                    <div class="form-group">
                                        <label>门店名称</label>
                                        <input type="text" name="name" placeholder="" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>电话</label>
                                        <input type="text" name="tel" placeholder="" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>城市</label>
                                        <select class="form-control m-b" name="city_id" id="city">
                                            <option>正在加载...</option>
                                        </select>
                                    </div>
                                    <div class="form-group" >
                                        <label>门店地址</label>
                                        <input type="text" name="address" id="address" placeholder="" class="form-control">
                                       <div id="container"></div>
                                       <div id="tip">
                                           <span id="result"></span>
                                       </div>
                                       <div id="myPageTop" style="display: none">
                                           <table>
                                               <tr>
                                                   <td>
                                                       <label>按关键字搜索：</label>
                                                   </td>
                                                   <td class="column2">
                                                       <label>左击获取经纬度：</label>
                                                   </td>
                                               </tr>
                                               <tr>
                                                   <td>
                                                       <input type="text" placeholder="请输入关键字进行搜索" id="tipinput">
                                                   </td>
                                                   <td class="column2">
                                                       <input type="text" readonly="true" id="lnglat">
                                                   </td>
                                               </tr>
                                           </table>
                                       </div>
                                    </div>
                                    <div class="form-group">
                                        <label>门店维度</label>
                                        <input type="text" name="latitude" id="latitude"  placeholder="" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>门店经度</label>
                                        <input type="text" name="longitude" id="longitude" placeholder="" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>品牌</label>
                                        <select class="form-control m-b" name="brand_id" id="brand">
                                            <option>正在加载...</option>
                                        </select>
                                    </div>
                                    <!-- <div class="form-group">
                                        <label>门店老师</label>
                                        <select class="form-control m-b" name="teachers[]" id="teacher" multiple>
                                            <option>正在加载...</option>
                                        </select>
                                    </div> -->
                                </div>
                                <div class="col-sm-7">
                                    <div class="form-group">
                                        <label>门店图集</label>
                                        <div class="input-group">
                                            <input type="hidden" name="gallery">
                                            <div id="gallery-picker">上传</div>
                                            <div style="margin-top:10px" class="gallery-container"></div>
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

            //获取城市信息
            $.get('/city/list/json',function(data){
                var html = '';
                for(var i in data){
                    html += '<option value="'+data[i].id+'">'+data[i].name+'</option>';
                }
                $('#city').html(html);
            },'json');
        });
    </script>
    <style type="text/css">
        #container {width:500px; height: 380px; }  
    </style>
    <script src="https://webapi.amap.com/maps?v=1.4.8&key={{$key}}&plugin=AMap.Geocoder&plugin=AMap.Autocomplete"></script>
    <script type="text/javascript" src="https://cache.amap.com/lbs/static/addToolbar.js"></script>
    <script type="text/javascript">
        var map = new AMap.Map("container", {
            resizeEnable: true
        });
        var geocoder = new AMap.Geocoder({
           // city: "010", //城市，默认：“全国”
            //radius: 1000 //范围，默认：500
        });
        $("#address").blur(function(){
            var address = $("#address").val();
             //地理编码,返回地理编码结果
            geocoder.getLocation(address, function(status, result) {
                if (status === 'complete' && result.info === 'OK') {
                    geocoder_CallBack(result);
                
                }
            });
            function addMarker(i, d) {
                var marker = new AMap.Marker({
                    map: map,
                    position: [ d.location.getLng(),  d.location.getLat()]
                });
                var infoWindow = new AMap.InfoWindow({
                    content: d.formattedAddress,
                    offset: {x: 0, y: -30}
                });
                marker.on("mouseover", function(e) {
                    infoWindow.open(map, marker.getPosition());
                });
            }
            //地理编码返回结果展示
            function geocoder_CallBack(data) {
                var resultStr = "";
                //地理编码结果数组
                var geocode = data.geocodes;
                for (var i = 0; i < geocode.length; i++) {
                    map.clearMap();
                    //拼接输出html
                    resultStr += "<span style=\"font-size: 12px;padding:0px 0 4px 2px; border-bottom:1px solid #C1FFC1;\">" + "<b>地址</b>：" + geocode[i].formattedAddress + "" + "&nbsp;&nbsp;<b>的地理编码结果是:</b><b>&nbsp;&nbsp;&nbsp;&nbsp;坐标</b>：" + geocode[i].location.getLng() + ", " + geocode[i].location.getLat() + "" + "<b>&nbsp;&nbsp;&nbsp;&nbsp;匹配级别</b>：" + geocode[i].level + "</span>";
                    addMarker(i, geocode[i]);
                    //alert(geocode[i].location.getLng()+','+geocode[i].location.getLat());
                    //对经纬度进行赋值
                    $('#longitude').val(geocode[i].location.getLng()); 
                    $('#latitude').val(geocode[i].location.getLat());
                
                }

                map.setFitView();
                document.getElementById("result").innerHTML = resultStr;
            }
        })
        var clickEventListener = map.on('click', function(e) {
            $('#longitude').val(e.lnglat.getLng()); 
            $('#latitude').val(e.lnglat.getLat());
            document.getElementById("lnglat").value = e.lnglat.getLng() + ',' + e.lnglat.getLat();
            map.clearMap();
            var marker = new AMap.Marker({
                position:[e.lnglat.getLng(), e.lnglat.getLat()]//位置
            })
            map.add(marker);//添加到地图
        });
        var auto = new AMap.Autocomplete({
            input: "tipinput"
        });
        AMap.event.addListener(auto, "select", select);//注册监听，当选中某条记录时会触发
        function select(e) {
            if (e.poi && e.poi.location) {
                map.setZoom(15);
                map.setCenter(e.poi.location);
            }
        }
    </script>
@endsection

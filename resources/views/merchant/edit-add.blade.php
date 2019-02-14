<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>发布广告</title>
    <style type="text/css">
        .frame{
            border:1px solid #ccc;
        }
        .show{
            display:block;
        }
        .hide{
            display:none;
        }

    </style>
</head>
<body>
<div>
    <form action="/update-add" method="post" class="form-post-add">
        <table>
            <input type="hidden" name="id" value="{{ $add->id }}">
            <tr><td>标题：</td><td><input name="title" value="{{ $add->title }}" placeholder="广告标题"></td></tr>
            <tr>
                <td>类型：</td>
                <td>
                    <span class="tab"><label for="type-1">图文</label>
                        <input type="radio" name="type" value="1" id="type-1" @if($add->type ==1) checked @endif></span>
                    <span class="tab"><label for="type-2">外链</label>
                        <input type="radio" name="type" value="2" id="type-2" @if($add->type ==2) checked @endif></span>
                    <span class="tab"><label for="type-3">内部</label>
                        <input type="radio" name="type" value="3" id="type-3" @if($add->type ==3) checked @endif></span>
                </td>
            </tr>
            <tr>
                <td>投放广告的门店：</td>
                <td>
                    <div class="alt-stores" style="border:1px solid #ccc;">
                        @foreach($stores as $store)
                            <div>
                                <label for="alt-store-{{ $store->id }}">{{ $store->name }}</label>
                                <input type="checkbox" id="alt-store-{{ $store->id }}" class="storeIds" name="altStores" value="{{ $store->id }}" @if(in_array($store->id,$relatedStores)) checked @endif >
                            </div>
                        @endforeach
                    </div>
                </td>
            </tr>
            <tr>
                <td>封面：</td>
                <td>
                    <div style="position:relative;">
                        <input type="hidden" name="image" value="{{ $add->image }}">
                        <div id="image-picker">选择图片</div>
                        <div class="image-preview">
                            <img width="160" height="90" src="@if($add->path){{ config('upload.static_base_url') }}/{{ $add->path }}@endif" alt="封面">
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>是否推荐：</td>
                <td>
                    <label for="flag-no">否</label>
                    <input id="flag-no" type="radio" name="flag" value="0" @if($add->flag ==0) checked @endif>
                    <label for="flag-yes">是</label>
                    <input id="flag-yes" type="radio" name="flag" value="1" @if($add->flag ==1) checked @endif>
                </td>
            </tr>
            <tr>
                <td>内容：</td>
                <td>
                    <div class="content frame @if($add->type == 1) show @else hide @endif ">
                        <textarea name="content" id="ckeditor">{{ $add->content }}</textarea>
                    </div>
                    <div class="content @if($add->type == 2) show @else hide @endif">
                        <input name="content" value="{{ $add->content }}" placeholder="外链，须加上 http://">
                    </div>
                    <div class="content @if($add->type == 3) show @else hide @endif">
                        <table>
                            <tr>
                                <td>选择门店：</td>
                                <td>
                                    <select class="stores">
                                        <option>请选择门店</option>
                                        @foreach($stores as $store)
                                            <option value="{{ $store->id }}" @if(isset($add->store_id) && $add->store_id == $store->id) selected @endif >{{ $store->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>产品类型：</td>
                                <td>
                                    <select class="types" name="product-type">
                                        <option>请选择产品类型</option>
                                        <option value="1" @if(isset($add->product_type) && $add->product_type == 1) selected @endif >套餐</option>
                                    </select>
                                </td>
                                <td>产品名称：</td>
                                <td>
                                    <select class="products">
                                        <option>请选择产品名称</option>
                                    </select>
                                </td>
                            </tr>

                        </table>
                    </div>
                </td>
            </tr>
            <tr><td></td><td class="container"><button type="button" class="btn-post-add">提交</button></td></tr>
            <tr><td colspan="2" class="result"></td></tr>
        </table>
    </form>
</div>
</body>
</html>
<script type="text/javascript" src="merchant/js/jquery.min.js"></script>
<script type="text/javascript" src="merchant/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="merchant/js/youyibao.js"></script>
<script type="text/javascript" src="merchant/js/webuploader.min.js"></script>
<script type="text/javascript" src="/merchant/layer/layer/layer.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        // 切换选项卡
        $('.tab').click(function(){
            $('.content').hide();
            $('.content').eq($(this).index()).show();
        });

        // 初始化编辑器实例
        CKEDITOR.replace('ckeditor',{
            customConfig:'/merchant/ckeditor/config.js'
        });

        $('.tab').click(function(){
            $('.content').hide();
            $('.content').eq($(this).index()).show().children('input[name=content]').val('');
        });

        // 图片上传
        var imageUploader = WebUploader.create({
            auto:true,
            swf:'/merchant/js/Uploader.swf',
            server:'/upload',
            pick:'#image-picker',
            accept:{
                title:'Images',
                extensions:'jpg,png,bmp,gif',
                mimeTypes:'image/*'
            }
        });
        imageUploader.on('uploadSuccess',function(file, response){
            $('.image-preview').empty().append($('<img>').attr('src',response.data[0].absolute_path).css({width:160,height:90}));
            $('input[name=image]').val(response.data[0].id);
        });

        // 处理套餐列表
        $('.types').change(function(){
            var store_id = parseInt($('select.stores').val());
            if(!store_id){
                alert('请选择门店');
                return false;
            }
            var type = $(this).val();
            if(type == 1){
                youyibao.getPackages(store_id,function(data){
                    var res = null;
                    $.each(data,function(index,value){
                        res += '<option value="' + value.id + '">' + value.name + '</opton>';
                    });
                    $('.products').empty().append(res);
                });
            }
        });

        @if(isset($add->product_type))
        var productType = @if(isset($add->product_type)) {{  $add->product_type }} @endif;
        if(productType == 1){
            var packages = youyibao.getPackages( @if(isset($add->store_id)) {{ $add->store_id }} @endif,function(data){
                var res = null;
                $.each(data,function(index,value){
                    var select;
                    if( @if(isset($add->product_id)) {{ $add->product_id }} @endif == value.id){
                        select = 'selected';
                    }else{
                        select = '';
                    }
                    res += '<option value="' + value.id + '"' + select +'>' + value.name + '</opton>';
                });
                $('.products').empty().append(res);
            });
        }
        @endif



        $('.btn-post-add').click(function(){
            var $formAdd = $('form.form-post-add');
            var type = $('input[name=type]:checked').val();
            var place = $('input[name=place]:checked').val();
            var content = null;
            if(type == 1){
                content = CKEDITOR.instances.ckeditor.getData();
            }else if(type == 2){
                content = $('input[name=content]').val();
            }else if(type == 3){
                content = $('.products').val();
            }

            var altStores = [];
            var $checkStores = $('.storeIds:checked');
            $.each($checkStores,function(index,item){
                altStores.push(item.value);
            });

            $.ajax({
                type:'post',
                url:$formAdd.attr('action'),
                data:{
                    id:$('input[name=id]').val(),
                    title:$('input[name=title]').val(),
                    type:$('input[name=type]:checked').val(),
                    image:$('input[name=image]').val(),
                    flag:$('input[name=flag]:checked').val(),
                    content:content,
                    altStores:altStores,
                    product:$('.types').val()
                },
                success:function(data){
                    if(data.code == 200){
                        if(data.url != undefined){
                            window.location.href = data.url;
                        }
                    }else{
                        layer.msg(data.msg,{icon:5});
                    }
                }
            });
        });


    });
</script>

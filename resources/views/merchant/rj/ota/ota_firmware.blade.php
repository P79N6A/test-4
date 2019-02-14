@extends('admin.layouts.parent')
@section('page-title','OTA升级')
@section('main')
<link href="/admin/css/webuploader.css" rel="stylesheet">
 <link href="/admin/css/new-add.css" rel="stylesheet">
@extends(env('Merchant_view').'.layouts.ota_menu')
    <div class="row" >
        <div class="col-sm-12">
        
         <!-- 门店资料开始 -->
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                <div class="z_form">
                	<div class="z_title z_font16 z_border_b z_pb10">
        				<div class="z_w50p clearfix">
	        				<span class="z_fontb">上传新版本:</span>
        				</div>
        			</div>
	                <form id="form1" action="{{ route('business.rj_ota_firmware') }}" method="post">
	                	{{ csrf_field() }}
		        		<div class="z_inputbox z_mt10 z_mb10">
	        				<label class="z_w120-tr"><span class="z_color_red">*</span>固件版本号:</label>
	        				<input type="text" name="firmware_sn" class="z_w335"/>
	        			</div>
	        			<div class="z_inputbox  z_mb10">
	        				<label class="z_w120-tr"><span class="z_color_red">*</span>硬件版本号:</label>
	        				<input type="text" name="hardware_sn" class="z_w335"/>
	        			</div>
	        			<div class="z_inputbox  z_mb10">
	        				<label class="z_w120-tr"><span class="z_color_red">*</span>上传固件版本:</label>
	        				<input type="text" disabled id="firmware_id" class="z_w335"/>
	        				<input type="hidden" name="firmware_id"  class="z_w335"/>
	        				<span id="logo-picker">
	        					<img src="/admin/img/u294.png"   width="29" height="29"/>
	        				</span>
	        				
	        			</div>
	        			<div class="z_inputbox z_mb10">
	        				<label class="z_w120-tr">备注信息:</label>
	        				<textarea rows="3" cols="30" name="ramrk"></textarea>
	        			</div>
	        			<div class="z_inputbox z_mb10">
	        				<label class="z_w120-tr z_mr10 "><span class="z_color_red">*</span>状态设置:</label>
	        				<label class="z_mr10">
	        					<input type="radio" name="start" checked value="1" />
	        					上线
	        				</label>
	        				<label class="z_mr10">
	        					<input type="radio" name="start" value="0" />
	        					下线
	        				</label>
	        			</div>
	        		 <button class="btn z_bar-blue z_color_white z_w100  z_mt40 "  style="margin:0px 0px 35px 140px" type="submit">保存</button>
                    </form>	
        			
        			<div class="z_title z_font16 z_border_b z_pb10">
        				<div class="z_w50p clearfix">
	        				<span class="z_fontb">已上传版本:</span>
        				</div>
        			</div>
        			<div class="z_border z_mt20">
        				<table class="z_table2 js-z_table2">
		        			<thead>
		        				<tr>
		        					<th class="">固件版本号</th>
		        					<th class="">硬件版本号</th>
		        					<th class="">状态</th>
		        					<th class="">上传时间</th>
		        					<th class="">备注</th>
		        					<th>操作</th>
		        				</tr>
		        			</thead>
		        			<tbody>
			        			@if(!empty($ads))
			        				@foreach($ads as $val)
			        				<tr>
			        					<td>{{$val->firmware_sn}}</td>
			        					<td>{{$val->hardware_sn}}</td>
			        					<td> @if($val->start=='1') 上线  @else 下线  @endif </td>
			        					<td>{{$val->add_date}}</td>
			        					<td>{{$val->ramrk}}</td>
			        					<td>
			        						<a href="javascript:void(0);"  onclick="start(this,{{$val->id}})">
			        						 	<button class="btn btn-white btn-sm" data-start="@if($val->start=='1') 0 @else 1 @endif" >@if($val->start=='1') 下线  @else 上线  @endif </button> 
			        						</a>  
			        						<a href="{{route('business.ota_select_machine',['id'=>$val->id])}}" >
			        							<button class="btn btn-white btn-sm" >选择机台</button> 
			        						</a> 
			        						<a href="{{route('business.ota_firmware_info',['id'=>$val->id])}}">
			        							<button class="btn btn-white btn-sm" >查看详情</button> 
			        						</a>   
			        					</td>
			        				</tr>
			        				@endforeach
			        			@endif
		        			</tbody>
		        		</table>
        			</div>
        			<div class="text-right">
                    	@if(!empty($ads) && !empty($ads->links()))
                            {{ $ads->links() }}
                        @endif
                    </div>
	        	</div>
                </div>
            </div>
            <!-- 门店资料结束 -->
            
            
        </div>
    </div>
<script src="/admin/js/webuploader.min.js"></script>	
    <script>
    	var _token=$('input[name="_token"]').val();
    	function start(obj,id){
    		layer.confirm('您确定要改变版本状态？', {
  			  btn: ['确定','取消'] //按钮
  			}, function(){
  				start = $(obj).children('button').attr('data-start');
  	        	data = {'_token':_token,'id':id,'start':start}
  				$.post('{{route("business.ota_firmware_update")}}',data,function(res){
  					
  					if(res.code==200){
  						console.log(start);
  						layer.msg(res.msg,{icon: 1,time: 500},function() {
  							history.go(0);
  						});
  					}else{
  						layer.msg(res.msg,{icon: 5});
  					}
  				});
  			}, function(){ });
        	
        }

        
    </script>
	<script>
	

    var imageUploader = WebUploader.create({
        swf: '/admin/js/Uploader.swf',
        server: '/upload',
        pick: '#logo-picker',
        resize: false,
        auto: true,
        duplicate :true  
    });
    imageUploader.on('uploadSuccess', function (file, response) {
     	$('#firmware_id').val(response.data[0].absolute_path);
     	$('input[name="firmware_id"]').val(response.data[0].id);
     	
    });

    // 提交表单
    $('form').submit(function(e){
        e.preventDefault();
        youyibao.httpSend($(this),'post',1);
    });
    
	</script>
    
    <script>
        $(function(){
            $('.switch-publish').click(function(){
                youyibao.httpSend($(this),'get',1);
            });
            $('.btn-del-ad').click(function(){
                youyibao.httpSend($(this),'get',1);
            });
        });
    </script>
@endsection

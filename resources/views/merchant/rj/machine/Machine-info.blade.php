@extends('admin.layouts.parent')
@section('page-title','机台详情')
@section('main')
 <link rel="stylesheet" href="/admin/css/webuploader.css">
 <link href="/admin/css/new-add.css" rel="stylesheet">
<style>
.inline_block{    width:100px;  display:inline-block;  }
.img-responsive{width:200px;}

</style>

     <div class="wrapper wrapper-content animated fadeInUp">
        <div class="row">
        	<!--奖品设置-->
        	{{ csrf_field() }}
        	<input type="hidden" name="m_id"  value="{{$info['m_id']}}"/>
        	<input type="hidden" name="p_id"  value="{{$info['p_id']}}"/>
        	<input type="hidden" name="d_id"  value="{{$info['d_id']}}"/>
        	<div class="col-sm-12">
        		<div class="z_form">
	        		<div class="z_inputbox z_mt10 z_mb10 clearfix">
        				<label class="z_w120-tr fl z_mr10 z_fontb"><span class="z_color_red">*</span>机台图片:</label>
        				<div class="fl">
        					<div class="z_">
	        						<div class=" z_mb10 z_mr10  ">		        							
	        							<div id="gallery-picker">上传</div>
	        						</div>
	        				</div>
	        				<div class="z_">
	        						<div class=" z_mb10 z_mr10">	
		        						<div class="gallery-container">
		        						<?php foreach($info['gallery'] as $key_img=>$val_img){ ?>
	        						  		<div class="file-box">
	                                             <div class="file">
	                                                  <span class="corner"></span>
	                                                  <div class="image">
	                                                        <input type="hidden" name="gallery_photos[]" value="{{$key_img}}">
	                                                        <img alt="image" class="img-responsive" src="{{env('STATIC_BASE_URL').'/'.$val_img}}">
	                                                   </div>
	                                                   <div class="file-name text-center">
	                                                        <button class="btn btn-warning btn-circle btn-del-photo" value="{{$key_img}}" type="button">
	                                                        	<i class="fa fa-times"></i>
	                                                         </button>
	                                                    </div>
	                                              </div>
	                                         </div>
	                                    <?php }?>
	                                    </div>
                                    </div>
        					</div>
        				</div>
        			</div>
        			
        			<div class="z_inputbox z_mb10">
        				<label class="z_w120-tr z_mr10 z_fontb"><span class="z_color_red">*</span>品牌:</label>
        				<span class="z_font14 z_color-3"><?php echo $brand_name;?></span>
        			</div>
        			
        			<div class="z_inputbox z_mb10">
        				<label class="z_w120-tr z_mr10 z_fontb"><span class="z_color_red">*</span>门店:</label>
        				<span class="z_font14 z_color-3"><?php echo $info['stores_name'];?></span>
        			</div>
        			<div class="z_inputbox z_mb10">
        				<label class="z_w120-tr z_mr10 z_fontb"><span class="z_color_red">*</span>机台型号:</label>
        				<span class="z_font14 z_color-3"><?php echo $info['model'];?></span>
        			</div>
        			<div class="z_inputbox z_mb10">
        				<label class="z_w120-tr z_mr10 z_fontb"><span class="z_color_red">*</span>硬件编号:</label>
        				<span class="z_font14 z_color-3"><?php echo $info['code'];?></span>
        			</div>
        			<div class="z_inputbox z_mb10">
        				<label class="z_w120-tr z_mr10 z_fontb"><span class="z_color_red">*</span>机台名称:</label>
        				<input type="text" class="z_w200" name="m_name" onchange="m_name(this)" value="<?php echo $info['m_name']?>"/>
        			</div>
        			<div class="z_inputbox z_mb10">
        				<label class="z_w120-tr z_mr10 z_fontb"><span class="z_color_red">*</span>固件版本号:</label>
        				<span class="z_font14 z_color-3"><?php echo $info['firmware_sn'];?></span>
        			</div>
        			<div class="z_inputbox z_mb10">
        				<label class="z_w120-tr z_mr10 z_fontb"><span class="z_color_red">*</span>硬件版本号:</label>
        				<span class="z_font14 z_color-3"><?php echo $info['hardware_sn'];?></span>
        			</div>
        			<div class="z_inputbox z_mb10">
        				<label class="z_w120-tr z_mr10 z_fontb"><span class="z_color_red">*</span>在线状态:</label>
        				<span class="z_font14 z_color-3"><?php if($info['is_open']=='1'){ echo '在线';}else{echo '离线';}?></span>
        			</div>
        			<div class="z_inputbox z_mb10">
        				<label class="z_w120-tr z_mr10 z_fontb"><span class="z_color_red">*</span>激活状态:</label>
        				<span class="z_font14 z_color-3"><?php if($info['is_activate']=='2'){ echo '已激活';}else{echo '未激活';}?></span>
        			</div>
					<!--
        			<div class="z_inputbox z_mb10"  id="dl">
        				<label class="z_w120-tr z_mr10 z_fontb"><span class="z_color_red">*</span>禁用状态:</label>
        				<label class="z_mr10">
        					<input type="radio" <?php if($info['is_disable']=='1'){ echo 'checked';}?> name="is_disable" value="1" />
        					禁用
        				</label>
        				<label class="z_mr10">
        					<input type="radio" <?php if($info['is_disable']=='2'){ echo 'checked';}?> name="is_disable" value="2" />
        					启动
        				</label>
        			</div>
        			-->
        			<div class="z_inputbox z_mb10">
        				<label class="z_w120-tr z_mr10 z_fontb"><span class="z_color_red">*</span>游戏局数:</label>
						<span class="z_font14 z_color-3">{{$GameNumMsg}}</span>
						<div class="z_inputbox z_mb10" style="width:30%;     margin-left: 126px; ">
							<div class="z_box z_p10 ">
								@if(!empty($GameNum))
									@foreach($GameNum as $key=>$val)
										<div class="z_border z_list z_pl10 ">
											<span class="z_mr70">{{$key}}</span>
											<span class="z_mr70">{{$val}}局</span>
										</div>
									@endforeach
								@endif
							</div>
						</div>
        			</div>
        			<div class="z_inputbox z_mb10">
        				<label class="z_w120-tr z_mr10 z_fontb"><span class="z_color_red">*</span>单局币数:</label>
                        <?php if($info['m_type']==2){ ?>
							<span class="z_font14 z_color-3"><?php echo $info['coin_sum'];?> 币/局</span>
                        <?php }else{ ?>
							<input type="text" class="z_w200 z_mr10" name="coin_sum" onchange="coin_sum(this)" value="<?php echo $info['coin_sum'];?>" /><span>币/局</span>
                        <?php } ?>
					</div>
        			<div class="z_inputbox z_mb10">
        				<label class="z_w120-tr z_mr10 z_fontb"><span class="z_color_red">*</span>机台玩家位数量:</label>
        				<span class="z_font14 z_color-3"><?php echo $info['sum'];?></span>
        			</div>
					@if($info['pay_type'] != 3)
						<div class="z_inputbox z_mb10">
							<label class="z_w120-tr z_mr10 z_fontb"><span class="z_color_red">*</span>支付方式:</label>
							<label class="z_mr10">
								<input type="radio" name="pay_type[]" value="1" <?php if(strstr($info['pay_type'],'1')){ echo 'checked'; } ?> />
								线上支付
							</label>
							<label class="z_mr10">
								<input type="radio" name="pay_type[]" value="2" <?php if(strstr($info['pay_type'],'2')){ echo 'checked'; } ?>  />
								线下支付
							</label>
						</div>
					@endif

        			<div class="z_inputbox z_mb10">
        				<label class="z_w120-tr z_mr10 z_fontb"><span class="z_color_red">*</span>套餐配置:</label>
        			</div>
        			<div class="z_border clearfix">
						<!--
        				<div class="z_w50p z_border_r boxsizing fl">
        					<div class="z_inputbox z_mt10 z_mb10 z_pl20">
		        				<label class="z_mr10 z_fontb"><span class="z_color_red">*</span>A 套餐价格配置:</label>
		        			</div>
		        			<div class="clearfix z_p10">
		        				<div class="col-sm-10 z_border">
		        					<div class="z_inputbox z_mb10 z_mt10">
				        				<input type="number"  name="package_A_coin_qty" onchange="num_coin_qty(this)"  class="z_w80 z_mr10" placeholder=" "/><span>局</span>	
				        				<span>-</span>
				        				<input type="number"  name="package_A_num" readonly class="z_w80 z_mr10" placeholder=""/><spap>币</spap>
				        			</div>
				        			<div class="z_inputbox z_mb10">
				        				<input type="number"  name="package_A_coin_price" onchange="price(this)" class="z_w80 z_mr10" placeholder=" "/><span>游币/局</span>
				        			</div>
		        				</div>
		        				<div class="col-sm-2 z_mt20">
		        					<a href="javascript:void(0);" class="btn z_bar-greed z_color_white z_w50 js-a">确定</a>
		        				</div>
		        			</div>

		        			<div class="z_box z_p10 js-z_box-a">
			        			@foreach($package as $k=>$v)
			        				@if($v->package_type=='A')
				        				<div class="z_border z_list z_pl10 z_mb10">
				        					<span class="z_off2 js-z_off2" data-id="{{$v->id}}"></span>
				        					<input type="checkbox"  name="package[]" @if($v->is_open=='1') checked @endif /> 
				        					<span class="inline_block">{{$v->coin_qty}}币 {{$v->num}}局</span>
				        					<span>{{$v->coin_price}}游币/局</span>
				        				</div>
			        				@endif
			        			@endforeach	
		        			</div>

        				</div>
					-->

        				<div class="z_w50p  boxsizing fl z_border_l">
        					<div class="z_inputbox z_mt10 z_mb10 z_pl20">
		        				<label class="z_mr10 z_fontb"><span class="z_color_red">*</span>套餐价格配置:</label>
		        			</div>
		        			<div class="clearfix z_p10">
		        				<div class="col-sm-10 z_border" style="height: 76px;">
									<div class="z_inputbox z_mb10 z_mt30 fl" >
										<input type="number" name="package_B_coin_price"  onchange="price(this)"  class="z_w80 z_mr10" placeholder=" "/><span>游币　</span>
									</div>
									<div class="z_inputbox z_mb10 z_mt30 fl" >
										<input type="number" name="package_B_coin"  onchange="price(this)"  class="z_w80 z_mr10" placeholder=" "/><span>局</span>
									</div>
		        				</div>
		        				<div class="col-sm-2 z_mt20">
		        					<a href="javascript:void(0);" class="btn z_bar-greed z_color_white z_w50 js-b">确定</a>
		        				</div>
		        			</div>
		        			<!--内容 盒子-->
		        			<div class="z_box z_p10 js-z_box-b">
			        			@foreach($package as $k=>$v)
			        				@if($v->package_type=='B')
				        				<div class="z_border z_list z_pl10 z_mb10">
				        					<span class="z_off2 js-z_off2" data-id="{{$v->id}}"></span>
				        					<input type="checkbox" name="package[]" @if($v->is_open=='1') checked @endif />
											{{--<span class="z_mr70">{{$v->coin_price}}游币/局</span>--}}
											<span class="z_mr70">{{$v->coin_qty}}游币</span>
											<span class="z_mr70">{{$v->num}}局</span>
				        				</div>
			        				@endif
			        			@endforeach
		        			</div>
		        			<!--内容 盒子 end-->
        				</div>
        			</div>
	        	</div>
        	</div>
        	
        	<!--奖品设置-->
        </div>
    </div>
    <script type="text/javascript" src="/admin/js/webuploader.min.js"></script>
    <script src="/admin/js/template.js"></script>
    <script>
	    var m_id = $('input[name="m_id"]').val();
		var d_id = $('input[name="d_id"]').val();
		var p_id = $('input[name="p_id"]').val();
		var _token=$('input[name="_token"]').val();
    </script>
    
    <!-- 机台相册上传 -->
    <script>
    del();
	    // 删除相册图片
	   function del(){
		   $('.btn-del-photo').click(function () {
		        $(this).parents('div.file-box').empty().remove();
		       var img_value = $(this).attr('value');
		      //入库
	            var data = {
	    	            'start':'del',
	                    'img_id':img_value,
	                    'p_id':p_id,
						'_token':_token
					};
				m_save(data)
		    });
		}

	    // 相册图片上传
        var galleryUploader = WebUploader.create({
            swf: '/admin/js/Uploader.swf',
            server: '/upload',
            pick: '#gallery-picker',
            resize: false,
            auto: true,
            duplicate :true  
        });
        var $gallery = $('.gallery-container');
        galleryUploader.on('uploadSuccess', function (file, response) {
            if(response.data.length>0){
            	//入库
                var data = {
                		'start':'add',
                        'img_id':response.data[0].id,
                        'p_id':p_id,
    					'_token':_token
    				};
    			m_save(data);

    			var msg={ 'img_id':response.data[0].id, 'img_path':response.data[0].absolute_path }
    			var html =template('file',msg);

    			//多图清空下是追加
				// $gallery.append(html);
			
			//暂时单图处理，上面有处理多图，如需要多图，注释下面这段代码
    			 $gallery.html(html);


    			//绑定事件
    	         del();
    	        
            }else{
            	 layer.msg(response.msg[0], {icon: 5});
            }
		
        });

    </script>
    
    <script id="file" type="text/html">
	    <div class="file-box">
		    <div class="file">
		         <span class="corner"></span>
		         <div class="image">
		               <input type="hidden" name="gallery_photos[]" value="<!--{img_id}-->">
		               <img alt="image" class="img-responsive" src="<!--{ img_path }-->">
		          </div>
		          <div class="file-name text-center">
		               <button class="btn btn-warning btn-circle btn-del-photo" type="button" value="<!--{img_id}-->" >
		               	<i class="fa fa-times"></i>
		                </button>
		           </div>
		     </div>
		</div>
    </script>
    
    <script id="list" type="text/html">
    	<div class="z_border z_list z_pl10 z_mb10">
			<span class="z_off2 js-z_off2" data-id="<!--{id}-->"></span>
			<input type="checkbox" name="package[]" checked />
			<span class="inline_block"><!--{coin_qty}-->币 <!--{num}-->局</span>
			<span><!--{coin_price}-->元/币</span>
		</div>
	</script>
	<script id="list2" type="text/html">
    	<div class="z_border z_list z_pl10 z_mb10">
			<span class="z_off2 js-z_off2" data-id="<!--{id}-->"></span>
			<input type="checkbox" name="package[]" checked />
			{{--<span class="z_mr70"><!--{coin_price}-->游币/局</span>--}}
			<span class="z_mr70"><!--{coin_price}-->游币</span>
			<span class="z_mr70"><!--{num}-->局</span>
		</div>
	</script>
	
	<!-- 编辑机台基本信息 -->
	<script>
		
//修改每局币数		
		function coin_sum(obj){
			var _var = $(obj).val();
			var data = {
					'machine':'1',
					'data':{'0':'coin_qty','1':_var},
					'm_id':p_id,
					'_token':_token
				};
			m_save(data);
		};
//修改机台名称
		function m_name(obj){
			var _var = $(obj).val();
			var data = {
					'machine':'1',
					'data':{'0':'name','1':_var},
					'm_id':m_id,
					'_token':_token
				};
			m_save(data);
		}

//支付方式
		$('input[name="pay_type[]"]').click(function(){
			var _val =[];
			 $('input[name="pay_type[]"]:checked').each(function(){
					var _value = $(this).val() ;	//
					 if(_value == 3){
						alert(' 该机台未开通支付功能，如要开通请联系世宇管理员！！！');
						return false;
					 }
					 _val.push(_value);
			});

			console.log(_val);
			if(_val.length>0){
				console.log(_val);
				if(_val.length==1){var arr_val = _val[0]; }
				if(_val.length==2){var arr_val = _val[0]+','+_val[1]; }
				var data = {
						'machine':'1',
						'data':{'0':'pay_type','1':arr_val},
						'm_id':p_id,
						'_token':_token
					};
				m_save(data);
			}  
			
		});
//是否禁用
		$('input[name="is_disable"]').click(function(){
			var _val = $('input[name="is_disable"]:checked').val();	//  .is('checked')
			console.log(_val);
			var data = {
					'machine':'1',
					'data':{'0':'is_disable','1':_val},
					'm_id':d_id,
					'_token':_token
				};
			m_save(data);
		});
		
		function m_save(data){
			$.post("{{ route('business.rj_machine_save') }}",data,function(res){
				console.log(res);
				if(res.code=='200'){
					layer.msg(res.msg, {icon: 1});
				}else{
                    layer.msg(res.msg, {icon: 5}, function(){
                        $("#dl").load(location.href+" #dl");
                    });
				}
			});
		}
	</script>
	
	<!-- 编辑套餐信息 -->
    <script >

		var _token = $('input[name="_token"]').val();
		function package2(){
			$('input[name="package[]"]').click(function(){
				if($(this).is(':checked')){
					var is_open = 1;
				}else{
					var is_open = 0;
				}
				
				var id = $(this).prev().attr('data-id');
				console.log(id);
				data = {'package_type':'1','is_open':is_open,'package_id':id,'_token':_token};
				$.post("{{ route('business.rj_machine_save') }}",data,function(res){
					console.log(res);
					if(res.code=='200'){
						layer.msg(res.msg, {icon: 1});
					}else{
						layer.msg(res.msg, {icon: 5});
					}
				});
			});
		}	
		package2();
		
    
		function num_coin_qty(obj){
			var coin_sum = $('input[name="coin_sum"]').val();
			var val = $(obj).val();
			if(val<0){
				layer.msg('不允许负数');
				 $(obj).val('');
				return false;
			}
			var _val = parseInt(val*coin_sum);

			console.log(coin_sum);
			console.log(val);
			console.log(_val);
			
			$('input[name="package_A_num"]').attr('value',_val);
		}
		function price(obj){
			var val = $(obj).val();
			if(val<0){
				layer.msg('不允许负数');
				 $(obj).val('');
				return false;
			}
		}
    
    	//删除2
		function off2(){
			$(".js-z_off2").click(function(){
				var obj = this;
				var id = $(obj).attr('data-id');
				console.log(id);
				data = {'package_type':'1','is_delete':'1','package_id':id,'_token':_token};
				$.post("{{ route('business.rj_machine_save') }}",data,function(res){
					console.log(res);
					if(res.code=='200'){
						$(obj).parent().remove();
						layer.msg(res.msg, {icon: 1});
					}else{
						layer.msg(res.msg, {icon: 5});
					}
				});
			});
		}
		off2();
		
    	// A 套餐价格配置
		$(".js-a").click(function(){
			
			var m_id = $('input[name="m_id"]').val();
			var _token = $('input[name="_token"]').val();
			var num=$('input[name="package_A_num"]').val();
			if(num==''){
				layer.msg('请填写参数', {icon: 5});
				return false;
			}
			
			var coin_price=$('input[name="package_A_coin_price"]').val();
			if(coin_price==''){
				layer.msg('请填写参数', {icon: 5});
				return false;
				}

			var package_A_coin_qty=$('input[name="package_A_coin_qty"]').val();

			var data = {
					'm_id':m_id,
					'coin_price':coin_price,
					'coin_qty':package_A_coin_qty,
					'num':num,
					'package_type':'A',
					'_token':_token
				};
			
			$.post("{{ route('business.rj_machine_save') }}",data,function(res){

				console.log(res);
				if(res.code=='200'){
					layer.msg(res.msg, {icon: 1});

					data.id = res.data;
				
					var html =template('list',data);
					console.log(html);
					
					$('.js-z_box-a').append(html);
					//绑定事件
					off2();
					package2();
	
					$('input[name="package_A_num"]').val('');
					$('input[name="package_A_coin_qty"]').val('');
					$('input[name="package_A_coin_price"]').val('');
				}else{
					layer.msg(res.msg, {icon: 5});
				}
			});
			
		});
		
    	 //B 币价格配置
		$(".js-b").click(function(){

            var m_id = $('input[name="m_id"]').val();
            var _token = $('input[name="_token"]').val();
            var coin_price = $('input[name="package_B_coin_price"]').val();
            var num = $('input[name="package_B_coin"]').val();
            if (coin_price == '') {
                layer.msg('请填写参数', {icon: 5});
                return false;
            }
            if(num==''){
                layer.msg('请填写参数', {icon: 5});
                return false;
            }

            var data = {
                'm_id':m_id,
                'coin_price':coin_price,
                'coin_qty':coin_price,
                'num':num,
                'package_type':'B',
                '_token':_token
            };


            $.post("{{ route('business.rj_machine_save') }}",data,function(res){
				console.log(res);

				if(res.code){
					layer.msg(res.msg, {icon: 1});

					data.id = res.data;
					
					var html =template('list2',data);
					console.log(html);
				//	return false;
				
					$('.js-z_box-b').append(html);
				
					//绑定事件
					off2();
					package2();

					$('input[name="package_B_coin_price"]').val('');
                    $('input[name="package_B_coin"]').val('');
				}else{
					layer.msg(res.msg, {icon: 5});
				}
			});
			
			
		});
    </script>
    
   
@endsection

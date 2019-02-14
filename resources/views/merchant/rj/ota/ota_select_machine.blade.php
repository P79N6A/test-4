@extends('admin.layouts.parent')
@section('page-title','选择机台')
@section('main')

 <link href="/admin/css/new-add.css" rel="stylesheet">
 <link href="/admin/css/plugins/datapicker/datepicker3.css" rel="stylesheet">
    <div class="row" >
          	<!--新加的-->
        	<div class="col-sm-12 ">
        		<div class="z_bar-gray z_border_t z_border_b clearfix">
        		 	<form id="form1" action="{{ route('business.ota_select_machine') }}" method="get">
        		 		<input type="hidden" name="id" value="{{$id}}">
		        		<div class="col-sm-8 m-b-xs z_pt10">
		        			<div class="clearfix z_mb10">
			        			<div class="z_inputbox fl z_mr30">
			        				<label>品牌名称:</label>
			        				<input type="text" name="brand_name" value="{{$brand_name}}"/>
			        			</div>
			        			<div class="z_inputbox fl z_mr30">
			        				<label>门店名称：</label>
			        				<input type="text" name="stores_name" value="{{$stores_name}}"/>
			        			</div>
			        		
		        			</div>
		        			<div class="clearfix z_mb10">
			        			<div class="z_inputbox fl z_mr30">
			        				<label>机台名称:</label>
			        				<input type="text" name="m_name" value="{{$m_name}}" />
			        			</div>
			        			<div class="z_inputbox fl z_mr30">
			        				<label>固件版本号</label>
			        				<input type="text" name="firmware_sn" value="{{$firmware_sn}}" />
			        			</div>
		        			</div>
		        			<div class="clearfix z_mb10">
			        			<div class="z_inputbox fl z_mr30">
			        				<label>机台型号:</label>
			        				<input type="text" name="model" value="{{$model}}" />
			        			</div>
			        			<div class="z_inputbox fl z_mr30">
			        				<label>硬件版本号</label>
			        				<input type="text" name="hardware_sn" value="{{$hardware_sn}}" />
			        			</div>
		        			</div>
		        		</div>
		        		<div class="col-sm-1 tc z_mt54" style="width: 80px;">
		        			<button class="btn z_bar-blue btn-xs z_color_white z_w60 z_mb5" >查询</button>
		        		</div>
	        		</form>
        		</div>
        	</div>
        	<!--机台详情-->
    
        <div class="col-sm-12">
        
         <!-- 门店资料开始 -->
            <div class="ibox float-e-margins">
				<div class="ibox-title clear">
					<h5 calss="fl">机台列表</h5>
					<button class="btn z_bar-blue btn-xs z_color_white fr" onclick="history.go(-1)" >返回上一页</button>
				</div>
               <form id="form2" action="{{ route('business.ota_select_machine',['id'=>$id]) }}" method="post">
               		{{ csrf_field() }}
	                <div class="ibox-content">
	                    <div class="table-responsive"  style="overflow-y: auto; display: block;   max-height:500px; ">
	                        <table class="table table-striped  "> 
	                            <thead>
		                            <tr>  
		                             	<th>选择机台</th>                      
		                                <th>序号(机台)</th>
		                                <th>品牌</th>
		                                <th style="width: 11%;">门店</th>
		                                <th>机台型号</th>
		                                <th>硬件编号</th>
		                                <th>机台名称</th>
		                                <th>固件版本</th>
		                                <th>硬件版本</th>
		                            </tr>
	                            </thead>
	                            <tbody>
	                            @if(!empty($ads))
	                              @foreach($ads as $k=>$dev)
	                           		<tr>
	                           		   <td><input type="checkbox" name="sel[]" <?php if(in_array($dev->m_id,$arr_machine_id)) echo 'checked'; ?> value="{{$dev->m_id}}"></td>
	                                   <td>{{$dev->m_id}}</td>
		                               <td>
			                               <?php  $brand_id = $dev->brand_id ? $dev->brand_id : 0 ;
					                               if(!empty($brand_id)){
						                               	$name = DB::table(config('tables.base').'.brand')->where('id', "$brand_id")->value('name');
						                               	echo $name?$name:'暂无';
					                               }else{
					                               		echo '暂无';
					                               }
					                               $brand_id = 0;
			                               ?>
		                               </td>
		                               <td>{{$dev->stores_name?$dev->stores_name:'暂无'}}</td>
		                               <td>{{$dev->model}}</td>
		                               <td>{{$dev->code}}</td>
		                               <td>{{$dev->m_name}}@if($dev->m_type == '1') (旧) @else (新) @endif</td>
		                               <td>{{$dev->firmware_sn}}</td>
		                               <td>{{$dev->hardware_sn}}</td>
	                                </tr>
	                              @endforeach
	                            @endif
	                            </tbody>
	                        </table>
	                    </div>
	                    <div class="z_title z_font16 z_border_b z_pb10">
	        				<div class="z_w50p clearfix">
		        				<span class="z_fontb">选择升级方式:</span>
	        				</div>
	        			</div>
	        			
	        			 <div class="z_title z_font16  z_pb10" style="margin-left:100px;">
	        				<div class="clearfix" >
		        				<div class="btn z_bar-gray z_color_white z_w100  z_mt20 update_type" data-value="0">立即升级</div>
	        				</div>
	        				<div class="clearfix">
		        				<div class="btn z_bar-blue z_color_white z_w100  z_mt10 update_type" data-value="1">开机升级</div>
	        				</div>
	        				<div class="clearfix">
		        				<div class="fl btn z_bar-blue z_color_white z_w100  z_mt10 update_type " data-value="2">定时升级</div> 
		        				<div class="fl">
			        				<span class="fl z_mt10 z_fw z_lineh34 z_ml16" >时间段：</span>
			        				<div class="fl z_mt10" style="display:none;" >
				        				2017-06-15 23:00:00至2017-06-16 23:00:00  <sapn class="set_time"> 更改</span> 
			        				</div>
			        				<div class="fl z_lineh30 col-sm-9 z_mt10">
	                                      <div class="input-daterange input-group">
	                                           <input type="text" class="form-control" name="start" id="get_start">
	                                           <span class="input-group-addon">至</span>
	                                           <input type="text" class="form-control" name="end" id="get_end">
	                                      </div>
	                                </div>
                                </div>
	        				</div>
	        				<input type="hidden" name="type" value="0"><!-- 升级方式隐藏框  -->
	        			</div>
	        			<div class="center z_mt40">
	        				<input type="hidden" name="id" value="{{$id}}"><!-- 版本id -->
	        				<button class="btn z_bar-blue z_color_white z_w100  z_mt10" type="submit">确认提交</button> 
	        			</div>
	                </div>
                </form>	
            </div>
            <!-- 门店资料结束 -->
            
            
        </div>
    </div>
    
    <script src="/admin/js/plugins/layer/laydate/laydate.js"></script>
    <script type="text/javascript">
	    var get_start = {
	        elem: "#get_start",
	        format: "YYYY-MM-DD hh:mm:ss",
	        min: laydate.now(),
	        max: "2099-06-16 23:59:59",
	        istime: true,
	        istoday: false,
	        choose: function(datas) {
	            get_end.min = datas;
	            get_end.start = datas
	        }
	    };
	    var get_end = {
	        elem: "#get_end",
	        format: "YYYY-MM-DD hh:mm:ss",
	        min: laydate.now(),
	        max: "2099-06-16 23:59:59",
	        istime: true,
	        istoday: false,
	        choose: function(datas) {
	            get_start.max = datas
	        }
	    };
	    laydate(get_start);
	    laydate(get_end);
    </script>
    <script>
		$(function(){
			$('.update_type').click(function(){
				
				$('.update_type').removeClass('z_bar-gra');
				$('.update_type').addClass('z_bar-blue');
				$(this).addClass('z_bar-gray');
				$(this).removeClass('z_bar-blue');
				var _value = $(this).attr('data-value');
				console.log(_value);
				$('input[name="type"]').attr('value' , _value);
				
			});
		})
		
			 // 提交表单
	    $('#form2').submit(function(e){
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

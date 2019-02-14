
@extends('admin.layouts.parent')
@section('page-title','云积分活动列表')
@section('main')

<link href="/admin/css/plugins/datapicker/datepicker3.css" rel="stylesheet">
<link href="/admin/css/new-add.css" rel="stylesheet">
<style>
	.css-display{ display:none; }
	.page-tabs-content{margin: 0 5px;  color: #fff;  background-color: #16cfe2;}
	.row{margin-top:20px;}
	 button, input, select, textarea{    height: 32px; border:0px; }
	 .z_inputbox label{ font-weight: bold; }
	 select.input-sm{ height: 34px;}
</style>


<?php $action = request()->route()->getAction();  ?>
<div class="row content-tabs " >
	<nav class="page-tabs J_menuTabs">
		<div class="page-tabs-content">
			<a href="{{ route('business.rj_yun_list') }}" class="<?php if($action['as']=='business.rj_yun_list'){echo 'active';} ?> J_menuTab"
			   data-id="home.html">@yield('page-title')</a>
		</div>
	</nav>
</div>

    <div class="row" >
    	{{ csrf_field() }}
    		<!--新加的-->
        	<div class="col-sm-12 ">
        		<div class="z_bar-gray z_border_t z_border_b clearfix" style="">
        		 	<form action="{{ route('business.rj_yun_list') }}" method="get">
		        		<div class="col-sm-6 m-b-xs z_pt10 z_ml26">
							<div class="clearfix z_mb10">
								<div class="z_inputbox fl z_mr30">
									<label>门店名称:</label>
									<input type="text" name="store_name" value="{{$store_name}}"/>
								</div>
							</div>
		        			<div class="clearfix z_mb10">
			        			<div class="z_inputbox fl z_mr30">
			        				<label>活动名称:</label>
			        				<input type="text" name="name" value="{{$name}}"/>
			        			</div>
		        			</div>
		        			<div class="clearfix z_mb10">
			        			<div class="z_inputbox fl ">
			        				<label style=" padding-top: 5px; ">活动时间:</label>
			        			</div>
			        			<div class="fl z_lineh30 col-sm-6 " style="padding-left:4px; top:-2px;">
                                      <div class="input-daterange input-group">
                                           <input type="text"  name="start_time" id="get_start" value="{{$start_time}}">
                                           <span class="input-group-addon">至</span>
                                           <input type="text"  name="end_time" id="get_end" value="{{$end_time}}" >
                                      </div>
                                </div>
		        			</div>

		        			 
		        		</div>
		        		<div class="col-sm-1 tc z_mt50" style="width: 80px;">
		        			<button class="btn z_bar-blue btn-xs z_color_white z_w60 z_mb5" type="submit">查询</button>
		        		</div>
	        		</form>
        		</div>
        	</div>
        	<!--机台详情-->
        	
	        <div class="col-sm-12">
	         <!-- 门店资料开始 -->
	            <div class="ibox float-e-margins">
	                <div class="ibox-title">
	                    <h5>活动列表</h5>
	                    <div class="ibox-tools">
	                        <a href="{{route('business.rj_yun_add')}}" class="btn btn-primary btn-xs">创建活动</a>
	                    </div>
	                </div>
	                <div class="ibox-content overflow_auto">
	
	                    <div class="table-responsive">
	                       <table class="table table-striped">
	                            <thead>
		                            <tr>
										<th>活动时间</th>
		                                <th>活动名称</th>
		                                <th>积分模式</th>
		                                <th>活动创建者</th>
										<th>参与门店</th>
		                                <th>机台名称</th>
		                                <th>门店数量</th>
										<th>机台数量</th>
										<th>活动状态</th>
										<th>操作</th>
		                            </tr>
	                            </thead>
	                            <tbody>
	                            @if(!empty($ads))
	                            	@foreach($ads as $key=>$val)
		                           		<tr>
											<td>{{date('Y-m-d H:i:s',$val->start_time)}}</td>
			                                <td>{{$val->name}}</td>
			                                <td>{{$val->game_type=='1'?'固定积分模式':'积分累计模式'}}</td>
											<td>{{$val->username}}</td>
											<td>{{$activity['data'][$key]['store_name']}}</td>
											<td>{{$activity['data'][$key]['machine_name']}}</td>
											<td>{{$activity['data'][$key]['store_num']}}</td>
											<td>{{$activity['data'][$key]['machine_num']}}</td>

											<td>
												{{$val->activity_type==1?'开启':'关闭'}}
			                                </td>
		                                    <td>
												<a href="{{ route('business.rj_yun_details',['id'=>$val->id,'type'=>0,'game_type'=>$val->game_type]) }}" class="btn btn-white btn-sm">活动详情</a>
												@if($username == $val->username)
													@if($val->game_type == 1)
														@if($val->lottery_type == 1)
															@if($val->execute_awards < $val->total_awards)
																<a href="{{ route('business.rj_yun_edit',['id'=>$val->id,'type'=>0,'game_type'=>$val->game_type]) }}" class="btn btn-white btn-sm">编辑</a>
															@endif
														@elseif($val->lottery_type == 2)
															@if(time() < $val->end_time)
																<a href="{{ route('business.rj_yun_edit',['id'=>$val->id,'type'=>0,'game_type'=>$val->game_type]) }}" class="btn btn-white btn-sm">编辑</a>
															@endif
														@endif
													@elseif($val->game_type == 2)
														@if($val->execute_awards < $val->total_awards)
															<a href="{{ route('business.rj_yun_edit',['id'=>$val->id,'type'=>0,'game_type'=>$val->game_type]) }}" class="btn btn-white btn-sm">编辑</a>
														@endif
													@endif
		                                   		<button class="btn btn-white btn-sm" onclick="del(this,{{$val->id}})" >删除</button>
												<button class="btn btn-white btn-sm" onclick="openORclose(this,{{$val->id}},{{$val->activity_type}})" > {{$val->activity_type==1?'关闭':'开启'}}  </button>
												@endif
											</td>
		                                </tr>
	                                @endforeach 
	                            @endif
	                            </tbody>
	                        </table>
	                    </div>
	                    <div class="text-right">
	                        @if(!empty($ads) && !empty($ads->links()))
	                            {{ $ads->appends([
	                            					'store_name'=>$store_name,
						                            'name'=>$name,
						                            'start_time'=>$start_time,
						                            'end_time'=>$end_time,
						                            'activity_type'=>$activity_type
					                            ])->links() }}
	                        @endif
	                    </div>
	                </div>
	            </div>
	            <!-- 门店资料结束 -->
	         
	            
	        </div>
    </div>
    
    <script src="/admin/js/plugins/layer/laydate/laydate.js"></script>
    <script type="text/javascript">
	    var get_start = {
	        elem: "#get_start",
	        format: "YYYY-MM-DD hh:mm:ss",
	//        min: laydate.now(),
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
	 //       min: laydate.now(),
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
		function del(obj,id){
			
			layer.confirm('确定要删除该活动吗？', {
				  btn: ['确定','取消'] 
				}, function(){
					var _token = $('input[name="_token"]').val();
					$.post('{{route("business.rj_yun_del")}}',{'id':id,'_token':_token},function(res){
						if(res.code=='200'){
							$(obj).parent().parent().remove();
							layer.msg(res.msg,{icon:1});
						}else{
							layer.msg(res.msg,{icon:5});
						}
					})
				}, function(){
					return true;
				});
			
		}

		function openORclose(obj,id,_type){
		    var str = '确定要开启该活动吗？';
            var _html = '<button class="btn btn-white btn-sm" onclick="openORclose(this,'+id+',1)" >关闭</button>';
            var _status = '开启';
		    if(_type=='1'){
                var str = '确定要关闭该活动吗？';
                var _html = '<button class="btn btn-white btn-sm" onclick="openORclose(this,'+id+',2)" >开启</button>';
                var _status = '关闭';
			}


            layer.confirm(str, {
                btn: ['确定','取消']
            }, function(){
                var _token = $('input[name="_token"]').val();
                $.post('{{route("business.rj_yun_activity_type")}}',{'id':id,'type':_type,'_token':_token},function(res){
                    if(res.code=='200'){

                        $(obj).parent().prev().html(_status);
                        $(obj).replaceWith(_html);
                        layer.msg(res.msg,{icon:1});
                    }else{
                        layer.msg(res.msg,{icon:5});
                    }
                })
            }, function(){
                return true;
            });
		}

    
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

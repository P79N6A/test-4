@extends('admin.layouts.parent')
@section('page-title','管理')
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

@extends(env('Merchant_view').'.layouts.menu')

    <div class="row" >
    {{ csrf_field() }}
    		<!--新加的-->
        	<div class="col-sm-12 ">
        		<div class="z_bar-gray z_border_t z_border_b clearfix" style="">
        		 	<form action="{{ route('business.rj_activity_list') }}" method="get">
		        		<div class="col-sm-6 m-b-xs z_pt10 z_ml26">
		        			<div class="clearfix z_mb10">
			        			<div class="z_inputbox fl z_mr30">
			        				<label>活动名称:</label>
			        				<input type="text" name="name" value="{{$name}}"/>
			        			</div>
		        			</div>
		        			<div class="clearfix z_mb10">
			        			<div class="z_inputbox fl z_mr30">
			        				<label>活动状态:</label>
			        				<select name="activity_type" class="z_w172 z_h24 input-sm form-control input-s-sm inline">
			        					<option value="0" @if($activity_type=='') selected @endif>全部</option>
			        					<option value="1" @if($activity_type=='1') selected @endif>未开始</option>
			        					<option value="2" @if($activity_type=='2') selected @endif>进行中</option>
			        					<option value="3" @if($activity_type=='3') selected @endif>已结束</option>
			        				</select>
			        			</div>
			        			
		        			</div>
		        			<!-- 
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
		        			 -->
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
	                        <a href="{{route('business.add_activity')}}" class="btn btn-primary btn-xs">创建活动</a>
	                    </div>
	                </div>
	                <div class="ibox-content overflow_auto">
	
	                    <div class="table-responsive">
	                       <table class="table table-striped">
	                            <thead>
		                            <tr>                     
		                                <th>活动名称</th>
		                                <th>游戏名称</th>
		                                <th>机台数量(台)</th>
										<th>活动模式</th>
		                                <th>活动状态</th>
		                                <th>操作</th>
		                            </tr>
	                            </thead>
	                            <tbody>
	                            @if(!empty($ads))
	                            	@foreach($ads as $val)
		                           		<tr>
			                               <td>{{$val->name}}</td>
			                               <td>{{$val->game_name}}</td>
			                               <td>
				                               <?php 
					                               $s_id_array = DB::table(config('tables.base').'.rj_activity_scduhele')->where('a_id',$val->id)->lists('id');
					                               if($s_id_array){
					                               	   $mid_list = DB::table(config('tables.base').'.rj_activity_scduhele_machine')->whereIn('s_id',$s_id_array)->groupBy('m_id')->lists('m_id');
					                               	   echo count($mid_list);
					                               }else{
					                               	  echo 0;
					                               }
				                               ?>
			                               </td>
											<td>
												{{$val->game_type==1?'单体':'团队'}}
											</td>
			                               <td>
				                               <?php 
				                               		$time = time();
				                               		if($time>($val->end_time)){
				                               			echo '已结束';
				                               		}elseif($time<($val->start_time)){
				                               			echo '未开始';
				                               		}elseif($time>($val->start_time)&&$time<($val->end_time)){
				                               			echo '进行中';
				                               		}
				                               ?>
			                               </td>
		                                   <td>

											   <a href="{{ route('business.activity_game_info',['id'=>$val->id,'type'=>1,'game_type'=>$val->game_type]) }}" class="btn btn-white btn-sm"><i class="fa fa-pencil"></i>活动详情</a>
		                                   		@if($time>($val->end_time))<!-- 已结束 -->
			                                      	<a href="{{ route('business.update_activity',['id'=>$val->id,'type'=>1,'game_type'=>$val->game_type]) }}" class="btn btn-white btn-sm"><i class="fa fa-pencil"></i>查看活动</a>
		                                   		@endif
		                                   
				                                @if($time>($val->start_time)&&$time<($val->end_time))  <!-- 进行中 -->
			                                       	<a href="{{ route('business.add_schedule',['id'=>$val->id,'type'=>0,'game_type'=>$val->game_type]) }}" class="btn btn-white btn-sm"><i class="fa fa-pencil"></i>编辑赛程</a>
			                                      	<a href="{{ route('business.update_activity',['id'=>$val->id,'type'=>0,'game_type'=>$val->game_type]) }}" class="btn btn-white btn-sm"><i class="fa fa-pencil"></i>编辑活动</a>
				                                @endif
				                                
				                                @if($time<($val->start_time))   <!-- 未开始 -->
				                                  	<a href="{{ route('business.add_schedule',['id'=>$val->id,'type'=>0,'game_type'=>$val->game_type]) }}" class="btn btn-white btn-sm"><i class="fa fa-pencil"></i>编辑赛程</a>
			                                      	<a href="{{ route('business.update_activity',['id'=>$val->id,'type'=>0,'game_type'=>$val->game_type]) }}" class="btn btn-white btn-sm"><i class="fa fa-pencil"></i>编辑活动</a>
				                                @endif
		                                   		<button class="btn btn-white btn-sm" onclick="del(this,{{$val->id}})" ><i class="fa fa-pencil" ></i>删除</button>  
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
					$.post('{{route("business.rj_activity_del")}}',{'id':id,'_token':_token},function(res){
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

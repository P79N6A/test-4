@extends('admin.layouts.parent')
@section('page-title','操作日志')
@section('main')

 <link href="/admin/css/new-add.css" rel="stylesheet">
    <div class="row" >
          	<!--新加的-->
        	<div class="col-sm-12 ">
        		<div class="z_bar-gray z_border_t z_border_b clearfix">
        		 	<form action="{{ route('business.rj_system_list') }}" method="get">
		        		<div class="col-sm-8 m-b-xs z_pt10">
		        			<div class="clearfix z_mb10">
		        				<div class="z_inputbox fl z_mr30">
			        				<label>行为名称：</label>
			        				<input type="text" name="action" value="{{$action}}"/>
			        			</div>
		        			</div>
		        			<div class="clearfix z_mb10">
			        			<div class="z_inputbox fl z_mr30">
			        				<label>执行时间：</label>
			        				<input type="text" name="execution_time" value="{{$execution_time}}"/>
			        			</div>
		        			</div>
		        			<div class="clearfix z_mb10">
			        			<div class="z_inputbox fl z_mr30">
			        				<label>执行者　：</label>
			        				<input type="text" name="execution_name" value="{{$execution_name}}" />
			        			</div>
		        			</div>
		        		</div>
		        		<div class="col-sm-1 tc z_mt54" style="width: 80px;">
		        			<button class="btn z_bar-blue btn-xs z_color_white z_w60 z_mb5">查询</button>
		        		</div>
	        		</form>
        		</div>
        	</div>
        	<!--机台详情-->
    
        <div class="col-sm-12">
        
         <!-- 门店资料开始 -->
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>日志列表</h5>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
	                            <tr> 
	                            	<th>日志ID</th>              
	                                <th>行为</th>
	                                <th>执行者</th>
	                                <th>执行时间</th>
	                                <th>操作</th>
	                            </tr>
                            </thead>
                            <tbody>
	                            @if(!empty($ads))
		                            @foreach($ads as $val)
		                               	<tr>
		                               	   <td>{{$val->id}}</td>
			                               <td>{{$val->action}}</td>
			                               <td>{{$val->execution_name}}</td>
			                               <td>{{$val->execution_time}} </td>
		                                   <td>
		                                   		<button class="btn btn-white btn-sm" onclick="del(this,{{$val->id}})"> 移除 </button>                                         
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
					                            'action'=>$action,
								    			'execution_name'=>$execution_name,
								    			'execution_time'=>$execution_time,
				                            ])->links() }}
                        @endif
                    </div>
                </div>
            </div>
            <!-- 门店资料结束 -->
            
            
        </div>
    </div>
	{{ csrf_field() }}
    <script>
    	var _token=$('input[name="_token"]').val();
    	function del(obj,id){
    		layer.confirm('您确定要删除日志？', {
    			  btn: ['确定','取消'] //按钮
    			}, function(){
    				data = {'_token':_token,'id':id}
    				$.post('{{route("business.rj_system_del")}}',data,function(res){
    					
    					if(res.code==200){
    						$(obj).parent().parent().remove();
    						layer.msg(res.msg,{icon: 1});
    					}else{
    						layer.msg(res.msg,{icon: 5});
    					}
    				});
    			}, function(){
    			 
    			});
        	
        }
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

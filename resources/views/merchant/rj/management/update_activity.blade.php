@extends('admin.layouts.parent')
@section('page-title','添加活动')
@section('main')

<style>
.css-display{ display:none; }
.page-tabs-content{margin: 0 5px;  color: #fff;  background-color: #16cfe2;}
.row{margin-top:20px;}
 .overflow_auto{overflow: hidden;  height: 400px; overflow-y: auto; }
 textarea{    padding: 6px 12px; }
 .form-control{text-align: left!important; width: 155px!important; padding-right:0px!important;}
</style>

<link href="/admin/css/plugins/datapicker/datepicker3.css" rel="stylesheet">
<link href="/admin/css/new-add.css" rel="stylesheet">

    <div class="row" >

	        <div class="col-sm-12">
		     	<form action="{{ route('business.update_activity',['id'=>$_GET['id']]) }}" method="post">
		            <div class="ibox float-e-margins">
		                <!--创建活动-->
			        	<div class="z_border_b z_font14  z_lineh40 z_mb10">
			        		@if($_GET['type']!=1)
				        		编辑活动
			        			<button class="btn z_bar-blue  z_color_white z_w80 fr z_mr20" type="submit" >保存</button>
			        		@else
			        			查看活动
			        		@endif
			        	</div>
			        	<!--创建活动 end-->
			        	<div class="col-sm-12">
			        	{{ csrf_field() }}
			        		<div class="z_form">
			        			<div class="z_inputbox z_mt10 z_mb10 clearfix">
			        				<label class="z_lineh34 fl z_mr15"><span class="z_color_red">*</span>活动名称：</label>
			        				<input type="text" name="name" value="{{$first->name}}" class="z_w335 form-control">
			        			</div>
			        			<div class="z_inputbox z_mb10 clearfix">
			        				<label class="fl z_lineh34"><span class="z_color_red">*</span>活动时间：</label>
			        				<div class="fl z_lineh30 col-sm-9"  >
			                              <div class="input-daterange input-group">
			                                   <input type="text" placeholder="开始时间" name="start_time"  id="get_start" value="{{date('Y-m-d H:i:s',$first->start_time)}}"  class="form-control" >
			                                   <span class="input-group-addon">至</span>
			                                   <input type="text"  placeholder="结束时间" name="end_time" id="get_end" value="{{date('Y-m-d H:i:s',$first->end_time)}}" class="form-control" >
			                              </div>
			                        </div>
			        			</div>
			        			<div class="z_inputbox z_mb10 clearfix">
			        				<label class="vt z_lineh34 fl z_mr15"><span class="z_color_red">*</span>活动规则：</label>
			        				<textarea class="z_w335 z_h100 z_lineh24 z_border" name="rule" >{{$first->rule}}</textarea>
			        			</div>
			        			<div class="z_inputbox z_mb10 clearfix">
			        				<label class="z_lineh34 fl z_mr15"><span class="z_color_red" name="title">*</span>活动标题：</label>
			        				<input type="text" class="z_w335 form-control" name="title" value="{{$first->title}}" >
			        			</div>
			        			<div class="z_inputbox z_mb10 clearfix">
			        				<label class="z_lineh34 fl z_mr15"><span class="z_color_red">*</span>游戏名称：</label>
			        				<input type="text" class="z_w335 form-control" name="game_name" value="{{$first->game_name}}" >
			        			</div>
			        			<div class="z_inputbox z_mb10 clearfix z_mr15">
			        				<label class="vt z_lineh34 fl z_mr15"><span class="z_color_red">*</span>游戏规则：</label>
			        				<textarea class="z_w335 z_h100 z_lineh24 z_border" name="game_rule">{{$first->game_rule}}</textarea>		
			        			</div>
								<div class="z_inputbox z_mb10 clearfix z_mr15">
									<label class="vt z_lineh34 fl z_mr15"><span class="z_color_red">*</span>游戏模式：</label>
									<div class="clearfix" >
										<ul class="z_uls clearfix">
											<input type="hidden"  name="game_type" value="{{ $first->game_type }}">
											@if($first->game_type==1)
												<li type="1" title="单机模式" style=" background-color:#a7a3a3; " >单机模式</li>
											@else
												<li type="2" title="团队模式" style=" background-color:#a7a3a3; " >团队模式</li>
											@endif
										</ul>
									</div>
								</div>
			        		</div>
			        	
			        	</div>
		            </div>
	            
	             </form>
	        </div>
    </div>
    
     
    <script src="/admin/js/plugins/layer/laydate/laydate.js"></script>
     <script src="/admin/js/template.js"></script>
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

		//提交数据
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

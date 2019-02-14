
@extends('admin.layouts.parent')
@section('page-title','怪兽猎人')
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
		.laydate_body .laydate_m { width: 101px; }
	</style>

	@extends(env('Merchant_view').'.layouts.ranking')

	<div class="row" >
	{{ csrf_field() }}
	<!--新加的-->
		<div class="col-sm-12 ">
			<div class="z_bar-gray z_border_t z_border_b clearfix" style="">

				{{--	<div class="col-sm-12">
						<div class="z_form posr">
							<form action="{{ route('admin.rj_ranking') }}" method="get">
								<div class="col-sm-5">
									<div class="z_inputbox z_mb10 z_mt30 z_mb30 clearfix">
										<label class="z_lineh34 fl z_mr15 z_w148 tr">按时间段:</label>
										<div class="input-daterange input-group">
											<input type="text" class="form-control" placeholder="开始时间" name="start2" id="get_start2">
											<span class="input-group-addon">至</span>
											<input type="text" class="form-control" placeholder="结束时间" name="end2" id="get_end2">
										</div>
									</div>
								</div>
								<div class="col-sm-1 tc z_mt30 z_mb30" style="width: 80px;">
									<button class="btn z_bar-blue btn-xs z_color_white z_w60 z_mb5" type="submit">查询</button>
								</div>
							</form>
						</div>
					</div>
--}}
			</div>


		</div>
		<!--机台详情-->

		<div class="col-sm-12">
			<!-- 门店资料开始 -->
			<div class="ibox float-e-margins">
				<div class="ibox-title">
					<h5 style="line-height: 35px; font-weight: bold;">怪兽猎人排名</h5>
					<div class="z_inputbox clearfix">
						<label class="z_lineh34 fl z_mr15 z_w148 tr">当前赛季设置:</label>
						<input type="text" class="z_w200 form-control fl" readonly name="saicheng" value="{{!empty($season)?$season['code']:''}}">
						<span class="z_lineh34 z_ml10 z_cursor z_color_blue fl js-setUp_btn">设置</span>
					</div>
				</div>
				<div class="ibox-content overflow_auto">

					<div class="table-responsive">
						<table class="table table-striped">
							<thead>
								<tr>
									<th>用户名</th>
									<th>用户ID</th>
									<th>总票数</th>
									<th>日排名</th>
									<th>月排名</th>
									<th>年排名</th>
									<th>赛季排名</th>
								</tr>
							</thead>
							<tbody>
								@if(!empty($res))
									@foreach($res as $key=>$val)
										<tr>
											<td>{{$val['nickname']?$val['nickname']:$val['mobile']}}</td>
											<td>{{$val['u_id']}}</td>
											<td>{{$val['ticket_num']}}</td>
											<td>{{$val['day']}}</td>
											<td>{{$val['month']}}</td>
											<td>{{$val['saiji']}}</td>
											<td>{{$val['yean']}}</td>
										</tr>
									@endforeach
								@endif
							</tbody>
						</table>
					</div>
					<div class="text-right">
						@if(!empty($ads) && !empty($ads->links()))
							{{ $ads->appends([ ])->links() }}
						@endif
					</div>
				</div>
			</div>
			<!-- 门店资料结束 -->


		</div>
	</div>

	<div class="z_tankuan z_border  z_w400 z_p10 hide js-setUp">

		<div class="z_inputbox z_mt10 z_mb10 clearfix">
			<label class="z_lineh34 fl z_mr15 z_w88 tr">赛季名称:</label>
			<input type="text" class="z_w200 form-control fl" name="code" value="{{!empty($season)?$season['code']:''}}">
		</div>
		<div class="z_inputbox z_mb10 clearfix">
			<label class="fl z_lineh34 z_w88 tr">赛季时间段:</label>
			<div class="fl z_lineh30 col-sm-9">
				<div class="input-daterange input-group">
					<input type="text" class="form-control" placeholder="开始时间" name="start" id="get_start" value="{{!empty($season)?date('Y-m-d',$season['start_time']):''}}">
					<span class="input-group-addon">至</span>
					<input type="text" class="form-control" placeholder="结束时间" name="end" id="get_end" value="{{!empty($season)?date('Y-m-d',$season['end_time']):''}}">
				</div>
			</div>
		</div>
		<div class="z_inputbox z_mb10 clearfix tc">
			<a href="javascript:void(0);" class="btn z_bar-gray  z_color_white z_w80 z_mt40 z_mr70 z_mb10 js-z_off3">取消</a>
			<a href="javascript:void(0);" class="btn z_bar-blue  z_color_white z_w80 z_mt40  z_mb10" onclick="seasonSize()">确定</a>
		</div>
	</div>
	<div class="shadow"></div>
	{{ csrf_field() }}
	<script src="/admin/js/plugins/layer/laydate/laydate.js"></script>
	<script>
		function seasonSize(){
            var _token=$('input[name="_token"]').val();
            var url = "{{route('business.seasonSize')}}";
            var model="{{$model}}";
            var start_time = $("input[name='start']").val();
            var end_time = $("input[name='end']").val();
            var code = $('input[name="code"]').val();
            var data = {'model':model,'code':code,'start_time':start_time,'end_time':end_time,'_token':_token};
            console.log(data);
            $.post(url,data,function(res){
                if(res.code=='200'){
                    layer.msg(res.msg, {icon: 1},function(){
						$('input[name=saicheng]').val(code);
                        $(".js-z_off3").parent().parent().addClass("hide");
                        $('.shadow').hide();
					});
                }else{
                    layer.msg(res.msg, {icon: 5});
                }
            });
		}
	</script>
	<script type="text/javascript">
        var get_start = {
            elem: "#get_start",
            format: "YYYY-MM-DD",
            istime: true,
            istoday: false,
            choose: function(datas) {
                get_end.min = datas;
                get_end.start = datas
            }
        };
        var get_end = {
            elem: "#get_end",
            format: "YYYY-MM-DD",
            istime: true,
            istoday: false,
            choose: function(datas) {
                get_start.min = datas
            }
        };
        laydate(get_start);
        laydate(get_end);
        //时间
        var get_start2 = {
            elem: "#get_start2",
            format: "YYYY-MM-DD",
            max: laydate.now(),
            istime: true,
            istoday: false,
            choose: function(datas) {
                get_end2.min = datas;
                get_end2.start = datas
            }
        };
        var get_end2 = {
            elem: "#get_end2",
            format: "YYYY-MM-DD",
            max: laydate.now(),
            istime: true,
            istoday: false,
            choose: function(datas){
                get_start2.max = datas;
            }
        };
        laydate(get_start2);
        laydate(get_end2);
	</script>

	<script>
        //关闭
        $(".js-z_off3").click(function(){
            $(this).parent().parent().addClass("hide");
            $('.shadow').hide();
        });
        //设置 js-setUp
        function setUp(){
            $(".js-setUp_btn").click(function(){
                var a = $(this);
                var y = a.offset().top;
                var x = a.offset().left + 30;
                $('.js-setUp').css({
                    top: y + 'px',
                    left: x + 'px',
					'z-index':1001
                });
                $(".js-setUp").removeClass("hide");
                $('.shadow').show();

            });
        };
        setUp();
	</script>
@endsection

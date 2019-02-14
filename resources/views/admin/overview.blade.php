@extends('admin.layouts.parent')
@section('page-title','概览')
@section('main')
 <script src="/admin/js/plugins/layer/laydate/laydate.js"></script>
 <!-- <link href="/admin/css/new-add.css" rel="stylesheet"> -->
  <style>
	  input, select, textarea{    height: 32px; border:1px bold; width:172px; padding: 0px 5px; }
	 .z_inputbox label{ font-weight: bold; }
	 select.input-sm{ height: 34px;}
 </style>
  <div class="wrapper wrapper-content">
        
        <div class="row">
            <div class="col-sm-3">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>收入</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins">{{$OrderIncome}}</h1>
                        <div class="stat-percent font-bold text-success"><!-- 98% <i class="fa fa-bolt"></i> -->
                        </div>
                        <small>总收入</small>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <!-- <span class="label label-success pull-right">月</span> -->
                        <h5>用户总游戏数</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins">{{$UserGameNum}}</h1>
                        <div class="stat-percent font-bold text-success">
                            <!-- 98% <i class="fa fa-bolt"></i> -->
                        </div>
                        <small>&nbsp</small>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <!-- <span class="label label-primary pull-right">今天</span> -->
                        <h5>门店数</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins">{{$StoreNum}}</h1>
                        <div class="stat-percent font-bold text-navy">
                            <!-- 44% <i class="fa fa-level-up"></i> -->
                        </div>
                        <small>全国门店数</small>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <!-- <span class="label label-danger pull-right">最近一个月</span> -->
                        <h5>机台数</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins">{{$MenchineNum}}</h1>
                        <div class="stat-percent font-bold text-danger">
                            <!-- 38% <i class="fa fa-level-down"></i> -->
                        </div>
                        <small>总机台数</small>
                    </div>
                </div>
            </div>
        </div> 
        <div class="row">
            <div class="col-sm-3">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <!-- <span class="label label-primary pull-right">今天</span> -->
                        <h5>用户</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins">{{$UserNum}}</h1>
                        <div class="stat-percent font-bold text-navy">
                            <!-- 44% <i class="fa fa-level-up"></i> -->
                        </div>
                        <small>总用户数</small>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <span class="label label-primary pull-right">今天</span>
                        <h5>店员今日开启机台数</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins">{{$TodayStartMenchineNum['total_num']}}</h1>
                        <div class="stat-percent font-bold @if ($TodayStartMenchineNum['trend'] == 'up') text-info @elseif($TodayStartMenchineNum['trend'] == 'down') text-danger @endif">{{$TodayStartMenchineNum['rate']}}%  @if ($TodayStartMenchineNum['trend'] == 'up')<i class="fa fa-level-up"></i> @elseif($TodayStartMenchineNum['trend'] == 'down')  <i class="fa fa-level-down"></i>@endif
                        </div>
                        <small>&nbsp</small>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <!-- <span class="label label-info pull-right">全年</span> -->
                        <span class="label label-primary pull-right">今天</span>
                        <h5>注册人数</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins">{{$TodayRegisterNum['total_num']}}</h1>
                        <div class="stat-percent font-bold @if ($TodayRegisterNum['trend'] == 'up') text-info @elseif($TodayRegisterNum['trend'] == 'down') text-danger @endif">{{$TodayRegisterNum['rate']}}%@if ($TodayRegisterNum['trend'] == 'up')<i class="fa fa-level-up"></i> @elseif($TodayRegisterNum['trend'] == 'down') <i class="fa fa-level-down"></i>@endif
                        </div>
                        <small>新注册人数</small>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <!-- <span class="label label-danger pull-right">最近一个月</span> -->
                        <span class="label label-primary pull-right">今天</span>
                        <h5>用户今日游戏数</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins">{{$TodayUserGameNum['total_times']}}</h1>
                        <div class="stat-percent font-bold  @if ($TodayUserGameNum['trend'] == 'up') text-info @elseif($TodayUserGameNum['trend'] == 'down') text-danger @endif">{{$TodayUserGameNum['rate']}}% @if ($TodayUserGameNum['trend'] == 'up')<i class="fa fa-level-up"></i> @elseif($TodayUserGameNum['trend'] == 'down') <i class="fa fa-level-down"></i>@endif
                        </div>
                        <small><?php echo date('Y-m-d') ?></small>
                    </div>
                </div>
            </div>
        </div>


 <div class="ibox-content">
    <div>
        <div class="z_inputbox fl z_mr30">
            <label>订单日期:</label>
            <input type="text" name="start_time" value=""  id="start_date" placeholder="开始时间"/>
            --
            <input type="text" name="end_time"   value=""  id="end_date" placeholder="结束时间" />

            <button type="submit" id="order_num_search" class="btn btn-sm btn-primary"> 搜索</button>
        </div>
    </div>
    <div class="row">
        <!-- 保留页面 -->
        <div class="col-sm-10 m-b-xs z_pt10">   
                <div class="clearfix z_mb10">
                    <div class="z_inputbox fl z_mr30">
                        <div class="clearfix z_mb10">
                            
                        </div>
                    </div>
                </div>
        </div>
        <div style="display: inline;">
        <div id="main" style="width: 50%;height:350px;float: left;"></div>
        <div id="main2" style="width: 50%; height: 350px;float: right;"></div>
        </div>
    </div>
 </div>

     <script src="/admin/js/echarts.min.js"></script>
    
     <script type="text/javascript">
        var d = new Date(),
        mon = parseInt(d.getMonth()+1),
        end_date = "";
        if(mon<10){
            end_date = d.getFullYear()+"-0"+(d.getMonth()+1)+"-"+d.getDate();
        }else{
            end_date = d.getFullYear()+"-"+(d.getMonth()+1)+"-"+d.getDate();
        }


        var oneweekdate = new Date(d-7*24*3600*1000);        
        var y = oneweekdate.getFullYear();        
        var m = oneweekdate.getMonth()+1;        
        var d = oneweekdate.getDate();        
        var start_date = y+'-'+m+'-'+d;

        $('#start_date').val(start_date);
        $('#end_date').val(end_date);        



        $.get('/overview-order-num', { start_date: start_date,end_date: end_date }, function(data){
            console.log(data);
            var myChart = echarts.init(document.getElementById('main'));
            var myChart2 = echarts.init(document.getElementById('main2'));

            // 指定图表的配置项和数据
            // var option = {
            //             title: {
            //               //  text: '下单数量'
            //             },
            //             tooltip: {},
            //             legend: {
            //                 data:['数量','金额']
            //             },
            //             xAxis: {
            //                 data: data.date
            //             },
            //             yAxis: {},
            //             series: [
            //             {
            //                 name: '数量',
            //                 type: 'bar',
            //                 data: data.num
            //             }
            //             ]
            //         };
            var option = {
                // color: ['#3398DB'],
                tooltip : {
                    trigger: 'axis',
                    axisPointer : {            // 坐标轴指示器，坐标轴触发有效
                        type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
                    }
                },
                legend: {
                            data:['数量']
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis : [
                    {
                        type : 'category',
                        data : data.date,
                        axisTick: {
                            alignWithLabel: true
                        }
                    }
                ],
                yAxis : [
                    {
                        type : 'value'
                    }
                ],
                series : [
                    {
                        name:'数量',
                        type:'bar',
                        data:data.num
                    }
                ]
            };
            // 使用刚指定的配置项和数据显示图表。
            myChart.setOption(option);
            option2 = {
                    color: ['#3398DB'],
                    tooltip : {
                        trigger: 'axis',
                        axisPointer : {            // 坐标轴指示器，坐标轴触发有效
                            type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
                        }
                    },
                    legend: {
                            data:['金额']
                    },
                    grid: {
                        left: '3%',
                        right: '4%',
                        bottom: '3%',
                        containLabel: true
                    },
                    xAxis : [
                        {
                            type : 'category',
                            data : data.date,
                            axisTick: {
                                alignWithLabel: true
                            }
                        }
                    ],
                    yAxis : [
                        {
                            type : 'value'
                        }
                    ],
                    series : [
                        {
                            name:'金额',
                            type:'bar',
                            data:data.total
                        }
                    ]
                };
            myChart2.setOption(option2);
        },'json');

        // 基于准备好的dom，初始化echarts实例
    
    </script>

     <script type="text/javascript">
     var start_date = {
         elem: "#start_date",
         format: "YYYY-MM-DD",
         max: laydate.now(),
         istime: true,
         istoday: false,
         choose: function(datas) {
             end_date.min = datas;
             end_date.start = datas
         }
     };
     var end_date = {
         elem: "#end_date",
         format: "YYYY-MM-DD",
         max: laydate.now(),
         istime: true,
         istoday: false,
         choose: function(datas) {
             start_date.max = datas
         }
     };
     laydate(start_date);
     laydate(end_date);
 </script>
 <script type="text/javascript">
 	$("#order_num_search").click(function(){
        if($('#start_date').val() == "" ||  $('#end_date').val() == ""){
            return false;
        }
  		$.get('/overview-order-num', { start_date: $('#start_date').val(),end_date: $('#end_date').val() }, function(data){
            console.log(data);
            var myChart = echarts.init(document.getElementById('main'));
            var myChart2 = echarts.init(document.getElementById('main2'));

            // 指定图表的配置项和数据
            // var option = {
            //             title: {
            //               //  text: '下单数量'
            //             },
            //             tooltip: {},
            //             legend: {
            //                 data:['数量','金额']
            //             },
            //             xAxis: {
            //                 data: data.date
            //             },
            //             yAxis: {},
            //             series: [
            //             {
            //                 name: '数量',
            //                 type: 'bar',
            //                 data: data.num
            //             }
            //             ]
            //         };
            var option = {
                // color: ['#3398DB'],
                tooltip : {
                    trigger: 'axis',
                    axisPointer : {            // 坐标轴指示器，坐标轴触发有效
                        type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
                    }
                },
                legend: {
                            data:['数量']
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis : [
                    {
                        type : 'category',
                        data : data.date,
                        axisTick: {
                            alignWithLabel: true
                        }
                    }
                ],
                yAxis : [
                    {
                        type : 'value'
                    }
                ],
                series : [
                    {
                        name:'数量',
                        type:'bar',
                        data:data.num
                    }
                ]
            };
            // 使用刚指定的配置项和数据显示图表。
            myChart.setOption(option);
            option2 = {
                    color: ['#3398DB'],
                    tooltip : {
                        trigger: 'axis',
                        axisPointer : {            // 坐标轴指示器，坐标轴触发有效
                            type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
                        }
                    },
                    legend: {
                            data:['金额']
                    },
                    grid: {
                        left: '3%',
                        right: '4%',
                        bottom: '3%',
                        containLabel: true
                    },
                    xAxis : [
                        {
                            type : 'category',
                            data : data.date,
                            axisTick: {
                                alignWithLabel: true
                            }
                        }
                    ],
                    yAxis : [
                        {
                            type : 'value'
                        }
                    ],
                    series : [
                        {
                            name:'金额',
                            type:'bar',
                            data:data.total
                        }
                    ]
                };
            myChart2.setOption(option2);
                },'json');
	});
 </script>
@endsection

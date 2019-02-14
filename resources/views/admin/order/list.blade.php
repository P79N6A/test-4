@extends('admin.layouts.parent')
@section('page-title','订单列表')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>订单列表</h5>
                    <div class="ibox-tools">
                        
                    </div>

                </div>

                <div class="ibox-content">
                    <form role="form" class="form-inline" action="{{ route('admin.order.list') }}">
                                <div class="form-group">
                                <input type="text" name="keyword" value="{{$keyword}}" placeholder="请输入订单号"
                               class="input-sm form-control">
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputPassword2">城市：</label>
                                    <select class="form-control" name="order_city" id="city">
                                        <option value="" >全部</option>
                                        @foreach($city_list as $val)
                                        <option value="{{$val->id}}" @if($order_city==$val->id) selected @endif>{{$val->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group stores" @if($order_city == "") style="display: none" @endif>
                                    <label for="exampleInputPassword2">适用门店:</label>
                                    <select class="form-control store" id="order_store" name="order_store">
                                        @if($order_city)
                                        <option class="store_option" value="">全部</option>
                                        @foreach($order_store_list as $val){
                                            <option class="store_option" value="{{$val->id}}" @if($order_store==$val->id) selected @endif>{{$val->name}}</option>
                                        }
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group courses" @if($order_store == "") style="display: none" @endif>
                                    <label for="exampleInputPassword2">课程名称:</label>
                                    <select class="form-control course" id="order_course" name="order_course">
                                        @if($order_store)
                                        <option class="course_option" value="">全部</option>
                                        @foreach($order_course_list as $val){
                                            <option class="course_option" value="{{$val->id}}" @if($order_course==$val->id) selected @endif>{{$val->name}}</option>
                                        }
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputPassword2">订单状态：</label>
                                    <select class="form-control" name="order_status">
                                    <option value="" >全部</option> 
                                    <option value="1" @if($order_status==1) selected @endif>已支付</option>
                                    <option value="2" @if($order_status==2) selected @endif>已取消</option>
                                    <option value="3" @if($order_status==3) selected @endif>已超时</option>
                                    <option value="4" @if($order_status==4) selected @endif>待支付</option>
                                </select>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputPassword2">订单日期：</label>
                                        <input type="text" class="input-sm form-control" name="start_time" value="{{$start_time}}" autocomplete="off"  id="start_date" placeholder="开始时间"/>
                                        --
                                        <input type="text" class="input-sm form-control" name="end_time"   value="{{$end_time}}" autocomplete="off"  id="end_date" placeholder="结束时间" />
                                </div>
                                
                                <button type="submit" class="btn btn-sm btn-primary"> 搜索</button>
                        </form>
                        <div style="float: right">
                        <a href="{{ route('admin.order.export',request()->input()) }}">导出列表</a>
                        </div>
                        <hr>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>订单号</th>
                                <th>订单价</th>
                                <th>订单类型</th>
                                <th>课程类型</th>
                                <th>城市</th>
                                <th>门店</th>
                                <th>购买课程</th>
                                <th>用户</th>
                                <th>订单状态</th>
                                <th>下单时间</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($list))
                                @foreach($list as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>{{ $item->order_num }}</td>
                                        <td>{{$item->total/100}}</td>
                                        <td>{{$type[$item->type]}}</td>
                                        <td>{{$courseType[$item->course->type_id]}}</td>
                                        <td>{{ $citys[$item->course->city_id] }}</td>
                                        <td>{{ $stores[$item->course->store_ids] }}</td>
                                        <td>
                                            @if($item->type == 1)
                                            VIP课程
                                            @elseif($item->type == 2)
                                            单次购买
                                            @else
                                            {{$item->course->name}}
                                            @endif
                                        </td>
                                        <td>{{$item->user->nickname or ''}}</td>
                                        <td>{{$status[$item->status]}}</td>
                                        <td>{{$item->created_at}}</td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="text-right">
                        @if(!empty($list) && !empty($list->links()))
                            {{ $list->appends([
                                                'keyword'=>$keyword,
                                                'order_store'=>$order_store,
                                                'order_course'=>$order_course,
                                                'order_course_list'=>$order_course_list,
                                                'order_status'=>$order_status,
                                                'order_city'=>$order_city,
                                                'order_store_list'=>$order_store_list,
                                                'end_time'=>$end_time,
                                                'start_time'=>$start_time
                                            ])->links() 
                            }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="/admin/js/plugins/layer/laydate/laydate.js"></script>
    <script type="text/javascript">
     var start_date = {
         elem: "#start_date",
         format: "YYYY-MM-DD hh:mm:ss",
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
         format: "YYYY-MM-DD hh:mm:ss",
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
    <script>
        $(function(){
            $('.btn-del-user').click(function(){
                var $this = $(this);
                layer.msg('您确定要删除该类型吗？',{
                    time:0,
                    btn:['是','否'],
                    yes:function(index){
                        layer.close(index);
                        youyibao.httpSend($this,'get',1);
                    }
                });
            });

            $("#city").change(function(){
                console.log($('#city').val());
                if($('#city').val() == ""){
                    $('.store_option').remove();
                }
                $.get('/business/store/list/json', { city_id: $('#city').val() }, function(data){
                    if(data.length == 0){
                        //layer.msg('该城市没有分店信息，请重新选择');
                        //$("#city").find("option[value='0']").attr("selected",true);
                        $('.stores').css('display','none');
                        $(".i-checks").remove();
                        return false;
                    }
                    $('.store_option').remove();
                    $('.course_option').remove();
                    $('.courses').css('display','none');
                    var html = '<option class="store_option" value="">全部</option>';
                    for(var i in data){
                        // html += `
                        //     <label class="checkbox-inline i-checks" for="store_[${data[i].id}]">
                        //         <input type="radio" id="store_[${data[i].id}]" name="store_ids[]" value="${data[i].id}">${data[i].name}
                        //     </label>
                        // `;
                        html += `
                            <option class="store_option" value="${data[i].id}">
                               ${data[i].name}
                            </option>
                        `;
                    }
                    console.log(html);

                    $('.store').append(html);
                   // $(".i-checks").iCheck({checkboxClass: "icheckbox_square-green", radioClass: "iradio_square-green",});
                    $('.stores').css('display','inline');
                },'json');
            });

            $("#order_store").change(function(){
                console.log($('#order_store').val());
                if($('#order_store').val() == ""){
                    $('.course_option').remove();
                }
                $.get('/business/course/list/json', { store_ids: $('#order_store').val() }, function(data){
                    console.log(data);
                    if(data.length == 0){
                        //layer.msg('该城市没有分店信息，请重新选择');
                        //$("#city").find("option[value='0']").attr("selected",true);
                        $('.courses').css('display','none');
                        $(".i-checks").remove();
                        return false;
                    }
                    $('.course_option').remove();
                    var html = '<option class="course_option" value="">全部</option>';
                    for(var i in data){
                        // html += `
                        //     <label class="checkbox-inline i-checks" for="store_[${data[i].id}]">
                        //         <input type="radio" id="store_[${data[i].id}]" name="store_ids[]" value="${data[i].id}">${data[i].name}
                        //     </label>
                        // `;
                        html += `
                            <option class="course_option" value="${data[i].id}">
                               ${data[i].name}
                            </option>
                        `;
                    }
                    console.log(html);

                    $('.course').append(html);
                   // $(".i-checks").iCheck({checkboxClass: "icheckbox_square-green", radioClass: "iradio_square-green",});
                    $('.courses').css('display','inline');
                },'json');
            });

        });
    </script>
@endsection

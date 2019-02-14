@extends('admin.layouts.parent')
@section('page-title','课程列表')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>课程列表</h5>
                    <div class="ibox-tools">
                        <a href="{{ route('admin.course.add') }}" class="btn btn-primary btn-xs">添加课程</a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        
                            <form role="form" class="form-inline" action="{{ route('admin.course.list') }}">
                                <div class="form-group">
                                <input type="text" name="keyword" value="{{$keyword}}" placeholder="请输入课程名称"
                               class="input-sm form-control">
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputPassword2">城市：</label>
                                    <select class="form-control" name="city" id="city">
                                    <option value="" >全部</option>
                                    @foreach($city_list as $val)
                                    <option value="{{$val->id}}" @if($city==$val->id) selected @endif>{{$val->name}}</option>
                                    @endforeach
                                </select>
                                </div>
                                 <div class="form-group stores" @if($city == "") style="display: none" @endif>
                                    <label for="exampleInputPassword2">适用门店:</label>
                                    <select class="form-control store" id="store" name="store">
                                        @if($city)
                                        <option class="store_option" value="">全部</option>
                                        @foreach($store_list as $val){
                                            <option class="store_option" value="{{$val->id}}" @if($store==$val->id) selected @endif>{{$val->name}}</option>
                                        }
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                                
                                <button type="submit" class="btn btn-sm btn-primary"> 搜索</button>
                            </form>
                       
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>城市</th>
                                <th>课程名称</th>
                                <th>图片</th>
                                <th>类型</th>
                                <th>价格（元）</th>
                                <th>限购次数</th>
                                <!-- <th>适合年龄</th> -->
                                <th>适用门店</th>
                                <th>课时</th>
                                <th>推荐</th>
                                <th>热门</th>
                                <th>停用</th>
                                <th>添加时间</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($list))
                                @foreach($list as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>{{ $item->city->name }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>
                                            @if(!empty($item->pic->path))
                                            <img src="{{$item->pic->path}}" alt="" width="80">
                                            @endif
                                        </td>
                                        <td>
                                            {{$item->type->name}}
                                        </td>
                                        <td>
                                            {{$item->price / 100}}
                                        </td>
                                        <td>
                                            @if(!empty($item->buy_limit))
                                                {{$item->buy_limit}}
                                            @else
                                                -
                                            @endif
                                            
                                        </td>
                                        <!-- <td>
                                            \{\{$item->suitable->name\}\}
                                        </td> -->
                                        <td>
                                            {{$item->stores}}
                                        </td>
                                        <td>
                                            {{$item->class_num}}
                                        </td>
                                        <td>
                                            @if($item->is_recommend == 1)<span class="label label-primary">是</span>
                                            @else <span class="label label-default">否</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->is_hot == 1)<span class="label label-primary">是</span>
                                            @else <span class="label label-default">否</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->disabled == 1)<span class="label label-primary">是</span>
                                            @else <span class="label label-default">否</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{$item->created_at}}
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button data-toggle="dropdown"
                                                        class="btn btn-primary btn-sm dropdown-toggle">操作
                                                    <span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a href="{{ route('admin.course.modify',['id'=>$item->id]) }}" class="btn btn-warning btn-sm">修改课程</a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ route('admin.course.class.add',['course_id'=>$item->id]) }}" class="btn btn-primary btn-sm">添加课时</a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ route('admin.course.class.list',['course_id'=>$item->id]) }}" class="btn btn-default btn-sm">课时列表</a>
                                                    </li>
                                                    <li>
                                                        <!-- <a href="{{ route('admin.course.delete',['id'=>$item->id]) }}" class="btn btn-danger btn-sm delete-course">删除课程</a> -->
                                                        <a href="javascript:;" data-url="{{ route('admin.course.delete') }}"
                                                        data-type="id" data-id="{{ $item->id }}"
                                                        class="btn btn-danger delete-course"><i class="fa fa-trash"></i> 删除
                                                        </a>
                                                    </li>                                                                                                                               </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="text-right">
                        @if(!empty($list) && !empty($list->links()))
                            {{ $list->appends([
                                                'city'=>$city,
                                                'store'=>$store,
                                                'keyword'=>$keyword
                                                ])
                                ->links() 
                            }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(function(){
            $('.btn-del-user').click(function(){
                var $this = $(this);
                layer.msg('您确定要删除该广告吗？',{
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
                    console.log(data);
                    if(data.length == 0){
                        //layer.msg('该城市没有分店信息，请重新选择');
                        //$("#city").find("option[value='0']").attr("selected",true);
                        $('.stores').css('display','none');
                        $(".i-checks").remove();
                        return false;
                    }

                    $('.store_option').remove();
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
        });

        $('.delete-course').click(function(){
            var $this = $(this);
            layer.msg('您确定要删除该课程吗？',{
                time:0,
                btn:['是','否'],
                yes:function(index){
                    layer.close(index);
                    youyibao.httpSend($this,'get',1);
                }
            });
        });
    </script>
@endsection

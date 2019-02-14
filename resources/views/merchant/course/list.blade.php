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
                                    <select class="form-control" name="city">
                                    <option value="" >全部</option>
                                    @foreach($city_list as $val)
                                    <option value="{{$val->id}}" @if($city==$val->id) selected @endif>{{$val->name}}</option>
                                    @endforeach
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
                                            <img src="{{$item->pic->path}}" alt="" width="120">
                                            @endif
                                        </td>
                                        <td>
                                            {{$item->type->name}}
                                        </td>
                                        <td>
                                            {{$item->price / 100}}
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
                                                        <a href="{{ route('admin.course.modify',['id'=>$item->id]) }}" class="btn btn-warning btn-sm">修改</a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ route('admin.course.class.add',['course_id'=>$item->id]) }}" class="btn btn-primary btn-sm">添加课时</a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ route('admin.course.class.list',['course_id'=>$item->id]) }}" class="btn btn-default btn-sm">课时列表</a>
                                                    </li>
                                                </ul>
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
                            {{ $list->links() }}
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

        });
    </script>
@endsection

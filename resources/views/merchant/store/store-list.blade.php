@extends('admin.layouts.parent')
@section('page-title','门店列表')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>门店列表</h5>
                    <div class="ibox-tools">
                        <a href="{{ route('admin.business.store.add') }}" class="btn btn-primary btn-xs">添加门店</a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        
                            <form role="form" class="form-inline" action="{{ route('admin.business.store.list') }}">
                                            <div class="form-group">
                                            <input type="text" name="keyword" value="{{$keyword}}" placeholder="请输入店名、地址或电话"
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
                                <th>店名</th>
                                <th>电话</th>
                                <th>品牌</th>
                                <th>商户</th>
                                <th>地址</th>
                                <th>门店课程</th>
                                <th>门店机台</th>
                                <th>门店营收</th>
                                <th>门店店员</th>
                                <th>添加时间</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($list))
                                @foreach($list as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>{{ $item->city->name or '未知' }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>
                                            {{$item->tel}}
                                        </td>
                                        <td>
                                            {{$item->brand->name}}
                                        </td>
                                        <td>
                                            {{$item->brand->belongs->nickname}}
                                        </td>
                                        <td>
                                            {{$item->address}}
                                        </td>
                                        <td>
                                        <a href="{{ route('business.course.list',['store_id'=>$item->id]) }}">查看</a>
                                        </td>
                                        <td>
                                            <a href="{{ route('business.equipment.list',['store_id'=>$item->id]) }}">查看</a>
                                        </td>
                                        <td>
                                            -
                                        </td>
                                        <td>
                                        <a href="{{ route('business.member-list',['store_id'=>$item->id]) }}">查看</a>
                                        </td>
                                        <td>
                                            {{$item->created_at}}
                                        </td>
                                        <td>
                                            <a href="{{ route('merchant.edit-store',['id'=>$item->id]) }}" class="btn btn-warning btn-sm">
                                               <i class="fa fa-pencil"></i> 修改
                                            </a>
                                            <a class="btn btn-primary btn-sm" href="{{ route('business.store.bindStaff',['id'=>$item->id]) }}">
                                                <i class="fa fa-users"></i> 绑定店员
                                            </a>
                                            <!-- <a href="javascript:;" data-url="{{ route('admin.business.store.delete') }}"
                                               data-type="id" data-id="{{ $item->id }}"
                                               class="btn btn-white btn-sm btn-del-user"><i class="fa fa-trash"></i> 删除
                                            </a> -->
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

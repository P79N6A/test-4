@extends('admin.layouts.parent')
@section('page-title','游戏列表')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>游戏列表</h5>
                    <div class="ibox-tools">
                        <a href="{{ route('admin.equipment.games.add') }}" class="btn btn-primary btn-xs">添加游戏</a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        
                           
                       
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>门店名称</th>
                                <th>机台名称</th>
                                <th>游戏名称</th>
                                <th>机台型号</th>
                                <th>游戏编号</th>
                                <!-- <th>状态</th> -->
                                <th>停用</th>
                                <th>添加时间</th>
                                <th>更新时间</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($list))
                                @foreach($list as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>{{ isset($item->store->name) ? $item->store->name : '-' }}</td>
                                        <td>{{ $item->equipment->name }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->equipment->model }}</td>
                                        <td>{{ $item->model }}</td>
                                        <!-- <td>
                                            @if($item->online == 1)<span class="label label-primary">在线</span>
                                            @else <span class="label label-danger">离线</span>
                                            @endif
                                        </td> -->
                                        <td>
                                            @if($item->disabled == 1)<span class="label label-danger">是</span>
                                            @else <span class="label label-primary">否</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{$item->created_at}}
                                        </td>
                                        <td>
                                            {{$item->updated_at}}
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button data-toggle="dropdown"
                                                        class="btn btn-primary btn-sm dropdown-toggle">操作
                                                    <span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a href="{{ route('admin.equipment.games.modify',['id'=>$item->id]) }}" class="btn btn-warning btn-sm">修改</a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ route('admin.equipment.games.level',['id'=>$item->id]) }}" class="btn btn-danger btn-sm">设置游戏难易度</a>
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
                            {{ $list->appends([
                                                'city'=>$city,
                                                'store'=>$store,
                                                'keyword'=>$keyword,
                                                'store_list'=>$store_list
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
                    // console.log(html);

                    $('.store').append(html);
                   // $(".i-checks").iCheck({checkboxClass: "icheckbox_square-green", radioClass: "iradio_square-green",});
                    $('.stores').css('display','inline');
                },'json');
            });
    </script>
@endsection

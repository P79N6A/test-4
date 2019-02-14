@extends('admin.layouts.parent')
@section('page-title','商家菜单列表')
@section('main')

    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>@yield('page-title')</h5>
                    <a href="{{ route('admin.add-bus-menu') }}" class="btn btn-xs btn-primary pull-right">创建菜单</a>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-sm-12">
                            <table class="table table-responsive table-hover">
                                <thead>
                                <tr>
                                    <th>路由操作</th>
                                    <th>名称</th>
                                    <th>描述</th>
                                    <th>显示状态</th>
                                    <th>排序</th>
                                    <th>状态</th>
                                    <th>是否可分配给子账号</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(!empty($menus))
                                    @foreach($menus as $menu)
                                        <tr>
                                            <td>{{ $menu['action'] }}</td>
                                            <td>{{ $menu['name'] }}</td>
                                            <td>{{ $menu['description'] }}</td>
                                            <td>
                                                @if($menu['display'] == 1)<span class="label label-primary">显示</span>
                                                @elseif($menu['display'] == 0)<span class="label">隐藏</span>
                                                @endif
                                            </td>
                                            <td>{{ $menu['display_order'] }}</td>
                                            <td>
                                                @if($menu['status'] == 1)<span class="label label-primary">启用</span>
                                                @elseif($menu['status'] == 0)<span class="label label-danger">禁用</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($menu['assignable'] == 1)<span class="label label-primary">是</span>
                                                @elseif($menu['assignable'] == 0)<span
                                                        class="label label-danger">否</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <button data-toggle="dropdown"
                                                            class="btn btn-sm btn-success dropdown-toggle">操作 <span
                                                                class="caret"></span>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a href="{{ route('admin.edit-bus-menu',['id'=>$menu['id']]) }}">修改</a>
                                                        </li>
                                                        <li>
                                                            <a class="delete-bue-menu" href="javascript:void(0);"
                                                               data-url="{{ route('admin.delete-bus-menu',['id'=>$menu['id']]) }}">删除</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                        @if(!empty($menu['children']))
                                            @foreach($menu['children'] as $tchild)
                                                <tr>
                                                    <td>
                                                        &nbsp;&nbsp;&nbsp;&nbsp;|--&nbsp;&nbsp;{{ $tchild['action'] }}</td>
                                                    <td>
                                                        &nbsp;&nbsp;&nbsp;&nbsp;|--&nbsp;&nbsp;{{ $tchild['name'] }}</td>
                                                    <td>{{ $tchild['description'] }}</td>
                                                    <td>
                                                        @if($tchild['display'] == 1)<span
                                                                class="label label-primary">显示</span>
                                                        @elseif($tchild['display'] == 0)<span class="label">隐藏</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $tchild['display_order'] }}</td>
                                                    <td>
                                                        @if($tchild['status'] == 1)<span
                                                                class="label label-primary">启用</span>
                                                        @elseif($tchild['status'] == 0)<span class="label label-danger">禁用</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($tchild['assignable'] == 1)<span
                                                                class="label label-primary">是</span>
                                                        @elseif($tchild['assignable'] == 0)<span
                                                                class="label label-danger">否</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <button data-toggle="dropdown"
                                                                    class="btn btn-sm btn-success dropdown-toggle">操作 <span
                                                                        class="caret"></span>
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                <li>
                                                                    <a href="{{ route('admin.edit-bus-menu',['id'=>$tchild['id']]) }}">修改</a>
                                                                </li>
                                                                <li>
                                                                    <a class="delete-bue-menu"
                                                                       href="javascript:void(0);"
                                                                       data-url="{{ route('admin.delete-bus-menu',['id'=>$tchild['id']]) }}">删除</a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @if(!empty($tchild['children']))
                                                    @foreach($tchild['children'] as $third)
                                                        <tr>
                                                            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|--&nbsp;&nbsp;{{ $third['action'] }}</td>
                                                            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|--&nbsp;&nbsp;{{ $third['name'] }}</td>
                                                            <td>{{ $third['description'] }}</td>
                                                            <td>
                                                                @if($third['display'] == 1)<span
                                                                        class="label label-primary">显示</span>
                                                                @elseif($third['display'] == 0)<span
                                                                        class="label">隐藏</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $third['display_order'] }}</td>
                                                            <td>
                                                                @if($third['status'] == 1)<span
                                                                        class="label label-primary">启用</span>
                                                                @elseif($third['status'] == 0)<span
                                                                        class="label label-danger">禁用</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if($third['assignable'] == 1)<span
                                                                        class="label label-primary">是</span>
                                                                @elseif($third['assignable'] == 0)<span
                                                                        class="label label-danger">否</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <div class="btn-group">
                                                                    <button data-toggle="dropdown"
                                                                            class="btn btn-sm btn-success dropdown-toggle">操作
                                                                        <span class="caret"></span>
                                                                    </button>
                                                                    <ul class="dropdown-menu">
                                                                        <li>
                                                                            <a href="{{ route('admin.edit-bus-menu',['id'=>$third['id']]) }}">修改</a>
                                                                        </li>
                                                                        <li>
                                                                            <a class="delete-bue-menu"
                                                                               href="javascript:void(0);"
                                                                               data-url="{{ route('admin.delete-bus-menu',['id'=>$third['id']]) }}">删除</a>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        @endif
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(function () {
            $('.delete-bue-menu').click(function () {
                var $this = $(this);
                layer.msg('删除后可能会影响系统正常运行，您确定要删除该菜单吗？', {
                    time: 0,
                    btn: ['是', '否'],
                    yes: function (index) {
                        layer.close(index);
                        youyibao.httpSend($this, 'get', 1);
                    }
                });
            });
        });
    </script>
@endsection
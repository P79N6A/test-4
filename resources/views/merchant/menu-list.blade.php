<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="favicon.ico"> <link href="merchant/css/bootstrap.min14ed.css?v=3.3.6" rel="stylesheet">
    <link href="merchant/css/font-awesome.min93e3.css?v=4.4.0" rel="stylesheet">
    <!-- Data Tables -->
    <link href="merchant/css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">
    <link href="merchant/css/animate.min.css" rel="stylesheet">
    <link href="merchant/css/style.min862f.css?v=4.1.0" rel="stylesheet">
</head>
<body>
    <div class="ibox">
        <form action="/order-menu" method="post" class="form-order-menu">
            <table class="table table-striped table-bordered table-hover dataTables-example">
                <thead>
                <tr>
                    <th>名称</th>
                    <th>路由</th>
                    <th>描述</th>
                    <th>是否显示</th>
                    <th>显示顺序</th>
                    <th>状态</th>
                    <th>添加时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td colspan="7"></td>
                    <td><button type="button" class="btn-order-menu btn btn-sm btn-success">排序</button></td>
                </tr>
                @foreach($menus as $menu)
                    <tr>
                        <td>{{ $menu['name'] }}</td>
                        <td>{{ $menu['action'] }}</td>
                        <td>{{ $menu['description'] }}</td>
                        <td> @if($menu['display'] == 0) 否 @else 是 @endif </td>
                        <td><input class="input-small" name="display_order[{{ $menu['id'] }}]" value="{{ $menu['display_order'] }}"></td>
                        <td> @if($menu['status'] == 0) 禁用 @else 启用 @endif </td>
                        <td>{{ date('Y-m-d H:i:s',$menu['addtime']) }}</td>
                        <td>
                            <a href="/edit-menu?id={{ $menu['id'] }}" class="btn btn-white btn-sm"><i class="fa fa-edit"></i>修改</a>
                            <a href='javascript:void(0)' data-url="/delete-menu" data-type="id" data-id="{{ $menu['id'] }}" class="del-menu btn btn-white btn-sm"><i class="fa fa-trash"></i>删除</a>
                        </td>
                    </tr>

                    @if(!empty($menu['children']))
                        @foreach($menu['children'] as $child)
                            <tr>
                                <td>&nbsp;&nbsp;&nbsp;&nbsp;|--{{ $child['name'] }}</td>
                                <td>{{ $child['action'] }}</td>
                                <td>{{ $child['description'] }}</td>
                                <td> @if($child['display'] == 0) 否 @else 是 @endif </td>
                                <td><input class="input-small" name="display_order[{{ $child['id'] }}]" value="{{ $child['display_order'] }}"></td>
                                <td> @if($child['status'] == 0) 禁用 @else 启用 @endif </td>
                                <td>{{ date('Y-m-d H:i:s',$child['addtime']) }}</td>
                                <td>
                                    <a href="/edit-menu?id={{ $child['id'] }}" class="btn btn-white btn-sm"><i class="fa fa-edit"></i>修改</a>
                                    <a href='javascript:void(0)' data-url="/delete-menu" data-type="id" data-id="{{ $child['id'] }}" class="del-menu btn btn-white btn-sm"><i class="fa fa-trash"></i>删除</a>
                                </td>
                            </tr>

                            @if(!empty($child['children']))
                                @foreach($child['children'] as $tchild)
                                    <tr>
                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|--{{ $tchild['name'] }}</td>
                                        <td>{{ $tchild['action'] }}</td>
                                        <td>{{ $tchild['description'] }}</td>
                                        <td> @if($tchild['display'] == 0) 否 @else 是 @endif </td>
                                        <td><input class="input-small" name="display_order[{{ $tchild['id'] }}]" value="{{ $tchild['display_order'] }}"></td>
                                        <td> @if($tchild['status'] == 0) 禁用 @else 启用 @endif </td>
                                        <td>{{ date('Y-m-d H:i:s',$tchild['addtime']) }}</td>
                                        <td>
                                            <a href="/edit-menu?id={{ $tchild['id'] }}" class="btn btn-white btn-sm"><i class="fa fa-edit"></i>修改</a>
                                            <a href='javascript:void(0)' data-url="/delete-menu" data-type="id" data-id="{{ $tchild['id'] }}" class="del-menu btn btn-white btn-sm"><i class="fa fa-trash"></i>删除</a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        @endforeach
                    @endif
                @endforeach
                <tr>
                    <td colspan="7"></td>
                    <td><button type="button" class="btn-order-menu btn btn-sm btn-success">排序</button></td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</body>
</html>
<script type="text/javascript" src="merchant/js/jquery.min.js"></script>
<script type="text/javascript" src="merchant/js/youyibao.js"></script>
<script type="text/javascript" src="/merchant/layer/layer/layer.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('a.del-menu').click(function(){
            youyibao.httpSend($(this),'get',1);
        });

        $('.btn-order-menu').click(function(){
            youyibao.httpSend($('form.form-order-menu'),'post',1);
        });
    });
</script>
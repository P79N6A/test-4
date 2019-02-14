<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>权限列表</title>
    <link href="merchant/css/bootstrap.min14ed.css?v=3.3.6" rel="stylesheet">
    <link href="merchant/css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">
    <link href="merchant/css/animate.min.css" rel="stylesheet">
    <link href="merchant/css/style.min862f.css?v=4.1.0" rel="stylesheet">
</head>
<form method="post" class="form-allo-perm" action="/allocate-permission">
    <input type="hidden" name="role_id" value="{{ $role_id }}">
    <div class="row">
        <div class="col-sm-push-1">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active tabs"><a href="#">网站权限</a></li>
                <li role="presentation" class="tabs"><a href="#">APP权限</a></li>
            </ul>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-offset-0">
            <div class="ibox content-tab">
                <table class="table table-striped table-bordered table-hover dataTables-example">
                    <thead>
                    <tr>
                        <th>权限名称</th>
                        <th>权限描述</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($perms as $perm)
                        <tr>
                            <td> {{ $perm['name'] }}</td>
                            <td> {{ $perm['description'] }}</td>
                            <td>
                                <input type="checkbox" name="menus[]" value="{{ $perm['action'] }}" @if(in_array($perm['action'],$myPerms)) checked @endif >
                            </td>
                        </tr>

                        @if(!empty($perm['children']))
                            @foreach($perm['children'] as $child)
                                <tr>
                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;|-- {{ $child['name'] }}</td>
                                    <td> {{ $child['description'] }}</td>
                                    <td>
                                        <input type="checkbox" name="menus[]" value="{{ $child['action'] }}" @if(in_array($child['action'],$myPerms)) checked @endif >
                                    </td>
                                </tr>

                                @if(!empty($child['children']))
                                    @foreach($child['children'] as $tchild)
                                        <tr>
                                            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|-- {{ $tchild['name'] }}</td>
                                            <td> {{ $tchild['description'] }}</td>
                                            <td>
                                                <input type="checkbox" name="menus[]" value="{{ $tchild['action'] }}" @if(in_array($tchild['action'],$myPerms)) checked @endif >
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif

                            @endforeach
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="ibox content-tab hide">
                <table class="table table-striped table-bordered table-hover dataTables-example">
                    <thead>
                        <tr>
                            <th>权限名称</th>
                            <th>权限描述</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($appPerms as $appPerm)
                        <tr>
                            <td>{{ $appPerm->display_name }}</td>
                            <td>{{ $appPerm->description }}</td>
                            <td>
                                <input type="checkbox" name="app-perms[]" value="{{ $appPerm->id }}" @if(in_array($appPerm->id,$allocatedAppPerms)) checked @endif >
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-1 col-sm-push-11">
            <button type="button" class="btn-allo-perm">提交</button>
        </div>
    </div>
</form>
</body>
</html>
<script type="text/javascript" src="merchant/js/jquery.min.js"></script>
<script type="text/javascript" src="merchant/js/youyibao.js"></script>
<script type="text/javascript" src="/merchant/layer/layer/layer.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('.btn-allo-perm').click(function(){
            youyibao.httpSend($('.form-allo-perm'),'post',1);
        });

        $('.tabs').click(function(){
            $('.tabs').removeClass('active').eq($(this).index()).addClass('active');
            $('.content-tab').addClass('hide').eq($(this).index()).removeClass('hide').show();
        });

    });
</script>

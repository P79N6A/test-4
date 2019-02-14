@extends('business.layouts.frame-parent')
@section('page-title','分配功能权限')
@section('main')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>功能权限</h5>
                    </div>
                    <div class="ibox-content">
                        <h2>{{ $role->name }}<br>
                        </h2>
                        <p>{{ $role->description }}</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <form action="allocate-permission" method="post" class="form-allo-perm">
                    <input type="hidden" name="role_id" value="{{ $role->id }}">
                    <div class="panel">
                        <div class="panel-heading">
                            <div class="panel-title m-b-md">
                                <h4>详细权限</h4>
                            </div>
                            <div class="panel-options">
                                <ul class="nav nav-tabs">
                                    <li class="active">
                                        <a data-toggle="tab" href="tabs_panels.html#tab-4" aria-expanded="true"><i class="fa fa-desktop"></i>电脑端</a>
                                    </li>
                                    <li class="">
                                        <a data-toggle="tab" href="tabs_panels.html#tab-5" aria-expanded="false"><i class="fa fa-mobile-phone"></i>手机端</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="tab-content">
                                <div id="tab-4" class="tab-pane active">
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
                                <div id="tab-5" class="tab-pane">
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
                            <div class="hr-line-dashed"></div>
                            <button class="btn btn-sm btn-primary btn-allo-perm" type="button">保存</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
@endsection

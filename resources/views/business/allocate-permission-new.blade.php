@extends('business.layouts.frame-parent')
@section('page-title','分配功能权限')
@section('main')
    <link rel="stylesheet" href="/jqwidgets-ver4.4.0/jqwidgets/styles/jqx.base.css">
    <link rel="stylesheet" href="/jqwidgets-ver4.4.0/jqwidgets/styles/jqx.bootstrap.css">
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
                                    <div id='jqxTree'>
                                        <ul>
                                            @if(!empty($perms))
                                                @foreach($perms as $perm)
                                                    @if(empty($perm['children']))
                                                        <li @if(in_array($perm['action'],$myPerms)) item-checked='true' item-expanded="false" @endif data-action="{{ $perm['action'] }}">
                                                            @if(in_array($perm['action'],$myPerms)) <input type="hidden" name="menus[]" value="{{ $perm['action'] }}"> @endif
                                                            {{ $perm['name'] }}
                                                        </li>
                                                    @else
                                                        <li @if(in_array($perm['action'],$myPerms)) item-selected='true' @endif item-expanded='true' data-action="{{ $perm['action'] }}">
                                                            @if(in_array($perm['action'],$myPerms)) <input type="hidden" name="menus[]" value="{{ $perm['action'] }}"> @endif
                                                            <a>{{ $perm['name'] }}</a>
                                                            <ul>
                                                                @foreach($perm['children'] as $child)
                                                                    @if(empty($child['children']))
                                                                        <li @if(in_array($child['action'],$myPerms)) item-checked='true' @endif data-action="{{ $child['action'] }}" >
                                                                            @if(in_array($child['action'],$myPerms)) <input type="hidden" name="menus[]" value="{{ $child['action'] }}"> @endif
                                                                            {{ $child['name'] }}
                                                                        </li>
                                                                    @else
                                                                        <li @if(in_array($child['action'],$myPerms)) item-selected='true' @endif data-action="{{ $child['action'] }}" >
                                                                            @if(in_array($child['action'],$myPerms)) <input type="hidden" name="menus[]" value="{{ $child['action'] }}"> @endif
                                                                            <a>{{ $child['name'] }}</a>
                                                                            <ul>
                                                                                @foreach($child['children'] as $tchild)
                                                                                    <li @if(in_array($tchild['action'],$myPerms)) item-checked='true' @endif data-action="{{ $tchild['action'] }}" >
                                                                                        @if(in_array($tchild['action'],$myPerms)) <input type="hidden" name="menus[]" value="{{ $tchild['action'] }}"> @endif
                                                                                        {{ $tchild['name'] }}
                                                                                    </li>
                                                                                @endforeach
                                                                            </ul>
                                                                        </li>
                                                                    @endif
                                                                @endforeach
                                                            </ul>
                                                        </li>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </ul>
                                    </div>
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
    <script type="text/javascript" src="/jqwidgets-ver4.4.0/jqwidgets/jqxcore.js"></script>
    <script type="text/javascript" src="/jqwidgets-ver4.4.0/jqwidgets/jqxbuttons.js"></script>
    <script type="text/javascript" src="/jqwidgets-ver4.4.0/jqwidgets/jqxscrollbar.js"></script>
    <script type="text/javascript" src="/jqwidgets-ver4.4.0/jqwidgets/jqxpanel.js"></script>
    <script type="text/javascript" src="/jqwidgets-ver4.4.0/jqwidgets/jqxtree.js"></script>
    <script type="text/javascript" src="/jqwidgets-ver4.4.0/jqwidgets/jqxcheckbox.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){

            $('#jqxTree').jqxTree({
                height: '500px',
                width: '100%',
                theme:'bootstrap',
                hasThreeStates: true,
                checkboxes: true
            });
            $('#jqxTree').on('checkChange',function(event){
                var args = event.args;
                var action = '<input type="hidden" name="menus[]" value="'+$(args.element).data('action')+'">';
                if(args.checked == null || args.checked == true){
                    $(args.element).prepend(action);
                }else{
                    $(args.element).find('input').remove();
                }
            });


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

@extends('admin.layouts.parent')
@section('page-title','分配访问菜单')
@section('main')
    <link rel="stylesheet" href="/jqwidgets-ver4.4.0/jqwidgets/styles/jqx.base.css">
    <link rel="stylesheet" href="/jqwidgets-ver4.4.0/jqwidgets/styles/jqx.bootstrap.css">

    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>@yield('page-title')</h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-sm-8 col-sm-push-2">
                            <form action="{{ route('admin.allocate-menu-for-role') }}" method="post">
                                {{ csrf_field() }}
                                <input type="hidden" name="id" value="{{ $role->id }}">
                                <div id='jqxTree'>
                                    <ul>
                                        @if(!empty($perms))
                                            @foreach($perms as $perm)
                                                @if(empty($perm['children']))
                                                    <li @if(in_array($perm['id'],$myPerms)) item-checked='true' item-expanded="false" @endif data-action="{{ $perm['id'] }}">
                                                        @if(in_array($perm['id'],$myPerms)) <input type="hidden" name="menus[]" value="{{ $perm['id'] }}"> @endif
                                                        {{ $perm['name'] }}
                                                    </li>
                                                @else
                                                    <li @if(in_array($perm['id'],$myPerms)) item-selected='true' @endif item-expanded='true' data-action="{{ $perm['id'] }}">
                                                        @if(in_array($perm['id'],$myPerms)) <input type="hidden" name="menus[]" value="{{ $perm['id'] }}"> @endif
                                                        <a>{{ $perm['name'] }}</a>
                                                        <ul>
                                                            @foreach($perm['children'] as $child)
                                                                @if(empty($child['children']))
                                                                    <li @if(in_array($child['id'],$myPerms)) item-checked='true' @endif data-action="{{ $child['id'] }}">
                                                                        @if(in_array($child['id'],$myPerms)) <input type="hidden" name="menus[]" value="{{ $child['id'] }}"> @endif
                                                                        {{ $child['name'] }}
                                                                    </li>
                                                                @else
                                                                    <li @if(in_array($child['id'],$myPerms)) item-selected='true' @endif data-action="{{ $child['id'] }}">
                                                                        @if(in_array($child['id'],$myPerms)) <input type="hidden" name="menus[]" value="{{ $child['id'] }}"> @endif
                                                                        <a>{{ $child['name'] }}</a>
                                                                        <ul>
                                                                            @foreach($child['children'] as $tchild)
                                                                                <li @if(in_array($tchild['id'],$myPerms)) item-checked='true' @endif data-action="{{ $tchild['id'] }}">
                                                                                    @if(in_array($tchild['id'],$myPerms))<input type="hidden" name="menus[]" value="{{ $tchild['id'] }}"> @endif
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
                                <div class="hr-line-dashed"></div>
                                <button type="submit" class="btn btn-sm btn-primary">提交</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="/jqwidgets-ver4.4.0/jqwidgets/jqxcore.js"></script>
    <script type="text/javascript" src="/jqwidgets-ver4.4.0/jqwidgets/jqxbuttons.js"></script>
    <script type="text/javascript" src="/jqwidgets-ver4.4.0/jqwidgets/jqxscrollbar.js"></script>
    <script type="text/javascript" src="/jqwidgets-ver4.4.0/jqwidgets/jqxpanel.js"></script>
    <script type="text/javascript" src="/jqwidgets-ver4.4.0/jqwidgets/jqxtree.js"></script>
    <script type="text/javascript" src="/jqwidgets-ver4.4.0/jqwidgets/jqxcheckbox.js"></script>
    <script>
        $(function () {
            $('#jqxTree').jqxTree({
                height: '600px',
                width: '100%',
                theme: 'bootstrap',
                hasThreeStates: true,
                checkboxes: true
            }).on('checkChange', function (event) {
                var args = event.args;
                var action = '<input type="hidden" name="menus[]" value="' + $(args.element).data('action') + '">';
                if (args.checked == null || args.checked == true) {
                    if($(args.element).find('input').val() === undefined || ($(args.element).find('input').val() !== undefined && parseInt($(args.element).find('input').val()) !== $(args.element).data('action'))){
                        $(args.element).prepend(action);
                    }
                } else {
                    $(args.element).find('input').remove();
                }
            });

            $('form').submit(function (e) {
                e.preventDefault();
                youyibao.httpSend($(this), 'post', 1);
            });

        });
    </script>
@endsection
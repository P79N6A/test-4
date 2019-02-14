@extends('admin.layouts.parent')
@section('page-title','分配权限')
@section('main')
    <link rel="stylesheet" href="/jqwidgets-ver4.4.0/jqwidgets/styles/jqx.base.css">
    <link rel="stylesheet" href="/jqwidgets-ver4.4.0/jqwidgets/styles/jqx.bootstrap.css">

    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>功能权限</h5>
                </div>
                <div class="ibox-content">
                    <h2>{{ $role->display_name }}<br></h2>
                    <p>{{ $role->description }}</p>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="panel">
                <div class="panel-heading">
                    <div class="panel-title m-b-md">
                        <h4>详细权限</h4>
                    </div>
                </div>
                <div class="panel-body">
                    <form action="{{ route('admin.allocate-permission') }}" method="post" class="form-allo-perm">
                        {{ csrf_field() }}
                        <input type="hidden" name="id" value="{{ $role->id }}">
                        <div id='jqxTree'>
                            <ul>
                                @if(!empty($perms))
                                    @foreach($perms as $perm)
                                        @if(empty($perm['children']))
                                            <li @if(in_array($perm['id'],$allocatedPerms)) item-checked='true' item-expanded="false" @endif data-action="{{ $perm['id'] }}">
                                                @if(in_array($perm['id'],$allocatedPerms)) <input type="hidden" name="perms[]" value="{{ $perm['id'] }}"> @endif
                                                {{ $perm['display_name'] }}
                                            </li>
                                        @else
                                            <li @if(in_array($perm['id'],$allocatedPerms)) item-selected='true' @endif item-expanded='true' data-action="{{ $perm['id'] }}">
                                                @if(in_array($perm['id'],$allocatedPerms)) <input type="hidden" name="perms[]" value="{{ $perm['id'] }}"> @endif
                                                <a>{{ $perm['display_name'] }}</a>
                                                <ul>
                                                    @foreach($perm['children'] as $child)
                                                        @if(empty($child['children']))
                                                            <li @if(in_array($child['id'],$allocatedPerms)) item-checked='true' @endif data-action="{{ $child['id'] }}">
                                                                @if(in_array($child['id'],$allocatedPerms)) <input type="hidden" name="perms[]" value="{{ $child['id'] }}"> @endif
                                                                {{ $child['display_name'] }}
                                                            </li>
                                                        @else
                                                            <li @if(in_array($child['id'],$allocatedPerms)) item-selected='true' @endif data-action="{{ $child['id'] }}">
                                                                @if(in_array($child['id'],$allocatedPerms)) <input type="hidden" name="perms[]" value="{{ $child['id'] }}"> @endif
                                                                <a>{{ $child['display_name'] }}</a>
                                                                <ul>
                                                                    @foreach($child['children'] as $tchild)
                                                                        <li @if(in_array($tchild['id'],$allocatedPerms)) item-checked='true' @endif data-action="{{ $tchild['id'] }}">
                                                                            @if(in_array($tchild['id'],$allocatedPerms))<input type="hidden" name="perms[]" value="{{ $tchild['id'] }}"> @endif
                                                                            {{ $tchild['display_name'] }}
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
    <script type="text/javascript" src="/jqwidgets-ver4.4.0/jqwidgets/jqxcore.js"></script>
    <script type="text/javascript" src="/jqwidgets-ver4.4.0/jqwidgets/jqxbuttons.js"></script>
    <script type="text/javascript" src="/jqwidgets-ver4.4.0/jqwidgets/jqxscrollbar.js"></script>
    <script type="text/javascript" src="/jqwidgets-ver4.4.0/jqwidgets/jqxpanel.js"></script>
    <script type="text/javascript" src="/jqwidgets-ver4.4.0/jqwidgets/jqxtree.js"></script>
    <script type="text/javascript" src="/jqwidgets-ver4.4.0/jqwidgets/jqxcheckbox.js"></script>
    <script>
        $(function(){
            $('#jqxTree').jqxTree({
                height: '600px',
                width: '100%',
                theme: 'bootstrap',
                hasThreeStates: true,
                checkboxes: true
            }).on('checkChange', function (event) {
                var args = event.args;
                var action = '<input type="hidden" name="perms[]" value="' + $(args.element).data('action') + '">';
                if (args.checked == null || args.checked == true) {
                    if($(args.element).find('input').val() === undefined || ($(args.element).find('input').val() !== undefined && parseInt($(args.element).find('input').val()) !== $(args.element).data('action'))){
                        $(args.element).prepend(action);
                    }
                } else {
                    $(args.element).find('input').remove();
                }
            });

            $('form.form-allo-perm').submit(function(e){
                e.preventDefault();
                youyibao.httpSend($(this),'post',1);
            });

        });
    </script>
@endsection

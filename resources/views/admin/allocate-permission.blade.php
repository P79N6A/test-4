@extends('admin.layouts.parent')
@section('page-title','分配权限')
@section('main')
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
                        <input type="hidden" name="id" value="{{ $role->id }}">
                        <div class="tab-content">
                            <div id="tab-4" class="tab-pane active">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>权限名称</th>
                                        <th>权限描述</th>
                                        <th>允许</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(!empty($perms))
                                        @foreach($perms as $perm)
                                            <tr>
                                                <td>{{ $perm['display_name'] }}</td>
                                                <td>{{ $perm['description'] }}</td>
                                                <td>
                                                    <input type="checkbox" name="perms[]" value="{{ $perm['id'] }}" @if(in_array($perm['id'],$allocatedPerms)) checked @endif>
                                                </td>
                                            </tr>
                                            @if(!empty($perm['children']))
                                                @foreach($perm['children'] as $child)
                                                    <tr>
                                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;|--{{ $child['display_name'] }}</td>
                                                        <td>{{ $child['description'] }}</td>
                                                        <td>
                                                            <input type="checkbox" name="perms[]" value="{{ $child['id'] }}" @if(in_array($child['id'],$allocatedPerms)) checked @endif>
                                                        </td>
                                                    </tr>
                                                    @if(!empty($child['children']))
                                                        @foreach($child['children'] as $tchild)
                                                            <tr>
                                                                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|--{{ $tchild['display_name'] }}</td>
                                                                <td>{{ $tchild['description'] }}</td>
                                                                <td>
                                                                    <input type="checkbox" name="perms[]" value="{{ $tchild['id'] }}" @if(in_array($tchild['id'],$allocatedPerms)) checked @endif>
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
                        <div class="hr-line-dashed"></div>
                        <button class="btn btn-sm btn-primary" type="submit">保存</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(function(){
            $('form.form-allo-perm').submit(function(e){
                e.preventDefault();
                youyibao.httpSend($(this),'post',1);
            });


        });
    </script>
@endsection

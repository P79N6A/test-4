@extends('admin.layouts.parent')
@section('pagge-title','分配角色')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>分配角色</h5>
                </div>
                <div class="ibox-content">
                    <h2>{{ $user->name }}<br>
                    </h2>
                    <p>可以为管理员分配多个角色</p>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>角色列表</h5>
                </div>
                <div class="ibox-content">
                    <form action="{{ route('admin.allocate-role') }}" method="post">
                        <input type="hidden" name="userid" value="{{ $user->id }}">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>角色名称</th>
                                <th>加入</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($roles))
                                @foreach($roles as $role)
                                    <tr>
                                        <td>{{ $role->id }}</td>
                                        <td>{{ $role->display_name }}</td>
                                        <td>
                                            <input type="checkbox" name="role_ids[]" value="{{ $role->id }}" @if(in_array($role->id,$allocatedRoles)) checked @endif>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>

                        <div class="hr-line-dashed"></div>
                        <button class="btn btn-sm btn-primary" type="submit">保存</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(function(){
            $('form').submit(function(e){
                e.preventDefault();
                youyibao.httpSend($(this),'post',1);
            });




        });
    </script>
@endsection

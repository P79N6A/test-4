@extends('business.layouts.frame-parent')
@section('page-title','分配角色')
@section('main')
<div class="row">
    <div class="col-sm-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>角色分配</h5>
            </div>
            <div class="ibox-content">
                <h2>{{ session('username') }}#{{ $user->name }}<br>
                </h2>
                <p>可以为操作分配多个角色</p>
            </div>
        </div>
    </div>
    <div class="col-sm-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>角色列表</h5>
            </div>
            <div class="ibox-content">
                <form action="/allocate-role" method="post" class="form-allo-role">
                    <input type="hidden" name="uid" value="{{ $user->id }}">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>角色名称</th>
                            <th>角色描述</th>
                            <th>加入</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($roles as $role)
                            <tr>
                                <td>{{ $role->id }}</td>
                                <td>{{ $role->name }}</td>
                                <td>{{ $role->description }}</td>
                                <td>
                                    <input type="checkbox" name="ids[]" value="{{ $role->id }}" @if(in_array($role->id,$allocatedRoles)) checked @endif>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </form>
                <div class="hr-line-dashed"></div>
                <button class="btn btn-sm btn-primary btn-allo-role" type="button">保存</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $('.btn-allo-role').click(function(){
            youyibao.httpSend($('.form-allo-role'),'post',1);
        });

    });

</script>
@endsection

@extends('business.layouts.frame-parent')
@section('page-title','修改角色')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>修改角色</h5>
                </div>
                <div class="ibox-content">
                    <form action="/edit-role" class="form-add-role" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" name="id" value="{{ $role->id }}">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>角色名称</label>
                                    <input type="text" name="name" value="{{ $role->name }}" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>角色描述</label>
                                    <textarea class="form-control" name="description" rows="3">{{ $role->description }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label>状态</label>
                                    <select name="status" class="form-control m-b">
                                        <option value="1" @if($role->status == 1) selected @endif >启用</option>
                                        <option value="0" @if($role->status == 0) selected @endif >禁用</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="hr-line-dashed"></div>
                    <button id="add-role" class="btn btn-sm btn-primary" type="button" >修改角色</button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function(){
            $('#add-role').on('click',function(){
                youyibao.httpSend($('.form-add-role'),'post',1);
            });
        });
    </script>

@endsection

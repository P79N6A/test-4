@extends('admin.layouts.parent')
@section('page-title','创建权限')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>修改权限</h5>
                </div>
                <div class="ibox-content">
                    <form action="{{ route('admin.edit-permission') }}" class="form-edit-permission" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" name="id" value="{{ $permission->id }}">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>父权限</label>
                                    <select name="parent_id" class="form-control m-b">
                                        <option value="0">无</option>
                                        @if(!empty($tree))
                                            @foreach($tree as $item)
                                                <option value="{{ $item['id'] }}" @if($permission->parent_id == $item['id']) selected @endif @if($permission->id == $item['id']) disabled @endif >{{ $item['display_name'] }}</option>
                                                @if(!empty($item['children']))
                                                    @foreach($item['children'] as $child)
                                                        <option value="{{ $child['id'] }}" @if($permission->parent_id == $child['id']) selected @endif @if($permission->id == $child['id']) disabled @endif >&nbsp;&nbsp|--{{ $child['display_name'] }}</option>
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>权限唯一标识符</label>
                                    <input type="text" name="name" value="{{ $permission->name }}" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>权限名称</label>
                                    <input type="text" name="display_name" value="{{ $permission->display_name }}" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>权限描述</label>
                                    <textarea class="form-control" name="description" rows="3">{{ $permission->description }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label>显示状态</label>
                                    <select class="form-control m-b" name="status">
                                        <option value="1" @if($permission->status == 1) selected @endif >是</option>
                                        <option value="0" @if($permission->status == 0) selected @endif >否</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>禁用状态</label>
                                    <select class="form-control m-b" name="disable">
                                        <option @if($permission->disable == 0) selected @endif value="0">否</option>
                                        <option @if($permission->disable == 1) selected @endif value="1">是</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>显示排序</label>
                                    <input type="text" name="display_order" value="{{ $permission->display_order }}" placeholder="大于等于0的整数" class="form-control">
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="hr-line-dashed"></div>
                    <button class="btn btn-sm btn-primary btn-edit-permission" type="button" >保存</button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function(){
            $('.btn-edit-permission').click(function(){
                youyibao.httpSend($('.form-edit-permission'),'post',1);
            });
        });
    </script>

@endsection

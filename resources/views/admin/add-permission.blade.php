@extends('admin.layouts.parent')
@section('page-title','创建权限')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>创建权限</h5>
                </div>
                <div class="ibox-content">
                    <form action="{{ route('admin.add-permission') }}" class="form-add-permission" method="post">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>父权限</label>
                                    <select name="parent_id" class="form-control m-b">
                                        <option value="0">无</option>
                                        @if(!empty($tree))
                                            @foreach($tree as $item)
                                                <option value="{{ $item['id'] }}">{{ $item['display_name'] }}</option>
                                                @if(!empty($item['children']))
                                                    @foreach($item['children'] as $child)
                                                        <option value="{{ $child['id'] }}">&nbsp;&nbsp;|--{{ $child['display_name'] }}</option>
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>权限唯一标识符</label>
                                    <input type="text" name="name" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>权限名称</label>
                                    <input type="text" name="display_name" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>权限描述</label>
                                    <textarea class="form-control" name="description" rows="3"></textarea>
                                </div>
                                <div class="form-group">
                                    <label>显示状态</label>
                                    <select class="form-control m-b" name="status">
                                        <option value="1">是</option>
                                        <option value="0">否</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>禁用状态</label>
                                    <select class="form-control m-b" name="disable">
                                        <option value="0">否</option>
                                        <option value="1">是</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>显示排序</label>
                                    <input type="text" name="display_order" placeholder="大于等于0的整数" class="form-control">
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="hr-line-dashed"></div>
                    <button class="btn btn-sm btn-primary btn-add-permission" type="button" >创建权限</button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function(){
            $('.btn-add-permission').click(function(){
                youyibao.httpSend($('.form-add-permission'),'post',1);
            });
        });
    </script>

@endsection

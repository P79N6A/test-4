@extends('admin.layouts.parent')
@section('page-title','修改商户菜单')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>@yield('page-title')</h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <form action="{{ route('admin.edit-bus-menu') }}">
                                {{ csrf_field() }}
                                <input type="hidden" name="id" value="{{ $detail->id }}">
                                <div class="form-group">
                                    <label>父菜单</label>
                                    <select class="form-control" name="parent_id">
                                        <option value="0">无</option>
                                        @if(!empty($menus))
                                            @foreach($menus as $menu)
                                                <option value="{{ $menu['id'] }}" @if($menu['id'] == $detail->parent_id) selected @endif>{{ $menu['name'] }}</option>
                                                @if(!empty($menu['children']))
                                                    @foreach($menu['children'] as $tchild)
                                                        <option value="{{ $tchild['id'] }}" @if($tchild['id'] == $detail->parent_id) selected @endif>
                                                            &nbsp;&nbsp;&nbsp;&nbsp;|--&nbsp;&nbsp;{{ $tchild['name'] }}
                                                        </option>
                                                        @if(!empty($tchild['children']))
                                                            @foreach($tchild['children'] as $third)
                                                                <option value="{{ $third['id'] }}" @if($third['id'] == $detail->parent_id) selected @endif>
                                                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|--&nbsp;&nbsp;
                                                                    {{ $third['name'] }}
                                                                </option>
                                                            @endforeach
                                                        @endif
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>操作</label>
                                    <input class="form-control" name="action" value="{{ $detail->action }}" placeholder="请输入英文操作名称">
                                </div>
                                <div class="form-group">
                                    <label>菜单名称</label>
                                    <input class="form-control" name="name" value="{{ $detail->name }}" placeholder="请输入菜单名称">
                                </div>
                                <div class="form-group">
                                    <label>菜单描述</label>
                                    <textarea class="form-control" name="description" placeholder="可选">{{ $detail->description }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label>是否显示</label>
                                    <select class="form-control" name="display">
                                        <option value="1" @if($detail->display == 1) selected @endif>是</option>
                                        <option value="0" @if($detail->display == 0) selected @endif>否</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>排序</label>
                                    <input class="form-control" name="display_order" value="{{ $detail->display_order }}" type="number" min="0" placeholder="不填默认0">
                                </div>
                                <div class="form-group">
                                    <label>启用状态</label>
                                    <select class="form-control" name="status">
                                        <option value="1" @if($detail->status == 1) selected @endif>启用</option>
                                        <option value="0" @if($detail->status == 0) selected @endif>禁用</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>是否可分配给子账号</label>
                                    <select class="form-control" name="assignable">
                                        <option value="1" @if($detail->assignable == 1) selected @endif>是</option>
                                        <option value="0" @if($detail->assignable == 0) selected @endif>否</option>
                                    </select>
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
    <script>
        $(function () {
            $('form').submit(function (e) {
                e.preventDefault();
                youyibao.httpSend($(this), 'post', 1);
            });
        });
    </script>
@endsection
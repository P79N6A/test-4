@extends('admin.layouts.parent')
@section('page-title','创建商户菜单')
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
                            <form action="{{ route('admin.add-bus-menu') }}">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label>父菜单</label>
                                    <select class="form-control" name="parent_id">
                                        <option value="0">无</option>
                                        @if(!empty($menus))
                                            @foreach($menus as $menu)
                                                <option value="{{ $menu['id'] }}">{{ $menu['name'] }}</option>
                                                @if(!empty($menu['children']))
                                                    @foreach($menu['children'] as $tchild)
                                                        <option value="{{ $tchild['id'] }}">
                                                            &nbsp;&nbsp;&nbsp;&nbsp;|--&nbsp;&nbsp;{{ $tchild['name'] }}
                                                        </option>
                                                        @if(!empty($tchild['children']))
                                                            @foreach($tchild['children'] as $third)
                                                                <option value="{{ $third['id'] }}">
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
                                    <input class="form-control" name="action" placeholder="请输入英文操作名称">
                                </div>
                                <div class="form-group">
                                    <label>菜单名称</label>
                                    <input class="form-control" name="name" placeholder="请输入菜单名称">
                                </div>
                                <div class="form-group">
                                    <label>菜单描述</label>
                                    <textarea class="form-control" name="description" placeholder="可选"></textarea>
                                </div>
                                <div class="form-group">
                                    <label>是否显示</label>
                                    <select class="form-control" name="display">
                                        <option value="1">是</option>
                                        <option value="0">否</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>排序</label>
                                    <input class="form-control" name="display_order" type="number" min="0" placeholder="不填默认0">
                                </div>
                                <div class="form-group">
                                    <label>是否可分配给子账号</label>
                                    <select class="form-control" name="assignable">
                                        <option value="1">是</option>
                                        <option value="0">否</option>
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
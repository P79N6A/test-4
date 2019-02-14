@extends('admin.layouts.parent')
@section('page-title','创建角色')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>创建角色</h5>
                </div>
                <div class="ibox-content">
                    <form action="{{ route('admin.add-role') }}" class="form-add-role" method="post">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>角色唯一标识符</label>
                                    <input type="text" name="name" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>角色名称</label>
                                    <input type="text" name="display_name" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>角色描述</label>
                                    <textarea class="form-control" name="description" rows="3"></textarea>
                                </div>
                                <div class="form-group">
                                    <label>状态</label>
                                    <select class="form-control m-b" name="status">
                                        <option value="1">启用</option>
                                        <option value="0">禁用</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="hr-line-dashed"></div>
                    <button class="btn btn-sm btn-primary btn-add-role" type="button" >创建角色</button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function(){
            $('.btn-add-role').click(function(){
                youyibao.httpSend($('.form-add-role'),'post',1);
            });
        });
    </script>

@endsection

@extends('admin.layouts.parent')
@section('page-title','绑定店员')
@section('main')
    <div class="row">
            <div class="col-sm-12">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>绑定店员</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <form action="{{ route('business.store.bindStaff') }}" class="forms" method="post">
                                {{ csrf_field() }}
                                <div class="col-sm-5">
                                    <div class="form-group">
                                        <label>门店</label>
                                        <input type="text" name="name" placeholder="" class="form-control" value="{{ $store->name }}" disabled>
                                        <input type="hidden" name="store_id" placeholder="" class="form-control" value="{{ $store->id }}">
                                    </div>
                                    <div class="form-group">
                                        <label>新绑定店员</label>
                                        <input type="text" name="user_name" placeholder="点击进行绑定" class="form-control" id="user_name">
                                        <input type="hidden" name="user_id" placeholder="" class="form-control" id="user_id">
                                    </div>
                                </div>
                            </form>
                        </div>
                        <button class="btn btn-sm btn-primary btn-options" type="submit">绑定</button>
                    </div>
                </div>
            </div>
        </div>
    <script src="/admin/js/plugins/iCheck/icheck.min.js"></script>
    <link href="/admin/css/plugins/iCheck/custom.css" rel="stylesheet">
    <style type="text/css">
        #container {width:500px; height: 380px; }  
    </style>
    <script src="/admin/js/webuploader.min.js"></script>
    <link href="/admin/css/webuploader.css" rel="stylesheet">
    <script type="text/javascript">
        $(document).ready(function(){
            $('.btn-options').click(function(){
                youyibao.httpSend($('form.forms'),'post',1);
            });
        });
        $('#user_name').click(function(){
            layer.open({
                title: '搜索',
                type: 1,
                skin: 'layui-layer-rim', //加上边框
                area: ['420px', '240px'], //宽高
                content: '<div class="input-group"><input type="text" id="keyword" placeholder="请输入手机号进行搜索" class="input-sm form-control"> <span class="input-group-btn"><button class="btn btn-sm btn-primary" id="search"> 搜索</button> </span></div>'
            });

            $('#search').click(function(){
                layer.load();
                var keyword = $('#keyword').val();

                if(keyword.length != 11){
                    layer.msg('请输入正确手机号');
                    layer.closeAll('loading');
                    return false;
                }
                $.get('/member/list/json', { keyword: keyword },function(data){
                    if(data.length == 1){
                        if(data[0]['store_id']){
                            layer.msg('该用户已绑定门店');
                            layer.closeAll('loading');
                            return false;
                        }
                        if(data[0]['role'] == 2){
                            layer.msg('该用户是医生');
                            layer.closeAll('loading');
                            return false;
                        }
                        $('#user_id').val(data[0]['id']);
                        $('#user_name').val(data[0]['nickname']);
                        layer.closeAll();
                    } else {
                        layer.msg('搜索不到该用户信息');
                        layer.closeAll('loading');
                        return false;
                    }
                },'json');
            });
        });
    </script>
@endsection
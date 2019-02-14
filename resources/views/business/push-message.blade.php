@extends('business.layouts.frame-parent')
@section('page-title','推送新消息')
@section('main')
    <link href="/business/css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css" rel="stylesheet">
    <link href="/business/css/plugins/iCheck/custom.css" rel="stylesheet">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>@yield('page-title')</h5>
                </div>
                <div class="ibox-content">
                    <form action="{{ route('business.push-message') }}" method="post">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>标题</label>
                                    <input type="text" name="title" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>内容</label>
                                    <script id="content" name="content" type="text/plain"></script>
                                </div>
                                <div class="form-group">
                                    <label>推送目标人群</label>
                                    <div>
                                        <div class="radio radio-info radio-inline">
                                            <input type="radio" id="inlineRadio1" value="1" name="target" checked class="target">
                                            <label for="inlineRadio1">指定用户</label>
                                        </div>
                                        <div class="radio radio-info radio-inline">
                                            <input type="radio" id="inlineRadio2" value="2" name="target" class="target">
                                            <label for="inlineRadio2">访客</label>
                                        </div>
                                        <div class="radio radio-info radio-inline">
                                            <input type="radio" id="inlineRadio3" value="3" name="target" class="target">
                                            <label for="inlineRadio3">消费过的用户</label>
                                        </div>
                                    </div>
                                    <div class="form-group filter-box">
                                        <br>
                                        <div class="input-group m-b">
                                            <input type="text" id="keyword" placeholder="输入用户名关键字" class="form-control">
                                            <span class="input-group-addon btn btn-sm btn-primary btn-search">检索</span>
                                        </div>
                                        <div id="user-box"></div>
                                    </div>
                                </div>
                           </div> </div>
                        <div class="hr-line-dashed"></div>
                        <button class="btn btn-sm btn-primary btn-create-user" type="submit">推送</button>
                    </form>
                </div>
            </div>
            </div>
    </div>
    <script src="/business/js/plugins/iCheck/icheck.min.js"></script>
    <script type="text/javascript" src="/ueditor/ueditor.config.modified.js"></script>
    <script type="text/javascript" src="/ueditor/ueditor.all.js"></script>
    <script>
        $(function(){
            $('input.target').change(function(){
                if($(this).val() == 1){
                    $('.filter-box').show();
                }else{
                    $('.filter-box').hide();
                }
            });

            var ue = UE.getEditor('content',{
                initialFrameHeight:400
            });

            // 检索用户
            $('.btn-search').click(function(){
                $.ajax({
                    type:'get',
                    url:"{{ route('business.search-user') }}",
                    data:{
                        keyword:$('#keyword').val()
                    },
                    success:function(data){
                        if(data.length > 0){
                            if(data.length > 200){
                                $('#user-box').empty().html('数据量大于200，无法显示，请缩小检索范围');
                                setTimeout(function () {
                                    $('#user-box').empty();
                                },2000);
                                return false;
                            }

                            var els = '';
                            $.each(data,function(index,value){
                                els += '<label class="checkbox-inline i-checks">' +
                                    '<div class="icheckbox_square-green" style="position: relative;">' +
                                    '<input type="checkbox" name="uids[]" value="' + value.id + '" style="position: absolute; opacity: 0;">' +
                                    '<ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins>' +
                                    '</div>' +
                                    value.username +
                                    '</label><span class="btn-del-user" style="color:red;cursor:pointer;">x</span>&nbsp;';
                            });
                            $('#user-box').append(els);

                            $(".i-checks").iCheck({
                                checkboxClass: "icheckbox_square-green",
                                radioClass: "iradio_square-green"
                            });

                            $('.btn-del-user').click(function () {
                                $(this).prev('label').remove();
                                $(this).remove();
                            });

                        }else{
                            $('#user-box').empty().html('没有搜索到相关数据');
                            setTimeout(function () {
                                $('#user-box').empty();
                            },2000);
                        }
                    }
                });
            });

            $('form').submit(function(e){
                e.preventDefault();
                youyibao.httpSend($(this),'post',1);
            });
        });

    </script>
@endsection

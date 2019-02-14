@extends('business.layouts.frame-parent')
@section('page-title','修改资讯')
@section('main')
    <link rel="stylesheet" type="text/css" href="http://ueditor.baidu.com/umeditor/themes/default/css/umeditor.min.css">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>修改资讯</h5>
                </div>
                <div class="ibox-content">
                    <form action="{{ route('business.edit-activity-info') }}" method="post" class="edit-info">
                        {{ csrf_field() }}
                        <input type="hidden" name="id" value="{{ $info->id }}">
                        <div class="row">
                            <div class="col-sm-7">
                                <div class="form-group">
                                    <label>名称</label>
                                    <input type="text" name="title" value="{{ $info->title }}" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>门店</label>
                                    <select class="form-control m-b" name="store_id">
                                        <option>请选择门店</option>
                                        @if(!empty($stores))
                                            @foreach($stores as $store)
                                                <option value="{{ $store->id }}" @if($store->id == $info->store_id) selected @endif>{{ $store->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>APP提醒</label>
                                    <p class="help-block">过多提醒会骚扰用户，请适当使用。</p>
                                    <div>
                                        <div class="switch">
                                            <div class="onoffswitch">
                                                <input type="checkbox" name="push_flag" value="1" @if($info->push_flag == 1) checked @endif class="onoffswitch-checkbox" id="rec1">
                                                <label class="onoffswitch-label" for="rec1">
                                                    <span class="onoffswitch-inner"></span>
                                                    <span class="onoffswitch-switch"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-5">
                                <div class="form-group">
                                    <label>资讯内容</label>
                                    <div>
                                        <script id="container" style="width:100%;height:300px" name="content" type="text/plain">{!! $info->content !!}</script>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <button class="btn btn-sm btn-primary btn-edit-info" type="button">保存</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{--<script type="text/javascript" charset="utf-8" src="http://ueditor.baidu.com/umeditor/umeditor.config.js"></script>--}}
    {{--<script type="text/javascript" charset="utf-8" src="http://ueditor.baidu.com/umeditor/umeditor.min.js"></script>--}}
    {{--<script type="text/javascript" src="http://ueditor.baidu.com/umeditor/lang/zh-cn/zh-cn.js"></script>--}}
    {{--<script src="/business/umeditor/umeditor.js"></script>--}}
    {{--<script src="/business/umeditor/umeditor.config.js"></script>--}}
    {{--<script src="/business/umeditor/lang/zh-cn/zh-cn.js"></script>--}}
    <script src="/ueditor/ueditor.config.modified.js"></script>
    <script src="/ueditor/ueditor.all.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {
        $(".i-checks").iCheck({
            checkboxClass: "icheckbox_square-green",
            radioClass: "iradio_square-green",
        });
        /*
        window.um = UM.getEditor('container', {
            // 传入配置参数,可配参数列表看umeditor.config.js
            toolbar: ['bold italic underline', 'image', 'removeformat', 'paragraph', 'fontsize']
        });
        */
        var ue = UE.getEditor('container',{
            initialFrameHeight:400,
        });

        $('.btn-edit-info').click(function(){
            youyibao.httpSend($('form.edit-info'),'post',1);
        });
    });
    </script>
@endsection

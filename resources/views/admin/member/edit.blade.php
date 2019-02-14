@extends('admin.layouts.parent')
@section('page-title','修改医生信息')
@section('main')
    <div class="row">
            <div class="col-sm-12">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>修改医生信息</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <form action="{{ route('admin.edit-member') }}" method="post" class="forms">
                                {{ csrf_field() }}
                                <div class="col-sm-5">
                                    <div class="form-group">
                                        <label>昵称</label>
                                        <input type="text" name="nickname" placeholder="" value="{{$info->nickname}}" class="form-control">
                                    </div>
                                     <input type="hidden" name="id" value="{{$info->id}}">
                                </div>
                            </form>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <button class="btn btn-sm btn-primary btn-options" type="submit">修改</button>
                    </div>
                </div>
            </div>
        </div>
    <script src="/admin/js/webuploader.min.js"></script>
    <link href="/admin/css/webuploader.css" rel="stylesheet">
    <script type="text/javascript">
        $(document).ready(function(){
            $('.btn-options').click(function(){
                youyibao.httpSend($('form.forms'),'post',1);
            });
        });
    </script>
@endsection

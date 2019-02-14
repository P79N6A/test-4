@extends('business.layouts.frame-parent')
@section('page-title','推送资讯')
@section('main')
    <link href="/business/css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css" rel="stylesheet">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>推送资讯</h5>
                </div>
                <div class="ibox-content">
                    <form action="{{ route('business.push-info') }}" method="post" class="push-info">
                        {{ csrf_field() }}
                        <input type="hidden" name="id" value="{{ $id }}">
                        <div class="row">
                            <div class="col-sm-7">
                                <div class="form-group">
                                    <label>门店</label>
                                    <select class="form-control m-b" name="store_id">
                                        <option>请选择门店</option>
                                        @if(!empty($stores))
                                            @foreach($stores as $store)
                                                <option value="{{ $store->id }}">{{ $store->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>目标人群</label>
                                    <div>
                                        <div class="radio radio-info radio-inline">
                                            <input type="radio" id="inlineRadio1" value="1" name="target">
                                            <label for="inlineRadio1">该门店消费者</label>
                                        </div>
                                        <div class="radio radio-info radio-inline">
                                            <input type="radio" id="inlineRadio2" value="2" name="target">
                                            <label for="inlineRadio2">门店访客</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <button class="btn btn-sm btn-primary btn-push-info" type="button">推送</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            $(".i-checks").iCheck({
                checkboxClass: "icheckbox_square-green",
                radioClass: "iradio_square-green"
            });
            $('.btn-push-info').click(function(){
                youyibao.httpSend($('form.push-info'),'post',1);
            });
        });
    </script>
@endsection

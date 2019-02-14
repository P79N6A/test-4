@extends('business.layouts.frame-parent')
@section('page-title','修改秒杀信息')
@section('main')
    <div class="row">
            <div class="col-sm-12">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>秒杀</h5>
                    </div>
                    <form action="{{ route('business.edit-sekill') }}" method="post" class="form-edit-sekill">
                        {{ csrf_field() }}
                        <input type="hidden" name="id" value="{{ $package->id }}">
                        <div class="ibox-content">
                            <blockquote>
                                <p>{{ $package->name }}</p>
                            </blockquote>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>秒杀价格</label>
                                        <input type="text" value="{{ $package->sekillInfo->price }}" class="form-control" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label>秒杀库存</label>
                                        <input type="text" name="stock" value="{{ $package->sekillInfo->stock }}" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>秒杀限购</label>
                                        <input type="text" value="{{ $package->sekillInfo->buy_limit }}" class="form-control" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label>秒杀时间</label>
                                        <div>
                                            <input placeholder="开始时间" value="{{ date('Y-m-d H:i:s',$package->sekillInfo->start_date) }}" class="form-control layer-date" id="start" readonly>
                                            <input placeholder="结束时间" value="{{ date('Y-m-d H:i:s',$package->sekillInfo->end_date) }}" class="form-control layer-date" id="end" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <button class="btn btn-sm btn-primary btn-edit-sekill" type="button">保存</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="/business/js/plugins/layer/laydate/laydate.js"></script>
    <script>
        $('.btn-edit-sekill').click(function(){
            youyibao.httpSend($('.form-edit-sekill'),'post',1);
        });

    </script>
@endsection

@extends('business.layouts.frame-parent')
@section('page-title','门店详情')
@section('main')
    <div class="row">
        <div class="col-sm-4">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>基本信息</h5>
                </div>
                <div>
                    <div class="ibox-content no-padding border-left-right">
                        <img alt="image" class="img-responsive" src="{{ config('static.base_url').'/'.$store->path }}">
                    </div>
                    <div class="ibox-content profile-content">
                        <h4>{{ $store->brand_name }}<strong>（{{ $store->name }}）</strong></h4>
                        <p><i class="fa fa-map-marker"></i> {{ $store->address }}</p>
                        <p>{{ $store->mobile }}</p>
                        <p>
                            @if($store->status == 1)
                                <span class="badge badge-primary">营业中</span>
                            @elseif($store->status == 3)
                                <span class="badge">关停</span>
                            @elseif($store->status == 2)
                                <span class="badge badge-warning">待审核</span>
                            @endif
                        </p>
                        <div class="row m-t-lg text-center">
                            <div class="col-sm-4">
                                <h5><strong>{{ $store->package_count }}</strong> 个商品</h5>
                            </div>
                            <div class="col-sm-4">
                                <h5><strong>{{ $store->machine_count }}</strong> 个机台</h5>
                            </div>
                            <div class="col-sm-4">
                                <h5><strong>{{ $store->ticket_count }}</strong> 个卡券</h5>
                            </div>
                        </div>
                        <div class="user-button">
                            <a data-url="{{ route('business.download-qrcode',['type'=>11,'storeId'=>$store->id]) }}" class="btn btn-primary btn-sm btn-block qrcode-preview"><i class="fa fa-qrcode"></i> 二维码</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-8">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>图片</h5>
                </div>
                <div class="ibox-content">
                    @foreach($store->gallery as $item)
                        <a class="fancybox" href="{{ config('static.base_url').'/'.$item }}">
                            <img alt="image" src="{{ config('static.base_url').'/'.$item }}" />
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <script>
        $(function(){
            $('.qrcode-preview').click(function(){
                location.href = $(this).attr('data-url');
            });
        });
    </script>
@endsection

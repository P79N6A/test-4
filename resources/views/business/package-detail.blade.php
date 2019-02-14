@extends('business.layouts.frame-parent')
@section('page-title','套餐详情')
@section('main')
    <div class="row">
        <div class="col-sm-4">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>基本信息</h5>
                </div>
                <div>
                    <div class="ibox-content no-padding border-left-right">
                        <img alt="image" class="img-responsive" src="{{ config('static.base_url').'/'.$detail->path }}">
                    </div>
                    <div class="ibox-content profile-content">
                        <h4>{{ $detail->name }}</h4>
                        <p>
                            @if($detail->expire_date >= time())
                                <span class="label badge-primary ">未过期</span>
                            @else
                                <span class="label badge-default ">已过期</span>
                            @endif
                        </p>
                        <p>价格: ￥{{ $detail->price }}</p>
                        <p>库存: {{ $detail->stock }}</p>
                        <p>销量: {{ $detail->sales }}</p>
                        <p>创建时间: {{ date('Y-m-d H:i:s',$detail->addtime) }}</p>
                        <p>过期时间: {{ date('Y-m-d H:i:s',$detail->expire_date) }}</p>
                        <div class="user-button">
                            <a href="{{ route('business.edit-package',['id'=>$detail->id]) }}" class="btn btn-primary btn-sm btn-block">编辑</a>
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
                    @foreach($detail->gallery as $item)
                        <a class="fancybox" target="_blank" href="{{  config('static.base_url').'/'.$item }}" title="图片1">
                            <img alt="image" src="{{  config('static.base_url').'/'.$item }}" />
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-sm-8">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>适用门店/二维码</h5>
                </div>
                <div class="ibox-content">
                    <div class="attachment">
                        @foreach($availableStores as $store)
                        <div class="file-box">
                            <div class="file">
                                <a title="下载二维码" class="download-qrcode" data-url="{{ route('business.download-qrcode',['type'=>39,'storeId'=>$store->id,'packageId'=>$detail->id]) }}">
                                    <span class="corner"></span>
                                    <div class="icon">
                                        <i class="fa fa-qrcode"></i>
                                    </div>
                                    <div class="file-name text-center">{{ $store->name }}</div>
                                </a>
                            </div>
                        </div>
                        @endforeach

                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(function(){
            $('a.download-qrcode').click(function(){
                location.href = $(this).data('url');
            });



        });
    </script>
@endsection

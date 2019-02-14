@extends('business.layouts.frame-parent')
@section('page-title','会员卡套餐管理')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>@yield('page-title')</h5>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>图片</th>
                                <th>名字</th>
                                <th>价格</th>
                                <th>币数</th>
                                <th>门店</th>
                                <th>限购数量</th>
                                <th>添加时间</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($packages))
                                @foreach($packages as $package)
                                    <tr>
                                        <td><img class="img-preview-sm" src="{{ $package->image }}"></td>
                                        <td>{{ $package->name }}</td>
                                        <td>{{ $package->price }}</td>
                                        <td>{{ $package->coin }}</td>
                                        <td>{{ $package->store_name }}</td>
                                        <td>{{ round($package->limitNum,0) }}</td>
                                        <td>{{ date('Y-m-d H:i:s',$package->addtime) }}</td>
                                        <td>
                                            <a class="btn btn-sm btn-white"
                                               href="{{ route('business.show-qrcode',[
                                               'type'=>49,'packageId'=>$package->id,
                                               'storeId'=>$package->store_id,
                                               'setcoinno'=>$package->setcoinno
                                               ]) }}">
                                                查看二维码
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="text-right">
                        @if(!empty($packages))
                            {{ $packages->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(function () {
            $('.btn-del-package').click(function () {
                youyibao.httpSend($(this), 'get', 1);
            });
        });
    </script>
@endsection

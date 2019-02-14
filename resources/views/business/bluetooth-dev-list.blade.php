@extends('business.layouts.frame-parent')
@section('page-title','蓝牙设备')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>所有蓝牙设备</h5>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>门店</th>
                                    <th>MAJOR</th>
                                    <th>MINOR</th>
                                    <th>摇一摇微信配置链接</th>
                                    <th>备注</th>
                                    <th>创建时间</th>
                                </tr>
                            </thead>
                            <tbody>
                            @if(!empty($devices))
                            @foreach($devices as $device)
                                <tr>
                                    <td>{{ $device->store_name }}</td>
                                    <td>{{ $device->major }}</td>
                                    <td>{{ $device->minor }}</td>
                                    <td>{{ config('misc.store_shake_url').$device->store_id }}</td>
                                    <td>{{ $device->note }}</td>
                                    <td>{{ date('Y-m-d H:i:s',$device->addtime) }}</td>
                                </tr>
                            @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="text-right">
                        @if( is_object($devices) && !empty($devices->links()) )
                            {{ $devices->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

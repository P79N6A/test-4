@extends('business.layouts.frame-parent')
@section('page-title','商户端APP下载')
@section('main')
    <link rel="stylesheet" href="/business/css/webuploader.css">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>@yield('page-title')</h5>
                </div>
                <div class="ibox-content">
                    <div class="text-center">
                        <img class="img-rounded" src="{{ config('static.base_url').'/system/yybstore_download.png' }}" alt="商户端APP二维码">
                        <br><br>
                        <h2><label>麦麦天空商户端下载</label></h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

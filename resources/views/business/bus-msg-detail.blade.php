@extends('business.layouts.frame-parent')
@section('page-title','消息详情')
@section('main')
    <div class="ibox">
        <div class="ibox-content">
            <div class="text-center article-title">
                <h1>{{ $detail->title }}</h1>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-8 col-sm-push-2 col-md-6 col-md-push-3">
                    {!! $detail->content !!}
                </div>
            </div>
            <hr>
        </div>
    </div>
@endsection

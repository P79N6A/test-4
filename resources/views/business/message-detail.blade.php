@extends('admin.layouts.parent')
@section('page-title','消息详情')
@section('main')
    <div class="ibox">
        <div class="ibox-content">
            <div class="text-center article-title">
                <h1>{{ $msg->title }}</h1>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div>
                        {!! $msg->content !!}
                    </div>
                </div>
            </div>
            <hr>
        </div>
    </div>
@endsection

@extends('business.layouts.frame-parent')
@section('page-title','错误')
@section('main')
    <div class="middle-box text-center">
        <h1>{{ $code }}</h1>
        <h3 class="font-bold">{{ $msg }}</h3>
        <div class="error-desc">
            <br/><a id="go-back" data-url="@if(!empty($referrer)) {{ $referrer }} @endif" class="btn btn-primary m-t">后退</a>
        </div>
    </div>
    <script>
        $(function () {
            $('#go-back').click(function () {
                if($(this).data('url')){
                    location.href = $(this).data('url');
                }else{
                    window.history.go(-1);
                }
            });
        });
    </script>
@endsection

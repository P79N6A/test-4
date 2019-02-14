@extends('admin.layouts.parent')
@section('page-title','错误')
@section('main')
    <div class="middle-box text-center animated fadeInUp">
        <h1>{{ $code }}</h1>
        <h3 class="font-bold">{{ $msg }}</h3>
        <div class="error-desc">
            <br/><a href="javascript:history.back(-1);" class="btn btn-primary m-t">后退</a>
        </div>
    </div>
@endsection

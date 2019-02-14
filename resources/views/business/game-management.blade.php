@extends('admin.layouts.parent')
@section('page-title','VR管理后台入口')
@section('main')
    <script>
        location.href = '{{ $url }}';
    </script>
@endsection
@extends('business.layouts.frame-parent')
@section('page-title','商品可用门店')
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
                                <th>门店名称</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($stores))
                                @foreach($stores as $store)
                                    <tr><td>{{ $store->name }}</td></tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="text-right">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('business.layouts.frame-parent')
@section('page-title','财务-门店列表')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>可操作提现门店</h5>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>门店</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($stores as $store)
                                <tr>
                                <td>{{$store->name}}</td>
                                <td>
                                    <a href="{{route('business.store-balance',['id'=>$store->id])}}" class="btn btn-white btn-sm">
                                        <i class="fa fa-pencil"></i> 查看详情
                                    </a>
                                </td>

                                </tr>
                                    
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-right">
                        @if(!empty($stores->links()))
                            {{ $stores->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


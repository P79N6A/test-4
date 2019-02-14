@extends('business.layouts.frame-parent')
@section('page-title','分配门店权限')
@section('main')
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>门店权限</h5>
                    </div>
                    <div class="ibox-content">
                        <h2>{{ $role->name }}<br>
                        </h2>
                        <p>{{ $role->description }}</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>门店列表</h5>
                    </div>
                    <div class="ibox-content">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>门店ID</th>
                                    <th>门店名称</th>
                                    <th>允许</th>
                                </tr>
                            </thead>
                            <tbody>
                            <form action="" method="post" class="form-allocate">
                                {{ csrf_field() }}
                                <input type="hidden" name="id" value="{{ $role->id }}">
                                @foreach($stores as $k=>$store)
                                    <tr>
                                        <td>{{ $store->id }}</td>
                                        <td>{{ $store->name }}</td>
                                        <td>
                                            <input type="checkbox" name="store_ids[]" value="{{ $store->id }}"  @if(in_array($store->id,$alloStores)) checked @endif>
                                        </td>
                                    </tr>
                                @endforeach
                            </form>
                            </tbody>
                        </table>
                        <div class="hr-line-dashed"></div>
                        <button class="btn btn-sm btn-primary btn-allocate" type="button">保存</button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            $(document).ready(function(){
                $('.btn-allocate').click(function(){
                    youyibao.httpSend($('.form-allocate'),'post',1);
                });
            });
        </script>
@endsection

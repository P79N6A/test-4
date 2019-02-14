@extends('business.layouts.frame-parent')
@section('page-title','VR机台列表')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>@yield('page-title')</h5>
                </div>
                <div class="ibox-content">
                    <div class="row form-horizontal">
                        <form action="{{ route('business.vr-machine-management') }}">
                            <div class="col-sm-3 m-b-xs">
                                <div class="form-group col-sm-12">
                                    <select name="network_status" class="form-control">
                                        <option @if($params['network_status'] == 0) selected @endif value="0">全部连接状态</option>
                                        <option @if($params['network_status'] == 1) selected @endif value="1">连接</option>
                                        <option @if($params['network_status'] == -1) selected @endif value="-1">断开</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3 m-b-xs">
                                <div class="form-group col-sm-12">
                                    <select name="machine_status" class="form-control">
                                        <option @if($params['machine_status'] == 0) selected @endif value="0">全部机台状态</option>
                                        <option @if($params['machine_status'] == 1) selected @endif value="1">正常</option>
                                        <option @if($params['machine_status'] == -1) selected @endif value="-1">异常</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3 m-b-xs">
                                <div class="form-group col-sm-12">
                                    <input class="form-control" name="name" value="{{ $params['name'] }}" placeholder="机台名称">
                                </div>
                            </div>
                            <div class="col-sm-2 m-b-xs">
                                <div class="form-group col-sm-12">
                                    <input class="form-control" name="store_name" value="{{ $params['store_name'] }}" placeholder="门店名称">
                                </div>
                            </div>
                            <div class="col-sm-1 m-b-xs">
                                <button type="submit" class="btn btn-sm btn-primary">查询</button>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>机台名称</th>
                                <th>机台所在门店</th>
                                <th>机台状态</th>
                                <th>网络连接状态</th>
                                <th>创建时间</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($machines))
                                @foreach($machines as $machine)
                                    <tr>
                                        <td>{{ $machine->Id }}</td>
                                        <td>{{ $machine->Name }}</td>
                                        <td>{{ $machine->StoreNeame }}</td>
                                        <td>{{ $machine->Status }}</td>
                                        <td>{{ $machine->Network }}</td>
                                        <td>{{ $machine->CreateDate }}</td>
                                        <td>
                                            <a href="{{ route('business.show-qrcode',['type'=>48,'storeId'=>$machine->StoreId,'machineId'=>$machine->Id]) }}" class="btn btn-sm btn-success">
                                                查看二维码
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="text-right">
                        <ul class="pagination">
                            <?php
                                if ($paginated->total >= $paginated->page){
                                    for ($i=1; $i <= $paginated->total; $i++){
                                        if ($i == $paginated->page){
                                            echo '<li class="active"><span>'.$i.'</span></li>';
                                        }else{
                                            if (preg_match('/\?/',request()->fullUrl())){
                                                if (preg_match('/page=\d+/',request()->fullUrl())){
                                                    $url = preg_replace('/page=\d+/','page='.$i,request()->fullUrl());
                                                }else{
                                                    $url = request()->fullUrl().'&page='.$i;
                                                }
                                            }else{
                                                $url = request()->fullUrl().'?page='.$i;
                                            }
                                            echo '<li><a href="'.$url.'">'.$i.'</li>';
                                        }
                                    }
                                }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

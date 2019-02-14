@extends('business.layouts.frame-parent')
@section('page-title','后台操作日志')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>后台操作日志</h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td>操作IP</td>
                                            <td>{{$info->ip}}</td>
                                        </tr>
                                        <tr>
                                            <td>用户代理</td>
                                            <td>{{$info->user_agent}}</td>
                                        </tr>
                                        <tr>
                                            <td>操作说明</td>
                                            <td>{{$info->comment}}</td>
                                        </tr>
                                        <tr>
                                            <td>提交方式</td>
                                            <td>{{$info->method}}</td>
                                        </tr>
                                        <tr>
                                            <td>访问地址</td>
                                            <td>{{$info->uri}}</td>
                                        </tr>
                                        <tr>
                                            <td>操作时间</td>
                                            <td>{{$info->create_at}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td>操作地区</td>
                                            <td>{{$info->ip_area}}</td>
                                        </tr>
                                        <tr>
                                            <td>操作用户</td>
                                            <td>@if($info->userid !=0 ) {{$info->user->name}}， {{$info->user->description}} @else 未知 @endif</td>
                                        </tr>
                                        <tr>
                                            <td>操作数据库</td>
                                            <td>{{$info->table}}</td>
                                        </tr>
                                        <tr>
                                            <td>提交参数</td>
                                            <td>{{$info->param}}</td>
                                        </tr>
                                        <tr>
                                            <td>操作方式</td>
                                            <td>{{$action[$info->action]}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>操作前数据</h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-sm-12">
                            @if(is_array($info->before))
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        @foreach($info->before as $key => $val)
                                        <tr>
                                            <td>{{$key}}</td>
                                            <td>{{$val}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>操作后数据</h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-sm-12">
                            @if(is_array($info->after))
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        @foreach($info->after as $key => $val)
                                        <tr>
                                            <td>{{$key}}</td>
                                            <td>@if(is_array($val)) {{json_encode($val)}} @else {{$val}} @endif </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


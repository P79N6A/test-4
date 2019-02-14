@extends('business.layouts.frame-parent')
@section('page-title','广告列表')
@section('main')
    <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>所有广告</h5>
                        <div class="ibox-tools">
                            <a href="{{ route('business.add-ad') }}" class="btn btn-primary btn-xs">创建广告</a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>封面</th>
                                        <th>标题/类型/位置</th>
                                        <th>浏览量</th>
                                        <th>发布</th>
                                        <th>创建时间</th>
                                        <th>上架状态</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @if(!empty($ads))
                                    @foreach($ads as $ad)
                                        <tr>
                                            <td>
                                                <img class="feed-photo" src="{{ config('static.base_url').'/'.$ad->path }}">
                                            </td>
                                            <td>
                                                <p>{{ $ad->title }}</p>
                                                <p>
                                                    <span class="label label-primary">
                                                        @if($ad->type == 1)图文/链接
                                                        @elseif($ad->type == 2)链接
                                                        @elseif($ad->type == 3)套餐
                                                        @endif
                                                    </span>
                                                </p>
                                                <p>
                                                <span class="label label-success">
                                                    @if($ad->place == 1)应用首页@elseif($ad->place == 2)启动页@elseif($ad->place == 3)门店页@endif
                                                </span>
                                                </p>
                                                <p>
                                                    <span class="label label-success">
                                                        @if($ad->platform == 0)全平台
                                                        @elseif($ad->platform == 1)IOS
                                                        @elseif($ad->platform == 2)android
                                                        @endif
                                                    </span>
                                                </p>
                                            </td>
                                            <td>
                                                <p><span class="badge badge-danger">{{ $ad->views }}</span></p>
                                            </td>
                                            <td>
                                                <div class="switch">
                                                    <div class="onoffswitch">
                                                        <input type="checkbox"
                                                               @if( $ad->flag == 1) checked @endif
                                                               class="onoffswitch-checkbox switch-post"
                                                               data-url="{{ route('business.switch-post-ad') }}"
                                                               data-type="id"
                                                               data-id="{{$ad->id}}&s=@if($ad->flag==0) 1 @elseif($ad->flag==1) 2 @endif"
                                                               id="post_{{$ad->id}}" >
                                                        <label class="onoffswitch-label" for="post_{{$ad->id}}">
                                                            <span class="onoffswitch-inner"></span>
                                                            <span class="onoffswitch-switch"></span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ date('Y-m-d H:i:s',$ad->addtime) }}</td>
                                            <td>
                                                @if($ad->enable == 1)上架
                                                @elseif($ad->enable == 0)下架
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('business.edit-ad',['id'=>$ad->id]) }}" class="btn btn-white btn-sm"><i class="fa fa-pencil"></i> 修改 </a>
                                                <a href="javascript:;" data-url="{{ route('business.delete-ad') }}" data-type="id" data-id="{{ $ad->id }}" class="btn btn-del-ad btn-white btn-sm">
                                                    <i class="fa fa-trash"></i> 删除
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                        <div class="text-right">
                           @if(!empty($ads) && !empty($ads->links()))
                               {{ $ads->links() }}
                           @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <script>
        $(function(){
            $('.btn-del-ad').click(function(){
                var $this = $(this);
                layer.msg('您确定要删除该广告吗？',{
                    time:0,
                    btn:['是','否'],
                    yes:function(index){
                        layer.close(index);
                        youyibao.httpSend($this,'get',1);
                    }
                });
            });
            $('.switch-post').click(function(){
                youyibao.httpSend($(this),'get',1);
            });
        });
    </script>
@endsection

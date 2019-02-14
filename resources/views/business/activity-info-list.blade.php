@extends('business.layouts.frame-parent')
@section('page-title','资讯列表')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>所有资讯</h5>
                    <div class="ibox-tools">
                        <a href="{{ route('business.add-activity-info') }}" class="btn btn-primary btn-xs">创建资讯</a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <form action="{{ route('business.activity-info-list') }}" method="get">
                            <div class="col-sm-3 m-b-xs">
                                <select class="input-sm form-control input-s-sm inline" name="sid">
                                    <option value="0">全部门店</option>
                                    @foreach($stores as $store)
                                        <option value="{{ $store->id }}"
                                                @if($store->id == $sid) selected @endif >{{ $store->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-4 m-b-xs">
                                <div class="input-group">
                                    <input type="text" placeholder="请输入关键词" name="keyword" value="{{ $keyword }}"
                                           class="input-sm form-control"> <span class="input-group-btn">
                                        <button type="submit" class="btn btn-sm btn-primary"> 搜索</button> </span>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>门店/标题/描述</th>
                                <th>APP提醒</th>
                                <th>发布</th>
                                <th>推荐</th>
                                <th>创建时间</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($infos))
                                @foreach($infos as $info)
                                    <tr>
                                        <td>
                                            <p><span class="label label-success">{{ $info->store_name }}</span></p>
                                            <p>{{ $info->title }}</p>
                                            <p>
                                                <small>{{ $info->description }}</small>
                                            </p>
                                        </td>
                                        <td>
                                            @if($info->push_flag == 0)
                                                <span class="label">否</span>
                                            @elseif($info->push_flag == 1 && $info->is_push == 0)
                                                <span class="label label-primary">是</span>
                                            @elseif($info->push_flag == 1 && $info->is_push == 1)
                                                <span class="label label-primary">已发送</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="switch">
                                                <div class="onoffswitch">
                                                    <input type="checkbox"
                                                           @if($info->post_flag == 1) checked @endif class="onoffswitch-checkbox post-info"
                                                           data-url="{{ route('business.switch-post-info') }}" data-type="id" data-id="{{ $info->id }}&s=@if($info->post_flag == 0) 1 @else 2 @endif"
                                                           id="post_{{ $info->id }}">
                                                    <label class="onoffswitch-label" for="post_{{ $info->id }}">
                                                        <span class="onoffswitch-inner"></span>
                                                        <span class="onoffswitch-switch"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="switch">
                                                <div class="onoffswitch">
                                                    <input type="checkbox"
                                                           @if($info->flag == 1) checked @endif class="onoffswitch-checkbox post-info"
                                                           data-url="{{ route('business.recommend-act-info') }}" data-type="id" data-id="{{ $info->id }}"
                                                           id="recommend_{{ $info->id }}">
                                                    <label class="onoffswitch-label" for="recommend_{{ $info->id }}">
                                                        <span class="onoffswitch-inner"></span>
                                                        <span class="onoffswitch-switch"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ date('Y-m-d H:i:s',$info->addtime) }}</td>
                                        <td>
                                            <a href="{{ route('business.edit-activity-info',['id'=>$info->id]) }}"
                                               class="btn btn-white btn-sm"><i class="fa fa-pencil"></i> 修改 </a>
                                            <a href="javascript:;" data-url="{{ route('business.del-activity-info') }}"
                                               data-type="id" data-id="{{ $info->id }}"
                                               class="btn btn-white btn-sm btn-del-info"><i class="fa fa-trash"></i> 删除
                                            </a>
                                            @if($info->push_flag == 1 && $info->is_push == 0)
                                                <a href="{{ route('business.push-info',['id'=>$info->id]) }}" class="btn btn-white btn-sm"><i class="fa fa-pencil"></i> 推送 </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="text-right">
                        @if(!empty($infos) && !empty($infos->links()))
                            {{ $infos->appends(['sid'=>$sid,'keyword'=>$keyword])->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(function(){
            $('.btn-del-info').click(function(){
                var $this = $(this);
                layer.msg('您确定要删除该资讯吗？',{
                    time:0,
                    btn:['是','否'],
                    yes:function(index){
                        layer.close(index);
                        youyibao.httpSend($this,'get',1);
                    }
                });
            });

            $('.post-info').click(function(){
                youyibao.httpSend($(this),'get',1);
            });


        });
    </script>
@endsection


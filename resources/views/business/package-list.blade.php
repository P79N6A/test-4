@extends('business.layouts.frame-parent')
@section('page-title','套餐列表')
@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>所有套餐</h5>
                    <div class="ibox-tools">
                        <a href="{{ route('business.add-package') }}" class="btn btn-primary btn-xs">创建套餐</a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <form action="{{ route('business.package-list') }}" method="get">
                            <div class="col-sm-2">
                                <select class="input-sm form-control input-s-sm inline" name="expire">
                                    <option value="0" @if($expire == 0) selected @endif >全部</option>
                                    <option value="1" @if($expire == 1) selected @endif >未过期</option>
                                    <option value="2" @if($expire == 2) selected @endif >已过期</option>
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <select class="input-sm form-control input-s-sm inline" name="visible">
                                    <option value="0" @if($visible == 0) selected @endif >全部</option>
                                    <option value="1" @if($visible == 1) selected @endif >显示</option>
                                    <option value="2" @if($visible == 2) selected @endif >隐藏</option>
                                </select>
                            </div>
                            <div class="col-xs-12 col-sm-2">
                                <input type="text" name="keyword" value="{{ $keyword }}" placeholder="输入套餐关键词"
                                       class="input-sm form-control">
                            </div>
                            <div class="col-sm-2">
                                <input type="text" name="store" value="{{ $store }}" placeholder="输入可用门店关键词"
                                       class="input-sm form-control">
                            </div>
                            <div class="col-sm-1">
                                <span class="input-group-btn"><button type="submit"
                                                                      class="btn btn-sm btn-primary">搜索</button></span>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>套餐名称/套餐ID/二维码</th>
                                <th>可用门店数</th>
                                <th>库存/销量</th>
                                <th>价格</th>
                                <th>前端显示状态</th>
                                <th>创建时间/过期时间</th>
                                <th>排序（小数靠前）</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($packages))
                                <form action="{{ route('business.order-package') }}" method="post"
                                      class="form-order-package">
                                    {{ csrf_field() }}
                                    @foreach($packages as $package)
                                        <tr>
                                            <td>
                                                <a href="/package-detail?id={{ $package->id }}">{{ $package->name }}</a>
                                                <br/> {{ $package->id }}<br/>
                                                <a href="/package-detail?id={{ $package->id }}"><i class="fa fa-qrcode"
                                                                                                   style="font-size:18px;"></i></a>
                                            </td>
                                            <td>
                                                <a href="{{ route('business.package-available-stores',['id'=>$package->id]) }}"
                                                   title="查看可用门店列表"
                                                   class="btn btn-xs btn-success">{{ $package->available_store_count }}</a>
                                            </td>
                                            <td>库:{{ $package->stock }}<br/> 销: {{ $package->sales }}</td>
                                            <td>￥{{ $package->price }}
                                            </td>
                                            <td>
                                                @if($package->flag == 1)<span class="label label-primary">是</span>
                                                @elseif($package->flag == 0)<span class="label label-default">否</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ date('Y-m-d H:i:s',$package->addtime) }}<br/>
                                                <span class="label
                                                    @if($package->expire_date < time()) label-danger @endif">
                                                    {{ date('Y-m-d H:i:s',$package->expire_date) }}
                                                    过期</span>
                                            </td>
                                            <td>
                                                <div class="" style="width:60px;">
                                                    <input type="text" name="orders[{{ $package->id }}]"
                                                           value="{{ $package->display_order }}"
                                                           class="input-sm form-control">
                                                </div>
                                            </td>
                                            <td>
                                                <a href="{{ route('business.edit-package',['id'=>$package->id]) }}"
                                                   class="btn btn-white btn-sm"><i class="fa fa-pencil"></i> 修改 </a>
                                                <a href="#" data-url="{{ route('business.del-package') }}"
                                                   data-type="id" data-id="{{ $package->id }}"
                                                   class="btn btn-white btn-sm btn-del-package"><i
                                                            class="fa fa-trash"></i> 删除 </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>
                                            <button type="button" class="btn btn-primary btn-sm btn-order-package"
                                                    title="从0开始，数字越小越靠前"><i class="fa fa-pencil"></i> 排序
                                            </button>
                                        </td>
                                    </tr>
                                </form>
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="text-right">
                        @if(!empty($packages) && !empty($packages->links()))
                            {{ $packages->appends(['expire'=>$expire,'keyword'=>$keyword,'store'=>$store])->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(function () {
            $('.btn-order-package').click(function () {
                youyibao.httpSend($('form.form-order-package'), 'post', 1);
            });

            $('.btn-del-package').click(function () {
                var $this = $(this);
                layer.msg('您确定要删除该套餐吗？', {
                    time: 0,
                    btn: ['是', '否'],
                    yes: function (index) {
                        layer.close(index);
                        youyibao.httpSend($this, 'get', 1);
                    }
                });
            });

            $('a.quit-sekill').click(function () {
                layer.msg('您确定要退出秒杀活动吗？', {
                    time: 0,
                    btn: ['是', '否'],
                    yes: function (index) {
                        layer.close(index);
                        youyibao.httpSend($('a.quit-sekill'), 'get', 1);
                    }
                });
            });
        });
    </script>
@endsection

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>添加菜单</title>
</head>
<body>
    <div>
        <form class="form-add-menu" method="post" action="/update-menu">
            <input type="hidden" name="id" value="{{ $detail->id }}">
            <table>
                <tr>
                    <td>父菜单：</td>
                    <td>
                        <select name="parent_id">
                            <option value="0">无</option>
                            @foreach($menuTree as $menu)
                                <option value="{{ $menu['id'] }}" @if($detail->parent_id == $menu['id']) selected @endif >{{ $menu['name'] }}</option>

                                @if(!empty($menu['children']))
                                    @foreach($menu['children'] as $child)
                                        <option value="{{ $child['id'] }}" @if($detail->parent_id == $child['id']) selected @endif ><span>&nbsp;&nbsp;|--</span>{{ $child['name'] }}</option>

                                        @if(!empty($child['children']))
                                            @foreach($child['children'] as $tchild)
                                                <option value="{{ $tchild['id'] }}" @if($detail->parent_id == $tchild['id']) selected @endif ><span>&nbsp;&nbsp;&nbsp;&nbsp;|--</span>{{ $tchild['name'] }}</option>
                                            @endforeach
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>操作名称：</td>
                    <td><input name="action" value="{{ $detail->action }}" placeholder="填写对应路由名称"></td>
                </tr>
                <tr>
                    <td>名称：</td>
                    <td><input name="name" value="{{ $detail->name }}"></td>
                </tr>
                <tr>
                    <td>描述：</td>
                    <td><input name="description" value="{{ $detail->description }}"></td>
                </tr>
                @if($detail->parent_id == 0)
                <tr class="icon-list">
                    <td>图标：</td>
                    <td>
                        <select name="icon">
                            <option @if($detail->icon == 'fa-home') selected @endif value="fa-home">主页</option>
                            <option @if($detail->icon == 'fa-gift') selected  @endif value="fa-gift">礼物</option>
                            <option @if($detail->icon == 'fa-bar-chart-o') selected  @endif value="fa-bar-chart-o">柱状图</option>
                            <option @if($detail->icon == 'fa-cubes') selected  @endif value="fa-cubes">立方体</option>
                            <option @if($detail->icon == 'fa-coffee') selected  @endif value="fa-coffee">咖啡</option>
                            <option @if($detail->icon == 'fa-magnet') selected  @endif value="fa-magnet">磁铁</option>
                            <option @if($detail->icon == 'fa-gamepad') selected  @endif value="fa-gamepad">游戏手柄</option>
                            <option @if($detail->icon == 'fa-rmb') selected  @endif value="fa-rmb">人民币符号</option>
                            <option @if($detail->icon == 'fa-image') selected  @endif value="fa-image">图片</option>
                            <option @if($detail->icon == 'fa-newspaper-0') selected  @endif value="fa-newspaper-o">报纸</option>
                            <option @if($detail->icon == 'fa-users') selected  @endif value="fa-users">用户</option>
                        </select>
                    </td>
                </tr>
                @endif
                <tr>
                    <td>是否显示：</td>
                    <td>
                        <select name="display">
                            <option value="1" @if($detail->display == 1) selected @endif >是</option>
                            <option value="0" @if($detail->display == 0) selected @endif >否</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>显示顺序：</td>
                    <td>
                        <input name="display_order" value="{{ $detail->display_order }}" placeholder="大于等于0">
                    </td>
                </tr>
                <tr><td colspan="2"><button class="btn-add-menu" type="button">提交</button></td></tr>
            </table>
        </form>
    </div>
</body>
</html>
<script type="text/javascript" src="merchant/js/jquery.min.js"></script>
<script type="text/javascript" src="merchant/js/youyibao.js"></script>
<script type="text/javascript" src="/merchant/layer/layer/layer.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('.btn-add-menu').click(function(){
            youyibao.httpSend($('form.form-add-menu'),'post',1);
        });
    });
    $('select[name=parent_id]').change(function(){
        if($(this).val() != 0){
            $('.icon-list').hide();
        }else if($(this).val() == 0){
            $('.icon-list').show();
        }
    });
</script>

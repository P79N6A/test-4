<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>添加菜单</title>
</head>
<body>
    <div>
        <form class="form-add-menu" method="post" action="/store-menu">
            <table>
                <tr>
                    <td>父菜单：</td>
                    <td>
                        <select name="parent_id">
                            <option value="0">无</option>
                            @foreach($menus as $menu)
                                <option value="{{ $menu['id'] }}">{{ $menu['name'] }}</option>

                                @if(!empty($menu['children']))
                                    @foreach($menu['children'] as $child)
                                        <option value="{{ $child['id'] }}"><span>&nbsp;&nbsp;|-</span>{{ $child['name'] }}</option>

                                        @if(!empty($child['children']))
                                            @foreach($child['children'] as $tchild)
                                                <option value="{{ $tchild['id'] }}"><span>&nbsp;&nbsp;&nbsp;&nbsp;|-</span>{{ $tchild['name'] }}</option>
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
                    <td><input name="action" placeholder="填写对应路由名称"></td>
                </tr>
                <tr>
                    <td>名称：</td>
                    <td><input name="name"></td>
                </tr>
                <tr>
                    <td>描述：</td>
                    <td><input name="description"></td>
                </tr>
                <tr class="icon-list">
                    <td>图标：</td>
                    <td>
                        <select name="icon">
                            <option value="fa-home">主页</option>
                            <option value="fa-gift">礼物</option>
                            <option value="fa-bar-chart-o">柱状图</option>
                            <option value="fa-cubes">立方体</option>
                            <option value="fa-coffee">咖啡</option>
                            <option value="fa-magnet">磁铁</option>
                            <option value="fa-gamepad">游戏手柄</option>
                            <option value="fa-rmb">人民币符号</option>
                            <option value="fa-image">图片</option>
                            <option value="fa-newspaper-0">报纸</option>
                            <option value="fa-users">用户</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>是否显示：</td>
                    <td>
                        <select name="display">
                            <option value="1">是</option>
                            <option value="0">否</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>显示顺序：</td>
                    <td>
                        <input name="display_order" placeholder="大于等于0">
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
<script type="text/javascript" src="merchant/layer/layer/layer.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('.btn-add-menu').click(function(){
            youyibao.httpSend($('form.form-add-menu'),'post',1);
        });
        $('select[name=parent_id]').change(function(){
            if($(this).val() != 0){
                $('.icon-list').hide();
            }else if($(this).val() == 0){
                $('.icon-list').show();
            }
        });
    });
</script>

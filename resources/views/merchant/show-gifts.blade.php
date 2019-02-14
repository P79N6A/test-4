<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style type="text/css">
        .show{
            display:block;
        }
        .hide{
            display:none;
        }
    </style>
</head>
<body>
<div>
    <form action="/publish-shake-gift" mothod="post" class="form-publish">
        <input type="hidden" name="activity_id" value="{{ $activityId }}">
        <table border="1" cellspacing="0">
            <tr>
                <td>奖品类型：</td>
                <td>
                    <label for="1">卡券</label>
                    <input type="radio" id="1" name="type" value="1">
                    <label for="2">商品</label>
                    <input type="radio" id="2" name="type" value="2">
                </td>
            </tr>
            <tr>
                <td>奖品：</td>
                <td>
                    <div class="prizes hide">
                        @foreach($tickets as $tk=>$ticket)
                            @if(!in_array($ticket->id,$publishedTickets))
                            <p>
                                <label for="ticket_{{ $tk }}">{{ $ticket->name }}（总库存：{{ $ticket->stock }}）</label>
                                <input id="ticket_{{ $tk }}" type="radio" name="ticket_id" value="{{ $ticket->id }}">
                            </p>
                            @endif
                        @endforeach
                    </div>
                    <div class="prizes hide">
                        @foreach($packages as $k=>$package)
                            @if(!in_array($package->id,$publishedPackages))
                            <p>
                                <label for="package_{{ $k }}">{{ $package->name }}（总库存：{{ $package->stock }}）</label>
                                <input id="package_{{ $k }}" type="radio" name="package_id" value="{{ $package->id }}">
                            </p>
                            @endif
                        @endforeach
                    </div>
                    <div>备注：一次只能选择卡券或商品种类中的一种物品作为奖品</div>
                </td>
            </tr>
            <tr>
                <td>库存：</td>
                <td><input name="stock" placeholder="将从总库存中扣除"></td>
            </tr>
            <tr>
                <td>中奖概率：</td>
                <td><input name="probability" placeholder="小数点后一位小数，如：0.2"></td>
            </tr>
            <tr><td></td><td><button type="button" class="btn-sub">提交</button></td></tr>
        </table>
    </form>
</div>
</body>
</html>
<script type="text/javascript" src="/merchant/js/jquery.min.js"></script>
<script type="text/javascript" src="/merchant/js/youyibao.js"></script>
<script type="text/javascript" src="/merchant/layer/layer/layer.js"></script>
<script>
    $(function(){
        $('input[name=type]').change(function(){
            $('.prizes').hide().eq($(this).val()-1).show();
        });

        // 表单提交
        $('.btn-sub').click(function(){
            youyibao.httpSend($('form.form-publish'),'post',1);

        });

    });
</script>





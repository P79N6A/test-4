<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>发放代金券</title>
    <style type="text/css">
        .hide{
            display:none;
        }
        .show{
            display:block;
        }
    </style>
</head>
<body>
<div>
    <form action="/post-cash-coupon" method="post" class="form-post-coupon">
        <div>
            <input type="hidden" name="id" value="{{ $ticket_id }}">
            <table border="1" cellspacing="0">
                <tr>
                    <td>发放对象：</td>
                    <td>
                        <div class="tabs-content">
                            <span>选择门店：</span>
                            <select name="store-id">
                                <option>请选择门店</option>
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}">{{ $store->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="user-type">
                            <span>目标人群：</span>
                            <label for="consumer">门店消费过的用户</label><input id="consumer" type="radio" name="user_type" value="1">
                            <label for="visitor">门店访客</label><input id="visitor" type="radio" name="user_type" value="2">
                        </div>
                    </td>
                </tr>
                <tr><td colspan="2">备注：先选择门店，再选择用户分类</td></tr>
                <tr>
                    <td></td>
                    <td><button type="button" class="btn-post-coupon">提交</button></td>
                </tr>
            </table>
        </div>
    </form>
</div>
</body>
</html>
<script type="text/javascript" src="merchant/js/jquery.min.js"></script>
<script type="text/javascript" src="merchant/js/youyibao.js"></script>
<script type="text/javascript" src="/merchant/layer/layer/layer.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('.btn-post-coupon').click(function(){
            youyibao.httpSend($('form.form-post-coupon'),'post',1);
        });

    });
</script>

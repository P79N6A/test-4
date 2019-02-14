<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="favicon.ico"> <link href="merchant/css/bootstrap.min14ed.css?v=3.3.6" rel="stylesheet">
    <link href="merchant/css/font-awesome.min93e3.css?v=4.4.0" rel="stylesheet">
    <!-- Data Tables -->
    <link href="merchant/css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">
    <link href="merchant/css/animate.min.css" rel="stylesheet">
    <link href="merchant/css/style.min862f.css?v=4.1.0" rel="stylesheet">
</head>
<body>
    <div class="ibox">
        <table class="table table-striped table-bordered table-hover dataTables-example">
            <thead>
            <tr>
                <th>MAJOR</th>
                <th>MINOR</th>
                <th>门店</th>
                <th>备注</th>
                <th>添加时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($devices as $device)
                <tr>
                    <td>{{ $device->major }}</td>
                    <td>{{ $device->minor }}</td>
                    <td>{{ $device->store_name }}</td>
                    <td>{{ $device->note }}</td>
                    <td>{{ date('Y-m-d H:i:s',$device->addtime) }}</td>
                    <td></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="col-lg-push-1">{!! $devices->links() !!}</div>
</body>
</html>
<script type="text/javascript" src="merchant/js/jquery.min.js"></script>
<script type="text/javascript" src="merchant/js/youyibao.js"></script>
<script type="text/javascript" src="merchant/layer/layer/layer.js"></script>
<script type="text/javascript">
    $(document).ready(function(){

    });
</script>

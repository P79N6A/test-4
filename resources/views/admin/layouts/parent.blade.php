<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('page-title')</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link href="/admin/css/bootstrap.min14ed.css?v=3.3.6" rel="stylesheet">
    <link href="/admin/css/font-awesome.min93e3.css?v=4.4.0" rel="stylesheet">
    <link href="/admin/css/plugins/morris/morris-0.4.3.min.css" rel="stylesheet">
    <link href="/admin/js/plugins/gritter/jquery.gritter.css" rel="stylesheet">
    <link href="/admin/css/animate.min.css" rel="stylesheet">
    <link href="/admin/css/style.min862f.css?v=4.1.0" rel="stylesheet">
    <script src="/admin/js/jquery.min.js?v=2.1.4"></script>
    <script src="/admin/js/bootstrap.min.js?v=3.3.6"></script>
    <script src="/admin/js/plugins/peity/jquery.peity.min.js"></script>
    <script src="/admin/js/demo/peity-demo.min.js"></script>
    <script src="/admin/js/content.min.js?v=1.0.0"></script>
    <script src="/admin/js/plugins/jquery-ui/jquery-ui.min.js"></script>
    <script src="/admin/js/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
    <script src="/admin/js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
    <script src="/admin/js/plugins/easypiechart/jquery.easypiechart.js"></script>
    <script src="/admin/js/plugins/sparkline/jquery.sparkline.min.js"></script>
    <script src="/admin/js/demo/sparkline-demo.min.js"></script>
    <script src="/admin/js/youyibao.js"></script>
    <script src="/admin/js/layer/layer/layer.js"></script>
    <style>
        .table tbody {
            display: block;
            height: 530px;
            overflow-y: scroll;
        }

        table thead {
            display: table;
            width: 100%;
            table-layout: fixed;
            text-align: center;
        }

        table tbody tr {
            display: table;
            width: 100%;
            table-layout: fixed;
            word-break: break-all;
        }

        table thead {
            width: calc( 100% - 1em);
        }

        /* .table-responsive {
            height: 450px;
        } */
    </style>
</head>
<body class="gray-bg">
<div class="row content-tabs css-display">
    <nav class="page-tabs J_menuTabs">
        <div class="page-tabs-content">
            <a href="javascript:;" class="active J_menuTab" data-id="home.html">@yield('page-title')</a>
        </div>
    </nav>
</div>
<div class="wrapper wrapper-content animated fadeInUp" style="position:fixed;width:100%;overflow-y: scroll;top: 50px;bottom:0;">

    @section('main')
        {{-- 主内容区--}}
    @show

</div>
</body>
</html>

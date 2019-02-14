<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('page-title')</title>
    <link rel="shortcut icon" href="favicon.ico"> 
    <link href="/business/css/bootstrap.min14ed.css?v=3.3.6" rel="stylesheet">
    <link href="/business/css/font-awesome.min93e3.css?v=4.4.0" rel="stylesheet">
    <!-- Gritter -->
    <link href="/business/js/plugins/gritter/jquery.gritter.css" rel="stylesheet">
    <link href="/business/css/animate.min.css" rel="stylesheet">
    <link href="/business/css/style.min862f.css?v=4.1.0" rel="stylesheet">
    <link href="/business/css/style.syit.css" rel="stylesheet">


    <script src="/business/js/jquery.min.js?v=2.1.4"></script>
    <script src="/business/js/bootstrap.min.js?v=3.3.6"></script>
    <script src="/business/js/youyibao.js"></script>
    <script src="/business/js/content.min.js?v=1.0.0"></script>

    <script type="text/javascript">
        $(document).ready(function(){
            $(".tooltip-demo").tooltip({selector:"[data-toggle=tooltip]",container:"body"});
        })
    </script>
</head>
<body class="gray-bg">
<div class="row content-tabs">
    <nav class="page-tabs J_menuTabs">
        <div class="page-tabs-content">
            <a href="javascript:;" class="active J_menuTab" data-id="home.html">@yield('page-title')</a>
        </div>
    </nav>
</div>
<div class="wrapper wrapper-content animated fadeInUp">
@section('main')
    {{-- 主内容区--}}
@show
</div>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <title>商户中心</title>
    <!--[if lt IE 9]>
    <meta http-equiv="refresh" content="0;ie.html" />
    <![endif]-->
    <link rel="shortcut icon" href="favicon.ico">
    <link href="/business/css/bootstrap.min14ed.css?v=3.3.6" rel="stylesheet">
    <link href="/business/css/font-awesome.min93e3.css?v=4.4.0" rel="stylesheet">
    <link href="/business/css/animate.min.css" rel="stylesheet">
    <link href="/business/css/style.min862f.css?v=4.1.0" rel="stylesheet">
</head>

<body class="fixed-sidebar full-height-layout gray-bg" style="overflow:hidden">
    <div id="wrapper">
        <!--左侧导航开始-->
        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="nav-close"><i class="fa fa-times-circle"></i>
            </div>
            <div class="sidebar-collapse">
                <ul class="nav" id="side-menu">
                    <li class="nav-header">
                        <div class="dropdown profile-element">
                            <span><img alt="image" class="img-circle" src="/business/img/profile_small.jpg" /></span>
                            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                <span class="clear">
                               <span class="block m-t-xs"><strong class="font-bold">{{ session('username') }}</strong></span>
                                <span class="text-muted text-xs block"><b class="caret"></b></span>
                                </span>
                            </a>
                            <ul class="dropdown-menu animated fadeInRight m-t-xs">
                                <li><a class="J_menuItem" href="/change-password">修改密码</a>
                                </li>
                                <li class="divider"></li>
                                <li><a href="javascript:;" data-url="/logout" class="logout">退出系统</a>
                                </li>
                            </ul>
                        </div>
                        <div class="logo-element">麦麦天空
                        </div>
                    </li>

                    @foreach($leftMenus as $menu)
                        <li>
                            @if($menu['display'] == 1)
                                <a @if(empty($menu['children'])) class="J_menuItem" @endif href=" @if( empty($menu['children'])) {{ $menu['action'] }} @else # @endif ">
                                    <i class="fa @if(!empty($menu['icon'])) {{ $menu['icon'] }} @else fa-home @endif"></i>
                                    <span class="nav-label">{{ $menu['name'] }}</span>
                                    @if(!empty($menu['children']))
                                        <span class="fa arrow"></span>
                                    @endif
                                </a>
                            @endif

                            @if(!empty($menu['children']))
                                @foreach($menu['children'] as $child)
                                    @if(!empty($child['children']))
                                        <ul class="nav nav-second-level">
                                            @if($child['display'] == 1)
                                                <li>
                                                    <a href="#">{{ $child['name'] }}<span class="fa arrow"></span></a>
                                                    @if( !empty($child['children']) )
                                                        <ul class="nav nav-third-level">
                                                            @foreach($child['children'] as $tchild)
                                                                @if($tchild['display'] == 1)
                                                                    <li><a class="J_menuItem" href="/{{ $tchild['action'] }}" data-index="0">{{ $tchild['name'] }}</a></li>
                                                                @endif
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </li>
                                            @endif
                                        </ul>
                                    @else
                                        <ul class="nav nav-second-level">
                                            @if($child['display'] == 1)
                                                <li>
                                                    <a class="J_menuItem" href="/{{ $child['action'] }}" data-index="0">{{ $child['name'] }}</a>
                                                </li>
                                            @endif
                                        </ul>
                                    @endif
                                @endforeach
                            @endif
                        </li>
                    @endforeach
                    <li>
                        <!-- <a class="J_menuItem" href="/download-link" data-index="28">
                            <i class="fa fa-home "></i>
                            <span class="nav-label">商户端下载</span>
                        </a> -->
                    </li>
                </ul>
            </div>
        </nav>
        <!--左侧导航结束-->
        <!--右侧部分开始-->
        <div id="page-wrapper" class="gray-bg dashbard-1">
            <div class="row border-bottom">
                <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
                    <div class="navbar-header"><a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
                        <form role="search" class="navbar-form-custom" method="post" action="javascript:;">
                            <div class="form-group">
                                <input type="text" placeholder="搜索功能暂不可用 …" class="form-control" name="top-search" id="top-search">
                            </div>
                        </form>
                    </div>
                    <ul class="nav navbar-top-links navbar-right">
                        <li class="dropdown hidden-xs">
                            <a class="right-sidebar-toggle" aria-expanded="false">
                                <i class="fa fa-tasks"></i> 主题
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
            <div class="row J_mainContent" id="content-main">
                <iframe class="J_iframe" name="iframe0" width="100%" height="100%" src="/overview" frameborder="0" data-id="home.html" seamless></iframe>
            </div>
            <div class="footer">
                <div class="pull-right">&copy; 2018 麦麦天空</a>
                </div>
            </div>
        </div>
        <!--右侧部分结束-->
        <!--右侧边栏开始-->
        <div id="right-sidebar">
            <div class="sidebar-container">
                <ul class="nav nav-tabs navs-3">
                    <li class="active">
                        <a data-toggle="tab" href="#tab-1">
                            <i class="fa fa-gear"></i> 主题
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div id="tab-1" class="tab-pane active">
                        <div class="sidebar-title">
                            <h3> <i class="fa fa-comments-o"></i> 主题设置</h3>
                            <small><i class="fa fa-tim"></i> 你可以从这里选择和预览主题的布局和样式，这些设置会被保存在本地，下次打开的时候会直接应用这些设置。</small>
                        </div>
                        <div class="skin-setttings">
                            <div class="title">主题设置</div>
                            <div class="setings-item">
                                <span>收起左侧菜单</span>
                                <div class="switch">
                                    <div class="onoffswitch">
                                        <input type="checkbox" name="collapsemenu" class="onoffswitch-checkbox" id="collapsemenu">
                                        <label class="onoffswitch-label" for="collapsemenu">
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="setings-item">
                                <span>固定顶部</span>
                                <div class="switch">
                                    <div class="onoffswitch">
                                        <input type="checkbox" name="fixednavbar" class="onoffswitch-checkbox" id="fixednavbar">
                                        <label class="onoffswitch-label" for="fixednavbar">
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="setings-item">
                                <span>
                                固定宽度
                            </span>
                                <div class="switch">
                                    <div class="onoffswitch">
                                        <input type="checkbox" name="boxedlayout" class="onoffswitch-checkbox" id="boxedlayout">
                                        <label class="onoffswitch-label" for="boxedlayout">
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="title">皮肤选择</div>
                            <div class="setings-item default-skin nb">
                                <span class="skin-name ">
                         <a href="#" class="s-skin-0">
                             默认皮肤
                         </a>
                    </span>
                            </div>
                            <div class="setings-item blue-skin nb">
                                <span class="skin-name ">
                        <a href="#" class="s-skin-1">
                            蓝色主题
                        </a>
                    </span>
                            </div>
                            <div class="setings-item yellow-skin nb">
                                <span class="skin-name ">
                        <a href="#" class="s-skin-3">
                            黄色/紫色主题
                        </a>
                    </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--右侧边栏结束-->
    </div>
    <script src="/business/js/jquery.min.js?v=2.1.4"></script>
    <script src="/business/js/bootstrap.min.js?v=3.3.6"></script>
    <script src="/business/js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="/business/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
    <script src="/business/js/plugins/layer/layer.min.js"></script>
    <script src="/business/js/hplus.min.js?v=4.1.0"></script>
    <script src="/business/js/contabs.min.js"></script>
    <script src="/business/js/plugins/pace/pace.min.js"></script>
    <script src="/business/js/youyibao.js"></script>
    {{--<script src="/business/js/layer/layer/layer.js"></script>--}}
    <script>
        $(function(){
            $('.logout').click(function(){
                youyibao.httpSend($(this),'get',1)
            });
        });
    </script>
</body>
</html>

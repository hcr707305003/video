<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="nav-close"><i class="fa fa-times-circle"></i>
    </div>
    <div class="sidebar-collapse">
        <ul class="nav" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element">
                    <span><img alt="image" class="img-circle" src="{{asset('back')}}/img/profile_small.jpg" /></span>
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                <span class="clear">
                               <span class="block m-t-xs"><strong class="font-bold">{{ Auth::user()->name }}</strong></span>
                                <span class="text-muted text-xs block">超级管理员<b class="caret"></b></span>
                                </span>
                    </a>
                    <ul class="dropdown-menu animated fadeInRight m-t-xs">
                        <li><a class="J_menuItem" href="form_avatar.html">修改头像</a>
                        </li>
                        <li><a class="J_menuItem" href="profile.html">个人资料</a>
                        </li>
                        <li><a class="J_menuItem" href="contacts.html">联系我们</a>
                        </li>
                        <li><a class="J_menuItem" href="mailbox.html">信箱</a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                退出登入
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </li>
                    </ul>
                </div>
                <div class="logo-element">H+
                </div>
            </li>
            <li>
                <a href="{{route('index')}}" onclick="pop(); return false;">
                    <i class="fa fa-home"></i>
                    <span class="nav-label">视频列表</span>
                    <span class="fa arrow"></span>
                </a>
            </li>
            <li>
                <a href="{{route('sonIndex')}}"><i class="fa fa-columns" onclick="pop(); return false;"></i> <span class="nav-label">子站列表</span></a>
            </li>
            <li>
                <a href="{{route('taskIndex')}}">
                    <i class="fa fa fa-bar-chart-o"></i>
                    <span class="nav-label">任务列表</span>
                    <span class="fa arrow"></span>
                </a>
            </li>
        </ul>
    </div>
</nav>
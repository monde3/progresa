<!-- Main Header -->
<header class="main-header">

    <!-- Logo -->
    <a href="{{ url('/home') }}" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini"><b>P</b>RG</span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg"><b>P</b>rogresa</span>
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">{{ trans('adminlte_lang::message.togglenav') }}</span>
        </a>
        
        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
            
            <ul class="nav navbar-nav">
                
                @if (Auth::user()->activo and Auth::user()->rol!='administrador')
                    @if (Auth::user()->rol == 'alumno')
                        <!-- Level area -->
                        <li>
                            <a href="{{ url('/avatar') }}" data-toggle="tooltip" data-placement="bottom" title="{{ trans('adminlte_lang::message.level') }}">
                                    <strong id="header-lvl">
                                        <b>{{ trans('adminlte_lang::message.level') }} {{ Auth::user()->avatar->nivelAvatar() }}</b>
                                    </strong>
                            </a>
                        </li>
                        <li class="progress-li" id="progress_li">
                            <div id="progress-back" class="progress">
                                <div id="level_bar" class="progress-bar progress-bar-success progress-bar-striped"></div>
                            </div>
                        </li>
                        <!-- Points area -->
                        <li>
                            <a href="{{ url('/avatar') }}" data-toggle="tooltip" data-placement="bottom" title="{{ trans('adminlte_lang::message.experience') }}">
                                <i class='glyphicon glyphicon-star'></i>
                                <strong id="header-exp">{{ Auth::user()->avatar->exp }}</strong>
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('/avatar') }}" data-toggle="tooltip" data-placement="bottom" title="{{ trans('adminlte_lang::message.life') }}">
                                <i class='glyphicon glyphicon-heart'></i>
                                <strong id="header-vida">{{ Auth::user()->avatar->vida }}</strong>
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('/avatar') }}" data-toggle="tooltip" data-placement="bottom" title="{{ trans('adminlte_lang::message.gold') }}">
                                <i class='glyphicon glyphicon-usd'></i>
                                <strong id="header-oro">{{ Auth::user()->avatar->oro }}</strong>
                            </a>
                        </li>
                    @endif
                    <!-- Notifications Menu -->
                    <li class="dropdown notifications-menu">
                        <!-- Menu toggle button -->
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-placement="bottom" title="{{ trans('adminlte_lang::message.notifications') }}">
                            <i class="fas fa-bell"></i>
                            <span class="label label-warning">{{ Auth::user()->numNotificaciones() }}</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header">
                                Tiene {{ Auth::user()->numNotificaciones() }}
                                @if (Auth::user()->numNotificaciones() == 1)
                                    notificación
                                @else
                                    notificaciones
                                @endif
                            </li>
                            <li>
                                <!-- Inner Menu: contains the notifications -->
                                <ul class="menu">
                                    @if (Auth::user()->notificaciones->count() > 0)
                                        @foreach (Auth::user()->notificacionesActivas() as $not)
                                        <li>
                                            <a href="{{ route('tratarNotificacion',
                                                    ['notificacion_id' => 
                                                        $not->id]) }}">
                                                @if(!is_null($not->alumno_tarea_id))
                                                <i class="glyphicon glyphicon-tasks text-blue"></i>
                                                @elseif(!is_null($not->oponente_id))
                                                <i class="glyphicon glyphicon-screenshot text-black"></i>
                                                @endif
                                                {{ $not->texto }}
                                            </a>
                                        </li>
                                        @endforeach
                                    @endif
                                    @if (Auth::user()->numNotificacionesAlertaRoja() > 0)
                                        <li><!-- start notification -->
                                            <a href="{{ url('mistareas') }}">
                                                <i class="fa fa-clock-o text-red"></i>
                                                {{ Auth::user()->numNotificacionesAlertaRoja() }}
                                                
                                                @if (Auth::user()->numNotificacionesAlertaRoja() == 1)
                                                    tarea acaba
                                                @else
                                                    tareas acaban
                                                @endif

                                                en menos de 1 semana
                                            </a>
                                        </li><!-- end notification -->
                                    @endif
                                    @if (Auth::user()->numNotificacionesAlertaAmarilla() > 0)
                                        <li><!-- start notification -->
                                            <a href="{{ url('mistareas') }}">
                                                <i class="fa fa-clock-o text-yellow"></i>
                                                {{ Auth::user()->numNotificacionesAlertaAmarilla() }}
                                                tareas acaban en menos de 3 semanas
                                            </a>
                                        </li><!-- end notification -->
                                    @endif
                                </ul>
                            </li>                        
                        </ul>
                    </li>
                @endif

                <li>

                    <a href="{{ url('/logout') }}" data-toggle="tooltip" data-placement="bottom" title="{{ trans('adminlte_lang::message.logout') }}">
                        <i class='glyphicon glyphicon-log-out'></i>
                    </a>
                
                </li>
                
                @if (Auth::guest())
                    <li><a href="{{ url('/register') }}">{{ trans('adminlte_lang::message.register') }}</a></li>
                    <li><a href="{{ url('/login') }}">{{ trans('adminlte_lang::message.login') }}</a></li>
                   
                @endif

                <!-- Control Sidebar Toggle Button -->
                <!--
                <li>
                    <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
                </li> -->
            </ul>
        </div>
    </nav>
</header>

{{-- [multimod] Mise en page des pages PUBLIQUES (visiteurs sans connexion Steam, moteurs de recherche) :
     barre du haut sans menu joueur, balises pour Google (description, canonical, robots, Open Graph).
     Sections : title, subtitle, breadcrumb, content, head, footer, description, canonical, robots. --}}
@php($siteName = \App\Support\PanelSettings::siteName())
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | {{ $siteName }}</title>
    @hasSection('description')
        <meta name="description" content="@yield('description')">
        <meta property="og:description" content="@yield('description')">
    @endif
    <meta name="robots" content="@yield('robots', 'index,follow')">
    @hasSection('canonical')
        <link rel="canonical" href="@yield('canonical')">
        <meta property="og:url" content="@yield('canonical')">
    @endif
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="@yield('title')">

    <link href="{{asset('templates/adminlte205/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="{{asset('templates/adminlte205/dist/css/AdminLTE.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('templates/adminlte205/dist/css/skins/_all-skins.min.css')}}" rel="stylesheet" type="text/css" />
    <style>
        .navbar-brand { font-weight: 700; max-width: 60vw; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .content-header > h1 { word-break: break-word; }
    </style>

    @yield('head')
</head>
<body class="skin-blue layout-top-nav">
<div class="wrapper">

    <header class="main-header">
        <nav class="navbar navbar-static-top">
            <div class="container">
                <div class="navbar-header">
                    <a href="{{ url('/') }}" class="navbar-brand">{{ $siteName }}</a>
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
                        <span class="sr-only">{{ __('Menu') }}</span><i class="fa fa-bars"></i>
                    </button>
                </div>
                <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
                    <ul class="nav navbar-nav">
                        <li class="{{ request()->routeIs('players.*') ? 'active' : '' }}">
                            <a href="{{ route('players.index') }}"><i class="fa fa-users"></i> {{ __('Players') }}</a>
                        </li>
                    </ul>
                </div>
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        @include('templates.adminlte205.includes.langswitch', ['style' => 'nav'])
                        @if (session('store_user_id'))
                            <li><a href="{{ route('userpanel.dashboard') }}" rel="nofollow"><i class="fa fa-user"></i> <span class="hidden-xs">{{ __('My store') }}</span></a></li>
                        @else
                            <li><a href="{{ route('userpanel.auth.index') }}" rel="nofollow"><i class="fa fa-steam"></i> <span class="hidden-xs">{{ __('Sign in with Steam') }}</span></a></li>
                        @endif
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <div class="content-wrapper">
        <div class="container">
            <section class="content-header">
                <h1>
                    @yield('title')
                    <small>@yield('subtitle')</small>
                </h1>
                <ol class="breadcrumb">
                    @yield('breadcrumb')
                </ol>
            </section>

            <section class="content">
                @include('templates.adminlte205.includes.errorcallout')
                @yield('content')
            </section>
        </div>
    </div>

    <footer class="main-footer">
        <div class="container">
            @include('templates.adminlte205.includes.cont-footer')
        </div>
    </footer>

</div>

<script src="{{asset('templates/adminlte205/plugins/jQuery/jQuery-2.1.3.min.js')}}"></script>
<script src="{{asset('templates/adminlte205/bootstrap/js/bootstrap.min.js')}}" type="text/javascript"></script>
<script src="{{asset('templates/adminlte205/dist/js/app.min.js')}}" type="text/javascript"></script>

@yield('footer')

</body>
</html>

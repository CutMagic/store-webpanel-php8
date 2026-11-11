{{-- [multimod] Profil d'un joueur : SteamID, crédits, objets équipés et possédés (lecture seule).
     Sert aussi la page publique /players/{id} ($public = true, mise en page $layout, routes $routes). --}}
@extends($layout)

@section('title', $player->name)

@section('subtitle', __('player profile'))

@if ($public)
    @section('description', trim(__('Profile of :name on :site.', ['name' => $player->name, 'site' => \App\Support\PanelSettings::siteName()])
        . ($steam ? ' SteamID ' . $steam['steam2'] . '.' : '')
        . ' ' . __(':count owned item(s), :equipped equipped.', ['count' => $itemCount, 'equipped' => $equippedCount])
        . ($showCredits ? ' ' . __(':credits credits.', ['credits' => number_format($player->credits, 0, ',', ' ')]) : '')))
    @section('canonical', route('players.show', ['id' => $player->id]))
@endif

@section('breadcrumb')
    @if ($public)
        <li><a href="{{ url('/') }}"><i class="fa fa-home"></i> {{ __('Home') }}</a></li>
    @else
        <li><a href="{{URL::route('userpanel.dashboard')}}"><i class="fa fa-dashboard"></i> {{ __('Home') }}</a></li>
    @endif
    <li><a href="{{ route($routes . 'index') }}">{{ __('Players') }}</a></li>
    <li class="active">{{ __('Profile') }}</li>
@stop

@section('head')
    <style>
        .profile-avatar { width: 96px; height: 96px; border-radius: 50%; float: left; margin-right: 18px; }
        .steamids { margin: 6px 0 0; }
        .steamids dt { float: left; width: 90px; color: #444; font-weight: 600; }
        .steamids dd { margin-left: 100px; font-family: Consolas, Menlo, monospace; }
    </style>
    @if ($public)
        {{-- données structurées "page de profil" pour Google (JSON_HEX_TAG : un pseudo ne peut pas fermer la balise) --}}
        <script type="application/ld+json">{!! json_encode(array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'ProfilePage',
            'url' => route('players.show', ['id' => $player->id]),
            'mainEntity' => array_filter([
                '@type' => 'Person',
                'name' => $player->name,
                'identifier' => $steam ? $steam['steam2'] : null,
                'sameAs' => $steam ? $steam['url'] : null,
            ]),
        ]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_INVALID_UTF8_SUBSTITUTE) !!}</script>
    @endif
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body clearfix">
                    <img src="{{ route('userpanel.steamimage.auth', ['auth' => max(0, (int) $player->auth)]) }}" class="profile-avatar" alt="{{ $player->name }}">
                    <h3 style="margin-top: 4px">
                        {{ $player->name }}
                        @if ($isMe) <span class="label label-warning">{{ __('you') }}</span> @endif
                    </h3>
                    @if ($steam)
                        <dl class="steamids">
                            <dt>SteamID</dt><dd>{{ $steam['steam2'] }}</dd>
                            <dt>SteamID3</dt><dd>{{ $steam['steam3'] }}</dd>
                            <dt>SteamID64</dt><dd>{{ $steam['steam64'] }}</dd>
                        </dl>
                        <a href="{{ $steam['url'] }}" target="_blank" rel="noopener noreferrer" class="btn btn-default btn-sm" style="margin-top: 8px">
                            <i class="fa fa-steam"></i> {{ __('View Steam profile') }}
                        </a>
                    @else
                        <p class="text-muted">{{ __('Unknown SteamID (player without a valid Steam account).') }}</p>
                    @endif
                    <a href="{{ route($routes . 'index') }}" class="btn btn-default btn-sm" style="margin-top: 8px">
                        <i class="fa fa-arrow-left"></i> {{ __('Back to players') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        @if ($showCredits)
            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="fa fa-money"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ __('Credits') }}</span>
                        <span class="info-box-number">{{ number_format($player->credits, 0, ',', ' ') }}</span>
                        <span class="progress-description">{!! __('#:rank richest player', ['rank' => (int) $rank]) !!}</span>
                    </div>
                </div>
            </div>
        @endif
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="fa fa-cubes"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">{{ __('Owned items') }}</span>
                    <span class="info-box-number">{{ $itemCount }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-green"><i class="fa fa-check"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">{{ __('Equipped items') }}</span>
                    <span class="info-box-number">{{ $equippedCount }}</span>
                </div>
            </div>
        </div>
    </div>

    @foreach (array_chunk($equipped, 3) as $chunk)
    <div class="row">
        @foreach ($chunk as $l)
            <div class="col-md-4">
                <div class="box box-success">
                    <div class="box-header"><h3 class="box-title">{{ __('Loadout :name', ['name' => $l->name]) }}</h3></div>
                    <div class="box-body no-padding">
                        <table class="table table-condensed">
                            @forelse ($l->items as $it)
                                <tr><td>{{ $it->display_name }}</td><td class="text-muted">{{ $it->slot }}</td></tr>
                            @empty
                                <tr><td class="text-muted">{{ __('Nothing equipped.') }}</td></tr>
                            @endforelse
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @endforeach

    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header"><h3 class="box-title">{{ __('Owned items') }}</h3></div>
                <div class="box-body">
                    <table class="table table-bordered table-striped" id="table">
                        <thead>
                            <tr><th>{{ __('Item') }}</th><th>{{ __('Category') }}</th><th>{{ __('Type') }}</th><th>{{ __('Quantity') }}</th></tr>
                        </thead>
                        <tbody>
                        @foreach ($owned as $o)
                            <tr>
                                <td>{{ $o->display_name }}<br><small class="text-muted">{{ $o->description }}</small></td>
                                <td>{{ $o->category }}</td>
                                <td>{{ $o->type }}</td>
                                <td>{{ $o->quantity }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@section('footer')
    <link rel="stylesheet" type="text/css" href="{{asset('templates/adminlte205/dist/css/jquery.dataTables.min.css')}}">
    <script type="text/javascript" charset="utf8" src="{{asset('templates/adminlte205/dist/js/jquery.dataTables.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            $('#table').DataTable({ pageLength: 25, language: @include('templates.adminlte205.includes.dtlang') });
        });
    </script>
@stop

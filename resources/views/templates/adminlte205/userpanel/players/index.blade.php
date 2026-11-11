{{-- [multimod] Classement des joueurs, recherche par pseudo ou SteamID (lecture seule) --}}
@extends('templates.adminlte205.userpanel.app')

@section('title', __('Players'))

@section('subtitle', $showCredits ? __('ranking by credits') : __('player list'))

@section('breadcrumb')
    <li><a href="{{URL::route('userpanel.dashboard')}}"><i class="fa fa-dashboard"></i> {{ __('Home') }}</a></li>
    <li class="active">{{ __('Players') }}</li>
@stop

@section('head')
    <style>
        .player-avatar { width: 32px; height: 32px; border-radius: 50%; margin-right: 8px; }
        .steamid { font-family: Consolas, Menlo, monospace; white-space: nowrap; }
        tr.is-me td { background: #fcf8e3 !important; }
    </style>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header">
                    <form method="get" action="{{ route('userpanel.players.index') }}" class="form-inline">
                        <div class="input-group" style="max-width: 560px; width: 100%">
                            <input type="text" name="q" value="{{ $q }}" maxlength="64" class="form-control"
                                   placeholder="{{ __('Name or SteamID (STEAM_0:1:..., [U:1:...], 7656119..., Steam profile link)') }}">
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> {{ __('Search') }}</button>
                            </span>
                        </div>
                        @if ($q !== '')
                            <a href="{{ route('userpanel.players.index') }}" class="btn btn-default">{{ __('Show all') }}</a>
                        @endif
                        @if ($myPage)
                            <a href="{{ route('userpanel.players.index', ['page' => $myPage]) }}" class="btn btn-default"><i class="fa fa-user"></i> {{ __('My rank') }}</a>
                        @endif
                    </form>
                </div>
                <div class="box-body no-padding table-responsive">
                    <table class="table table-striped table-hover">
                        <tr>
                            @if ($showCredits)<th style="width: 60px">{{ __('Rank') }}</th>@endif
                            <th>{{ __('Player') }}</th>
                            <th>SteamID</th>
                            @if ($showCredits)<th class="text-right">{{ __('Credits') }}</th>@endif
                            <th class="text-right">{{ __('Items') }}</th>
                        </tr>
                        @forelse ($rows as $r)
                            <tr class="{{ $r->is_me ? 'is-me' : '' }}">
                                @if ($showCredits)<td>{{ $r->rank }}</td>@endif
                                <td>
                                    <a href="{{ route('userpanel.players.show', ['id' => $r->id]) }}">
                                        <img src="{{ route('userpanel.steamimage.auth', ['auth' => max(0, (int) $r->auth)]) }}" class="player-avatar" alt="" loading="lazy">{{ $r->name }}
                                    </a>
                                    @if ($r->is_me) <span class="label label-warning">{{ __('you') }}</span> @endif
                                </td>
                                <td>
                                    @if ($r->steam)
                                        <span class="steamid">{{ $r->steam['steam2'] }}</span>
                                        <a href="{{ $r->steam['url'] }}" target="_blank" rel="noopener noreferrer" title="{{ __('Steam profile') }} ({{ $r->steam['steam64'] }})"><i class="fa fa-steam"></i></a>
                                    @else
                                        <span class="text-muted">{{ __('unknown') }}</span>
                                    @endif
                                </td>
                                @if ($showCredits)<td class="text-right">{{ number_format($r->credits, 0, ',', ' ') }}</td>@endif
                                <td class="text-right">{{ $r->items }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5">{{ __('No player found.') }}</td></tr>
                        @endforelse
                    </table>
                </div>
                <div class="box-footer clearfix">
                    <span class="text-muted">{{ __(':count player(s)', ['count' => $players->total()]) }}</span>
                    <div class="pull-right">{{ $players->onEachSide(1)->links('pagination::bootstrap-4') }}</div>
                </div>
            </div>
        </div>
    </div>
@stop

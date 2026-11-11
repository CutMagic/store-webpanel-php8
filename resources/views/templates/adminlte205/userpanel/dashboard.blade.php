{{-- [multimod] Tableau de bord du joueur (Store 1.2) - textes traduits (lang/fr.json) --}}
@extends('templates.adminlte205.userpanel.app')

@section('title', __('Dashboard'))

@section('subtitle', $user->name)

@section('breadcrumb')
    <li><a href="{{URL::route('userpanel.dashboard')}}"><i class="fa fa-dashboard"></i> {{ __('Home') }}</a></li>
    <li class="active">{{ __('Dashboard') }}</li>
@stop

@section('content')
    <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-yellow"><i class="fa fa-money"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">{{ __('Credits') }}</span>
                    <span class="info-box-number">{{ number_format($user->credits, 0, ',', ' ') }}</span>
                    @if (config('userpanel.player_profiles') && config('userpanel.player_profiles_credits'))
                        <a href="{{ route('userpanel.players.index', ['page' => (int) ceil($rank / 25)]) }}">{!! __('#:rank richest player', ['rank' => (int) $rank]) !!}</a>
                    @else
                        <span class="progress-description">{!! __('#:rank richest player', ['rank' => (int) $rank]) !!}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="fa fa-cubes"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">{{ __('Owned items') }}</span>
                    <span class="info-box-number">{{ $itemCount }}</span>
                    <a href="{{URL::route('userpanel.useritems.index')}}">{{ __('See my items') }}</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-green"><i class="fa fa-check"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">{{ __('Equipped items') }}</span>
                    <span class="info-box-number">{{ $equippedCount }}</span>
                    <a href="{{URL::route('userpanel.loadouts.index')}}">{{ __('Manage') }}</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header"><h3 class="box-title">{{ __('Latest items received') }}</h3></div>
                <div class="box-body no-padding">
                    <table class="table table-striped">
                        <tr><th>{{ __('Item') }}</th><th>{{ __('Type') }}</th><th>{{ __('Date') }}</th><th>{{ __('Obtained by') }}</th></tr>
                        @forelse($latest as $l)
                            <tr>
                                <td>{{ $l->display_name }}</td>
                                <td>{{ $l->type }}</td>
                                <td>{{ $l->acquire_date }}</td>
                                <td>{{ ['shop' => __('in-game shop'), 'web' => __('website'), 'gift' => __('gift'), 'trade' => __('trade'), 'admin' => __('admin'), 'other' => __('other')][$l->acquire_method] ?? $l->acquire_method }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4">{{ __('No items yet.') }}</td></tr>
                        @endforelse
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

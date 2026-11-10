{{-- [multimod] Tableau de bord du joueur (Store 1.2) --}}
@extends('templates.adminlte205.userpanel.app')

@section('title', 'Tableau de bord')

@section('subtitle', $user->name)

@section('breadcrumb')
    <li><a href="{{URL::route('userpanel.dashboard')}}"><i class="fa fa-dashboard"></i> Accueil</a></li>
    <li class="active">Tableau de bord</li>
@stop

@section('content')
    <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-yellow"><i class="fa fa-money"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Crédits</span>
                    <span class="info-box-number">{{ number_format($user->credits, 0, ',', ' ') }}</span>
                    <span class="progress-description">{{ $rank }}<sup>e</sup> joueur le plus riche</span>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="fa fa-cubes"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Objets possédés</span>
                    <span class="info-box-number">{{ $itemCount }}</span>
                    <a href="{{URL::route('userpanel.useritems.index')}}">Voir mes objets</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-green"><i class="fa fa-check"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Objets équipés</span>
                    <span class="info-box-number">{{ $equippedCount }}</span>
                    <a href="{{URL::route('userpanel.loadouts.index')}}">Gérer</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header"><h3 class="box-title">Derniers objets reçus</h3></div>
                <div class="box-body no-padding">
                    <table class="table table-striped">
                        <tr><th>Objet</th><th>Type</th><th>Date</th><th>Obtenu par</th></tr>
                        @forelse($latest as $l)
                            <tr>
                                <td>{{ $l->display_name }}</td>
                                <td>{{ $l->type }}</td>
                                <td>{{ $l->acquire_date }}</td>
                                <td>{{ ['shop' => 'boutique en jeu', 'web' => 'site', 'gift' => 'cadeau', 'trade' => 'échange', 'admin' => 'admin', 'other' => 'autre'][$l->acquire_method] ?? $l->acquire_method }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4">Aucun objet pour l'instant.</td></tr>
                        @endforelse
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

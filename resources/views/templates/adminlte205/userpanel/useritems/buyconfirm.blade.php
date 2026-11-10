{{-- [multimod] Confirmation d'achat (Store 1.2) --}}
@extends('templates.adminlte205.userpanel.app')

@section('title', 'Acheter')

@section('subtitle', $item->display_name)

@section('breadcrumb')
    <li><a href="{{URL::route('userpanel.dashboard')}}"><i class="fa fa-dashboard"></i> Accueil</a></li>
    <li><a href="{{URL::route('userpanel.useritems.buyoverview')}}">Boutique</a></li>
    <li class="active">Acheter</li>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="box">
                <div class="box-header"><h3 class="box-title">Confirmer l'achat</h3></div>
                <div class="box-body no-padding">
                    <table class="table table-striped">
                        <tr><td>Objet</td><td>{{ $item->display_name }}</td></tr>
                        <tr><td>Description</td><td>{{ $item->web_description ?: $item->description }}</td></tr>
                        <tr><td>Catégorie</td><td>{{ $item->category ? $item->category->display_name : '' }}</td></tr>
                        <tr><td>Type / emplacement</td><td>{{ $item->type }} / {{ $item->loadout_slot }}</td></tr>
                        <tr><td>Prix</td><td><b>{{ $item->price }}</b> crédits (tu en as {{ $user->credits }})</td></tr>
                        <tr><td>Déjà possédé</td><td>{{ $owned }} exemplaire(s)</td></tr>
                        <tr><td>Infos</td><td>
                            @if($item->is_refundable == 1) <span class="badge bg-green">revendable</span> @else <span class="badge bg-red">non revendable</span> @endif
                            @if($item->enable_server_restriction == 1) <span class="badge bg-yellow">seulement sur certains serveurs</span> @endif
                        </td></tr>
                    </table>
                </div>
                <div class="box-footer">
                    @if(!$buyable)
                        <p class="text-red">Cet objet ne peut pas être acheté sur le site.</p>
                    @elseif($item->price > $user->credits)
                        <p class="text-red">Il te manque {{ $item->price - $user->credits }} crédits.</p>
                    @else
                        {!! Form::open(['method' => 'post', 'url' => route('userpanel.useritems.buyproc', ['item_id' => $item->id])]) !!}
                        {!! Form::submit('Acheter pour ' . $item->price . ' crédits', ['class' => 'btn btn-success']) !!}
                        <a class="btn btn-default" href="{{URL::route('userpanel.useritems.buyoverview')}}">Annuler</a>
                        {!! Form::close() !!}
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop

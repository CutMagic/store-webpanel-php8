{{-- [multimod] Objets équipés par loadout (Store 1.2) --}}
@extends('templates.adminlte205.userpanel.app')

@section('title', 'Objets équipés')

@section('subtitle', 'pris en compte à ta prochaine apparition en jeu')

@section('breadcrumb')
    <li><a href="{{URL::route('userpanel.dashboard')}}"><i class="fa fa-dashboard"></i> Accueil</a></li>
    <li class="active">Objets équipés</li>
@stop

@section('content')
    <?php $slots = collect($owned)->groupBy('loadout_slot'); ?>
    @foreach($loadouts as $i => $loadout)
        <div class="box box-default {{ $i == 0 ? '' : 'collapsed-box' }}">
            <div class="box-header with-border">
                <h3 class="box-title">{{ $loadout->display_name }}
                    <small>@if($loadout->game) {{ $loadout->game }} @endif @if($loadout->class) / {{ $loadout->class }} @endif @if($loadout->team) / équipe {{ $loadout->team }} @endif</small>
                </h3>
                <span class="label label-success">{{ isset($equipped[$loadout->id]) ? count($equipped[$loadout->id]) : 0 }} équipé(s)</span>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa {{ $i == 0 ? 'fa-minus' : 'fa-plus' }}"></i></button>
                </div>
            </div>
            <div class="box-body no-padding">
                <table class="table table-condensed">
                    <tr><th style="width: 20%">Emplacement</th><th>Tes objets</th></tr>
                    @forelse($slots as $slot => $items)
                        <tr>
                            <td><b>{{ $slot }}</b></td>
                            <td>
                                @foreach($items as $it)
                                    <?php $on = isset($equipped[$loadout->id][$it->id]); ?>
                                    {!! Form::open(['method' => 'post', 'style' => 'display:inline-block; margin: 2px', 'url' => route($on ? 'userpanel.loadouts.unequip' : 'userpanel.loadouts.equip', [$loadout->id, $it->id])]) !!}
                                    <button type="submit" class="btn btn-xs {{ $on ? 'btn-success' : 'btn-default' }}" title="{{ $on ? 'Cliquer pour retirer' : 'Cliquer pour équiper' }}">
                                        @if($on)<i class="fa fa-check"></i>@endif {{ $it->display_name }}
                                    </button>
                                    {!! Form::close() !!}
                                @endforeach
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="2">Tu n'as aucun objet équipable. <a href="{{URL::route('userpanel.useritems.buyoverview')}}">Boutique</a></td></tr>
                    @endforelse
                </table>
            </div>
        </div>
    @endforeach
@stop

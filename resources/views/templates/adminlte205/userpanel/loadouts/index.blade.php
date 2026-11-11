{{-- [multimod] Objets équipés par loadout (Store 1.2) - textes traduits (lang/fr.json) --}}
@extends('templates.adminlte205.userpanel.app')

@section('title', __('Equipped items'))

@section('subtitle', __('applied the next time you spawn in game'))

@section('breadcrumb')
    <li><a href="{{URL::route('userpanel.dashboard')}}"><i class="fa fa-dashboard"></i> {{ __('Home') }}</a></li>
    <li class="active">{{ __('Equipped items') }}</li>
@stop

@section('content')
    <?php $slots = collect($owned)->groupBy('loadout_slot'); ?>
    @foreach($loadouts as $i => $loadout)
        <div class="box box-default {{ $i == 0 ? '' : 'collapsed-box' }}">
            <div class="box-header with-border">
                <h3 class="box-title">{{ $loadout->display_name }}
                    <small>@if($loadout->game) {{ $loadout->game }} @endif @if($loadout->class) / {{ $loadout->class }} @endif @if($loadout->team) / {{ __('team :team', ['team' => $loadout->team]) }} @endif</small>
                </h3>
                <span class="label label-success">{{ __(':count equipped', ['count' => isset($equipped[$loadout->id]) ? count($equipped[$loadout->id]) : 0]) }}</span>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa {{ $i == 0 ? 'fa-minus' : 'fa-plus' }}"></i></button>
                </div>
            </div>
            <div class="box-body no-padding">
                <table class="table table-condensed">
                    <tr><th style="width: 20%">{{ __('Slot') }}</th><th>{{ __('Your items') }}</th></tr>
                    @forelse($slots as $slot => $items)
                        <tr>
                            <td><b>{{ $slot }}</b></td>
                            <td>
                                @foreach($items as $it)
                                    <?php $on = isset($equipped[$loadout->id][$it->id]); ?>
                                    {!! Form::open(['method' => 'post', 'style' => 'display:inline-block; margin: 2px', 'url' => route($on ? 'userpanel.loadouts.unequip' : 'userpanel.loadouts.equip', [$loadout->id, $it->id])]) !!}
                                    <button type="submit" class="btn btn-xs {{ $on ? 'btn-success' : 'btn-default' }}" title="{{ $on ? __('Click to unequip') : __('Click to equip') }}">
                                        @if($on)<i class="fa fa-check"></i>@endif {{ $it->display_name }}
                                    </button>
                                    {!! Form::close() !!}
                                @endforeach
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="2">{{ __('You have no item that can be equipped.') }} <a href="{{URL::route('userpanel.useritems.buyoverview')}}">{{ __('Shop') }}</a></td></tr>
                    @endforelse
                </table>
            </div>
        </div>
    @endforeach
@stop

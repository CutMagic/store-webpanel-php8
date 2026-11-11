{{-- [multimod] Confirmation d'achat (Store 1.2) - textes traduits (lang/fr.json) --}}
@extends('templates.adminlte205.userpanel.app')

@section('title', __('Buy'))

@section('subtitle', $item->display_name)

@section('breadcrumb')
    <li><a href="{{URL::route('userpanel.dashboard')}}"><i class="fa fa-dashboard"></i> {{ __('Home') }}</a></li>
    <li><a href="{{URL::route('userpanel.useritems.buyoverview')}}">{{ __('Shop') }}</a></li>
    <li class="active">{{ __('Buy') }}</li>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="box">
                <div class="box-header"><h3 class="box-title">{{ __('Confirm the purchase') }}</h3></div>
                <div class="box-body no-padding">
                    <table class="table table-striped">
                        <tr><td>{{ __('Item') }}</td><td>{{ $item->display_name }}</td></tr>
                        <tr><td>{{ __('Description') }}</td><td>{{ $item->web_description ?: $item->description }}</td></tr>
                        <tr><td>{{ __('Category') }}</td><td>{{ $item->category ? $item->category->display_name : '' }}</td></tr>
                        <tr><td>{{ __('Type / slot') }}</td><td>{{ $item->type }} / {{ $item->loadout_slot }}</td></tr>
                        <tr><td>{{ __('Price') }}</td><td><b>{{ $item->price }}</b> {{ __('credits (you have :credits)', ['credits' => $user->credits]) }}</td></tr>
                        <tr><td>{{ __('Already owned') }}</td><td>{{ __(':count copy(ies)', ['count' => $owned]) }}</td></tr>
                        <tr><td>{{ __('Info') }}</td><td>
                            @if($item->is_refundable == 1) <span class="badge bg-green">{{ __('can be sold') }}</span> @else <span class="badge bg-red">{{ __('cannot be sold') }}</span> @endif
                            @if($item->enable_server_restriction == 1) <span class="badge bg-yellow">{{ __('only on some servers') }}</span> @endif
                        </td></tr>
                    </table>
                </div>
                <div class="box-footer">
                    @if(!$buyable)
                        <p class="text-red">{{ __('This item cannot be bought on the website.') }}</p>
                    @elseif($item->price > $user->credits)
                        <p class="text-red">{{ __('You are missing :credits credits.', ['credits' => $item->price - $user->credits]) }}</p>
                    @else
                        {!! Form::open(['method' => 'post', 'url' => route('userpanel.useritems.buyproc', ['item_id' => $item->id])]) !!}
                        {!! Form::submit(__('Buy for :price credits', ['price' => $item->price]), ['class' => 'btn btn-success']) !!}
                        <a class="btn btn-default" href="{{URL::route('userpanel.useritems.buyoverview')}}">{{ __('Cancel') }}</a>
                        {!! Form::close() !!}
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop

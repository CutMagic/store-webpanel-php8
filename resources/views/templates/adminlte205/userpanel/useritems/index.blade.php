{{-- [multimod] Inventaire du joueur (Store 1.2), regroupé par objet - textes traduits (lang/fr.json) --}}
@extends('templates.adminlte205.userpanel.app')

@section('title', __('My items'))

@section('subtitle', __(':credits credits', ['credits' => number_format($user->credits, 0, ',', ' ')]))

@section('breadcrumb')
    <li><a href="{{URL::route('userpanel.dashboard')}}"><i class="fa fa-dashboard"></i> {{ __('Home') }}</a></li>
    <li class="active">{{ __('My items') }}</li>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">{{ __('Owned items') }}</h3>
                    <p class="text-muted">{{ __('Resale: :percent % of the price. "Non-refundable" items are simply thrown away.', ['percent' => round($refundfee * 100)]) }}</p>
                </div>
                <div class="box-body">
                    <table class="table table-bordered table-striped" id="table">
                        <thead>
                            <tr><th>{{ __('Item') }}</th><th>{{ __('Category') }}</th><th>{{ __('Type') }}</th><th>{{ __('Slot') }}</th><th>{{ __('Quantity') }}</th><th>{{ __('Price') }}</th><th>{{ __('Action') }}</th></tr>
                        </thead>
                        <tbody>
                        @foreach($owned as $o)
                            <tr>
                                <td>{{ $o->display_name }}<br><small class="text-muted">{{ $o->description }}</small></td>
                                <td>{{ $o->category }}</td>
                                <td>{{ $o->type }}</td>
                                <td>{{ $o->loadout_slot }}</td>
                                <td>{{ $o->quantity }}</td>
                                <td>{{ $o->price }}</td>
                                <td style="white-space: nowrap">
                                    {!! Form::open(['method' => 'post', 'url' => route('userpanel.useritems.sell', ['item_id' => $o->id]), 'style' => 'display:inline', 'onsubmit' => 'return confirm(' . json_encode(__('Confirm?'), JSON_HEX_APOS | JSON_HEX_QUOT) . ');']) !!}
                                    {!! Form::hidden('all', '0') !!}
                                    @if ($o->is_refundable == 1)
                                        {!! Form::submit(__('Sell 1 (+:amount)', ['amount' => floor($o->price * $refundfee)]), ['class' => 'btn btn-warning btn-xs']) !!}
                                    @else
                                        {!! Form::submit(__('Throw away 1'), ['class' => 'btn btn-danger btn-xs']) !!}
                                    @endif
                                    {!! Form::close() !!}
                                    @if ($o->quantity > 1)
                                        {!! Form::open(['method' => 'post', 'url' => route('userpanel.useritems.sell', ['item_id' => $o->id]), 'style' => 'display:inline', 'onsubmit' => 'return confirm(' . json_encode($o->is_refundable == 1 ? __('Sell all?') : __('Throw all away?'), JSON_HEX_APOS | JSON_HEX_QUOT) . ');']) !!}
                                        {!! Form::hidden('all', '1') !!}
                                        {!! Form::submit($o->is_refundable == 1 ? __('Sell all') : __('Throw all away'), ['class' => 'btn btn-danger btn-xs']) !!}
                                        {!! Form::close() !!}
                                    @endif
                                </td>
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

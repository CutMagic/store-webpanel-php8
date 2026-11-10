{{-- [multimod] Inventaire du joueur (Store 1.2), regroupé par objet --}}
@extends('templates.adminlte205.userpanel.app')

@section('title', 'Mes objets')

@section('subtitle', number_format($user->credits, 0, ',', ' ') . ' crédits')

@section('breadcrumb')
    <li><a href="{{URL::route('userpanel.dashboard')}}"><i class="fa fa-dashboard"></i> Accueil</a></li>
    <li class="active">Mes objets</li>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Objets possédés</h3>
                    <p class="text-muted">Revente : {{ round($refundfee * 100) }} % du prix. Les objets « non remboursables » sont simplement jetés.</p>
                </div>
                <div class="box-body">
                    <table class="table table-bordered table-striped" id="table">
                        <thead>
                            <tr><th>Objet</th><th>Catégorie</th><th>Type</th><th>Emplacement</th><th>Quantité</th><th>Prix</th><th>Action</th></tr>
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
                                    {!! Form::open(['method' => 'post', 'url' => route('userpanel.useritems.sell', ['item_id' => $o->id]), 'style' => 'display:inline', 'onsubmit' => "return confirm('Confirmer ?');"]) !!}
                                    {!! Form::hidden('all', '0') !!}
                                    @if ($o->is_refundable == 1)
                                        {!! Form::submit('Vendre 1 (+' . floor($o->price * $refundfee) . ')', ['class' => 'btn btn-warning btn-xs']) !!}
                                    @else
                                        {!! Form::submit('Jeter 1', ['class' => 'btn btn-danger btn-xs']) !!}
                                    @endif
                                    {!! Form::close() !!}
                                    @if ($o->quantity > 1)
                                        {!! Form::open(['method' => 'post', 'url' => route('userpanel.useritems.sell', ['item_id' => $o->id]), 'style' => 'display:inline', 'onsubmit' => "return confirm('Tout vendre ?');"]) !!}
                                        {!! Form::hidden('all', '1') !!}
                                        {!! Form::submit($o->is_refundable == 1 ? 'Tout vendre' : 'Tout jeter', ['class' => 'btn btn-danger btn-xs']) !!}
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
            $('#table').DataTable({ pageLength: 25, language: { search: 'Rechercher :', lengthMenu: '_MENU_ par page', info: '_START_ à _END_ sur _TOTAL_', paginate: { previous: 'Préc.', next: 'Suiv.' }, zeroRecords: 'Aucun objet', infoEmpty: '' } });
        });
    </script>
@stop

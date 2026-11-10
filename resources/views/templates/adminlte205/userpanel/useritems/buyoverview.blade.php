{{-- [multimod] Boutique du site (Store 1.2) --}}
@extends('templates.adminlte205.userpanel.app')

@section('title', 'Boutique')

@section('subtitle', number_format($user->credits, 0, ',', ' ') . ' crédits')

@section('breadcrumb')
    <li><a href="{{URL::route('userpanel.dashboard')}}"><i class="fa fa-dashboard"></i> Accueil</a></li>
    <li class="active">Boutique</li>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header"><h3 class="box-title">Objets en vente</h3></div>
                <div class="box-body">
                    <table class="table table-bordered table-striped" id="table">
                        <thead>
                            <tr><th>Objet</th><th>Catégorie</th><th>Type</th><th>Emplacement</th><th>Prix</th><th></th></tr>
                        </thead>
                        <tbody>
                        @foreach($items as $item)
                            <tr>
                                <td>{{ $item->display_name }}<br><small class="text-muted">{{ $item->web_description ?: $item->description }}</small></td>
                                <td>{{ $item->category ? $item->category->display_name : '' }}</td>
                                <td>{{ $item->type }}</td>
                                <td>{{ $item->loadout_slot }}</td>
                                <td data-order="{{ $item->price }}">{{ $item->price }}</td>
                                <td><a class="btn btn-success btn-xs {{ $item->price > $user->credits ? 'disabled' : '' }}" href="{{ route('userpanel.useritems.buyconf', ['item_id' => $item->id]) }}">Acheter</a></td>
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

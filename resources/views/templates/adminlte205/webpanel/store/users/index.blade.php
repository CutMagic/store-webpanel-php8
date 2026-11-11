{{-- [multimod] i18n : textes traduits (anglais dans le code, français dans lang/fr.json) --}}
{{--Copyright (c) 2015 "Werner Maisl"--}}

{{--This file is part of the Store Webpanel V2.--}}

{{--The Store Webpanel V2 is free software: you can redistribute it and/or modify--}}
{{--it under the terms of the GNU Affero General Public License as--}}
{{--published by the Free Software Foundation, either version 3 of the--}}
{{--License, or (at your option) any later version.--}}

{{--This program is distributed in the hope that it will be useful,--}}
{{--but WITHOUT ANY WARRANTY; without even the implied warranty of--}}
{{--MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the--}}
{{--GNU Affero General Public License for more details.--}}

{{--You should have received a copy of the GNU Affero General Public License--}}
{{--along with this program. If not, see <http://www.gnu.org/licenses/>.--}}

@extends('templates.adminlte205.webpanel.app')

@section('title', __('Users'))

@section('subtitle', __('Overview'))

@section('breadcrumb')
    <li><a href="{{ route('webpanel.dashboard') }}"><i class="fa fa-dashboard"></i> {{ __('Home') }}</a></li>
    <li class="active">{{ __('Users') }}</li>
    <li class="active">{{ __('Overview') }}</li>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">{{ __('All Users') }}</h3>
                </div><!-- /.box-header -->
                <div class="box-body">
                    <table class="table table-bordered table-striped" id="table">
                        <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>{{ __('Auth (SteamID32)') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Credits') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                    </table>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div><!-- /.col -->
    </div><!-- /.row -->
@stop

@section('footer')
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="{{asset('templates/adminlte205/dist/css/jquery.dataTables.min.css')}}">
    <!-- DataTables -->
    <script type="text/javascript" charset="utf8" src="{{asset('templates/adminlte205/dist/js/jquery.dataTables.min.js')}}"></script>

    <script>
        $(document).ready( function () {
            $('#table').DataTable({
                processing: true,
                serverSide: true,
                language: @include('templates.adminlte205.includes.dtlang'),
                ajax: '{{ route("webpanel.store.users.data") }}',
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'auth', name: 'auth'},
                    {data: 'name', name: 'name'},
                    {data: 'credits', name: 'credits'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ] // [php82] colonnes par nom (la colonne action seule n'est pas échappée)
            });
        } );
    </script>
@stop
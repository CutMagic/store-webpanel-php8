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

@section('title', __('Tools'))

@section('subtitle', __('Verify Import'))

@section('breadcrumb')
    <li><a href="{{ route('webpanel.dashboard') }}"><i class="fa fa-dashboard"></i> {{ __('Home') }}</a></li>
    <li class="active">{{ __('Tools') }}</li>
    <li class="active">{{ __('Import') }}</li>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            {!! Form::open(['route' => 'webpanel.store.tools.do_import', 'files' => true]) !!}
            {!! Form::hidden('fileName',$fileName)!!}
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">{{ __('Overview') }}</h3>
                </div><!-- /.box-header -->
                <div class="box-body"> <!-- TODO: Replace these with info messages -->
                    @if($json_version == 0)
                        {{ __('You are trying to import a legacy JSON object.') }}
                    @else
                        {{ __('You are trying to import a version :version JSON object.', ['version' => $json_version]) }}
                    @endif
                    <br>
                    {{ __('You are trying to import: :type', ['type' => $json_type]) }}

                </div><!-- /.box-body -->
                <div class="box-footer">
                    {!! Form::submit(__('Proceed'), ['class' => 'btn btn-primary']) !!}
                </div>
            </div><!-- /.box -->
            {!! Form::close() !!}
        </div><!-- /.col -->
        <div class="col-md-6">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">{{ __('Detailed Information') }}</h3>
                </div><!-- /.box-header -->
                <div class="box-body">
                    <!-- TODO: List the categories / items here -->
                </div><!-- /.box-body -->
                <div class="box-footer">
                </div>
            </div><!-- /.box -->
        </div><!-- /.col -->
    </div><!-- /.row -->
@stop
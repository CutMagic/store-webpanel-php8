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

@section('subtitle', __('Overview'))

@section('breadcrumb')
    <li><a href="{{ route('webpanel.dashboard') }}"><i class="fa fa-dashboard"></i> {{ __('Home') }}</a></li>
    <li class="active">{{ __('Tools') }}</li>
    <li class="active">{{ __('Overview') }}</li>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            {!! Form::open(['route' => 'webpanel.store.tools.import', 'files' => true]) !!}
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">{{ __('Import') }}</h3>
                </div><!-- /.box-header -->
                <div class="box-body">
                    <div class="panel box box-primary">
                        <div class="box-header with-border">
                            <h4 class="box-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                                    {{ __('JSON importer') }}
                                </a>
                            </h4>
                        </div>
                        <div id="collapseOne" class="panel-collapse collapse">
                            <div class="box-body">
                                {{ __('This is the JSON importer.') }}
                                <br>
                                {{ __('It imports the JSON files of the store modules.') }}
                            </div>
                        </div>
                    </div>
                    <div class="formgroup">
                        {!! Form::label('import', __('Import')) !!}
                        {!! Form::file('import', ['class' => 'form-control', 'accept' => '.json,.zip']) !!}
                    </div>
                </div><!-- /.box-body -->
                <div class="box-footer">
                    {!! Form::submit(__('Import'), ['class' => 'btn btn-primary']) !!}
                </div>
            </div><!-- /.box -->
            {!! Form::close() !!}
        </div><!-- /.col -->
        <div class="col-md-6">
            {!! Form::open(['route' => 'webpanel.store.tools.export']) !!}
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">{{ __('Export') }}</h3>
                </div><!-- /.box-header -->
                <div class="box-body">
                    <div class="panel box box-primary">
                        <div class="box-header with-border">
                            <h4 class="box-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
                                    {{ __('JSON exporter') }}
                                </a>
                            </h4>
                        </div>
                        <div id="collapseTwo" class="panel-collapse collapse">
                            <div class="box-body">
                                {{ __('This is the JSON exporter.') }}
                                <br>
                                {{ __('It exports JSON files that can be shipped with store modules.') }}
                                <br>
                                {{ __('Enter in "Require Plugin" the require_plugin value of the categories to export.') }}
                            </div>
                        </div>
                    </div>
                    {!! Form::label('require_plugin', __('Require Plugin')) !!}
                    {!! Form::text('require_plugin', null, ['class' => 'form-control']) !!}
                </div><!-- /.box-body -->
                <div class="box-footer">
                    {!! Form::submit(__('Export'), ['class' => 'btn btn-primary']) !!}
                </div>
            </div><!-- /.box -->
            {!! Form::close() !!}
        </div><!-- /.col -->
    </div><!-- /.row -->
    <div class="row">
        <div class="col-md-6">
            {!! Form::open(['route' => 'webpanel.store.tools.json_shrinker']) !!}
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">{{ __('JSON Shrinker') }}</h3>
                </div><!-- /.box-header -->
                <div class="box-body">
                    <div class="panel box box-primary">
                        <div class="box-header with-border">
                            <h4 class="box-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
                                    {{ __('JSON shrinker') }}
                                </a>
                            </h4>
                        </div>
                        <div id="collapseThree" class="panel-collapse collapse">
                            <div class="box-body">
                                {{ __('The JSON shrinker makes the JSON object in the attrs field of an item / category smaller.') }}
                                <br>
                                {{ __('Useful when the attrs are very large.') }}
                            </div>
                        </div>
                    </div>
                </div><!-- /.box-body -->
                <div class="box-footer">
                    {!! Form::submit(__('Shrink JSON'), ['class' => 'btn btn-primary']) !!}
                </div>
            </div><!-- /.box -->
            {!! Form::close() !!}
        </div><!-- /.col -->
        <div class="col-md-6">
            {!! Form::open(['route' => 'webpanel.store.tools.json_checker']) !!}
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">{{ __('JSON Checker') }}</h3>
                </div><!-- /.box-header -->
                <div class="box-body">
                    <div class="panel box box-primary">
                        <div class="box-header with-border">
                            <h4 class="box-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapseFour">
                                    {{ __('JSON checker') }}
                                </a>
                            </h4>
                        </div>
                        <div id="collapseFour" class="panel-collapse collapse">
                            <div class="box-body">
                                {{ __('This tool checks the JSON object in the attrs field.') }}
                                <br>
                                {{ __('Useful when the store plugin says that an attrs field is invalid and you do not know which one.') }}
                            </div>
                        </div>
                    </div>
                </div><!-- /.box-body -->
                <div class="box-footer">
                    {!! Form::submit(__('Check JSON'), ['class' => 'btn btn-primary']) !!}
                </div>
            </div><!-- /.box -->
            {!! Form::close() !!}
        </div><!-- /.col -->
    </div><!-- /.row -->
@stop
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

@section('title', __('Versions'))

@section('subtitle', __('Overview'))

@section('breadcrumb')
    <li><a href="{{ route('webpanel.dashboard') }}"><i class="fa fa-dashboard"></i> {{ __('Home') }}</a></li>
    <li class="active">{{ __('Versions') }}</li>
    <li class="active">{{ __('Overview') }}</li>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_1" data-toggle="tab">{{ __('Intro') }}</a></li>
                    <li><a href="#tab_2" data-toggle="tab">{{ __('Version Overview') }}</a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="{{route("webpanel.store.versions.update")}}">{{ __('Update plugin versions') }}</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab_1">
                        @if($private_plugins)
                            <div class="callout callout-danger">
                                <h4>{{ __('Private plugins detected') }}</h4>
                                <p>{{ __('You are using private plugins. The updater does not support them, so it will not work.') }}</p>
                            </div>
                        @endif
                        @if($date_diff > 10)
                            <div class="callout callout-info">
                                <h4>{{ __('Version data out of date') }}</h4>
                                <p>{{ __('The version data is out of date. Click "Update plugin versions" to compare with the latest versions.') }}</p>
                            </div>
                        @endif
                        {{ __('This page shows the installed versions of the store modules on your servers.') }}<br>
                        {{ __('From time to time, click "Update plugin versions" to download the latest version information.') }}<br>
                        {{ __('It is not done automatically because downloading the version files can take a while.') }}<br>
                        {{ __('A warning is shown when it has not been updated for 10 days.') }}<br>
                    </div><!-- /.tab-pane -->
                    <div class="tab-pane" id="tab_2">
                        @if(!$private_plugins)
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 10px">#</th>
                                    <th>{{ __('Module Name') }}</th>
                                    <th>{{ __('Module Description') }}</th>
                                    <th>{{ __('Installed Version') }}</th>
                                    <th>{{ __('Current Version') }}</th>
                                    <th>{{ __('Server ID') }}</th>
                                    <th>{{ __('Last Updated') }}</th>
                                    <th>{{ __('Update Status') }}</th>
                                </tr>
                                @foreach($plugin_versions as $version)
                                    <tr>
                                        <td><a href="{{ route('webpanel.store.versions.show', array($version["mod_id"])) }}">{{$version["mod_id"]}}</a></td>
                                        <td>{{$version["display-name"]}}</td>
                                        <td>{{$version["description"]}}</td>
                                        <td>{{$version["mod_ver_number"]}}</td>
                                        <td>{{$version["current-version"]}}</td>
                                        <td>{{$version["server_id"]}}</td>
                                        <td>{{$version["mod_last_updated"]}}</td>
                                        @if($version["version"]["code"] == 0)
                                            <td><small class="label label-success">{{ __('Up to date') }}</small></td>
                                        @elseif($version["version"]["code"] >= 10 && $version["version"]["code"] <= 19)
                                            <td><small class="label label-warning">{{ __('Out of date') }}</small></td>
                                        @elseif($version["version"]["code"] >= 91 && $version["version"]["code"] <= 99)
                                            <td><small class="label label-danger">{{ __('Error') }}</small></td>
                                        @endif
                                    </tr>
                                @endforeach
                            </table>
                        @else()
                            <div class="callout callout-danger">
                                <h4>{{ __('Private plugins detected') }}</h4>
                                <p>{{ __('You are using private plugins. The updater does not support them, so it will not work.') }}</p>
                            </div>
                        @endif
                    </div><!-- /.tab-pane -->
                </div><!-- /.tab-content -->
            </div><!-- nav-tabs-custom -->
        </div><!-- /.col -->
    </div><!-- /.row -->
@stop
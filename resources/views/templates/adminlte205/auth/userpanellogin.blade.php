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
@extends('templates.adminlte205.auth.app')

@section('title', __('Player login'))

@section('body-class', 'login-page')

@section('body')
    <div class="login-box">
        <div class="login-logo">
            <a href="{{ url('/') }}"><b>Store</b>UserPanel</a>
        </div><!-- /.login-logo -->
        <div class="login-box-body">
            <p class="login-box-msg">{{ __('Sign in with your Steam account to see your credits and items.') }}</p>

            <a href="{{ \App\Support\SteamOpenId::url(URL::route('userpanel.auth.steamlogin')) }}" class="btn btn-primary btn-block btn-flat"><i class="fa fa-steam"></i> {{ __('Sign in through Steam') }}</a>

        </div><!-- /.login-box-body -->
    </div><!-- /.login-box -->
@stop
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

@section('subtitle', __('Edit'))

@section('breadcrumb')
    <li><a href="{{ route('webpanel.dashboard') }}"><i class="fa fa-dashboard"></i> {{ __('Home') }}</a></li>
    <li class="active">{{ __('Users') }}</li>
    <li class="active">{{ __('Edit') }}</li>
@stop

@section('content')
    {!! Form::model($store_user,['method' => 'PATCH','route' => ['webpanel.store.users.update',$store_user->id]]) !!}
    @include('templates.adminlte205.webpanel.store.users._form',['LeftMenuTitle' => __('Edit User :name', ['name' => $store_user->name]),'SubmitButtonText' => __('Edit User')])
    {!! Form::close() !!}
@stop
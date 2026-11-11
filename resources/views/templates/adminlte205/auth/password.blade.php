{{-- [php82] Mot de passe oublié (comptes admin) : demande du lien par mail --}}
@extends('templates.adminlte205.auth.app')

@section('title', __('Forgot password'))

@section('body-class', 'login-page')

@section('body')
    <div class="login-box">
        <div class="login-logo">
            <a href="{{ url('/') }}"><b>Store</b>WebPanel</a>
        </div><!-- /.login-logo -->
        <div class="login-box-body">
            <p class="login-box-msg">{{ __('Forgot your password? Enter the e-mail address of your admin account: you will receive a link to choose a new one.') }}</p>

            <form method="POST" action="{{ route('password.email') }}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="form-group has-feedback">
                    <input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="{{ __('E-mail') }}" required>
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                </div>

                <div class="row">
                    <div class="col-xs-12">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">{{ __('Send the link') }}</button>
                    </div>
                </div>
            </form>

            <br>
            <a href="{{ route('login') }}">{{ __('Back to sign in') }}</a>
        </div><!-- /.login-box-body -->
    </div><!-- /.login-box -->
@stop

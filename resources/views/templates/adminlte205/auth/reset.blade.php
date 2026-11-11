{{-- [php82] Mot de passe oublié (comptes admin) : choix du nouveau mot de passe depuis le lien du mail --}}
@extends('templates.adminlte205.auth.app')

@section('title', __('New password'))

@section('body-class', 'login-page')

@section('body')
    <div class="login-box">
        <div class="login-logo">
            <a href="{{ url('/') }}"><b>Store</b>WebPanel</a>
        </div><!-- /.login-logo -->
        <div class="login-box-body">
            <p class="login-box-msg">{{ __('Choose a new password (at least 8 characters).') }}</p>

            <form method="POST" action="{{ route('password.update') }}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="form-group has-feedback">
                    <input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="{{ __('Account e-mail address') }}" required>
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                </div>

                <div class="form-group has-feedback">
                    <input type="password" class="form-control" name="password" placeholder="{{ __('New password') }}" autocomplete="new-password" required>
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>

                <div class="form-group has-feedback">
                    <input type="password" class="form-control" name="password_confirmation" placeholder="{{ __('Type the password again') }}" autocomplete="new-password" required>
                    <span class="glyphicon glyphicon-log-in form-control-feedback"></span>
                </div>

                <div class="row">
                    <div class="col-xs-12">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">{{ __('Change the password') }}</button>
                    </div>
                </div>
            </form>
        </div><!-- /.login-box-body -->
    </div><!-- /.login-box -->
@stop

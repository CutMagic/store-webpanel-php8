{{-- [php82] Mot de passe oublié (comptes admin) : choix du nouveau mot de passe depuis le lien du mail --}}
@extends('templates.adminlte205.auth.app')

@section('title', 'Nouveau mot de passe')

@section('body-class', 'login-page')

@section('body')
    <div class="login-box">
        <div class="login-logo">
            <a href="{{ url('/') }}"><b>Store</b>WebPanel</a>
        </div><!-- /.login-logo -->
        <div class="login-box-body">
            <p class="login-box-msg">Choisis un nouveau mot de passe (8 caractères minimum).</p>

            <form method="POST" action="{{ route('password.update') }}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="form-group has-feedback">
                    <input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="Adresse e-mail du compte" required>
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                </div>

                <div class="form-group has-feedback">
                    <input type="password" class="form-control" name="password" placeholder="Nouveau mot de passe" autocomplete="new-password" required>
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>

                <div class="form-group has-feedback">
                    <input type="password" class="form-control" name="password_confirmation" placeholder="Retape le mot de passe" autocomplete="new-password" required>
                    <span class="glyphicon glyphicon-log-in form-control-feedback"></span>
                </div>

                <div class="row">
                    <div class="col-xs-12">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">Changer le mot de passe</button>
                    </div>
                </div>
            </form>
        </div><!-- /.login-box-body -->
    </div><!-- /.login-box -->
@stop

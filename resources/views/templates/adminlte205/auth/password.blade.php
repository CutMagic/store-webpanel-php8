{{-- [php82] Mot de passe oublié (comptes admin) : demande du lien par mail --}}
@extends('templates.adminlte205.auth.app')

@section('title', 'Mot de passe oublié')

@section('body-class', 'login-page')

@section('body')
    <div class="login-box">
        <div class="login-logo">
            <a href="{{ url('/') }}"><b>Store</b>WebPanel</a>
        </div><!-- /.login-logo -->
        <div class="login-box-body">
            <p class="login-box-msg">Mot de passe oublié ? Indique l'adresse e-mail de ton compte admin : tu recevras un lien pour en choisir un nouveau.</p>

            <form method="POST" action="{{ route('password.email') }}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="form-group has-feedback">
                    <input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="E-mail" required>
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                </div>

                <div class="row">
                    <div class="col-xs-12">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">Envoyer le lien</button>
                    </div>
                </div>
            </form>

            <br>
            <a href="{{ route('login') }}">Retour à la connexion</a>
        </div><!-- /.login-box-body -->
    </div><!-- /.login-box -->
@stop

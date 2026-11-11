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

@extends('templates.installer.app')

@section('title', 'Finish')

@section('navbar')
    <li><a href="#">Welcome</a></li>
    <li><a href="#">Settings</a></li>
    <li><a href="#">Setup Database</a></li>
    <li class="active"><a href="#">Finish</a></li>
@endsection

@section('body')
    <div class="alert alert-success" role="alert">
        <strong>Setup complete / Installation terminée</strong><br>
        The installer is now locked (file <code>INSTALLED</code>). /
        L'installateur est maintenant fermé (fichier <code>INSTALLED</code>).<br><br>
        Admin account / Compte admin : see / voir <code>storage/app/premier-admin.txt</code>
        (random password / mot de passe tiré au hasard). Log in, set your real e-mail and a new password
        in Panel &gt; Users, then delete that file. /
        Connecte-toi, mets ta vraie adresse e-mail et un nouveau mot de passe dans Panel &gt; Users,
        puis supprime ce fichier.<br><br>
        <a class="btn btn-primary" href="{{ route('login') }}">Log in / Se connecter</a>
    </div>

    {{-- [php82] contrôle de sécurité : les fichiers privés ne doivent pas être lisibles depuis le web --}}
    @if (is_array($checks))
        @php($danger = collect($checks)->contains('status', 'danger'))
        @php($unknown = collect($checks)->contains('status', 'unknown'))
        <div class="alert {{ $danger ? 'alert-danger' : ($unknown ? 'alert-warning' : 'alert-success') }}" role="alert">
            <strong>Security check / Contrôle de sécurité</strong><br>
            @if ($danger)
                <strong>DANGER : some private files can be read from the web. /
                certains fichiers privés sont lisibles depuis le web.</strong><br>
                Fix it now / Corrige-le tout de suite : point the (sub)domain to the <code>public</code> folder,
                or keep the protection part of the <code>.htaccess</code> file at the panel root (Apache / LiteSpeed),
                or block these paths in your server config (Nginx). See README / INSTALL.txt.
                Then change the MySQL and SMTP passwords that were in <code>.env</code>.
            @elseif ($unknown)
                Some files could not be checked automatically (the host may block requests from the site to itself).
                Open these links yourself: each one must show a 403 or 404 error. /
                Certains fichiers n'ont pas pu être vérifiés : ouvre ces liens toi-même, chacun doit afficher une erreur 403 ou 404.
            @else
                Private files cannot be read from the web. / Les fichiers privés ne sont pas lisibles depuis le web.
            @endif
            <ul style="margin-top:8px">
                @foreach ($checks as $c)
                    <li>
                        <code>{{ $c['label'] }}</code> :
                        @if ($c['status'] === 'ok')
                            protected / protégé (HTTP {{ $c['http'] }})
                        @elseif ($c['status'] === 'danger')
                            <strong>READABLE / LISIBLE</strong>
                        @else
                            not checked / non vérifié
                        @endif
                        - <a href="{{ $c['url'] }}" target="_blank" rel="noopener noreferrer">{{ $c['url'] }}</a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
@endsection

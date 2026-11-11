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
{{-- [multimod] page d'accueil lisible (texte foncé, vrais boutons) et bouton de langue FR / EN --}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Store WebPanel</title>

    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet" type="text/css">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <style>
        body {
            margin: 0;
            min-height: 100vh;
            box-sizing: border-box;
            padding: 56px 16px 24px; /* le haut reste libre pour le bouton de langue */
            background: #f4f6f8;
            color: #1f2933;
            font-family: 'Lato', 'Segoe UI', Arial, sans-serif;
            font-weight: 400;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .content { text-align: center; max-width: 760px; }
        .title {
            font-size: 64px;
            font-weight: 300;
            color: #111;
            margin: 0 0 12px;
            line-height: 1.1;
        }
        .subtitle {
            font-size: 22px;
            color: #333;
            margin-bottom: 36px;
        }
        .buttons a {
            display: inline-block;
            margin: 6px 8px;
            padding: 14px 28px;
            border-radius: 4px;
            background: #3c8dbc;
            color: #fff;
            font-size: 18px;
            font-weight: 700;
            text-decoration: none;
        }
        .buttons a:hover, .buttons a:focus { background: #2f6f95; }
        .buttons a.secondary { background: #222d32; }
        .buttons a.secondary:hover, .buttons a.secondary:focus { background: #111; }
        .footer {
            margin-top: 40px;
            font-size: 13px;
            color: #444;
            line-height: 1.8;
        }
        .lang-switch {
            position: absolute;
            top: 14px;
            right: 18px;
            font-size: 15px;
            color: #333;
        }
        .lang-switch a { color: #1a5a85; }
        .lang-switch .sep { color: #888; margin: 0 4px; }
        @media (max-width: 600px) { .title { font-size: 40px; } .subtitle { font-size: 18px; } }
    </style>
</head>
<body>
@include('templates.adminlte205.includes.langswitch', ['style' => 'plain'])
<div class="content">
    <h1 class="title">Store WebPanel</h1>
    <div class="subtitle">{{ __('Web panel of the SourceMod Store plugin') }}</div>
    <div class="buttons">
        <a href="{{ URL::route('userpanel.dashboard') }}"><i class="fa fa-user"></i> {{ __('Player area') }}</a>
        <a href="{{ URL::route('webpanel.dashboard') }}" class="secondary"><i class="fa fa-lock"></i> {{ __('Admin area') }}</a>
    </div>
    <div class="footer">
        Store Webpanel 2 - &copy; 2015-2016 Werner Maisl<br>
        Store Plugin - &copy; 2013-2016 Alon Gubkin, Keith Warren (Drixevel)
    </div>
</div>
</body>
</html>

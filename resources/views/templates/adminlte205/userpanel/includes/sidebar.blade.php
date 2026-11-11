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
{{-- [multimod] menu de l'espace joueur pour le Store 1.2 --}}
<aside class="main-sidebar">
    <section class="sidebar">
        <ul class="sidebar-menu">
            <li class="header">{{ __('MY STORE') }}</li>
            <li><a href="{{URL::route('userpanel.dashboard')}}"><i class="fa fa-dashboard"></i> <span>{{ __('Dashboard') }}</span></a></li>
            <li><a href="{{URL::route('userpanel.useritems.index')}}"><i class="fa fa-cubes"></i> <span>{{ __('My items') }}</span></a></li>
            <li><a href="{{URL::route('userpanel.useritems.buyoverview')}}"><i class="fa fa-shopping-cart"></i> <span>{{ __('Shop') }}</span></a></li>
            <li><a href="{{URL::route('userpanel.loadouts.index')}}"><i class="fa fa-check-square-o"></i> <span>{{ __('Equipped items') }}</span></a></li>
            @if (config('userpanel.player_profiles'))
                <li><a href="{{URL::route('userpanel.players.index')}}"><i class="fa fa-users"></i> <span>{{ __('Players') }}</span></a></li>
            @endif
            <li><a href="{{URL::route('userpanel.auth.logout')}}"><i class="fa fa-sign-out"></i> <span>{{ __('Log out') }}</span></a></li>
        </ul>
    </section>
</aside>

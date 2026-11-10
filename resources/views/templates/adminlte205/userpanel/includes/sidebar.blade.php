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
            <li class="header">MON STORE</li>
            <li><a href="{{URL::route('userpanel.dashboard')}}"><i class="fa fa-dashboard"></i> <span>Tableau de bord</span></a></li>
            <li><a href="{{URL::route('userpanel.useritems.index')}}"><i class="fa fa-cubes"></i> <span>Mes objets</span></a></li>
            <li><a href="{{URL::route('userpanel.useritems.buyoverview')}}"><i class="fa fa-shopping-cart"></i> <span>Boutique</span></a></li>
            <li><a href="{{URL::route('userpanel.loadouts.index')}}"><i class="fa fa-check-square-o"></i> <span>Objets équipés</span></a></li>
            <li><a href="{{URL::route('userpanel.auth.logout')}}"><i class="fa fa-sign-out"></i> <span>Déconnexion</span></a></li>
        </ul>
    </section>
</aside>

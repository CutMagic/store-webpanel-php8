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
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <ul class="sidebar-menu">
            <li class="header">{{ __('MAIN NAVIGATION') }}</li>
            <li><a href="{{URL::route('webpanel.dashboard')}}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }}</a></li>

            <!-- Store -->
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-briefcase"></i> <span>{{ __('Store') }}</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">

                    <!-- Categories-->
                    <li class="treeview">
                        <a href="#">
                            <i class="fa fa-folder-open"></i>
                            <span>{{ __('Categories') }}</span>
                            <span class="label label-primary pull-right">{{$storeCategoryCount}}</span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="{{URL::route('webpanel.store.categories.index')}}"><i class="fa fa-circle-o"></i> {{ __('View') }} </a></li>
                            <li><a href="{{URL::route('webpanel.store.categories.create')}}"><i class="fa fa-circle-o"></i> {{ __('Create') }} </a></li>
                            <!--<li><a href="{{URL::route('webpanel.dashboard')}}"><i class="fa fa-circle-o"></i> Stats</a></li>  TODO: Add named route -->
                        </ul>
                    </li>

                    <!-- Items -->
                    <li class="treeview">
                        <a href="#">
                            <i class="fa fa-files-o"></i>
                            <span>{{ __('Items') }}</span>
                            <span class="label label-primary pull-right">{{$storeItemCount}}</span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="{{URL::route('webpanel.store.items.index')}}"><i class="fa fa-circle-o"></i> {{ __('View') }} </a></li>
                            <li><a href="{{URL::route('webpanel.store.items.create')}}"><i class="fa fa-circle-o"></i> {{ __('Create') }} </a></li>
                            <!--<li><a href="{{URL::route('webpanel.dashboard')}}"><i class="fa fa-circle-o"></i> Stats</a></li>  TODO: Add named route -->
                        </ul>
                    </li>

                    <!-- Users -->
                    <li class="treeview">
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>{{ __('Users') }}</span>
                            <span class="label label-primary pull-right">{{$storeUserCount}}</span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="{{URL::route('webpanel.store.users.index')}}"><i class="fa fa-circle-o"></i> {{ __('View') }} </a></li>
                            <li><a href="{{URL::route('webpanel.store.users.create')}}"><i class="fa fa-circle-o"></i> {{ __('Create') }} </a></li>
                            <!--<li><a href="{{URL::route('webpanel.dashboard')}}"><i class="fa fa-circle-o"></i> Stats</a></li>  TODO: Add named route -->
                        </ul>
                    </li>

                    <!-- Loadouts -->
                    <!--<li class="treeview">
                            <a href="#">
                                <i class="fa fa-shopping-cart"></i>
                                <span>{{ __('Loadouts') }}</span>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="{{URL::route('webpanel.dashboard')}}"><i class="fa fa-circle-o"></i> {{ __('View') }} </a></li>  TODO: Add named route
                                <li><a href="{{URL::route('webpanel.dashboard')}}"><i class="fa fa-circle-o"></i> {{ __('Create') }} </a></li>  TODO: Add named route
                                <li><a href="{{URL::route('webpanel.dashboard')}}"><i class="fa fa-circle-o"></i> Stats</a></li>  TODO: Add named route
                            </ul>
                        </li>-->

                    <!-- Servers -->
                    <li class="treeview">
                        <a href="#">
                            <i class="fa fa-sitemap"></i>
                            <span>{{ __('Servers') }}</span>
                            <span class="label label-primary pull-right">{{$storeServerCount}}</span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="{{URL::route('webpanel.store.servers.index')}}"><i class="fa fa-circle-o"></i> {{ __('View') }} </a></li>
                            <li><a href="{{URL::route('webpanel.store.servers.create')}}"><i class="fa fa-circle-o"></i> {{ __('Create') }} </a></li>
                            <!--<li><a href="{{URL::route('webpanel.dashboard')}}"><i class="fa fa-circle-o"></i> Stats</a></li>  TODO: Add named route -->
                        </ul>
                    </li>

                    <!-- Versions-->
                    <li><a href="{{URL::route('webpanel.store.versions.index')}}"><i class="fa fa-desktop"></i> {{ __('Versions') }}</a></li>

                    <!-- Tools -->
                    <li><a href="{{URL::route('webpanel.store.tools.index')}}"><i class="fa fa-wrench"></i> {{ __('Tools') }}</a></li>

                </ul>
            </li>

            <!-- WebPanel -->
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-cogs"></i> <span>{{ __('WebPanel') }}</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">

                    <!-- Permissions -->
                    <li class="treeview">
                        <a href="#">
                            <i class="fa fa-files-o"></i>
                            <span>{{ __('Permissions') }}</span>
                            <span class="label label-primary pull-right">{{$panelPermissionCount}}</span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="{{URL::route('webpanel.panel.permissions.index')}}"><i class="fa fa-circle-o"></i> {{ __('View') }} </a></li>
                            <li><a href="{{URL::route('webpanel.panel.permissions.create')}}"><i class="fa fa-circle-o"></i> {{ __('Create') }} </a></li>
                            <!--<li><a href="{{URL::route('webpanel.dashboard')}}"><i class="fa fa-circle-o"></i> Stats</a></li>  TODO: Add named route -->
                        </ul>
                    </li>

                    <!-- Roles -->
                    <li class="treeview">
                        <a href="#">
                            <i class="fa fa-folder-open"></i>
                            <span>{{ __('Roles') }}</span>
                            <span class="label label-primary pull-right">{{$panelRoleCount}}</span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="{{URL::route('webpanel.panel.roles.index')}}"><i class="fa fa-circle-o"></i> {{ __('View') }} </a></li>
                            <li><a href="{{URL::route('webpanel.panel.roles.create')}}"><i class="fa fa-circle-o"></i> {{ __('Create') }} </a></li>
                            <!--<li><a href="{{URL::route('webpanel.dashboard')}}"><i class="fa fa-circle-o"></i> Stats</a></li>  TODO: Add named route -->
                        </ul>
                    </li>

                    <!-- Users -->
                    <li class="treeview">
                        <a href="#">
                            <i class="fa fa-users"></i>
                            <span>{{ __('Users') }}</span>
                            <span class="label label-primary pull-right">{{$panelUserCount}}</span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="{{URL::route('webpanel.panel.users.index')}}"><i class="fa fa-circle-o"></i> {{ __('View') }} </a></li>
                            <li><a href="{{URL::route('webpanel.panel.users.create')}}"><i class="fa fa-circle-o"></i> {{ __('Create') }} </a></li>
                            <!--<li><a href="{{URL::route('webpanel.dashboard')}}"><i class="fa fa-circle-o"></i> Stats</a></li>  TODO: Add named route -->
                        </ul>
                    </li>
                </ul>
            </li>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>
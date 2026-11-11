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
<header class="main-header">
    <!-- Logo -->
    <a href="{{ route('userpanel.dashboard') }}" class="logo"><b>User</b>Panel</a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                @include('templates.adminlte205.includes.langswitch', ['style' => 'nav'])
                <!-- Messages: style can be found in dropdown.less-->
                {{--<li class="dropdown messages-menu">--}}
                    {{--<a href="#" class="dropdown-toggle" data-toggle="dropdown">--}}
                        {{--<i class="fa fa-envelope-o"></i>--}}
                        {{--<span class="label label-success">4</span>--}}
                    {{--</a>--}}
                    {{--<ul class="dropdown-menu">--}}
                        {{--<li class="header">You have 4 messages</li><!-- TODO: Implement a Info Message feature-->--}}
                        {{--<li>--}}
                            {{--<!-- inner menu: contains the actual data -->--}}
                            {{--<ul class="menu">--}}
                                {{--<li><!-- start message -->--}}
                                    {{--<a href="#">--}}
                                        {{--<div class="pull-left">--}}
                                            {{--<img src="{{asset('templates/adminlte205/dist/img/user2-160x160.jpg')}}" class="img-circle" alt="User Image"/>--}}
                                        {{--</div>--}}
                                        {{--<h4>--}}
                                            {{--Support Team--}}
                                            {{--<small><i class="fa fa-clock-o"></i> 5 mins</small>--}}
                                        {{--</h4>--}}
                                        {{--<p>Why not buy a new awesome theme?</p>--}}
                                    {{--</a>--}}
                                {{--</li><!-- end message -->--}}
                                {{--<li>--}}
                                    {{--<a href="#">--}}
                                        {{--<div class="pull-left">--}}
                                            {{--<img src="{{asset('templates/adminlte205/dist/img/user3-128x128.jpg')}}" class="img-circle" alt="user image"/>--}}
                                        {{--</div>--}}
                                        {{--<h4>--}}
                                            {{--AdminLTE Design Team--}}
                                            {{--<small><i class="fa fa-clock-o"></i> 2 hours</small>--}}
                                        {{--</h4>--}}
                                        {{--<p>Why not buy a new awesome theme?</p>--}}
                                    {{--</a>--}}
                                {{--</li>--}}
                                {{--<li>--}}
                                    {{--<a href="#">--}}
                                        {{--<div class="pull-left">--}}
                                            {{--<img src="{{asset('templates/adminlte205/dist/img/user4-128x128.jpg')}}" class="img-circle" alt="user image"/>--}}
                                        {{--</div>--}}
                                        {{--<h4>--}}
                                            {{--Developers--}}
                                            {{--<small><i class="fa fa-clock-o"></i> Today</small>--}}
                                        {{--</h4>--}}
                                        {{--<p>Why not buy a new awesome theme?</p>--}}
                                    {{--</a>--}}
                                {{--</li>--}}
                                {{--<li>--}}
                                    {{--<a href="#">--}}
                                        {{--<div class="pull-left">--}}
                                            {{--<img src="{{asset('templates/adminlte205/dist/img/user3-128x128.jpg')}}" class="img-circle" alt="user image"/>--}}
                                        {{--</div>--}}
                                        {{--<h4>--}}
                                            {{--Sales Department--}}
                                            {{--<small><i class="fa fa-clock-o"></i> Yesterday</small>--}}
                                        {{--</h4>--}}
                                        {{--<p>Why not buy a new awesome theme?</p>--}}
                                    {{--</a>--}}
                                {{--</li>--}}
                                {{--<li>--}}
                                    {{--<a href="#">--}}
                                        {{--<div class="pull-left">--}}
                                            {{--<img src="{{asset('templates/adminlte205/dist/img/user4-128x128.jpg')}}" class="img-circle" alt="user image"/>--}}
                                        {{--</div>--}}
                                        {{--<h4>--}}
                                            {{--Reviewers--}}
                                            {{--<small><i class="fa fa-clock-o"></i> 2 days</small>--}}
                                        {{--</h4>--}}
                                        {{--<p>Why not buy a new awesome theme?</p>--}}
                                    {{--</a>--}}
                                {{--</li>--}}
                            {{--</ul>--}}
                        {{--</li>--}}
                        {{--<li class="footer"><a href="#">See All Messages</a></li><!-- TODO: Link to a proper messages Page-->--}}
                    {{--</ul>--}}
                {{--</li>--}}
                <!-- Notifications: style can be found in dropdown.less -->
                <li class="dropdown notifications-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-bell-o"></i>
                        <span class="label label-warning">{{$credits}}</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header">{{ __('My account') }}</li>
                        <li>
                            <!-- inner menu: contains the actual data TODO: Add proper Icons-->
                            <ul class="menu">
                                <li>
                                    <a href="#">
                                        <i class="fa fa-money text-aqua"></i> {{ __('You have :credits credits', ['credits' => $credits]) }}
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <i class="fa fa-cubes text-yellow"></i> {{ __('You have :count item(s)', ['count' => $owned_item_count]) }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{URL::route('userpanel.loadouts.index')}}">
                                        <i class="fa fa-check text-green"></i> {{ __(':count equipped item(s)', ['count' => $equipped_count]) }}
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="footer"><a href="{{URL::route('userpanel.dashboard')}}">{{ __('Dashboard') }}</a></li>
                    </ul>
                </li>
                <!-- Tasks: style can be found in dropdown.less -->
                <li class="dropdown tasks-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-flag-o"></i>
                        <span class="label label-danger">{{ count($latest_items) }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header">{{ __('Latest items received') }}</li>
                        <li>
                            <!-- inner menu: contains the actual data -->
                            <ul class="menu">
                                @foreach($latest_items as $item)
                                <li><!-- Task item -->
                                    <a href="#">
                                        <h3>
                                            {{$item->display_name}}
                                            <!--<small class="pull-right">20%</small>-->
                                        </h3>
                                    </a>
                                </li><!-- end task item -->
                                 @endforeach
                            </ul>
                        </li>
                        <li class="footer">
                            <a href="{{ URL::route("userpanel.useritems.index") }}">{{ __('See all my items') }}</a>
                        </li>
                    </ul>
                </li>
                <!-- User Account: style can be found in dropdown.less -->
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="{{URL::route("userpanel.steamimage.current") }}" class="user-image" alt="User Image"/>
                        <span class="hidden-xs">{{$username}}</span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            <img src="{{URL::route("userpanel.steamimage.current") }}" class="img-circle" alt="User Image" />
                            <p>
                                {{$username}}
                                <small>{{ __(':credits credits', ['credits' => $credits]) }}</small>
                            </p>
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                @if (config('userpanel.player_profiles'))<a href="{{ route('userpanel.players.show', ['id' => (int) session('store_user_id')]) }}" class="btn btn-default btn-flat">{{ __('My profile') }}</a>@endif
                            </div>
                            <div class="pull-right">
                                <a href="{{URL::route('userpanel.auth.logout')}}" class="btn btn-default btn-flat">{{ __('Log out') }}</a>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>
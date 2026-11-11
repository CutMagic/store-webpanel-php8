{{-- [multimod] Admin > Réglages du site : page Joueurs publique (Google), nom et description du site --}}
@extends('templates.adminlte205.webpanel.app')

@section('title', __('Site settings'))

@section('subtitle', __('public pages and search engines'))

@section('breadcrumb')
    <li><a href="{{ route('webpanel.dashboard') }}"><i class="fa fa-dashboard"></i> {{ __('Home') }}</a></li>
    <li class="active">{{ __('Site settings') }}</li>
@stop

@section('content')
    @if (Session::has('message'))
        <div class="alert alert-success">{{ Session::get('message') }}</div>
    @endif

    <form method="post" action="{{ route('webpanel.settings.update') }}">
        @csrf
        <div class="row">
            <div class="col-md-7">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-globe"></i> {{ __('Public Players page') }}</h3>
                        <div class="box-tools pull-right">
                            @if ($publicActive)
                                <span class="label label-success">{{ __('enabled') }}</span>
                            @else
                                <span class="label label-default">{{ __('disabled') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="box-body">
                        <p>{{ __('By default, only players signed in with Steam can see the Players page (ranking and player profiles). When this option is on, everyone can see it without signing in, and search engines such as Google can index it.') }}</p>

                        @if (!$profilesEnabled)
                            <div class="callout callout-danger">
                                <p>{{ __('The Players page is turned off in the .env file (USERPANEL_PLAYER_PROFILES=false): this option has no effect until it is turned back on.') }}</p>
                            </div>
                        @endif

                        <input type="hidden" name="public_players" value="0">
                        <div class="checkbox" style="font-size: 16px">
                            <label>
                                <input type="checkbox" name="public_players" value="1" @checked(old('public_players', $settings['public_players'] ? '1' : '0') === '1')>
                                <strong>{{ __('Show the Players page to everyone (visible on Google)') }}</strong>
                            </label>
                        </div>

                        <div class="callout callout-warning">
                            <p>
                                {{ __('Visible to everyone: name, SteamID and Steam profile link, owned and equipped items of every player.') }}
                                @if ($showCredits)
                                    {{ __('Credits and rank too (USERPANEL_PLAYER_PROFILES_CREDITS=true in the .env file).') }}
                                @else
                                    {{ __('Credits and rank stay hidden (USERPANEL_PLAYER_PROFILES_CREDITS=false in the .env file).') }}
                                @endif
                            </p>
                        </div>

                        @if ($publicActive)
                            <table class="table table-condensed" style="margin-bottom: 0">
                                <tr>
                                    <th style="width: 170px">{{ __('Public page') }}</th>
                                    <td><a href="{{ route('players.index') }}" target="_blank" rel="noopener">{{ route('players.index') }}</a></td>
                                </tr>
                                <tr>
                                    <th>{{ __('Sitemap (for Google)') }}</th>
                                    <td><a href="{{ route('sitemap') }}" target="_blank" rel="noopener">{{ route('sitemap') }}</a></td>
                                </tr>
                            </table>
                            <p class="text-muted" style="margin-top: 10px">
                                <i class="fa fa-info-circle"></i>
                                {{ __('To appear faster on Google: add your site in Google Search Console (search.google.com/search-console), then submit the sitemap address above. Google can take a few days to a few weeks to show the pages.') }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-font"></i> {{ __('Site name and description') }}</h3>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label for="site_name">{{ __('Site name') }}</label>
                            <input type="text" id="site_name" name="site_name" maxlength="64" class="form-control"
                                   value="{{ old('site_name', $settings['site_name']) }}" placeholder="Store WebPanel">
                            <p class="help-block">{{ __('Shown on the home page, in the public pages and in the Google results.') }}</p>
                        </div>
                        <div class="form-group">
                            <label for="site_description">{{ __('Site description') }}</label>
                            <textarea id="site_description" name="site_description" maxlength="300" rows="4" class="form-control"
                                      placeholder="{{ __('Example: Counter-Strike: Source servers - store, ranking and player profiles') }}">{{ old('site_description', $settings['site_description']) }}</textarea>
                            <p class="help-block">{{ __('One or two sentences, shown under the title of the home page and under the link in the Google results (300 characters max).') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg"><i class="fa fa-save"></i> {{ __('Save') }}</button>
    </form>
@stop

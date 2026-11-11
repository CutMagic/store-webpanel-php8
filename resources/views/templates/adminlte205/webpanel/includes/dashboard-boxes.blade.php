{{-- [multimod] tableau de bord admin : compteurs (mêmes chiffres que le menu) et raccourcis --}}
<div class="row">
    <div class="col-md-3 col-sm-6 col-xs-12">
        <a href="{{ route('webpanel.store.categories.index') }}" class="info-box" style="display:block;color:inherit">
            <span class="info-box-icon bg-aqua"><i class="fa fa-folder-open"></i></span>
            <span class="info-box-content">
                <span class="info-box-text">{{ __('Categories') }}</span>
                <span class="info-box-number">{{ $storeCategoryCount }}</span>
            </span>
        </a>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <a href="{{ route('webpanel.store.items.index') }}" class="info-box" style="display:block;color:inherit">
            <span class="info-box-icon bg-green"><i class="fa fa-cubes"></i></span>
            <span class="info-box-content">
                <span class="info-box-text">{{ __('Items') }}</span>
                <span class="info-box-number">{{ $storeItemCount }}</span>
            </span>
        </a>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <a href="{{ route('webpanel.store.users.index') }}" class="info-box" style="display:block;color:inherit">
            <span class="info-box-icon bg-yellow"><i class="fa fa-users"></i></span>
            <span class="info-box-content">
                <span class="info-box-text">{{ __('Players') }}</span>
                <span class="info-box-number">{{ number_format($storeUserCount, 0, ',', ' ') }}</span>
            </span>
        </a>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <a href="{{ route('webpanel.store.servers.index') }}" class="info-box" style="display:block;color:inherit">
            <span class="info-box-icon bg-red"><i class="fa fa-sitemap"></i></span>
            <span class="info-box-content">
                <span class="info-box-text">{{ __('Servers') }}</span>
                <span class="info-box-number">{{ $storeServerCount }}</span>
            </span>
        </a>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-body">
                <a href="{{ route('webpanel.store.items.create') }}" class="btn btn-default"><i class="fa fa-plus"></i> {{ __('Create Item') }}</a>
                <a href="{{ route('webpanel.store.categories.create') }}" class="btn btn-default"><i class="fa fa-plus"></i> {{ __('Create Category') }}</a>
                <a href="{{ route('webpanel.store.tools.index') }}" class="btn btn-default"><i class="fa fa-wrench"></i> {{ __('Tools') }}</a>
                <a href="{{ route('webpanel.store.versions.index') }}" class="btn btn-default"><i class="fa fa-desktop"></i> {{ __('Versions') }}</a>
                <a href="{{ url('/userpanel') }}" class="btn btn-default"><i class="fa fa-user"></i> {{ __('Player area') }}</a>
                <a href="{{ route('webpanel.settings.index') }}" class="btn btn-default"><i class="fa fa-sliders"></i> {{ __('Site settings') }}</a>
            </div>
        </div>
    </div>
</div>

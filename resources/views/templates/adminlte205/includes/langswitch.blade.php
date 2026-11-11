{{-- [multimod] bouton de langue FR / EN. $style : 'nav' (barre du haut AdminLTE) ou 'plain' (pages simples) --}}
@php($cur = app()->getLocale())
@if (($style ?? 'plain') === 'nav')
    <li class="lang-switch">
        <a href="{{ route('lang.switch', ['locale' => $cur === 'fr' ? 'en' : 'fr']) }}" title="{{ $cur === 'fr' ? 'English' : 'Français' }}">
            <i class="fa fa-globe"></i> {{ $cur === 'fr' ? 'EN' : 'FR' }}
        </a>
    </li>
@else
    <div class="lang-switch">
        <i class="fa fa-globe"></i>
        @foreach (\App\Http\Middleware\SetLocale::LOCALES as $code => $label)
            @if ($code === $cur)
                <strong>{{ $label }}</strong>
            @else
                <a href="{{ route('lang.switch', ['locale' => $code]) }}">{{ $label }}</a>
            @endif
            @if (!$loop->last) <span class="sep">|</span> @endif
        @endforeach
    </div>
@endif

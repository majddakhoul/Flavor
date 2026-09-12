<footer class="site-footer">
    <div class="shell grid grid-3">
        <div>
            <a class="brand" href="{{ route('home') }}">
                <img src="{{ asset('assets/img/brand/mark.svg') }}" alt="">
                <span>{{ config('flavor.brand.name') }}<small>{{ __('app.tagline_short') }}</small></span>
            </a>
            <p class="small" style="margin-top:var(--space-2)">{{ __('app.footer_blurb') }}</p>
        </div>
        <div>
            <p class="eyebrow">{{ __('app.explore') }}</p>
            <ul style="list-style:none;padding:0;margin:0;display:grid;gap:.3rem">
                <li><a href="{{ route('menu.index') }}">{{ __('app.menu_nav') }}</a></li>
                <li><a href="{{ route('offers.index') }}">{{ __('app.offers') }}</a></li>
                <li><a href="{{ route('register') }}">{{ __('app.create_account') }}</a></li>
            </ul>
        </div>
        <div>
            <p class="eyebrow">{{ __('app.contact') }}</p>
            <p class="small mono">{{ config('flavor.brand.phone') }}</p>
            <p class="small mono">{{ config('flavor.brand.email') }}</p>
            <p class="small">{{ config('flavor.brand.address') }}</p>
        </div>
    </div>
    <div class="shell small muted" style="margin-top:var(--space-4)">
        © {{ now()->year }} {{ config('flavor.brand.name') }}. {{ __('app.rights') }}
    </div>
</footer>

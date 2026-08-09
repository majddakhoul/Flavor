@if (empty($cookieConsent))
    <aside class="cookie-bar hidden" data-cookie-bar>
        <div class="cluster" style="flex:1;min-width:240px">
            <x-icon name="cookie" size="lg" />
            <p>{{ __('app.cookie_notice') }}</p>
        </div>
        <div class="cluster">
            <button type="button" class="btn btn--ghost btn--sm" data-cookie-accept="essential">{{ __('app.cookie_essential') }}</button>
            <button type="button" class="btn btn--sm" data-cookie-accept="all">{{ __('app.cookie_accept') }}</button>
        </div>
    </aside>
@endif

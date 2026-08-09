<div class="menu-pop">
    <button type="button" class="btn btn--ghost btn--sm" data-toggle-target="#locale-menu" aria-expanded="false">
        <x-icon name="globe" />
        <span>{{ $currentLocale->label() }}</span>
    </button>
    <div class="menu-pop__panel" id="locale-menu">
        @foreach (\App\Enums\Locale::cases() as $locale)
            <a href="{{ route('locale.switch', $locale->value) }}" @class(['is-active' => $locale === $currentLocale])>
                <span class="mono small">{{ strtoupper($locale->value) }}</span>
                <span>{{ $locale->label() }}</span>
                @if ($locale === $currentLocale)<x-icon name="check" />@endif
            </a>
        @endforeach
    </div>
</div>

<button type="button" class="btn btn--ghost btn--sm" data-theme-toggle aria-pressed="false"
        data-label-dark="{{ __('app.theme_dark') }}" data-label-light="{{ __('app.theme_light') }}">
    <x-icon name="moon" />
    <span data-theme-label>{{ ($theme ?? 'light') === 'dark' ? __('app.theme_light') : __('app.theme_dark') }}</span>
</button>

@props(['fields' => [], 'model' => null])
@php($matrix = $model?->translationMatrix() ?? [])
<div>
    <div class="tabs" data-tabs>
        @foreach (\App\Enums\Locale::cases() as $index => $locale)
            <button type="button" data-tab="locale-{{ $locale->value }}" class="{{ $index === 0 ? 'is-active' : '' }}">
                {{ $locale->label() }}
            </button>
        @endforeach
    </div>

    @foreach (\App\Enums\Locale::cases() as $index => $locale)
        <div class="tab-panel {{ $index === 0 ? 'is-active' : '' }}" id="locale-{{ $locale->value }}">
            @if ($locale->value === config('app.fallback_locale'))
                <p class="field__hint">{{ __('app.base_locale_hint') }}</p>
                {{ $slot }}
            @else
                @foreach ($fields as $field => $label)
                    <x-field :name="'translations.' . $locale->value . '.' . $field" :label="$label . ' — ' . $locale->label()">
                        <input class="input" name="translations[{{ $locale->value }}][{{ $field }}]"
                               value="{{ old('translations.' . $locale->value . '.' . $field, $matrix[$locale->value][$field] ?? '') }}"
                               dir="{{ $locale->direction() }}">
                    </x-field>
                @endforeach
            @endif
        </div>
    @endforeach
</div>

<?php

namespace App\Support\Traits;

use App\Models\Translation;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Cache;

trait HasTranslations
{
    public function translations(): MorphMany
    {
        return $this->morphMany(Translation::class, 'translatable');
    }

    public function translatableFields(): array
    {
        return property_exists($this, 'translatable') ? $this->translatable : [];
    }

    public function t(string $field, ?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        $fallback = $this->getAttribute($field);

        if ($locale === config('app.fallback_locale') || ! in_array($field, $this->translatableFields(), true)) {
            return $fallback;
        }

        $value = $this->translationBag($locale)[$field] ?? null;

        return $value !== null && $value !== '' ? $value : $fallback;
    }

    public function translationBag(?string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();

        if ($this->relationLoaded('translations')) {
            return $this->translations
                ->where('locale', $locale)
                ->pluck('value', 'field')
                ->all();
        }

        return Cache::tags(['translations'])->remember(
            $this->translationCacheKey($locale),
            now()->addHours(12),
            fn () => $this->translations()->where('locale', $locale)->pluck('value', 'field')->all()
        );
    }

    public function syncTranslations(array $payload): void
    {
        foreach ($payload as $locale => $fields) {
            if (! in_array($locale, config('flavor.locales'), true)) {
                continue;
            }

            foreach ($fields as $field => $value) {
                if (! in_array($field, $this->translatableFields(), true)) {
                    continue;
                }

                if ($value === null || $value === '') {
                    $this->translations()->where('locale', $locale)->where('field', $field)->delete();

                    continue;
                }

                $this->translations()->updateOrCreate(
                    ['locale' => $locale, 'field' => $field],
                    ['value' => $value]
                );
            }

            Cache::tags(['translations'])->forget($this->translationCacheKey($locale));
        }

        $this->unsetRelation('translations');
    }

    public function translationMatrix(): array
    {
        $matrix = [];

        foreach (config('flavor.locales') as $locale) {
            $matrix[$locale] = $locale === config('app.fallback_locale')
                ? collect($this->translatableFields())->mapWithKeys(fn ($field) => [$field => $this->getAttribute($field)])->all()
                : $this->translations()->where('locale', $locale)->pluck('value', 'field')->all();
        }

        return $matrix;
    }

    protected function translationCacheKey(string $locale): string
    {
        return sprintf('translations:%s:%s:%s', $this->getMorphClass(), $this->getKey(), $locale);
    }

    protected static function bootHasTranslations(): void
    {
        static::deleting(function ($model) {
            $model->translations()->delete();
        });
    }
}

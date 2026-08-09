<?php

namespace App\Enums;

enum Locale: string
{
    case English = 'en';
    case Arabic = 'ar';
    case French = 'fr';

    public function label(): string
    {
        return match ($this) {
            self::English => 'English',
            self::Arabic => 'العربية',
            self::French => 'Français',
        };
    }

    public function direction(): string
    {
        return $this === self::Arabic ? 'rtl' : 'ltr';
    }

    public function isRtl(): bool
    {
        return $this->direction() === 'rtl';
    }

    public static function codes(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }

    public static function current(): self
    {
        return self::tryFrom(app()->getLocale()) ?? self::English;
    }
}

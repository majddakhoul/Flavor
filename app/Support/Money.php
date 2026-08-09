<?php

namespace App\Support;

final class Money
{
    public static function format(int|float|null $amount): string
    {
        $amount = (float) ($amount ?? 0);
        $formatted = number_format($amount, 0, '.', ',');

        return app()->getLocale() === 'ar'
            ? $formatted . ' ' . config('flavor.currency.symbol_ar')
            : config('flavor.currency.symbol') . ' ' . $formatted;
    }

    public static function raw(int|float|null $amount): int
    {
        return (int) round((float) ($amount ?? 0));
    }
}

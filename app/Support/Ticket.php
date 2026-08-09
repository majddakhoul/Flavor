<?php

namespace App\Support;

use Illuminate\Support\Str;

final class Ticket
{
    private const ALPHABET = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    public static function code(int $length = 8): string
    {
        $code = '';

        for ($i = 0; $i < $length; $i++) {
            $code .= self::ALPHABET[random_int(0, strlen(self::ALPHABET) - 1)];
        }

        return $code;
    }

    public static function slug(string $value): string
    {
        return Str::slug($value) ?: Str::lower(Str::random(8));
    }
}

<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    protected $except = [
        'flavor_theme',
        'flavor_locale',
        'flavor_cookie_consent',
    ];
}

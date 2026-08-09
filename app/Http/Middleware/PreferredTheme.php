<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class PreferredTheme
{
    public const COOKIE = 'flavor_theme';

    public function handle(Request $request, Closure $next): Response
    {
        $theme = in_array($request->cookie(self::COOKIE), ['light', 'dark'], true)
            ? $request->cookie(self::COOKIE)
            : 'light';

        View::share('theme', $theme);
        View::share('cookieConsent', $request->cookie('flavor_cookie_consent'));

        return $next($request);
    }
}

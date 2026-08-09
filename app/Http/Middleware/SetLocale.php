<?php

namespace App\Http\Middleware;

use App\Enums\Locale;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public const COOKIE = 'flavor_locale';

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->resolve($request);

        app()->setLocale($locale);
        Carbon::setLocale($locale);
        $request->session()->put('locale', $locale);

        $response = $next($request);

        if ($request->cookie(self::COOKIE) !== $locale) {
            $response->headers->setCookie(cookie()->forever(self::COOKIE, $locale, sameSite: 'lax'));
        }

        return $response;
    }

    protected function resolve(Request $request): string
    {
        $candidates = [
            $request->cookie(self::COOKIE),
            $request->session()->get('locale'),
            $request->user()?->preferred_locale ?? null,
            $request->getPreferredLanguage(Locale::codes()),
            config('app.locale'),
        ];

        foreach ($candidates as $candidate) {
            if (is_string($candidate) && in_array($candidate, Locale::codes(), true)) {
                return $candidate;
            }
        }

        return (string) config('app.fallback_locale');
    }
}

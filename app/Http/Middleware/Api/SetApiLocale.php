<?php

namespace App\Http\Middleware\Api;

use App\Enums\Locale;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

class SetApiLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->resolve($request);

        app()->setLocale($locale);
        Carbon::setLocale($locale);

        return $next($request);
    }

    protected function resolve(Request $request): string
    {
        $candidates = [
            $request->query('locale'),
            $request->header('X-Locale'),
            $request->user()?->preferred_locale,
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

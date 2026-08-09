<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetAppLang
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the locale from the URL segment (e.g., /en/post) choose for ex 4 mean germany ..5 france
        $locale = $request->segment(2);

        // Check if the locale is supported
        if (!in_array($locale, config('app.available_locales'))) {
            abort(400, 'Unsupported locale'); // Return a 400 error for unsupported locales
        }

        // Set the application locale
        App::setLocale($locale);

        // Continue the request
        return $next($request);
    }
}

<?php

namespace App\Http\Controllers\Web\Site;

use App\Enums\Locale;
use App\Http\Controllers\Controller;
use App\Http\Middleware\SetLocale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function __invoke(Request $request, string $locale): RedirectResponse
    {
        abort_unless(in_array($locale, Locale::codes(), true), 404);

        $request->session()->put('locale', $locale);

        return redirect()
            ->back()
            ->withCookie(cookie()->forever(SetLocale::COOKIE, $locale, sameSite: 'lax'));
    }
}

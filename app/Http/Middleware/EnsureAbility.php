<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAbility
{
    public function handle(Request $request, Closure $next, string ...$abilities): Response
    {
        $user = $request->user();

        if ($user === null || ! $user->isStaff()) {
            abort(403, __('errors.staff_only'));
        }

        foreach ($abilities as $ability) {
            if ($user->hasAbility($ability)) {
                return $next($request);
            }
        }

        abort(403, __('errors.missing_ability'));
    }
}

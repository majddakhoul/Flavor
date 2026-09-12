<?php

namespace App\Http\Middleware\Api;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user !== null && ! $user->status) {
            $user->currentAccessToken()?->delete();

            return response()->json([
                'success' => false,
                'message' => __('errors.account_disabled'),
            ], 403);
        }

        return $next($request);
    }
}

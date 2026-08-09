<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmailVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        if(!$user || $user->email_verified_at == null){
            return response()->json([
                'data' => null,
                'success' => false,
                'message' => 'User not authenticated. Check that your email is verified',
                'status' => 403
            ], 403);
        }
        return $next($request);
    }
}

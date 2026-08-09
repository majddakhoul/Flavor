<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserIsCustomer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user || in_array($user->user_type, ['Employee','Manager'])) {
            return response()->json([
                'data' => null,
                'success' => false,
                'message' => 'Only Customers are authorized to access this resource.',
                'status' => 403
            ], 403);
        }
        return $next($request);
    }
}

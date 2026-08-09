<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $status = auth()->user()->status;

        if ($status == 0) {
            return response()->json([
                'data' => null,
                'success' => false,
                'message' => 'User not authenticated. Check that your are logged in the app or not',
                'status' => 403
            ], 403);
        }
        return $next($request);
    }
}

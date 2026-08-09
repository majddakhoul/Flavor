<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserIsEmployee
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user->user_type == 'Employee') {
            $employee = $user->employee;
            if ($employee->position == 'Delivery' || $employee->position == 'Security') {

                return response()->json([
                    'data' => null,
                    'success' => false,
                    'message' => 'Only Chefs Waiters and Managers are authorized to access this resource.',
                    'status' => 403
                ], 403);
            }
        }
        if (!$user || in_array($user->user_type, ['Customer'])) {
            return response()->json([
                'data' => null,
                'success' => false,
                'message' => 'Only Employees and Managers are authorized to access this resource.',
                'status' => 403
            ], 403);
        }
        return $next($request);
    }
}

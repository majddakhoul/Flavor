<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserIsManagerOrWaiter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // If no user or user is a customer, deny access
        if (!$user || $user->user_type === 'Customer') {
            return response()->json([
                'data' => null,
                'success' => false,
                'message' => 'Only managers or waiters are authorized to access this resource.',
                'status' => 403
            ], 403);
        }

        // Allow access only for managers or chefs
        if (in_array($user->user_type, ['Manager', 'Employee'])) {
            if ($user->user_type == 'Employee') {
                $employee = $user->employee;
                if ($employee->position == 'Chef' || $employee->position == 'Delivery' || $employee->position == 'Security') {
                    return response()->json([
                        'data' => null,
                        'success' => false,
                        'message' => 'Only managers or waiters are authorized to access this resource.',
                        'status' => 403
                    ], 403);
                }
            }
            return $next($request);
        }

        return response()->json([
            'data' => null,
            'success' => false,
            'message' => 'Access denied. Required role: Manager or Waiter.',
            'status' => 403
        ], 403);
    }
}

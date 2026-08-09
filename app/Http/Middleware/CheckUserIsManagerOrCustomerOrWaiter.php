<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserIsManagerOrCustomerOrWaiter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // إذا لم يكن هناك مستخدم مسجل
        if (!$user) {
            return response()->json([
                'data' => null,
                'success' => false,
                'message' => 'Authentication required',
                'status' => 401
            ], 401);
        }

        // السماح للمديرين والزبائن مباشرة
        if ($user->user_type === 'Manager' || $user->user_type === 'Customer') {
            return $next($request);
        }

        // معالجة حالة الموظفين
        if ($user->user_type === 'Employee') {
            $position = $user->employee->position ?? null;

            // السماح للنوادل فقط
            if ($position === 'Waiter') {
                return $next($request);
            }

            // منع الطهاة وموظفي التوصيل والأمن
            if (in_array($position, ['Chef', 'Delivery', 'Security'])) {
                return response()->json([
                    'data' => null,
                    'success' => false,
                    'message' => 'Access denied. Your role ('.$position.') is not authorized.',
                    'status' => 403
                ], 403);
            }
        }

        // منع أي أنواع مستخدمين أخرى
        return response()->json([
            'data' => null,
            'success' => false,
            'message' => 'Access denied. Required role: Manager, Waiter or Customer.',
            'status' => 403
        ], 403);
    }
}

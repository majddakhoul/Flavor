<?php

namespace App\Http\Controllers\Api\UsersControllers\AuthControllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LogoutAllUsersController extends Controller
{
    public function logout_all_users(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'data' => null,
                'success' => false,
                'message' => 'User not authenticated',
                'status' => 401
            ], 401);
        }

        try {

            $User = User::find($user->id);
            $User->tokens()->delete();
            $User->status = false;
            $User->save();

            $cartKey = 'cart_' . $user->id;
            if (Session::has($cartKey)) {
                Session::forget($cartKey);
            }

            return response()->json([
                'data' => null,
                'success' => true,
                'message' => 'Logged out successfully',
                'status' => 200
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'data' => null,
                'success' => false,
                'message' => 'Logout failed: ' . $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Services\People\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function __construct(private readonly AuthService $auth)
    {
    }

    public function store(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = $this->auth->verifyCredentials($data['email'], $data['password']);

        $token = $user->createToken($request->userAgent() ?? 'flavor-app')->plainTextToken;

        return $this->ok([
            'user' => new UserResource($user),
            'token' => $token,
        ], __('flash.auth.welcome', ['name' => $user->first_name]));
    }

    public function destroy(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->noContent(__('flash.auth.signed_out'));
    }
}

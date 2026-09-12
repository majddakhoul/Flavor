<?php

namespace App\Http\Controllers\Api\Auth;

use App\DTOs\RegistrationData;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\People\AuthService;
use Illuminate\Http\JsonResponse;

class RegisterController extends Controller
{
    public function __construct(private readonly AuthService $auth)
    {
    }

    public function store(RegisterRequest $request): JsonResponse
    {
        $user = $this->auth->register(RegistrationData::fromArray($request->validated()));

        return $this->created(new UserResource($user), __('flash.auth.registered'));
    }
}

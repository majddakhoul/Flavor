<?php

namespace App\Http\Controllers\Api\Account;

use App\DTOs\ProfileData;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Account\UpdatePasswordRequest;
use App\Http\Requests\Account\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Services\People\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(private readonly AuthService $auth)
    {
    }

    public function show(Request $request): JsonResponse
    {
        return $this->ok(new UserResource($request->user()->load(['customer', 'employee', 'location'])));
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $this->auth->updateProfile($request->user(), ProfileData::fromArray($request->validated()));

        return $this->ok(new UserResource($user), __('flash.profile.updated'));
    }

    public function password(UpdatePasswordRequest $request): JsonResponse
    {
        $this->auth->changePassword($request->user(), $request->validated()['password']);

        return $this->noContent(__('flash.profile.password_updated'));
    }

    public function deactivate(Request $request): JsonResponse
    {
        $this->auth->deactivate($request->user());

        return $this->noContent(__('flash.profile.deactivated'));
    }
}

<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Auth\VerifyEmailRequest;
use App\Http\Resources\UserResource;
use App\Services\People\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    public function __construct(private readonly AuthService $auth)
    {
    }

    public function verify(VerifyEmailRequest $request): JsonResponse
    {
        $this->auth->verify($request->user(), $request->validated()['code']);

        return $this->ok(new UserResource($request->user()->fresh()), __('flash.auth.verified'));
    }

    public function resend(Request $request): JsonResponse
    {
        $this->auth->issueVerificationCode($request->user());

        return $this->noContent(__('flash.auth.code_sent'));
    }
}

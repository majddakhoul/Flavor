<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\VerifyEmailRequest;
use App\Services\People\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationController extends Controller
{
    public function __construct(private readonly AuthService $auth)
    {
    }

    public function notice(Request $request): View|RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('account.dashboard');
        }

        return view('auth.verify');
    }

    public function verify(VerifyEmailRequest $request): RedirectResponse
    {
        $this->auth->verify($request->user(), $request->validated()['code']);

        $target = $request->user()->isStaff() ? 'manage.dashboard' : 'account.dashboard';

        return $this->done($target, __('flash.auth.verified'));
    }

    public function resend(Request $request): RedirectResponse
    {
        $this->auth->issueVerificationCode($request->user());

        return $this->done('verification.notice', __('flash.auth.code_sent'));
    }
}

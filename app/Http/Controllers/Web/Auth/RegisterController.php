<?php

namespace App\Http\Controllers\Web\Auth;

use App\DTOs\RegistrationData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\Catalog\LocationService;
use App\Services\People\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function __construct(
        private readonly AuthService $auth,
        private readonly LocationService $locations,
    ) {
    }

    public function create(): View
    {
        return view('auth.register', ['locations' => $this->locations->options()]);
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $user = $this->auth->register(RegistrationData::fromArray($request->validated()));

        Auth::login($user);

        return $this->done('verification.notice', __('flash.auth.registered'));
    }
}

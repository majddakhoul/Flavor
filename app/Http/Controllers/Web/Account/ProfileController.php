<?php

namespace App\Http\Controllers\Web\Account;

use App\DTOs\ProfileData;
use App\Enums\Allergy;
use App\Enums\Gender;
use App\Http\Controllers\Controller;
use App\Http\Requests\Account\UpdatePasswordRequest;
use App\Http\Requests\Account\UpdateProfileRequest;
use App\Services\Catalog\LocationService;
use App\Services\People\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        private readonly AuthService $auth,
        private readonly LocationService $locations,
    ) {
    }

    public function edit(Request $request): View
    {
        return view('account.profile', [
            'user' => $request->user()->load(['customer', 'location']),
            'locations' => $this->locations->options(),
            'genders' => Gender::options(),
            'allergies' => Allergy::options(),
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $this->auth->updateProfile($request->user(), ProfileData::fromArray($request->validated()));

        return $this->done('account.profile.edit', __('flash.profile.updated'));
    }

    public function password(UpdatePasswordRequest $request): RedirectResponse
    {
        $this->auth->changePassword($request->user(), $request->validated()['password']);

        return $this->done('account.profile.edit', __('flash.profile.password_updated'));
    }

    public function deactivate(Request $request): RedirectResponse
    {
        $this->auth->deactivate($request->user());
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $this->done('home', __('flash.profile.deactivated'));
    }
}

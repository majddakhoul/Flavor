<?php

namespace App\Services\People;

use App\DTOs\ProfileData;
use App\DTOs\RegistrationData;
use App\Enums\UserType;
use App\Events\VerificationCodeIssued;
use App\Exceptions\Domain\DomainException;
use App\Models\Customer;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    private const CODE_TTL_MINUTES = 15;

    public function __construct(private readonly UserRepositoryInterface $users)
    {
    }

    public function register(RegistrationData $data): User
    {
        $user = DB::transaction(function () use ($data) {
            $user = $this->users->create($data->toArray() + [
                'password' => $data->password,
                'user_type' => UserType::Customer->value,
                'status' => true,
                'preferred_locale' => app()->getLocale(),
            ]);

            Customer::create(['user_id' => $user->id]);

            return $user;
        });

        $this->issueVerificationCode($user);

        return $user;
    }

    public function attempt(array $credentials, bool $remember = false): User
    {
        $credentials['email'] = strtolower(trim($credentials['email'] ?? ''));

        if (! Auth::attempt($credentials, $remember)) {
            throw new DomainException(__('auth.failed'), 422);
        }

        $user = Auth::user();

        if (! $user->status) {
            Auth::logout();

            throw new DomainException(__('errors.account_disabled'), 403);
        }

        return $user;
    }

    public function verifyCredentials(string $email, string $password): User
    {
        $user = $this->users->findByEmail($email);

        if ($user === null || ! Hash::check($password, $user->password)) {
            throw new DomainException(__('auth.failed'), 422);
        }

        if (! $user->status) {
            throw new DomainException(__('errors.account_disabled'), 403);
        }

        return $user;
    }

    public function issueVerificationCode(User $user): string
    {
        $code = (string) random_int(100000, 999999);

        Cache::put($this->codeKey($user), $code, now()->addMinutes(self::CODE_TTL_MINUTES));

        event(new VerificationCodeIssued($user, $code, self::CODE_TTL_MINUTES));

        return $code;
    }

    public function verify(User $user, string $code): void
    {
        $expected = Cache::get($this->codeKey($user));

        if ($expected === null) {
            throw new DomainException(__('errors.verification_expired'), 422);
        }

        if (! hash_equals($expected, trim($code))) {
            throw new DomainException(__('errors.verification_mismatch'), 422);
        }

        $user->forceFill(['email_verified_at' => now()])->save();
        Cache::forget($this->codeKey($user));
    }

    public function updateProfile(User $user, ProfileData $data): User
    {
        DB::transaction(function () use ($user, $data) {
            $user->update($data->toArray());

            if ($user->customer !== null) {
                $user->customer->update([
                    'allergies' => $data->allergies,
                    'favorite_categories' => $data->favorite_categories,
                ]);
            }
        });

        return $user->refresh();
    }

    public function changePassword(User $user, string $password): void
    {
        $user->update(['password' => $password]);
    }

    public function deactivate(User $user): void
    {
        $user->update(['status' => false]);
        $user->tokens()->delete();
    }

    protected function codeKey(User $user): string
    {
        return 'flavor:verification:' . $user->id;
    }
}

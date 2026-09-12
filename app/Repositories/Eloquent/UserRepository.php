<?php

namespace App\Repositories\Eloquent;

use App\Enums\EmployeePosition;
use App\Enums\UserType;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Support\QueryOptions;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    protected function model(): Model
    {
        return new User();
    }

    public function findByEmail(string $email): ?User
    {
        return $this->query()->where('email', strtolower($email))->first();
    }

    public function staffPaginated(QueryOptions $options): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->query()->staff()->applyOptions($options)->paginate($options->perPage)->withQueryString();
    }

    public function activeManagers(): Collection
    {
        return $this->query()
            ->active()
            ->where('user_type', UserType::Manager->value)
            ->get();
    }

    public function activeStaffWithAbility(string $ability): Collection
    {
        $positions = array_values(array_filter(
            EmployeePosition::cases(),
            fn (EmployeePosition $position) => in_array($ability, $position->abilities(), true)
                && $position !== EmployeePosition::Manager
        ));

        return $this->query()
            ->active()
            ->where(function ($query) use ($positions) {
                $query->where('user_type', UserType::Manager->value)
                    ->orWhereHas('employee', fn ($relation) => $relation->whereIn(
                        'position',
                        array_map(fn (EmployeePosition $position) => $position->value, $positions)
                    ));
            })
            ->get();
    }
}

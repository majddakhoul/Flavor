<?php

namespace App\Policies;

use App\Models\Location;
use App\Models\User;

class LocationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAbility('people');
    }

    public function view(User $user, Location $location): bool
    {
        return $user->hasAbility('people');
    }

    public function create(User $user): bool
    {
        return $user->isManager();
    }

    public function update(User $user, Location $location): bool
    {
        return $user->isManager();
    }

    public function delete(User $user, Location $location): bool
    {
        return $user->isManager();
    }
}

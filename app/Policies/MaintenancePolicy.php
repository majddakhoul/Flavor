<?php

namespace App\Policies;

use App\Models\Maintenance;
use App\Models\User;

class MaintenancePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAbility('inventory');
    }

    public function view(User $user, Maintenance $maintenance): bool
    {
        return $user->hasAbility('inventory');
    }

    public function create(User $user): bool
    {
        return $user->hasAbility('inventory');
    }

    public function update(User $user, Maintenance $maintenance): bool
    {
        return $user->hasAbility('inventory');
    }

    public function delete(User $user, Maintenance $maintenance): bool
    {
        return $user->isManager();
    }
}

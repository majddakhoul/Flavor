<?php

namespace App\Policies;

use App\Models\Meal;
use App\Models\User;

class MealPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAbility('catalog');
    }

    public function view(User $user, Meal $meal): bool
    {
        return $user->hasAbility('catalog');
    }

    public function create(User $user): bool
    {
        return $user->hasAbility('catalog');
    }

    public function update(User $user, Meal $meal): bool
    {
        return $user->hasAbility('catalog');
    }

    public function delete(User $user, Meal $meal): bool
    {
        return $user->isManager();
    }
}

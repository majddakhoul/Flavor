<?php

namespace App\Policies;

use App\Models\Ingredient;
use App\Models\User;

class IngredientPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAbility('inventory');
    }

    public function view(User $user, Ingredient $ingredient): bool
    {
        return $user->hasAbility('inventory');
    }

    public function create(User $user): bool
    {
        return $user->hasAbility('inventory');
    }

    public function update(User $user, Ingredient $ingredient): bool
    {
        return $user->hasAbility('inventory');
    }

    public function delete(User $user, Ingredient $ingredient): bool
    {
        return $user->isManager();
    }
}

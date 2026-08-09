<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        \App\Models\Meal::class => \App\Policies\MealPolicy::class,
        \App\Models\Category::class => \App\Policies\CategoryPolicy::class,
        \App\Models\Offer::class => \App\Policies\OfferPolicy::class,
        \App\Models\Ingredient::class => \App\Policies\IngredientPolicy::class,
        \App\Models\Table::class => \App\Policies\TablePolicy::class,
        \App\Models\Reservation::class => \App\Policies\ReservationPolicy::class,
        \App\Models\Order::class => \App\Policies\OrderPolicy::class,
        \App\Models\Employee::class => \App\Policies\EmployeePolicy::class,
        \App\Models\Customer::class => \App\Policies\CustomerPolicy::class,
        \App\Models\Maintenance::class => \App\Policies\MaintenancePolicy::class,
        \App\Models\Location::class => \App\Policies\LocationPolicy::class,
    ];

    public function boot(): void
    {
        Gate::define('view-dashboard', fn ($user) => $user->isStaff());
        Gate::define('view-reports', fn ($user) => $user->hasAbility('reports'));
        Gate::define('manage-translations', fn ($user) => $user->isManager());
    }
}

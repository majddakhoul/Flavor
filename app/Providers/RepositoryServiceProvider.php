<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    private const BINDINGS = [
        \App\Repositories\Contracts\UserRepositoryInterface::class => \App\Repositories\Eloquent\UserRepository::class,
        \App\Repositories\Contracts\CustomerRepositoryInterface::class => \App\Repositories\Eloquent\CustomerRepository::class,
        \App\Repositories\Contracts\EmployeeRepositoryInterface::class => \App\Repositories\Eloquent\EmployeeRepository::class,
        \App\Repositories\Contracts\CategoryRepositoryInterface::class => \App\Repositories\Eloquent\CategoryRepository::class,
        \App\Repositories\Contracts\MealRepositoryInterface::class => \App\Repositories\Eloquent\MealRepository::class,
        \App\Repositories\Contracts\OfferRepositoryInterface::class => \App\Repositories\Eloquent\OfferRepository::class,
        \App\Repositories\Contracts\IngredientRepositoryInterface::class => \App\Repositories\Eloquent\IngredientRepository::class,
        \App\Repositories\Contracts\TableRepositoryInterface::class => \App\Repositories\Eloquent\TableRepository::class,
        \App\Repositories\Contracts\ReservationRepositoryInterface::class => \App\Repositories\Eloquent\ReservationRepository::class,
        \App\Repositories\Contracts\OrderRepositoryInterface::class => \App\Repositories\Eloquent\OrderRepository::class,
        \App\Repositories\Contracts\LocationRepositoryInterface::class => \App\Repositories\Eloquent\LocationRepository::class,
        \App\Repositories\Contracts\MaintenanceRepositoryInterface::class => \App\Repositories\Eloquent\MaintenanceRepository::class,
        \App\Repositories\Contracts\PictureRepositoryInterface::class => \App\Repositories\Eloquent\PictureRepository::class,
    ];

    public function register(): void
    {
        foreach (self::BINDINGS as $contract => $implementation) {
            $this->app->bind($contract, $implementation);
        }
    }

    public function provides(): array
    {
        return array_keys(self::BINDINGS);
    }
}

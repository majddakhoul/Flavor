<?php

namespace App\Providers;

use App\Enums\Locale;
use App\Models\Order;
use App\Models\Reservation;
use App\Services\Sales\CartService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        Paginator::defaultView('components.pagination');
        Paginator::defaultSimpleView('components.pagination');

        Route::model('order', Order::class);
        Route::model('reservation', Reservation::class);

        Blade::directive('money', fn ($expression) => "<?php echo \App\Support\Money::format($expression); ?>");

        Blade::if('ability', fn (string $ability) => auth()->check() && auth()->user()->hasAbility($ability));

        View::composer('*', function ($view) {
            $view->with('currentLocale', Locale::current());
        });

        View::composer(['layouts.site', 'partials.header'], function ($view) {
            $view->with('cartCount', auth()->check() ? app(CartService::class)->count() : 0);
        });
    }
}

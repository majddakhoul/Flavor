<?php

use App\Http\Controllers\Api\Account\CartController;
use App\Http\Controllers\Api\Account\CheckoutController;
use App\Http\Controllers\Api\Account\DashboardController as AccountDashboardController;
use App\Http\Controllers\Api\Account\OrderController as AccountOrderController;
use App\Http\Controllers\Api\Account\ProfileController;
use App\Http\Controllers\Api\Account\RatingController;
use App\Http\Controllers\Api\Account\ReservationController as AccountReservationController;
use App\Http\Controllers\Api\Auth\EmailVerificationController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\PasswordResetController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Catalog\CategoryController;
use App\Http\Controllers\Api\Catalog\LocationController;
use App\Http\Controllers\Api\Catalog\MealController;
use App\Http\Controllers\Api\Catalog\OfferController;
use App\Http\Controllers\Api\Catalog\TableController;
use App\Http\Controllers\Api\Manage\CategoryController as ManageCategoryController;
use App\Http\Controllers\Api\Manage\CustomerController;
use App\Http\Controllers\Api\Manage\DashboardController as ManageDashboardController;
use App\Http\Controllers\Api\Manage\EmployeeController;
use App\Http\Controllers\Api\Manage\IngredientController;
use App\Http\Controllers\Api\Manage\LocationController as ManageLocationController;
use App\Http\Controllers\Api\Manage\MaintenanceController;
use App\Http\Controllers\Api\Manage\MealController as ManageMealController;
use App\Http\Controllers\Api\Manage\OfferController as ManageOfferController;
use App\Http\Controllers\Api\Manage\OrderController as ManageOrderController;
use App\Http\Controllers\Api\Manage\ReportController;
use App\Http\Controllers\Api\Manage\ReservationController as ManageReservationController;
use App\Http\Controllers\Api\Manage\TableController as ManageTableController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Middleware\Api\EnsureEmailIsVerified;
use App\Http\Middleware\Api\EnsureUserIsActive;
use Illuminate\Support\Facades\Route;

Route::prefix('catalog')->name('catalog.')->group(function () {
    Route::get('/meals', [MealController::class, 'index'])->name('meals.index');
    Route::get('/meals/featured', [MealController::class, 'featured'])->name('meals.featured');
    Route::get('/meals/top-selling', [MealController::class, 'topSelling'])->name('meals.top-selling');
    Route::get('/meals/{meal}', [MealController::class, 'show'])->name('meals.show');

    Route::get('/offers', [OfferController::class, 'index'])->name('offers.index');
    Route::get('/offers/running', [OfferController::class, 'running'])->name('offers.running');
    Route::get('/offers/top-selling', [OfferController::class, 'topSelling'])->name('offers.top-selling');
    Route::get('/offers/{offer}', [OfferController::class, 'show'])->name('offers.show');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/tree', [CategoryController::class, 'tree'])->name('categories.tree');
    Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');

    Route::get('/tables', [TableController::class, 'index'])->name('tables.index');
    Route::get('/locations', [LocationController::class, 'index'])->name('locations.index');
});

Route::prefix('auth')->name('auth.')->group(function () {
    Route::post('/register', [RegisterController::class, 'store'])->middleware('throttle:auth')->name('register');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:auth')->name('login');
    Route::post('/forgot-password', [PasswordResetController::class, 'email'])->middleware('throttle:auth')->name('forgot-password');
    Route::post('/reset-password', [PasswordResetController::class, 'update'])->middleware('throttle:auth')->name('reset-password');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
        Route::post('/verify-email', [EmailVerificationController::class, 'verify'])->name('verify-email');
        Route::post('/resend-verification', [EmailVerificationController::class, 'resend'])
            ->middleware('throttle:auth')
            ->name('resend-verification');
    });
});

Route::middleware(['auth:sanctum', EnsureUserIsActive::class])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    Route::middleware([EnsureEmailIsVerified::class])->group(function () {
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'password'])->name('profile.password');
        Route::delete('/profile', [ProfileController::class, 'deactivate'])->name('profile.deactivate');

        Route::prefix('account')->name('account.')->middleware('customer')->group(function () {
            Route::get('/dashboard', AccountDashboardController::class)->name('dashboard');

            Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
            Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
            Route::put('/cart', [CartController::class, 'update'])->name('cart.update');
            Route::delete('/cart/{type}/{id}', [CartController::class, 'destroy'])->name('cart.destroy');
            Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');

            Route::post('/checkout', [CheckoutController::class, 'store'])
                ->middleware('throttle:checkout')
                ->name('checkout.store');

            Route::get('/orders', [AccountOrderController::class, 'index'])->name('orders.index');
            Route::get('/orders/{order}', [AccountOrderController::class, 'show'])->name('orders.show');
            Route::delete('/orders/{order}', [AccountOrderController::class, 'cancel'])->name('orders.cancel');
            Route::post('/orders/{order}/revert', [AccountOrderController::class, 'revert'])->name('orders.revert');
            Route::post('/orders/{order}/restore', [AccountOrderController::class, 'restore'])->name('orders.restore');

            Route::get('/reservations', [AccountReservationController::class, 'index'])->name('reservations.index');
            Route::get('/reservations/availability', [AccountReservationController::class, 'availability'])->name('reservations.availability');
            Route::post('/reservations', [AccountReservationController::class, 'store'])->name('reservations.store');
            Route::get('/reservations/{reservation}', [AccountReservationController::class, 'show'])->name('reservations.show');
            Route::delete('/reservations/{reservation}', [AccountReservationController::class, 'cancel'])->name('reservations.cancel');

            Route::post('/ratings/meals/{meal}', [RatingController::class, 'meal'])->name('ratings.meal');
            Route::post('/ratings/offers/{offer}', [RatingController::class, 'offer'])->name('ratings.offer');
        });

        Route::prefix('manage')->name('manage.')->middleware('ability:catalog,inventory,floor,sales,people,reports')->group(function () {
            Route::get('/dashboard', ManageDashboardController::class)->name('dashboard');

            Route::middleware('ability:reports')->group(function () {
                Route::get('/reports/finance', [ReportController::class, 'finance'])->name('reports.finance');
                Route::get('/reports/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');
                Route::get('/reports/menu', [ReportController::class, 'menu'])->name('reports.menu');
                Route::get('/reports/ingredients/most-consumed', [ReportController::class, 'mostConsumedIngredients'])
                    ->name('reports.ingredients-most-consumed');
            });

            Route::middleware('ability:catalog')->group(function () {
                Route::apiResource('categories', ManageCategoryController::class)->except(['show']);
                Route::get('/categories/{category}', [ManageCategoryController::class, 'show'])->name('categories.show');

                Route::apiResource('meals', ManageMealController::class);
                Route::put('/meals/{meal}/ingredients', [ManageMealController::class, 'syncIngredients'])->name('meals.ingredients.sync');
                Route::delete('/meals/{meal}/ingredients/{ingredient}', [ManageMealController::class, 'detachIngredient'])
                    ->name('meals.ingredients.detach');
                Route::delete('/meals/{meal}/picture', [ManageMealController::class, 'destroyPicture'])->name('meals.picture.destroy');

                Route::apiResource('offers', ManageOfferController::class);
                Route::post('/offers/{offer}/toggle', [ManageOfferController::class, 'toggle'])->name('offers.toggle');
                Route::delete('/offers/{offer}/meals/{meal}', [ManageOfferController::class, 'detachMeal'])->name('offers.meals.detach');
            });

            Route::middleware('ability:inventory')->group(function () {
                Route::apiResource('ingredients', IngredientController::class);
                Route::post('/ingredients/{ingredient}/stock', [IngredientController::class, 'adjustStock'])->name('ingredients.stock');
            });

            Route::middleware('ability:floor')->group(function () {
                Route::apiResource('tables', ManageTableController::class)->except(['show']);

                Route::get('/reservations', [ManageReservationController::class, 'index'])->name('reservations.index');
                Route::get('/reservations/statistics', [ManageReservationController::class, 'statistics'])->name('reservations.statistics');
                Route::post('/reservations', [ManageReservationController::class, 'store'])->name('reservations.store');
                Route::get('/reservations/{reservation}', [ManageReservationController::class, 'show'])->name('reservations.show');
                Route::patch('/reservations/{reservation}/status', [ManageReservationController::class, 'status'])->name('reservations.status');
                Route::put('/reservations/{reservation}/tables', [ManageReservationController::class, 'tables'])->name('reservations.tables');
                Route::delete('/reservations/{reservation}', [ManageReservationController::class, 'destroy'])->name('reservations.destroy');
            });

            Route::middleware('ability:sales')->group(function () {
                Route::get('/orders', [ManageOrderController::class, 'index'])->name('orders.index');
                Route::get('/orders/statistics', [ManageOrderController::class, 'statistics'])->name('orders.statistics');
                Route::get('/orders/trashed', [ManageOrderController::class, 'trashed'])->name('orders.trashed');
                Route::get('/orders/{order}', [ManageOrderController::class, 'show'])->name('orders.show');
                Route::patch('/orders/{order}/status', [ManageOrderController::class, 'status'])->name('orders.status');
                Route::post('/orders/{order}/restore', [ManageOrderController::class, 'restore'])->name('orders.restore');
                Route::delete('/orders/{order}', [ManageOrderController::class, 'destroy'])->name('orders.destroy');
            });

            Route::middleware('ability:people')->group(function () {
                Route::apiResource('employees', EmployeeController::class)->except(['show']);
                Route::get('/employees/payroll', [EmployeeController::class, 'payroll'])->name('employees.payroll');
                Route::get('/employees/{employee}', [EmployeeController::class, 'show'])->name('employees.show');

                Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
                Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
                Route::post('/customers/{customer}/ban', [CustomerController::class, 'ban'])->name('customers.ban');
                Route::post('/customers/{customer}/unban', [CustomerController::class, 'unban'])->name('customers.unban');

                Route::apiResource('locations', ManageLocationController::class)->except(['show']);
                Route::apiResource('maintenances', MaintenanceController::class)->except(['show']);
                Route::get('/maintenances/totals', [MaintenanceController::class, 'totals'])->name('maintenances.totals');
                Route::get('/maintenances/{maintenance}', [MaintenanceController::class, 'show'])->name('maintenances.show');
            });
        });
    });
});

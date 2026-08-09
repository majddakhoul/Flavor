<?php

use App\Http\Controllers\Web\Account\CartController;
use App\Http\Controllers\Web\Account\CheckoutController;
use App\Http\Controllers\Web\Account\DashboardController as AccountDashboardController;
use App\Http\Controllers\Web\Account\OrderController as AccountOrderController;
use App\Http\Controllers\Web\Account\ProfileController;
use App\Http\Controllers\Web\Account\RatingController;
use App\Http\Controllers\Web\Account\ReservationController as AccountReservationController;
use App\Http\Controllers\Web\Auth\EmailVerificationController;
use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\Auth\PasswordResetController;
use App\Http\Controllers\Web\Auth\RegisterController;
use App\Http\Controllers\Web\Manage\CategoryController;
use App\Http\Controllers\Web\Manage\CustomerController;
use App\Http\Controllers\Web\Manage\DashboardController as ManageDashboardController;
use App\Http\Controllers\Web\Manage\EmployeeController;
use App\Http\Controllers\Web\Manage\IngredientController;
use App\Http\Controllers\Web\Manage\LocationController;
use App\Http\Controllers\Web\Manage\MaintenanceController;
use App\Http\Controllers\Web\Manage\MealController;
use App\Http\Controllers\Web\Manage\OfferController as ManageOfferController;
use App\Http\Controllers\Web\Manage\OrderController as ManageOrderController;
use App\Http\Controllers\Web\Manage\ReportController;
use App\Http\Controllers\Web\Manage\ReservationController as ManageReservationController;
use App\Http\Controllers\Web\Manage\TableController;
use App\Http\Controllers\Web\Site\HomeController;
use App\Http\Controllers\Web\Site\LocaleController;
use App\Http\Controllers\Web\Site\MenuController;
use App\Http\Controllers\Web\Site\OfferController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/locale/{locale}', LocaleController::class)->name('locale.switch');

Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
Route::get('/menu/{meal}', [MenuController::class, 'show'])->name('menu.show');
Route::get('/offers', [OfferController::class, 'index'])->name('offers.index');
Route::get('/offers/{offer}', [OfferController::class, 'show'])->name('offers.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:auth');
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->middleware('throttle:auth');
    Route::get('/forgot-password', [PasswordResetController::class, 'request'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'email'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'reset'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'update'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/verify-email', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::post('/verify-email', [EmailVerificationController::class, 'verify'])->name('verification.verify');
    Route::post('/verify-email/resend', [EmailVerificationController::class, 'resend'])
        ->middleware('throttle:auth')
        ->name('verification.resend');
});

Route::middleware(['auth', 'verified'])
    ->prefix('account')
    ->name('account.')
    ->group(function () {
        Route::get('/', AccountDashboardController::class)->name('dashboard');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'password'])->name('profile.password');
        Route::delete('/profile', [ProfileController::class, 'deactivate'])->name('profile.deactivate');

        Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
        Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
        Route::put('/cart', [CartController::class, 'update'])->name('cart.update');
        Route::delete('/cart/{type}/{id}', [CartController::class, 'destroy'])->name('cart.destroy');
        Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');

        Route::middleware('customer')->group(function () {
            Route::get('/checkout', [CheckoutController::class, 'create'])->name('checkout.create');
            Route::post('/checkout', [CheckoutController::class, 'store'])
                ->middleware('throttle:checkout')
                ->name('checkout.store');

            Route::get('/orders', [AccountOrderController::class, 'index'])->name('orders.index');
            Route::get('/orders/{order}', [AccountOrderController::class, 'show'])->name('orders.show');
            Route::delete('/orders/{order}', [AccountOrderController::class, 'cancel'])->name('orders.cancel');
            Route::post('/orders/{order}/revert', [AccountOrderController::class, 'revert'])->name('orders.revert');
            Route::post('/orders/{order}/restore', [AccountOrderController::class, 'restore'])->name('orders.restore');

            Route::get('/reservations', [AccountReservationController::class, 'index'])->name('reservations.index');
            Route::get('/reservations/create', [AccountReservationController::class, 'create'])->name('reservations.create');
            Route::post('/reservations', [AccountReservationController::class, 'store'])->name('reservations.store');
            Route::get('/reservations/{reservation}', [AccountReservationController::class, 'show'])->name('reservations.show');
            Route::delete('/reservations/{reservation}', [AccountReservationController::class, 'cancel'])->name('reservations.cancel');

            Route::post('/ratings/meals/{meal}', [RatingController::class, 'meal'])->name('ratings.meal');
            Route::post('/ratings/offers/{offer}', [RatingController::class, 'offer'])->name('ratings.offer');
        });
    });

Route::middleware(['auth', 'verified'])
    ->prefix('manage')
    ->name('manage.')
    ->group(function () {
        Route::get('/', ManageDashboardController::class)->name('dashboard');

        Route::middleware('ability:catalog')->group(function () {
            Route::get('/meals', [MealController::class, 'index'])->name('meals.index');
            Route::get('/meals/create', [MealController::class, 'create'])->name('meals.create');
            Route::post('/meals', [MealController::class, 'store'])->name('meals.store');
            Route::get('/meals/{meal}/edit', [MealController::class, 'edit'])->name('meals.edit');
            Route::put('/meals/{meal}', [MealController::class, 'update'])->name('meals.update');
            Route::delete('/meals/{meal}', [MealController::class, 'destroy'])->name('meals.destroy');
            Route::put('/meals/{meal}/recipe', [MealController::class, 'syncIngredients'])->name('meals.recipe');
            Route::delete('/meals/{meal}/recipe/{ingredient}', [MealController::class, 'detachIngredient'])->name('meals.recipe.detach');
            Route::delete('/meals/{meal}/photo', [MealController::class, 'destroyPicture'])->name('meals.photo.destroy');

            Route::resource('categories', CategoryController::class)->except('show');

            Route::get('/offers', [ManageOfferController::class, 'index'])->name('offers.index');
            Route::get('/offers/create', [ManageOfferController::class, 'create'])->name('offers.create');
            Route::post('/offers', [ManageOfferController::class, 'store'])->name('offers.store');
            Route::get('/offers/{offer}/edit', [ManageOfferController::class, 'edit'])->name('offers.edit');
            Route::put('/offers/{offer}', [ManageOfferController::class, 'update'])->name('offers.update');
            Route::patch('/offers/{offer}/toggle', [ManageOfferController::class, 'toggle'])->name('offers.toggle');
            Route::delete('/offers/{offer}/meals/{meal}', [ManageOfferController::class, 'detachMeal'])->name('offers.meals.detach');
            Route::delete('/offers/{offer}', [ManageOfferController::class, 'destroy'])->name('offers.destroy');
        });

        Route::middleware('ability:inventory')->group(function () {
            Route::resource('ingredients', IngredientController::class)->except('show');
            Route::patch('/ingredients/{ingredient}/stock', [IngredientController::class, 'adjust'])->name('ingredients.stock');
            Route::resource('maintenances', MaintenanceController::class)->except('show');
        });

        Route::middleware('ability:floor')->group(function () {
            Route::resource('tables', TableController::class)->except('show');

            Route::get('/reservations', [ManageReservationController::class, 'index'])->name('reservations.index');
            Route::get('/reservations/create', [ManageReservationController::class, 'create'])->name('reservations.create');
            Route::post('/reservations', [ManageReservationController::class, 'store'])->name('reservations.store');
            Route::get('/reservations/{reservation}', [ManageReservationController::class, 'show'])->name('reservations.show');
            Route::patch('/reservations/{reservation}/status', [ManageReservationController::class, 'status'])->name('reservations.status');
            Route::put('/reservations/{reservation}/tables', [ManageReservationController::class, 'tables'])->name('reservations.tables');
            Route::delete('/reservations/{reservation}', [ManageReservationController::class, 'destroy'])->name('reservations.destroy');
        });

        Route::middleware('ability:sales')->group(function () {
            Route::get('/orders', [ManageOrderController::class, 'index'])->name('orders.index');
            Route::get('/orders/trashed', [ManageOrderController::class, 'trashed'])->name('orders.trashed');
            Route::get('/orders/{order}', [ManageOrderController::class, 'show'])->name('orders.show');
            Route::patch('/orders/{order}/status', [ManageOrderController::class, 'status'])->name('orders.status');
            Route::post('/orders/{order}/restore', [ManageOrderController::class, 'restore'])->name('orders.restore');
            Route::delete('/orders/{order}', [ManageOrderController::class, 'destroy'])->name('orders.destroy');
        });

        Route::middleware('ability:people')->group(function () {
            Route::resource('employees', EmployeeController::class)->except('show');
            Route::resource('locations', LocationController::class)->except('show');

            Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
            Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
            Route::patch('/customers/{customer}/ban', [CustomerController::class, 'ban'])->name('customers.ban');
            Route::patch('/customers/{customer}/unban', [CustomerController::class, 'unban'])->name('customers.unban');
        });

        Route::middleware('ability:reports')->prefix('reports')->name('reports.')->group(function () {
            Route::get('/finance', [ReportController::class, 'finance'])->name('finance');
            Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
            Route::get('/menu', [ReportController::class, 'menu'])->name('menu');
        });
    });

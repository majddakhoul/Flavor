<?php

namespace App\Providers;

use App\Events\EmployeeHired;
use App\Events\LowStockDetected;
use App\Events\OrderPlaced;
use App\Events\OrderStatusChanged;
use App\Events\ReservationCreated;
use App\Events\ReservationStatusChanged;
use App\Events\VerificationCodeIssued;
use App\Listeners\AlertManagersOnLowStock;
use App\Listeners\NotifyManagersOfNewHire;
use App\Listeners\NotifyStaffOfNewOrder;
use App\Listeners\NotifyStaffOfNewReservation;
use App\Listeners\SendEmployeeCredentials;
use App\Listeners\SendOrderConfirmation;
use App\Listeners\SendOrderStatusUpdate;
use App\Listeners\SendReservationStatusUpdate;
use App\Listeners\SendReservationTicket;
use App\Listeners\SendVerificationCode;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        OrderPlaced::class => [SendOrderConfirmation::class, NotifyStaffOfNewOrder::class],
        OrderStatusChanged::class => [SendOrderStatusUpdate::class],
        ReservationCreated::class => [SendReservationTicket::class, NotifyStaffOfNewReservation::class],
        ReservationStatusChanged::class => [SendReservationStatusUpdate::class],
        LowStockDetected::class => [AlertManagersOnLowStock::class],
        EmployeeHired::class => [SendEmployeeCredentials::class, NotifyManagersOfNewHire::class],
        VerificationCodeIssued::class => [SendVerificationCode::class],
    ];

    public function boot(): void
    {
    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}

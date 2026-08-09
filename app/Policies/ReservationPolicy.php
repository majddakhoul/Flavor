<?php

namespace App\Policies;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use App\Models\User;

class ReservationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAbility('floor');
    }

    public function view(User $user, Reservation $reservation): bool
    {
        return $user->hasAbility('floor') || $this->owns($user, $reservation);
    }

    public function create(User $user): bool
    {
        return $user->isCustomer() || $user->hasAbility('floor');
    }

    public function update(User $user, Reservation $reservation): bool
    {
        return $user->hasAbility('floor')
            || ($this->owns($user, $reservation) && $reservation->status === ReservationStatus::Pending);
    }

    public function transition(User $user, Reservation $reservation): bool
    {
        return $user->hasAbility('floor');
    }

    public function cancel(User $user, Reservation $reservation): bool
    {
        return $reservation->status->holdsTables()
            && ($user->hasAbility('floor') || $this->owns($user, $reservation));
    }

    public function delete(User $user, Reservation $reservation): bool
    {
        return $user->isManager();
    }

    protected function owns(User $user, Reservation $reservation): bool
    {
        return $user->customer !== null && $reservation->customer_id === $user->customer->id;
    }
}

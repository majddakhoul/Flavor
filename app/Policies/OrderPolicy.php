<?php

namespace App\Policies;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAbility('sales');
    }

    public function view(User $user, Order $order): bool
    {
        return $user->hasAbility('sales') || $this->owns($user, $order);
    }

    public function create(User $user): bool
    {
        return $user->isCustomer() || $user->hasAbility('sales');
    }

    public function update(User $user, Order $order): bool
    {
        return $user->hasAbility('sales');
    }

    public function transition(User $user, Order $order): bool
    {
        return $user->hasAbility('sales');
    }

    public function cancel(User $user, Order $order): bool
    {
        if (! $order->status->isOpen()) {
            return false;
        }

        return $user->hasAbility('sales') || ($this->owns($user, $order) && $order->status === OrderStatus::Pending);
    }

    public function revert(User $user, Order $order): bool
    {
        return $order->status === OrderStatus::Pending
            && ($this->owns($user, $order) || $user->hasAbility('sales'));
    }

    public function restore(User $user, Order $order): bool
    {
        return $user->hasAbility('sales') || $this->owns($user, $order);
    }

    public function delete(User $user, Order $order): bool
    {
        return $user->isManager();
    }

    protected function owns(User $user, Order $order): bool
    {
        return $user->customer !== null && $order->customer_id === $user->customer->id;
    }
}

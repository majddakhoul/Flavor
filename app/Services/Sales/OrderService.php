<?php

namespace App\Services\Sales;

use App\Enums\OrderStatus;
use App\Events\OrderStatusChanged;
use App\Exceptions\Domain\InvalidStateTransitionException;
use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Services\Inventory\StockService;
use App\Services\Support\CacheService;
use App\Support\QueryOptions;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        private readonly OrderRepositoryInterface $orders,
        private readonly StockService $stock,
        private readonly CartService $cart,
        private readonly CacheService $cache,
    ) {
    }

    public function paginate(QueryOptions $options): LengthAwarePaginator
    {
        return $this->orders->paginate($options->withRelations([
            'customer.user', 'employee.user', 'location', 'meals.ingredients', 'offers.meals.ingredients',
        ]));
    }

    public function paginateForCustomer(int $customerId, QueryOptions $options): LengthAwarePaginator
    {
        return $this->orders->forCustomer($customerId, $options);
    }

    public function trashed(QueryOptions $options): LengthAwarePaginator
    {
        return $this->orders->trashedPaginated($options);
    }

    public function transition(Order $order, OrderStatus $target): Order
    {
        $current = $order->status;

        if ($current === $target) {
            return $order;
        }

        if (! $current->allows($target)) {
            throw InvalidStateTransitionException::between($current->label(), $target->label());
        }

        return DB::transaction(function () use ($order, $current, $target) {
            $locked = $this->orders->lockById($order->id);

            if ($target === OrderStatus::Cancelled) {
                $this->stock->restore($this->stock->requirementsForOrder($locked));
            }

            $locked->update(['status' => $target->value]);
            $this->cache->flush(['orders', 'dashboard']);

            event(new OrderStatusChanged($locked, $current, $target));

            return $locked->refresh();
        });
    }

    public function cancel(Order $order): Order
    {
        $order = $this->transition($order, OrderStatus::Cancelled);
        $order->delete();

        return $order;
    }

    public function restore(Order $order): Order
    {
        if (! $order->trashed()) {
            return $order;
        }

        if (in_array($order->status, [OrderStatus::Cancelled, OrderStatus::Confirmed], true)) {
            throw InvalidStateTransitionException::locked($order->status->label());
        }

        return DB::transaction(function () use ($order) {
            $requirements = $this->stock->requirementsForOrder($order);
            $this->stock->consume($requirements, $order->reference);

            $order->restore();
            $this->cache->flush(['orders', 'dashboard', 'inventory']);

            return $order->refresh();
        });
    }

    public function revertToCart(Order $order): array
    {
        if ($order->status !== OrderStatus::Pending) {
            throw InvalidStateTransitionException::locked($order->status->label());
        }

        return DB::transaction(function () use ($order) {
            $this->stock->restore($this->stock->requirementsForOrder($order));
            $cart = $this->cart->fillFromOrder($order);

            $order->meals()->detach();
            $order->offers()->detach();
            $order->forceDelete();

            $this->cache->flush(['orders', 'dashboard', 'inventory']);

            return $cart;
        });
    }

    public function purge(Order $order): void
    {
        DB::transaction(function () use ($order) {
            if (! $order->trashed() && $order->status->isOpen()) {
                $this->stock->restore($this->stock->requirementsForOrder($order));
            }

            $order->meals()->detach();
            $order->offers()->detach();
            $order->forceDelete();
        });

        $this->cache->flush(['orders', 'dashboard', 'inventory']);
    }

    public function statistics(): array
    {
        return $this->cache->remember('statistics', 'orders:statistics', function () {
            $byStatus = $this->orders->countByStatus();

            return [
                'total' => array_sum($byStatus),
                'by_status' => $byStatus,
                'by_type' => $this->orders->countByType(),
                'today' => $this->orders->query()->today()->count(),
                'revenue_month' => $this->orders->revenueBetween(now()->startOfMonth(), now()->endOfMonth()),
            ];
        }, ['orders', 'dashboard']);
    }
}

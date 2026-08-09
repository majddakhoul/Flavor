<?php

namespace App\Repositories\Eloquent;

use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Support\QueryOptions;
use Illuminate\Database\Eloquent\Model;

class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    protected function model(): Model
    {
        return new Order();
    }

    public function forCustomer(int $customerId, QueryOptions $options): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->query()
            ->ownedBy($customerId)
            ->with(['meals.ingredients', 'offers.meals.ingredients', 'location'])
            ->applyOptions($options)
            ->paginate($options->perPage)
            ->withQueryString();
    }

    public function countByStatus(): array
    {
        return $this->query()
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status')
            ->all();
    }

    public function countByType(): array
    {
        return $this->query()
            ->selectRaw('order_type, COUNT(*) as aggregate')
            ->groupBy('order_type')
            ->pluck('aggregate', 'order_type')
            ->all();
    }

    public function revenueBetween(\Illuminate\Support\Carbon $from, \Illuminate\Support\Carbon $to): int
    {
        return (int) $this->query()
            ->whereBetween('dated_at', [$from->toDateString(), $to->toDateString()])
            ->where('status', \App\Enums\OrderStatus::Completed->value)
            ->with(['meals.ingredients', 'offers.meals.ingredients'])
            ->get()
            ->sum(fn (Order $order) => $order->total);
    }

    public function dailyVolume(int $days = 14): \Illuminate\Support\Collection
    {
        return $this->query()
            ->selectRaw('DATE(dated_at) as day, COUNT(*) as aggregate')
            ->where('dated_at', '>=', now()->subDays($days)->toDateString())
            ->groupBy('day')
            ->orderBy('day')
            ->get();
    }

    public function latest(int $limit = 6): \Illuminate\Database\Eloquent\Collection
    {
        return $this->query()
            ->with(['customer.user', 'employee.user', 'meals.ingredients', 'offers.meals.ingredients'])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    public function trashedPaginated(QueryOptions $options): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->query()
            ->onlyTrashed()
            ->with(['customer.user', 'meals', 'offers'])
            ->applyOptions($options)
            ->paginate($options->perPage)
            ->withQueryString();
    }
}

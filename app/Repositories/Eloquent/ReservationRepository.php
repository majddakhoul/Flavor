<?php

namespace App\Repositories\Eloquent;

use App\Models\Reservation;
use App\Repositories\Contracts\ReservationRepositoryInterface;
use App\Support\QueryOptions;
use Illuminate\Database\Eloquent\Model;

class ReservationRepository extends BaseRepository implements ReservationRepositoryInterface
{
    protected function model(): Model
    {
        return new Reservation();
    }

    public function forCustomer(int $customerId, QueryOptions $options): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->query()
            ->ownedBy($customerId)
            ->applyOptions($options)
            ->paginate($options->perPage)
            ->withQueryString();
    }

    public function findByCode(string $code): ?Reservation
    {
        return $this->query()->with(['tables', 'customer.user'])->where('reservation_code', strtoupper($code))->first();
    }

    public function countByStatus(): array
    {
        return $this->query()
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status')
            ->all();
    }

    public function upcoming(int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        return $this->query()
            ->active()
            ->upcoming()
            ->with(['customer.user', 'tables'])
            ->orderBy('date')
            ->limit($limit)
            ->get();
    }
}

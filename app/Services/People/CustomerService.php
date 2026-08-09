<?php

namespace App\Services\People;

use App\Enums\ReservationStatus;
use App\Models\Customer;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Services\Support\CacheService;
use App\Support\QueryOptions;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CustomerService
{
    private const TAGS = ['customers', 'dashboard'];

    public function __construct(
        private readonly CustomerRepositoryInterface $customers,
        private readonly CacheService $cache,
    ) {
    }

    public function paginate(QueryOptions $options): LengthAwarePaginator
    {
        return $this->customers->paginate($options->withRelations(['user.location']));
    }

    public function ban(Customer $customer): Customer
    {
        $customer->update(['ban' => true, 'ban_date' => now()->toDateString()]);
        $this->cache->flush(self::TAGS);

        return $customer->refresh();
    }

    public function lift(Customer $customer): Customer
    {
        DB::transaction(function () use ($customer) {
            $customer->reservations()
                ->where('status', ReservationStatus::Cancelled->value)
                ->each(function ($reservation) {
                    $reservation->tables()->detach();
                    $reservation->delete();
                });

            $customer->update(['ban' => false, 'ban_date' => null]);
        });

        $this->cache->flush(self::TAGS);

        return $customer->refresh();
    }

    public function enforceCancellationPolicy(Customer $customer): void
    {
        if ($customer->cancelledReservationsCount() >= config('flavor.reservations.cancellation_limit')) {
            $this->ban($customer);
        }
    }

    public function liftExpiredBans(): int
    {
        $expired = $this->customers->query()
            ->banned()
            ->whereDate('ban_date', '<=', now()->subDays(config('flavor.reservations.ban_days'))->toDateString())
            ->get();

        $expired->each(fn (Customer $customer) => $this->lift($customer));

        return $expired->count();
    }

    public function updatePreferences(Customer $customer, ?string $allergies, ?string $favoriteCategories): Customer
    {
        $customer->update([
            'allergies' => $allergies,
            'favorite_categories' => $favoriteCategories,
        ]);

        return $customer->refresh();
    }
}

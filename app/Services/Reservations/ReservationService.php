<?php

namespace App\Services\Reservations;

use App\DTOs\ReservationData;
use App\Enums\ReservationStatus;
use App\Enums\ReservationType;
use App\Enums\UserType;
use App\Events\ReservationCreated;
use App\Events\ReservationStatusChanged;
use App\Exceptions\Domain\CustomerBannedException;
use App\Exceptions\Domain\DomainException;
use App\Exceptions\Domain\InvalidStateTransitionException;
use App\Models\Customer;
use App\Models\Reservation;
use App\Models\User;
use App\Repositories\Contracts\ReservationRepositoryInterface;
use App\Services\People\CustomerService;
use App\Services\Support\CacheService;
use App\Support\QueryOptions;
use App\Support\Ticket;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReservationService
{
    public function __construct(
        private readonly ReservationRepositoryInterface $reservations,
        private readonly TableAvailabilityService $availability,
        private readonly CustomerService $customers,
        private readonly CacheService $cache,
    ) {
    }

    public function paginate(QueryOptions $options): LengthAwarePaginator
    {
        return $this->reservations->paginate($options->withRelations(['customer.user', 'employee.user', 'tables']));
    }

    public function paginateForCustomer(int $customerId, QueryOptions $options): LengthAwarePaginator
    {
        return $this->reservations->forCustomer($customerId, $options->withRelations(['tables']));
    }

    public function create(User $user, ReservationData $data): Reservation
    {
        $this->assertWindow($data);

        $customer = null;

        if ($user->user_type === UserType::Customer) {
            $customer = $user->customer;

            if ($customer === null) {
                throw new DomainException(__('errors.customer_profile_missing'), 422);
            }

            $this->guardCustomerStanding($customer);
        }

        $reservation = DB::transaction(function () use ($user, $data, $customer) {
            $tables = $this->availability->assertAvailable($data->table_ids, $data->start_time, $data->end_time);
            $this->availability->assertCapacity($tables, $data->party_size);

            $reservation = Reservation::create([
                'reservation_code' => $this->uniqueCode(),
                'party_size' => $data->party_size,
                'special_requests' => $data->special_requests,
                'status' => ReservationStatus::Pending->value,
                'type' => $customer !== null ? ReservationType::Application->value : ReservationType::Locally->value,
                'date' => Carbon::parse($data->start_time)->toDateString(),
                'customer_id' => $customer?->id,
                'employee_id' => $customer === null ? $user->employee?->id : null,
            ]);

            foreach ($tables as $table) {
                $reservation->tables()->attach($table->id, [
                    'start_time' => $data->start_time,
                    'end_time' => $data->end_time,
                ]);
            }

            return $reservation;
        });

        $this->cache->flush(['reservations', 'dashboard']);

        event(new ReservationCreated($reservation->fresh(['tables', 'customer.user'])));

        return $reservation;
    }

    public function updateTables(Reservation $reservation, array $tableIds, string $start, string $end): Reservation
    {
        return DB::transaction(function () use ($reservation, $tableIds, $start, $end) {
            $tables = $this->availability->assertAvailable($tableIds, $start, $end, $reservation->id);
            $this->availability->assertCapacity($tables, $reservation->party_size);

            $reservation->tables()->sync(
                $tables->mapWithKeys(fn ($table) => [$table->id => ['start_time' => $start, 'end_time' => $end]])->all()
            );

            $reservation->update(['date' => Carbon::parse($start)->toDateString()]);
            $this->cache->flush(['reservations', 'dashboard']);

            return $reservation->fresh('tables');
        });
    }

    public function update(Reservation $reservation, array $attributes): Reservation
    {
        $reservation->update($attributes);
        $this->cache->flush(['reservations', 'dashboard']);

        return $reservation->refresh();
    }

    public function transition(Reservation $reservation, ReservationStatus $target): Reservation
    {
        $current = $reservation->status;

        if ($current === $target) {
            return $reservation;
        }

        if (! $current->allows($target)) {
            throw InvalidStateTransitionException::between($current->label(), $target->label());
        }

        return DB::transaction(function () use ($reservation, $current, $target) {
            $locked = $this->reservations->lockById($reservation->id);
            $locked->update(['status' => $target->value]);

            if ($target === ReservationStatus::Cancelled && $locked->customer !== null) {
                $this->customers->enforceCancellationPolicy($locked->customer);
            }

            $this->cache->flush(['reservations', 'dashboard']);
            event(new ReservationStatusChanged($locked, $current, $target));

            return $locked->refresh();
        });
    }

    public function delete(Reservation $reservation): void
    {
        DB::transaction(function () use ($reservation) {
            $reservation->tables()->detach();
            $reservation->delete();
        });

        $this->cache->flush(['reservations', 'dashboard']);
    }

    public function statistics(): array
    {
        return $this->cache->remember('statistics', 'reservations:statistics', fn () => [
            'by_status' => $this->reservations->countByStatus(),
            'upcoming' => $this->reservations->query()->active()->upcoming()->count(),
            'today' => $this->reservations->query()->whereDate('date', now()->toDateString())->count(),
            'occupancy' => $this->availability->occupancy(),
        ], ['reservations', 'dashboard']);
    }

    protected function guardCustomerStanding(Customer $customer): void
    {
        if ($customer->is_banned) {
            throw CustomerBannedException::until($customer->ban_until);
        }

        if ($customer->ban) {
            $this->customers->lift($customer);
        }

        $cancelled = $customer->cancelledReservationsCount();

        if ($cancelled >= config('flavor.reservations.cancellation_limit')) {
            $this->customers->ban($customer);

            throw CustomerBannedException::threshold($cancelled);
        }
    }

    protected function assertWindow(ReservationData $data): void
    {
        $start = Carbon::parse($data->start_time);
        $end = Carbon::parse($data->end_time);
        $minutes = $start->diffInMinutes($end, false);

        if ($minutes < config('flavor.reservations.min_duration_minutes')) {
            throw new DomainException(__('errors.reservation_too_short', [
                'minutes' => config('flavor.reservations.min_duration_minutes'),
            ]), 422);
        }

        if ($minutes > config('flavor.reservations.max_duration_minutes')) {
            throw new DomainException(__('errors.reservation_too_long', [
                'minutes' => config('flavor.reservations.max_duration_minutes'),
            ]), 422);
        }

        if ($start->isPast()) {
            throw new DomainException(__('errors.reservation_in_past'), 422);
        }
    }

    protected function uniqueCode(): string
    {
        do {
            $code = Ticket::code(8);
        } while (Reservation::query()->where('reservation_code', $code)->exists());

        return $code;
    }
}

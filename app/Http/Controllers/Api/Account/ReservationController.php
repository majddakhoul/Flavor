<?php

namespace App\Http\Controllers\Api\Account;

use App\DTOs\ReservationData;
use App\Enums\ReservationStatus;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Account\StoreReservationRequest;
use App\Http\Resources\ReservationResource;
use App\Http\Resources\TableResource;
use App\Models\Reservation;
use App\Services\Reservations\ReservationService;
use App\Services\Reservations\TableAvailabilityService;
use App\Support\QueryOptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function __construct(
        private readonly ReservationService $reservations,
        private readonly TableAvailabilityService $availability,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $paginator = $this->reservations->paginateForCustomer(
            $request->user()->customer->id,
            QueryOptions::fromRequest($request, ['tables'])
        );

        return $this->paginated($paginator, ReservationResource::class);
    }

    public function availability(Request $request): JsonResponse
    {
        $start = $request->filled('start_time') ? $request->date('start_time') : now()->addHour()->startOfHour();
        $end = $request->filled('end_time') ? $request->date('end_time') : $start->copy()->addHours(2);

        $tables = $this->availability->availableBetween(
            $start->toDateTimeString(),
            $end->toDateTimeString(),
            (int) $request->input('party_size', 1)
        );

        return $this->collection($tables, TableResource::class);
    }

    public function store(StoreReservationRequest $request): JsonResponse
    {
        $reservation = $this->reservations->create($request->user(), ReservationData::fromArray($request->validated()));

        return $this->created(
            new ReservationResource($reservation->load('tables')),
            __('flash.reservations.created', ['code' => $reservation->reservation_code])
        );
    }

    public function show(Reservation $reservation): JsonResponse
    {
        $this->authorize('view', $reservation);

        return $this->ok(new ReservationResource($reservation->load(['tables', 'order'])));
    }

    public function cancel(Reservation $reservation): JsonResponse
    {
        $this->authorize('cancel', $reservation);

        $reservation = $this->reservations->transition($reservation, ReservationStatus::Cancelled);

        return $this->ok(new ReservationResource($reservation), __('flash.reservations.cancelled'));
    }
}

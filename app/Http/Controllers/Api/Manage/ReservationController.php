<?php

namespace App\Http\Controllers\Api\Manage;

use App\DTOs\ReservationData;
use App\Enums\ReservationStatus;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Manage\ReservationStatusRequest;
use App\Http\Requests\Manage\ReservationTablesRequest;
use App\Http\Requests\Manage\StaffReservationRequest;
use App\Http\Resources\ReservationResource;
use App\Models\Reservation;
use App\Services\Reservations\ReservationService;
use App\Support\QueryOptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function __construct(private readonly ReservationService $reservations)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Reservation::class);

        return $this->paginated(
            $this->reservations->paginate(QueryOptions::fromRequest($request, ['tables', 'customer.user'])),
            ReservationResource::class
        );
    }

    public function statistics(): JsonResponse
    {
        $this->authorize('viewAny', Reservation::class);

        return $this->ok($this->reservations->statistics());
    }

    public function store(StaffReservationRequest $request): JsonResponse
    {
        $this->authorize('create', Reservation::class);

        $reservation = $this->reservations->create($request->user(), ReservationData::fromArray($request->validated()));

        return $this->created(
            new ReservationResource($reservation->load('tables')),
            __('flash.reservations.created', ['code' => $reservation->reservation_code])
        );
    }

    public function show(Reservation $reservation): JsonResponse
    {
        $this->authorize('view', $reservation);

        return $this->ok(new ReservationResource(
            $reservation->load(['tables', 'customer.user', 'employee.user', 'order'])
        ));
    }

    public function status(ReservationStatusRequest $request, Reservation $reservation): JsonResponse
    {
        $this->authorize('transition', $reservation);

        $reservation = $this->reservations->transition($reservation, ReservationStatus::from($request->validated()['status']));

        return $this->ok(new ReservationResource($reservation), __('flash.reservations.status_updated'));
    }

    public function tables(ReservationTablesRequest $request, Reservation $reservation): JsonResponse
    {
        $this->authorize('update', $reservation);

        $data = $request->validated();
        $reservation = $this->reservations->updateTables($reservation, $data['table_ids'], $data['start_time'], $data['end_time']);

        return $this->ok(new ReservationResource($reservation), __('flash.reservations.tables_updated'));
    }

    public function destroy(Reservation $reservation): JsonResponse
    {
        $this->authorize('delete', $reservation);

        $this->reservations->delete($reservation);

        return $this->noContent(__('flash.reservations.deleted'));
    }
}

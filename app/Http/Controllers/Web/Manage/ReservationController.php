<?php

namespace App\Http\Controllers\Web\Manage;

use App\DTOs\ReservationData;
use App\Enums\ReservationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Manage\ReservationStatusRequest;
use App\Http\Requests\Manage\ReservationTablesRequest;
use App\Http\Requests\Manage\StaffReservationRequest;
use App\Models\Reservation;
use App\Services\Reservations\ReservationService;
use App\Services\Reservations\TableAvailabilityService;
use App\Support\QueryOptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReservationController extends Controller
{
    public function __construct(
        private readonly ReservationService $reservations,
        private readonly TableAvailabilityService $availability,
    ) {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Reservation::class);

        return view('manage.reservations.index', [
            'reservations' => $this->reservations->paginate(QueryOptions::fromRequest($request)),
            'statistics' => $this->reservations->statistics(),
            'statuses' => ReservationStatus::options(),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Reservation::class);

        $start = $request->filled('start_time') ? $request->date('start_time') : now()->addHour()->startOfHour();
        $end = $request->filled('end_time') ? $request->date('end_time') : $start->copy()->addHours(2);

        return view('manage.reservations.create', [
            'tables' => $this->availability->availableBetween(
                $start->toDateTimeString(),
                $end->toDateTimeString(),
                (int) $request->input('party_size', 1)
            ),
            'start' => $start,
            'end' => $end,
            'partySize' => (int) $request->input('party_size', 2),
        ]);
    }

    public function store(StaffReservationRequest $request): RedirectResponse
    {
        $this->authorize('create', Reservation::class);

        $reservation = $this->reservations->create($request->user(), ReservationData::fromArray($request->validated()));

        return $this->done('manage.reservations.show', __('flash.reservations.created', [
            'code' => $reservation->reservation_code,
        ]), $reservation);
    }

    public function show(Reservation $reservation): View
    {
        $this->authorize('view', $reservation);

        return view('manage.reservations.show', [
            'reservation' => $reservation->load(['tables', 'customer.user', 'employee.user', 'order']),
            'statuses' => ReservationStatus::options(),
        ]);
    }

    public function status(ReservationStatusRequest $request, Reservation $reservation): RedirectResponse
    {
        $this->authorize('transition', $reservation);

        $this->reservations->transition($reservation, ReservationStatus::from($request->validated()['status']));

        return $this->done('manage.reservations.show', __('flash.reservations.status_updated'), $reservation);
    }

    public function tables(ReservationTablesRequest $request, Reservation $reservation): RedirectResponse
    {
        $this->authorize('update', $reservation);

        $data = $request->validated();
        $this->reservations->updateTables($reservation, $data['table_ids'], $data['start_time'], $data['end_time']);

        return $this->done('manage.reservations.show', __('flash.reservations.tables_updated'), $reservation);
    }

    public function destroy(Reservation $reservation): RedirectResponse
    {
        $this->authorize('delete', $reservation);

        $this->reservations->delete($reservation);

        return $this->done('manage.reservations.index', __('flash.reservations.deleted'));
    }
}

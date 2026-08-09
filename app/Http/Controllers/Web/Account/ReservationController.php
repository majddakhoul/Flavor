<?php

namespace App\Http\Controllers\Web\Account;

use App\DTOs\ReservationData;
use App\Enums\ReservationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Account\StoreReservationRequest;
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
        return view('account.reservations.index', [
            'reservations' => $this->reservations->paginateForCustomer(
                $request->user()->customer->id,
                QueryOptions::fromRequest($request)
            ),
        ]);
    }

    public function create(Request $request): View
    {
        $start = $request->filled('start_time')
            ? $request->date('start_time')
            : now()->addHour()->startOfHour();
        $end = $request->filled('end_time')
            ? $request->date('end_time')
            : $start->copy()->addHours(2);

        return view('account.reservations.create', [
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

    public function store(StoreReservationRequest $request): RedirectResponse
    {
        $reservation = $this->reservations->create(
            $request->user(),
            ReservationData::fromArray($request->validated())
        );

        return $this->done(
            'account.reservations.show',
            __('flash.reservations.created', ['code' => $reservation->reservation_code]),
            $reservation
        );
    }

    public function show(Reservation $reservation): View
    {
        $this->authorize('view', $reservation);

        return view('account.reservations.show', [
            'reservation' => $reservation->load(['tables', 'order']),
        ]);
    }

    public function cancel(Reservation $reservation): RedirectResponse
    {
        $this->authorize('cancel', $reservation);

        $this->reservations->transition($reservation, ReservationStatus::Cancelled);

        return $this->done('account.reservations.index', __('flash.reservations.cancelled'));
    }
}

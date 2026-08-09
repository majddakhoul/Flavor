@extends('layouts.account')

@section('title', __('app.my_reservations'))

@section('actions')
    <a class="btn" href="{{ route('account.reservations.create') }}"><x-icon name="plus" />{{ __('app.book_table') }}</a>
@endsection

@section('content')
    <x-card>
        <x-toolbar :action="route('account.reservations.index')" :sorts="['date' => __('app.date'), 'party_size' => __('app.party_size')]">
            <x-filter-select name="status" :label="__('app.status')" :options="\App\Enums\ReservationStatus::options()" />
        </x-toolbar>

        @forelse ($reservations as $reservation)
            <div class="between" style="padding:.75rem 0;border-bottom:1px solid var(--color-border)">
                <div>
                    <b class="mono">{{ $reservation->reservation_code }}</b>
                    <p class="small muted" style="margin:0">
                        {{ $reservation->starts_at?->translatedFormat('d M Y · H:i') }} — {{ $reservation->ends_at?->translatedFormat('H:i') }}
                        · {{ $reservation->party_size }} {{ __('app.guests') }}
                    </p>
                </div>
                <div class="cluster">
                    <x-status-badge :status="$reservation->status" />
                    <a class="btn btn--ghost btn--sm" href="{{ route('account.reservations.show', $reservation) }}"><x-icon name="eye" /></a>
                </div>
            </div>
        @empty
            <x-empty-state illustration="empty-calendar" :title="__('app.no_reservations')" :description="__('app.no_reservations_hint')">
                <a class="btn" href="{{ route('account.reservations.create') }}">{{ __('app.book_table') }}</a>
            </x-empty-state>
        @endforelse

        {{ $reservations->links() }}
    </x-card>
@endsection

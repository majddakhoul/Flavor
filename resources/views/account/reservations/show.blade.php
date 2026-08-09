@extends('layouts.account')

@section('title', $reservation->reservation_code)

@section('actions')
    <a class="btn btn--ghost btn--sm" href="{{ route('account.reservations.index') }}"><x-icon name="back" />{{ __('app.back') }}</a>
    @can('cancel', $reservation)
        <x-delete-form :action="route('account.reservations.cancel', $reservation)" :label="__('app.cancel_reservation')" :confirm="__('app.confirm_cancel_reservation')" />
    @endcan
@endsection

@section('content')
    <div class="ticket">
        <p class="eyebrow">{{ __('app.reservation_ticket') }}</p>
        <span class="ticket__code">{{ $reservation->reservation_code }}</span>
        <hr class="ticket__rule">
        <dl style="margin:0">
            <div class="ticket__row"><dt>{{ __('app.status') }}</dt><dd>{{ $reservation->status->label() }}</dd></div>
            <div class="ticket__row"><dt>{{ __('app.date') }}</dt><dd>{{ $reservation->date?->translatedFormat('d M Y') }}</dd></div>
            <div class="ticket__row"><dt>{{ __('app.from') }}</dt><dd>{{ $reservation->starts_at?->format('H:i') }}</dd></div>
            <div class="ticket__row"><dt>{{ __('app.to') }}</dt><dd>{{ $reservation->ends_at?->format('H:i') }}</dd></div>
            <div class="ticket__row"><dt>{{ __('app.guests') }}</dt><dd>{{ $reservation->party_size }}</dd></div>
            <div class="ticket__row"><dt>{{ __('app.tables') }}</dt><dd>{{ $reservation->tables->pluck('table_number')->implode(', ') }}</dd></div>
        </dl>
        <div class="ticket__total"><span>{{ __('app.table_charge') }}</span><span class="price">@money($reservation->tables_cost)</span></div>
        @if ($reservation->special_requests)
            <p class="small muted" style="margin-top:var(--space-3)">{{ $reservation->special_requests }}</p>
        @endif
    </div>

    <x-card :title="__('app.tables')">
        <div class="floor">
            @foreach ($reservation->tables as $table)
                <div class="floor__table">
                    <b>{{ $table->table_number }}</b>
                    <span class="small muted">{{ $table->location->label() }} · {{ $table->capacity }} {{ __('app.seats') }}</span>
                    <span class="small mono">{{ \Illuminate\Support\Carbon::parse($table->pivot->start_time)->format('H:i') }} — {{ \Illuminate\Support\Carbon::parse($table->pivot->end_time)->format('H:i') }}</span>
                </div>
            @endforeach
        </div>
    </x-card>
@endsection

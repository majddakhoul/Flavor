@extends('layouts.manage')

@section('title', $reservation->reservation_code)
@section('eyebrow', __('app.section_floor'))

@section('actions')
    <a class="btn btn--ghost btn--sm" href="{{ route('manage.reservations.index') }}"><x-icon name="back" />{{ __('app.back') }}</a>
    @can('delete', $reservation)
        <x-delete-form :action="route('manage.reservations.destroy', $reservation)" :label="__('app.delete')" />
    @endcan
@endsection

@section('content')
    <div class="split">
        <div class="stack">
            <x-card :title="__('app.change_status')">
                <form method="POST" action="{{ route('manage.reservations.status', $reservation) }}" class="cluster">
                    @csrf
                    @method('PATCH')
                    <x-select name="status" :options="$statuses" :selected="$reservation->status->value" style="max-width:240px" />
                    <button class="btn" type="submit"><x-icon name="check" />{{ __('app.apply') }}</button>
                </form>
                <p class="field__hint" style="margin-top:var(--space-2)">{{ __('app.status_transition_hint') }}</p>
            </x-card>

            <x-card :title="__('app.move_tables')">
                <form method="POST" action="{{ route('manage.reservations.tables', $reservation) }}">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-2">
                        <x-field name="start_time" :label="__('app.from')" required>
                            <input class="input" type="datetime-local" name="start_time" value="{{ $reservation->starts_at?->format('Y-m-d\TH:i') }}">
                        </x-field>
                        <x-field name="end_time" :label="__('app.to')" required>
                            <input class="input" type="datetime-local" name="end_time" value="{{ $reservation->ends_at?->format('Y-m-d\TH:i') }}">
                        </x-field>
                    </div>

                    <div class="floor" style="margin-bottom:var(--space-3)">
                        @foreach (\App\Models\Table::query()->active()->orderBy('table_number')->get() as $table)
                            <label class="floor__table {{ $reservation->tables->contains($table->id) ? 'is-busy' : '' }}">
                                <span class="cluster" style="justify-content:space-between">
                                    <b>{{ $table->table_number }}</b>
                                    <input type="checkbox" name="table_ids[]" value="{{ $table->id }}" @checked($reservation->tables->contains($table->id))>
                                </span>
                                <span class="small muted">{{ $table->location->label() }} · {{ $table->capacity }}</span>
                            </label>
                        @endforeach
                    </div>

                    <button class="btn" type="submit"><x-icon name="table" />{{ __('app.update_tables') }}</button>
                </form>
            </x-card>
        </div>

        <div class="ticket">
            <p class="eyebrow">{{ __('app.reservation_ticket') }}</p>
            <span class="ticket__code">{{ $reservation->reservation_code }}</span>
            <hr class="ticket__rule">
            <dl style="margin:0">
                <div class="ticket__row"><dt>{{ __('app.status') }}</dt><dd>{{ $reservation->status->label() }}</dd></div>
                <div class="ticket__row"><dt>{{ __('app.source') }}</dt><dd>{{ $reservation->type->label() }}</dd></div>
                <div class="ticket__row"><dt>{{ __('app.guest') }}</dt><dd>{{ $reservation->customer?->user?->full_name ?? '—' }}</dd></div>
                <div class="ticket__row"><dt>{{ __('app.handled_by') }}</dt><dd>{{ $reservation->employee?->user?->full_name ?? '—' }}</dd></div>
                <div class="ticket__row"><dt>{{ __('app.date') }}</dt><dd>{{ $reservation->date?->translatedFormat('d M Y') }}</dd></div>
                <div class="ticket__row"><dt>{{ __('app.party_size') }}</dt><dd>{{ $reservation->party_size }}</dd></div>
                <div class="ticket__row"><dt>{{ __('app.tables') }}</dt><dd>{{ $reservation->tables->pluck('table_number')->implode(', ') }}</dd></div>
                <div class="ticket__row"><dt>{{ __('app.duration') }}</dt><dd>{{ $reservation->duration_hours }} {{ __('app.hours') }}</dd></div>
            </dl>
            <div class="ticket__total"><span>{{ __('app.table_charge') }}</span><span class="price">@money($reservation->tables_cost)</span></div>
            @if ($reservation->special_requests)
                <p class="small muted" style="margin-top:var(--space-3)">{{ $reservation->special_requests }}</p>
            @endif
        </div>
    </div>
@endsection

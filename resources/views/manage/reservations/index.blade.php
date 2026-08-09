@extends('layouts.manage')

@section('title', __('app.reservations'))
@section('eyebrow', __('app.section_floor'))

@section('actions')
    <a class="btn" href="{{ route('manage.reservations.create') }}"><x-icon name="plus" />{{ __('app.new_reservation') }}</a>
@endsection

@section('content')
    <div class="grid grid-4">
        <x-stat :label="__('app.reservations_today')" :value="$statistics['today']" icon="calendar" />
        <x-stat :label="__('app.upcoming')" :value="$statistics['upcoming']" icon="clock" tone="info" />
        <x-stat :label="__('app.occupancy')" :value="$statistics['occupancy']['rate'] . '%'" icon="table" tone="teal"
                :meta="$statistics['occupancy']['busy'] . '/' . $statistics['occupancy']['total']" />
        <x-stat :label="__('app.cancelled')" :value="$statistics['by_status']['Cancelled'] ?? 0" icon="close" tone="danger" />
    </div>

    <x-card>
        <x-toolbar :action="route('manage.reservations.index')" :sorts="['date' => __('app.date'), 'party_size' => __('app.party_size')]">
            <x-filter-select name="status" :label="__('app.status')" :options="$statuses" />
            <div class="field">
                <label class="field__label" for="filter-date">{{ __('app.date') }}</label>
                <input class="input" id="filter-date" type="date" name="filters[date]" value="{{ request('filters.date') }}">
            </div>
        </x-toolbar>

        <div class="table-wrap">
            <table class="table">
                <thead>
                <tr>
                    <th>{{ __('app.code') }}</th>
                    <th>{{ __('app.guest') }}</th>
                    <th>{{ __('app.when') }}</th>
                    <th>{{ __('app.party_size') }}</th>
                    <th>{{ __('app.tables') }}</th>
                    <th>{{ __('app.status') }}</th>
                    <th style="text-align:end">{{ __('app.actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($reservations as $reservation)
                    <tr>
                        <td><a class="mono" href="{{ route('manage.reservations.show', $reservation) }}">{{ $reservation->reservation_code }}</a></td>
                        <td>{{ $reservation->customer?->user?->full_name ?? $reservation->employee?->user?->full_name ?? '—' }}<br>
                            <span class="small muted">{{ $reservation->type->label() }}</span></td>
                        <td class="small mono">{{ $reservation->starts_at?->translatedFormat('d M · H:i') }} — {{ $reservation->ends_at?->format('H:i') }}</td>
                        <td class="mono">{{ $reservation->party_size }}</td>
                        <td class="small mono">{{ $reservation->tables->pluck('table_number')->implode(', ') }}</td>
                        <td><x-status-badge :status="$reservation->status" /></td>
                        <td>
                            <div class="table__actions">
                                <a class="btn btn--ghost btn--sm" href="{{ route('manage.reservations.show', $reservation) }}"><x-icon name="eye" /></a>
                                @can('delete', $reservation)
                                    <x-delete-form :action="route('manage.reservations.destroy', $reservation)" />
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7">
                        <x-empty-state illustration="empty-calendar" :title="__('app.no_reservations')" :description="__('app.no_reservations_staff_hint')">
                            <a class="btn" href="{{ route('manage.reservations.create') }}">{{ __('app.new_reservation') }}</a>
                        </x-empty-state>
                    </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{ $reservations->links() }}
    </x-card>
@endsection

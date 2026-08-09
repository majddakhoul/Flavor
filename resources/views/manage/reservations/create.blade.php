@extends('layouts.manage')

@section('title', __('app.new_reservation'))
@section('eyebrow', __('app.section_floor'))

@section('actions')
    <a class="btn btn--ghost btn--sm" href="{{ route('manage.reservations.index') }}"><x-icon name="back" />{{ __('app.back') }}</a>
@endsection

@section('content')
    <x-card :title="__('app.choose_slot')">
        <form method="GET" action="{{ route('manage.reservations.create') }}" class="toolbar">
            <div class="field">
                <label class="field__label" for="start_time">{{ __('app.from') }}</label>
                <input class="input" id="start_time" name="start_time" type="datetime-local" value="{{ $start->format('Y-m-d\TH:i') }}">
            </div>
            <div class="field">
                <label class="field__label" for="end_time">{{ __('app.to') }}</label>
                <input class="input" id="end_time" name="end_time" type="datetime-local" value="{{ $end->format('Y-m-d\TH:i') }}">
            </div>
            <div class="field" style="max-width:130px">
                <label class="field__label" for="party_size">{{ __('app.guests') }}</label>
                <input class="input" id="party_size" name="party_size" type="number" min="1" max="40" value="{{ $partySize }}">
            </div>
            <button class="btn btn--sm" type="submit"><x-icon name="search" />{{ __('app.check_availability') }}</button>
        </form>
    </x-card>

    <form method="POST" action="{{ route('manage.reservations.store') }}">
        @csrf
        <input type="hidden" name="start_time" value="{{ $start->format('Y-m-d H:i:s') }}">
        <input type="hidden" name="end_time" value="{{ $end->format('Y-m-d H:i:s') }}">
        <input type="hidden" name="party_size" value="{{ $partySize }}">

        <x-card :title="__('app.available_tables')">
            @if ($tables->isEmpty())
                <x-empty-state illustration="empty-calendar" :title="__('app.no_tables')" :description="__('app.no_tables_hint')" />
            @else
                <div class="floor">
                    @foreach ($tables as $table)
                        <label class="floor__table is-free">
                            <span class="cluster" style="justify-content:space-between">
                                <b>{{ $table->table_number }}</b>
                                <input type="checkbox" name="table_ids[]" value="{{ $table->id }}">
                            </span>
                            <span class="small muted">{{ $table->location->label() }} · {{ $table->capacity }} {{ __('app.seats') }}</span>
                            <span class="small price">@money($table->price_per_hour)</span>
                        </label>
                    @endforeach
                </div>
            @endif
        </x-card>

        <x-card :title="__('app.reservation_details')" style="margin-top:var(--space-3)">
            <x-field name="special_requests" :label="__('app.special_requests')">
                <x-textarea name="special_requests" rows="3" />
            </x-field>
            <button class="btn" type="submit" @disabled($tables->isEmpty())><x-icon name="calendar" />{{ __('app.confirm_booking') }}</button>
        </x-card>
    </form>
@endsection

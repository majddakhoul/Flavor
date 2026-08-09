@extends('layouts.manage')

@section('title', __('app.locations'))
@section('eyebrow', __('app.section_people'))

@section('actions')
    <a class="btn" href="{{ route('manage.locations.create') }}"><x-icon name="plus" />{{ __('app.new_location') }}</a>
@endsection

@section('content')
    <x-card>
        <x-toolbar :action="route('manage.locations.index')" :sorts="['city' => __('app.city'), 'state' => __('app.state')]">
        </x-toolbar>

        <div class="table-wrap">
            <table class="table">
                <thead>
                <tr>
                    <th>{{ __('app.city') }}</th>
                    <th>{{ __('app.region') }}</th>
                    <th>{{ __('app.state') }}</th>
                    <th>{{ __('app.country') }}</th>
                    <th>{{ __('app.delivery_window') }}</th>
                    <th style="text-align:end">{{ __('app.actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($locations as $location)
                    <tr>
                        <td><b>{{ $location->city }}</b></td>
                        <td>{{ $location->region }}</td>
                        <td>{{ $location->state }}</td>
                        <td>{{ $location->country }}</td>
                        <td><span class="mono">{{ \Illuminate\Support\Str::of($location->delivery_time)->substr(0,5) }} → {{ $location->estimated_delivery }}</span></td>
                        <td>
                            <div class="table__actions">
                                <a class="btn btn--ghost btn--sm" href="{{ route('manage.locations.edit', $location) }}"><x-icon name="edit" /></a>
                                <x-delete-form :action="route('manage.locations.destroy', $location)" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">
                        <x-empty-state illustration="empty-box" :title="__('app.no_locations')" :description="__('app.no_locations_hint')">
                            <a class="btn" href="{{ route('manage.locations.create') }}">{{ __('app.new_location') }}</a>
                        </x-empty-state>
                    </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{ $locations->links() }}
    </x-card>
@endsection

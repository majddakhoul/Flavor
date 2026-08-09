@extends('layouts.manage')

@section('title', __('app.tables'))
@section('eyebrow', __('app.section_floor'))

@section('actions')
    <a class="btn" href="{{ route('manage.tables.create') }}"><x-icon name="plus" />{{ __('app.new_table') }}</a>
@endsection

@section('content')
    <x-card>
        <x-toolbar :action="route('manage.tables.index')" :sorts="['table_number' => __('app.number'), 'capacity' => __('app.capacity'), 'price_per_hour' => __('app.price')]">
            <x-filter-select name="location" :label="__('app.area')" :options="\App\Enums\TableLocation::options()" />
            <x-filter-select name="is_active" :label="__('app.status')" :options="['1' => __('app.active'), '0' => __('app.inactive')]" />
        </x-toolbar>

        <div class="table-wrap">
            <table class="table">
                <thead>
                <tr>
                    <th>{{ __('app.number') }}</th>
                    <th>{{ __('app.area') }}</th>
                    <th>{{ __('app.capacity') }}</th>
                    <th>{{ __('app.price_per_hour') }}</th>
                    <th>{{ __('app.status') }}</th>
                    <th style="text-align:end">{{ __('app.actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($tables as $table)
                    <tr>
                        <td><b class="mono">{{ $table->table_number }}</b></td>
                        <td>{{ $table->location->label() }}</td>
                        <td><span class="mono">{{ $table->capacity }}</span></td>
                        <td><span class="price">@money($table->price_per_hour)</span></td>
                        <td><x-badge :tone="$table->is_active ? 'success' : 'muted'">{{ $table->is_active ? __('app.active') : __('app.inactive') }}</x-badge></td>
                        <td>
                            <div class="table__actions">
                                <a class="btn btn--ghost btn--sm" href="{{ route('manage.tables.edit', $table) }}"><x-icon name="edit" /></a>
                                <x-delete-form :action="route('manage.tables.destroy', $table)" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">
                        <x-empty-state illustration="empty-calendar" :title="__('app.no_tables')" :description="__('app.no_tables_hint')">
                            <a class="btn" href="{{ route('manage.tables.create') }}">{{ __('app.new_table') }}</a>
                        </x-empty-state>
                    </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{ $tables->links() }}
    </x-card>
@endsection

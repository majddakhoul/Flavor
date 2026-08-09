@extends('layouts.manage')

@section('title', __('app.maintenances'))
@section('eyebrow', __('app.section_inventory'))

@section('actions')
    <a class="btn" href="{{ route('manage.maintenances.create') }}"><x-icon name="plus" />{{ __('app.new_maintenance') }}</a>
@endsection

@section('content')
    <div class="grid grid-4">
        <x-stat :label="__('app.gross_cost')" :value="\App\Support\Money::format($totals['price'])" icon="money" />
        <x-stat :label="__('app.net_cost')" :value="\App\Support\Money::format($totals['total_price'])" icon="receipt" tone="info" />
        <x-stat :label="__('app.total_discount')" :value="$totals['discount'] . '%'" icon="tag" tone="teal" />
        <x-stat :label="__('app.entries')" :value="$totals['entries']" icon="tools" />
    </div>

    <x-card>
        <x-toolbar :action="route('manage.maintenances.index')" :sorts="['price' => __('app.price'), 'created_at' => __('app.newest')]">
        </x-toolbar>

        <div class="table-wrap">
            <table class="table">
                <thead>
                <tr>
                    <th>{{ __('app.item') }}</th>
                    <th>{{ __('app.responsible') }}</th>
                    <th>{{ __('app.price') }}</th>
                    <th>{{ __('app.discount') }}</th>
                    <th>{{ __('app.total') }}</th>
                    <th style="text-align:end">{{ __('app.actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($maintenances as $maintenance)
                    <tr>
                        <td><b>{{ $maintenance->maintenance_item }}</b></td>
                        <td>{{ $maintenance->employee?->user?->full_name ?? '—' }}</td>
                        <td><span class="mono">@money($maintenance->price)</span></td>
                        <td>{{ $maintenance->discount ?? 0 }}%</td>
                        <td><span class="price">@money($maintenance->total_price)</span></td>
                        <td>
                            <div class="table__actions">
                                <a class="btn btn--ghost btn--sm" href="{{ route('manage.maintenances.edit', $maintenance) }}"><x-icon name="edit" /></a>
                                <x-delete-form :action="route('manage.maintenances.destroy', $maintenance)" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">
                        <x-empty-state illustration="empty-box" :title="__('app.no_maintenances')" :description="__('app.no_maintenances_hint')">
                            <a class="btn" href="{{ route('manage.maintenances.create') }}">{{ __('app.new_maintenance') }}</a>
                        </x-empty-state>
                    </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{ $maintenances->links() }}
    </x-card>
@endsection
